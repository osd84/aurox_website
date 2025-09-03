<?php

namespace OsdAurox;

use App\AppUrls;
use OsdAurox\Cache;

class Sec
{

    public static function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Récupère et nettoie un paramètre depuis GET, POST ou REQUEST.
     * XSS possibles c'est un filtre d'entrée pas de sortie
     *
     * @param string $key    Clé du paramètre à récupérer
     * @param string $type   Type de nettoyage à appliquer (défaut: 'alphaextra')
     *                       Les types possibles sont: 'int', 'float', 'price', 'bool', 'email', 'ip',
     *                       'ipv4', 'ipv6', 'url', 'alpha', 'alphaextra', 'aZ09', 'nohtml',
     *                       'alphanohtml', 'restricthtml'
     * @param int $source    Source du paramètre: 0 pour GET, 1 pour POST, 2 pour REQUEST, 3 pour POST puis GET (défaut: 3)
     *
     * @return mixed         Valeur nettoyée selon le type spécifié, ou null si non trouvée ou invalide
     */
    public static function getParam(string $key, string $type = 'aZ09extra', int $source = 3, bool $lowerCase = False, bool $upperCase = false): mixed
    {
        $raw = null;
        if ($source === 1 || $source === 3) {
            $raw = $_POST[$key] ?? null;
        }
        if ($raw === null && ($source === 0 || $source === 3)) {
            $raw = $_GET[$key] ?? null;
        }
        if ($source === 2) {
            $raw = $_REQUEST[$key] ?? null;
        }
        if (is_string($raw)) {
            $raw = trim($raw);
        }

        if($lowerCase) {
            $raw = mb_strtolower($raw);
        }
        if($upperCase) {
            $raw = mb_strtoupper($raw);
        }

        return self::sanitize($raw, $type);
    }

