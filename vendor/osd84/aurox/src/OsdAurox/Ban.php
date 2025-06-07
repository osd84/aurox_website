<?php

namespace OsdAurox;


class Ban
{
    public static Ban|null $banInstance = null;


    public static array $blackListWords = [
        '/.git/config',
        '.git/config',
        '/wp-admin/admin-ajax.php',
        '/xmlrpc.php',
        '/.env',
        '/.git',
        '/idea',
        '/.idea',
        '/.env.local',
        '/config/.env',
        '/.git/config',
        '/.env.production',
        '/.svn/entries',
        '/vendor/.env',
        '/docker-compose.yml',
        '/access.log',
        '/nginx.conf',
        '/cgit/config',
        '/apache2.conf',
        '/.kube/config',
        '/node_modules/package.json',
        '/.well-known/security.txt',
        '/.well-known/openid-configuration',
        '/.well-known/apple-app-site-association',
        '/api/v1/secrets',
        '/suitecrm/logfile.log',
        '/manager/html',
        '/zabbix/index.php',
        '/nagios/cgi-bin/status.cgi',
        '/.svn/text-base/index.php.svn-base',
        '/backend/.env',
        '/node_modules/.bin/',
        '/index.php.bak',
        '/.npmrc',
        '/config.old',
        '/phpinfo.php',
        '/.DS_Store',
        '/index.php.swp',
        '/config.php.swo',
        '/uploads',
        '/files',
        '/storage/logs/laravel.log',
        '/logs/access.log',
        '/logs/error.log',
        '/.editorconfig',
        '/.eslintrc',
        '/config.yml',
        '/database.yml',
        '/config.json',
        '/secrets.json',
        '/package.json',
        '/composer.json',
        '/composer.lock',
        '/.gitlab-ci.yml',
        '/.idea/workspace.xml',
        '/.bash_history',
        '/.vscode/settings.json',
        '/wp-content/debug.log',
        '/api/keys',
        '/debug/vars',
        '/env',
        '/.aws/credentials',
        '/.gcloud/credentials.json',
        '/wp-config.php',
        '/.svn/auth/',
        '/.git/objects',
        '/.bzr/branch/branch.conf',
        '/.hg/dirstate',
        '/.hg/hgrc',
        '/portal/.env',
        '/env/.env',
        '/api/.env',
        '/app/.env',
        '/dev/.env',
        '/new/.env',
        '/new/.env.local',
        '/new/.env.staging',
        '/_phpinfo.php',
        '/_profiler/phpinfo',
        '/_profiler/phpinfo/info.php',
        '/wp-config',
        '/aws-secret.yaml',
        '/awstats/.env',
        '/conf/.env',
        '/cron/.env',
        '/www/.env',
        '/env.backup',
        '/xampp/phpinfo.php',
        '/laravel/info.php',
        '/js/.env',
        '/laravel/.env',
        '/laravel/core/.env',
        '/mail/.env',
        '/mailer/.env',
        '/laravel/.env.local',
        '/laravel/core/.env.production',
        '/main/.env',
    ];


    public array $blackList = [];
    public string $realIp = '';
    public string $banMessage = '';


    private function __construct()
    {
        // sigleton
    }
    private function __clone()
    {
        // sigleton
    }

    public function init($banMessage='Oops !'): void
    {
        if(!file_exists(APP_ROOT . '/blacklist__.php')) {
            file_put_contents(APP_ROOT . '/blacklist__.php', "<?php \r\n");
        }
        if(!file_exists(APP_ROOT . '/banlog__.php')) {
            file_put_contents(APP_ROOT . '/banlog__.php', "<?php \r\n");
        }

        $this->banMessage = $banMessage;
    }

    public static function getInstance($banMessage='Oops !'): ?Ban
    {
        if(!self::$banInstance) {
            $o_ban = new self();
            $o_ban->init( $banMessage);
            self::$banInstance = $o_ban;
        }
        if($banMessage){
            self::$banInstance->banMessage = $banMessage;
        }
        return self::$banInstance;
    }

    public static function blockBlackListed($output=false): bool
    {
        $instance = self::getInstance();
        $instance->realIp = Sec::getRealIpAddr();
        $instance->blackList = $instance->loadBanList();
        if (in_array($instance->realIp, $instance->blackList)) {
            if($output) {
                return true;
            }
            header('HTTP/1.0 403 Forbidden');
            die($instance->banMessage);
        }
        return false;
    }

    public static function checkRequest($output=false): bool
    {
        $instance = self::getInstance();
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $currentPath = htmlspecialchars($uri, ENT_QUOTES, 'UTF-8');
        if(isset($_GET['url'])) {
            $currentPath = htmlspecialchars($_GET['url'], ENT_QUOTES, 'UTF-8');
        }
        $sanitarizedBlackList = [];
        foreach (self::$blackListWords as $word) {
            $sanitarizedBlackList[] = htmlspecialchars($word, ENT_QUOTES, 'UTF-8');
        }
        if (in_array($currentPath, $sanitarizedBlackList)) {
            $date = date('Y-m-d H:i:s');
            $log = "$instance->realIp\n";
            file_put_contents(APP_ROOT . '/blacklist__.php', $log, FILE_APPEND);
            $logtimed = "$date - $instance->realIp\n";
            file_put_contents(APP_ROOT. '/banlog__.php', $logtimed, FILE_APPEND);

            if($output) {
                return true;
            }
            header('HTTP/1.0 403 Forbidden');
            Discord::send('[BAN] Hack crawling detected by ' . Sec::hNoHtml($instance->realIp) . ' ' . $currentPath);
            die($instance->banMessage);
        }
        return false;
    }