    public static function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // Check IP from internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Check IP is passed from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // Check IP address from remote address
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        }
        return $ip;
    }


    /**
     * Sanitise une valeur selon le type spécifié.
     * XSS possibles c'est un filtre d'entrée pas de sortie
     *
     * Cette méthode permet de nettoyer et convertir une valeur selon différents types :
     * - int: conversion en entier
     * - float: conversion en nombre flottant (remplace la virgule par un point)
     * - price: extraction et conversion en nombre flottant pour un prix
     * - bool: conversion en booléen
     * - email: validation d'adresse email
     * - ip: validation d'adresse IP
     * - ipv4: validation d'adresse IPv4
     * - ipv6: validation d'adresse IPv6
     * - url: validation d'URL
     * - alpha: ne conserve que les caractères alphabétiques
     * - alphaextra: ne conserve que les caractères alphabétiques, espaces et tirets
     * - aZ09: ne conserve que les caractères alphanumériques
     * - aZ09extra: ne conserve que les caractères alphanumériques, espaces et tirets
     * - 09: ne conserve que les caractères numériques
     * - nohtml: supprime toutes les balises HTML
     * - alphanohtml: supprime les balises HTML et ne conserve que les caractères alphabétiques
     * - restricthtml: ne conserve que certaines balises HTML basiques
     *
     * Les tableaux sont rejetés et retournent null.
     *
     * @param mixed $value La valeur à sanitiser
     * @param string $type Le type de sanitisation à appliquer
     * @return mixed La valeur sanitisée selon le type spécifié, ou null si la valeur est un tableau ou si le type est invalide
     */
    protected static function sanitize(mixed $value, string $type): mixed
    {

        // tableau rejeté, à mettre à plat
        if (is_array($value)) return null;

        if($value === null) {
            return null;
        }

        switch ($type) {
            case 'int':
                return (int) $value;

            case 'float':
                $val = str_replace(',', '.', $value);
                return is_numeric($val) ? (float) $val : 0.0;

            case 'price':
                $clean = preg_replace('/[^\d,\.]/', '', $value);
                $clean = str_replace(',', '.', $clean);
                return (float) $clean;

            case 'bool':
                return (bool) $value;

            case 'email':
                return filter_var(trim($value), FILTER_VALIDATE_EMAIL) ?: '';

            case 'ip':
                return filter_var(trim($value), FILTER_VALIDATE_IP) ?: '';

            case 'ipv4':
                return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ?: '';

            case 'ipv6':
                return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ?: '';

            case 'url':
                return filter_var(trim($value), FILTER_VALIDATE_URL) ?: '';

            case 'alpha':
                return preg_replace('/[^a-zA-Z]/', '', strip_tags($value));

            case 'alphaextra':
                return preg_replace('/[^a-zA-Z \-]/', '', strip_tags($value));

            case 'aZ09':
                return preg_replace('/[^a-zA-Z0-9]/', '', strip_tags($value));

            case 'aZ09extra':
                return preg_replace('/[^a-zA-Z0-9 \-]/', '', strip_tags($value));

            case '09':
                return preg_replace('/[^0-9]/', '', strip_tags($value));

            case 'nohtml':
                return strip_tags($value);

            case 'alphanohtml':
                return preg_replace('/[^a-zA-Z]/', '', strip_tags($value));

            case 'restricthtml':
                return strip_tags($value, '<b><i><u><strong><em>');

            case 'raw':
                // Ne fait aucune modification, retourne la valeur brute à part le trim par défaut, converti en string
                return $value;

            case 'date_us':
                $value = trim(strip_tags($value));
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $dt = \DateTime::createFromFormat('Y-m-d', $value);
                    if ($dt && $dt->format('Y-m-d') === $value) {
                        return $value;
                    }
                }
                return '';

            case 'datetime_us':
                $value = trim(strip_tags($value));
                if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value)) {
                    $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
                    if ($dt && $dt->format('Y-m-d H:i:s') === $value) {
                        return $value;
                    }
                }
                return '';

            default:
                throw new \InvalidArgumentException('Invalid type: ' . $type);
        }

    }

    public static function h($string)
    {
        if ($string === null || $string === '') {
            return '';
        }
        if (is_array($string)) {
            return '<array>';
        }
        if (is_object($string) || is_resource($string)) {
            return '<complex-type>';
        }
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Récupère la valeur du paramètre 'action' et la nettoie pour prévenir les attaques XSS.
     *
     * Cette méthode récupère la valeur du paramètre 'action' à partir de la source spécifiée,
     * la nettoie pour supprimer le HTML et les caractères potentiellement dangereux,
     * puis supprime les espaces au début et à la fin.
     *
     * @param string $default Valeur par défaut à retourner si le paramètre 'action' n'existe pas
     * @param int $source Source des données (1=GET, 2=POST, 3=GET+POST, etc.)
     * @return string Valeur nettoyée du paramètre 'action' ou la valeur par défaut
     */
    public static function getAction(string $default = 'noaction',  int $source = 3)
    {
        $action = Sec::getParam('action', 'alphaextra', $source);
        if (empty($action)) {
            $action = $default;
        }
        return Sec::hNoHtml(trim($action));
    }

    public static function safeForLikeStrong($string)
    {
        if(!is_string($string)) {
            return '';
        }
        if(!$string) {
            return '';
        }

        $string = trim($string);

        if (strlen($string) < 2) {
            return '';
        }

        $string = substr($string, 0, 250);
        $string = strip_tags($string);

        $cleanString = preg_replace('/[^a-zA-Z0-9\p{L}\s]/u', '', $string);
        return str_replace(['%', '_'], ['\%', '\_'], $cleanString);
    }

    public static function safeForLike($string)
    {
        if (!is_string($string)) {
            return '';
        }
        if (!$string) {
            return '';
        }

        $string = trim($string);

        if (strlen($string) < 3) {
            return '';
        }

        $string = substr($string, 0, 250);

        $string = strip_tags($string);
        return str_replace(['%', '_'], ['\%', '\_'], $string);
    }


    public static function jsonDatas()
    {
        $jsonData = file_get_contents('php://input');
        $datas = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        $data_sanitized = [];
        foreach ($datas as $key => $value) {
            $data_sanitized[$key] = $value;
            if($value === 'on') {
                $data_sanitized[$key] = true;
            }
        }
        return $data_sanitized;
    }

    public static function isAdminOrDie($flash = true, $redirect = true)
    {
        if (Sec::isAdminBool() === false) {
            if ($flash) {
                Flash::add('danger', 'Accès refusé');
            }
            if ($redirect) {
                header('Location: ' . AppUrls::LOGIN);
                exit;
            }
            die('Accès refusé');
        }
    }

    public static function isAdminBool()
    {
        if (!isset($_SESSION['user'])){
            return false;
        }
        if (!isset($_SESSION['user']['role'])){
            return false;
        }
        if($_SESSION['user']['role'] === 'admin'){
            return true;
        }
        return false;
    }

    public static function isRoleOrDie($role, $flash = true, $redirect = true)
    {
        if (Sec::isRoleBool($role) === false) {
            if ($flash) {
                Flash::add('danger', 'Accès refusé');
            }
            if ($redirect) {
                header('Location: ' . AppUrls::LOGIN);
                exit;
            }
            die('Accès refusé');
        }
    }

    public static function isRoleBool($role)
    {
        if (!isset($_SESSION['user'])){
            return false;
        }
        if (!isset($_SESSION['user']['role'])){
            return false;
        }
        if($_SESSION['user']['role'] === $role){
            return true;
        }
        return false;
    }

    public static function isLogged($flash = true, $redirect = true): void
    {
        if (!isset($_SESSION['user'])) {
            if ($flash) {
                Flash::add('danger', 'Accès refusé');
            }
            if ($redirect) {
                header('Location: ' . AppUrls::LOGIN);
                exit;
            }
            die('Accès refusé');
        }
    }

    public static function isLoggedBool(): bool
    {
        if (!isset($_SESSION['user'])) {
            return false;
        }

        if (empty($_SESSION['user']['role'])) {
            return false;
        }

        return true;
    }


    public static function noneCsp(): string
    {
        return htmlspecialchars(NONCE, ENT_QUOTES, 'UTF-8');
    }

    public static function getPage():int
    {
        $page = Sec::h($_GET['page'] ?? 1);

        if ($page < 1) {
            $page = 1;
        }
        return (int) $page;
    }

    public static function getPerPage(): int
    {
        $perPage = Sec::h($_GET['per_page'] ?? 10);
        if ($perPage < 1) {
            $perPage = 10;
        }
        return (int) $perPage;
    }

    public static function hNoHtml($string): string
    {
        if ($string === null || $string === '') {
            return '';
        }
        if (is_array($string)) {
            return '{array}';
        }
        if (is_object($string) || is_resource($string)) {
            return '{complex-type}';
        }

        $string = strip_tags($string);
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public static function getUserIdOrDie(): ?int
    {
        if (!isset($_SESSION['user'])) {
            throw new \LogicException('User not logged');
        }
        if (!isset($_SESSION['user']['id'])) {
            throw new \LogicException('User not logged');
        }
        return (int) $_SESSION['user']['id'];
    }

    /**
     * Extrait et sécurise les valeurs d'une clé spécifique d'un tableau multidimensionnel
     *
     * @param array $array Tableau multidimensionnel source
     * @param string $key Clé à extraire
     * @param bool $strictFiltering Si true, utilise strip_tags + htmlspecialchars (hNoHtml), sinon uniquement htmlspecialchars (h)
     * @return array Tableau des valeurs extraites et sécurisées
     */
    public static function hArrayKey(array $array, string $key): array
    {
        if(!is_array($array) || empty($array)) {
            return [];
        }

        if (!array_key_exists($key, $array[0])) {
            return [];
        }

        $values = array_column($array, $key);
        if(!$values) {
            return [];
        }
        return array_map([self::class, 'hNoHtml'], $values);
    }

    /**
     * Extrait les IDs d'un tableau et les convertit en entiers
     *
     * @param array $array Tableau source
     * @return array Tableau des IDs convertis en entiers
     */
    public static function hArrayInt(array $array, string $key): array
    {
        if(!is_array($array) || empty($array)) {
            return [];
        }
        $safeArr = self::hArrayKey($array, $key);
        if(empty($safeArr)) {
            return [];
        }

        return array_map('intval', $safeArr);
    }

    public static function storeReferer(): void
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? null;
        $host = $_SERVER['HTTP_HOST'] ?? null;

        if(empty($requestUri) || empty($host)) {
            return;
        }

        $_SESSION['previous_url'] = [
            'url' => $requestUri,
            'host' => $host,
            'timestamp' => time()
        ];
    }

    public static function getReferer(): ?string
    {
        if (!isset($_SESSION['previous_url'])) {
            return null;
        }

        $data = $_SESSION['previous_url'];
        $currentHost = $_SERVER['HTTP_HOST'] ?? null;

        // Vérification du host
        if (empty($currentHost) || $data['host'] !== $currentHost) {
            unset($_SESSION['previous_url']);
            return null;
        }

        return $data['url'];
    }

    public static function redirectReferer(string $defaultPath = '/'): void
    {
        $referer = self::getReferer();

        if (empty($referer)) {
            $redirectUrl = $defaultPath;
        } else {
            // Vérification que l'URL est relative (commence par /) pour éviter les open redirects
            if (strpos($referer, '/') !== 0 || strpos($referer, '//') === 0) {
                // URL invalide ou tentative d'URL absolue, utiliser le chemin par défaut
                $redirectUrl = $defaultPath;
            } else {
                $redirectUrl = $referer;
            }
        }
        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * Génère un identifiant unique universel (UUID) version 4.
     *
     * Cette méthode crée un UUID version 4 conforme à la RFC 4122, basé sur des octets aléatoires.
     *
     * @return string UUID version 4 au format standard (8-4-4-4-12 caractères)
     */
    public static function uuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function setRateLimit(int $seconds, int $maxRequests = 10, string $endpoint = ''): bool
    {
        $ip = self::getRealIpAddr();
        $hashedIp = md5($ip);
        $cache = new Cache('rate_limit');
        // safe string
        $endpoint = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $endpoint);
        if (!$endpoint) {
            $endpoint = 'global';
        }
        $key = 'rate_' . $endpoint . '_' . $hashedIp;
        $data = $cache->get($key);

        if ($data === null) {
            $cache->set($key, 1, $seconds);
            return true;
        }

        if ($data >= $maxRequests) {
            return false;
        }

        $cache->set($key, $data + 1, $seconds);
        return true;
    }

    public static function setRateLimitOrDie(int $seconds, int $maxRequests = 10, string $endpoint = ''): void
    {
        if (!self::setRateLimit($seconds, $maxRequests, $endpoint)) {
            header('HTTP/1.1 429 Too Many Requests');
            die('Trop de requêtes, veuillez patienter.');
        }
    }

}