    public static function ban($ip): bool
    {
        $instance = self::getInstance();
        $instance->blackList = $instance->loadBanList();
        if (in_array($ip, $instance->blackList)) {
            return false;
        }
        $date = date('Y-m-d H:i:s');
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $currentPath = htmlspecialchars($uri, ENT_QUOTES, 'UTF-8');
        if(isset($_GET['url'])) {
            $currentPath = htmlspecialchars($_GET['url'], ENT_QUOTES, 'UTF-8');
        }
        $log = "$ip\n";
        file_put_contents(APP_ROOT . '/blacklist__.php', $log, FILE_APPEND);
        $logtimed = "$date - $ip\n";
        file_put_contents(APP_ROOT . '/banlog__.php', $logtimed, FILE_APPEND);
        if(AppConfig::get('ban_file_path')) {
            file_put_contents(AppConfig::get('ban_file_path'), $log, FILE_APPEND);
        }
        Discord::send('[BAN] Hack attempt detected on ' . Sec::hNoHtml(AppConfig::get('appName')) . ' by ' . Sec::hNoHtml(Sec::getRealIpAddr()) . ' ' . $currentPath);
        Discord::send(json_encode($_POST));
        Discord::send(json_encode($_GET));
        return true;
    }

    public static function banIfHackAttempt(): bool
    {
        $instance = self::getInstance();
        foreach ($_GET as $key => $value) {
            if ($instance->detectXssAttempt($value) || $instance->detectSqlInjectionAttempt($value)) {
                return $instance->ban( $instance->realIp);
            }
        }

        // Parcourir et vérifier les entrées POST
        foreach ($_POST as $key => $value) {
            if ($instance->detectXssAttempt($value) || $instance->detectSqlInjectionAttempt($value)) {
                return $instance->ban( $instance->realIp);
            }
        }

        return false;
    }

    private function loadBanList(): array
    {
        return file(APP_ROOT . '/blacklist__.php', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    public static function detectXssAttempt(string $input): bool
    {
        // Liste des schémas courants utilisés dans les attaques XSS
        $xssPatterns = [
            '/<script\b[^>]*>(.*?)<\/script>/is', // Script HTML
            '/javascript:/i',                    // JavaScript dans les URL
            '/on\w+=["\'].*?["\']/i',            // Attributs d'événements (ex : onerror, onclick, etc.)
            '/<iframe\b.*?>.*?<\/iframe>/is',    // IFrames intégrées
            '/document\.cookie/i',               // Accès aux cookies via script
            '/<embed\b.*?>.*?<\/embed>/is',      // Balises embed
            '/<object\b.*?>.*?<\/object>/is',    // Balises object
            '/vbscript:/i',                      // Code VBScript
            '/data:text\/html/i'                 // Utilisation de data uris HTML
        ];

        // Parcours des templates pour détecter un match
        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true; // Tentative détectée
            }
        }

        return false; // Aucun schéma dangereux détecté
    }

    public static function detectSqlInjectionAttempt(string $input): bool
    {
        // Liste des motifs communs aux attaques SQL injection
        $sqlInjectionPatterns = [
            '/(\b|\')OR(\b|\'|")/i',        // "OR" utilisé de manière suspecte
            '/(\b|\')AND(\b|\'|")/i',       // "AND" utilisé de manière suspecte
            '/\'\s*--/i',                   // Commentaires SQL (--) après une apostrophe
            '/--(\s|$)/i',                  // Commentaires SQL sans apostrophe
            '/;(\s|$)/i',                   // Fin de commande SQL avec un point-virgule
            '/UNION\s+SELECT/i',            // Union Select pour concaténer des requêtes
            '/SELECT\s.*\sFROM/i',          // Une requête SELECT complète
            '/INSERT\s+INTO/i',             // Une requête INSERT INTO
            '/UPDATE\s+\w+\s+SET/i',        // Une requête UPDATE via manipulation
            '/DELETE\s+FROM/i',             // Une requête DELETE FROM
            '/DROP\s+TABLE/i',              // Suppression de table
            '/CREATE\s+TABLE/i',            // Création de table
            '/INFORMATION_SCHEMA/i',        // Accès à metaschemas SQL
            '/\bor\b.*?=/i',                // OR avec comparaisons suspectes
            '/\bexec\b/i',                  // Commande exec
            '/sleep\(\d+\)/i',              // Utilisation de SLEEP pour time-based attacks
            '/benchmark\((.*?)\)/i',        // Utilisation de BENCHMARK
            '/load_file\(.+\)/i',           // Chargement de fichiers externes
            '/outfile\s+/i',                // Enregistrement dans un fichier externe
        ];

        // Parcourir les patterns pour détecter un match
        foreach ($sqlInjectionPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true; // Tentative de SQLi détectée
            }
        }

        return false; // Aucun motif suspect détecté
    }

    public static function unBan( $ip): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }
        $instance = self::getInstance();
        $instance->blackList = $instance->loadBanList();
        $newContent = '';
        $hint = false;
        foreach ($instance->blackList as $key => $value) {
            if($value != $ip) {
                $newContent .= $value . "\n";
            } else {
                $hint = true;
            }
        }
        file_put_contents(APP_ROOT . '/blacklist__.php', $newContent);
        $date = date('Y-m-d H:i:s');
        $logtimed = "$date - $ip - unban\n";
        file_put_contents(APP_ROOT . '/banlog__.php', $logtimed, FILE_APPEND);
        return $hint;
    }

}