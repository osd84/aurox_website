<?php

namespace OsdAurox;

use OsdAurox\Sec;

Class AppConfig {

    private static ?self $instance = null;
    public ?bool $debug;
    public ?string $appUrl;
    public array $lang;
    public bool $featureRegister;
    public bool $featureUserAllowAdminCreate;
    public bool $nonce;
    public int $passwordMaxLength;
    public int $passwordMinLength;
    public string $adminFolder;
    public string $appAuthor;
    public string $appDescription;
    public string $appFavicon;
    public string $appKeywords;
    public string $appLang;
    public string $appLogo;
    public string $appName;
    public string $appTitle;
    public string $appVersion;
    public string $charset;
    public string $db;
    public string $devIp;
    public string $devUrl;
    public string $discordWebhook;
    public string $host;
    public string $port;
    public string $loginUrlForm;
    public string $mailContactDest;
    public string $mailFrom;
    public string $mailHost;
    public string $mailPass;
    public string $mailPort;
    public string $mailSsl;
    public string $mailTls;
    public string $mailUser;
    public string $pass;
    public string $passwordComplexity;
    public string $prodUrl;
    public string $salt;
    public string $user;
    public string $ban_file_path;

    public static function init(array $config): self
    {
        if (self::$instance !== null) {
            throw new \RuntimeException("AppConfig déjà initialisé");
        }
        self::$instance = new self($config);
        return self::$instance;
    }


    private function __construct(array $conf)
    {
        $this->adminFolder = $conf['adminFolder'] ?? 'admin';
        $this->appAuthor = $conf['appAuthor'] ?? '-';
        $this->appDescription = $conf['appDescription'] ?? '-';
        $this->appFavicon = $conf['appFavicon'] ?? 'favicon.ico';
        $this->appKeywords = $conf['appKeywords'] ?? '-';
        $this->appLang = $conf['appLang'] ?? 'fr';
        $this->appLogo = $conf['appLogo'] ?? 'logo.png';
        $this->appName = $conf['appName'] ?? 'DefaultAppName';
        $this->appTitle = $conf['appTitle'] ?? 'DefaultTitle';
        $this->appUrl = $conf['appUrl'];
        $this->appVersion = $conf['appVersion'] ?? '1.0.0';
        $this->debug = $conf['debug'] ?? false;
        $this->devIp = $conf['devIp'] ?? '127.0.0.1';
        $this->devUrl = $conf['devUrl'] ?? 'http://localhost';
        $this->discordWebhook = $conf['discordWebhook'] ?? '';
        $this->featureRegister = $conf['featureRegister'] ?? false;
        $this->featureUserAllowAdminCreate = $conf['featureUserAllowAdminCreate'] ?? false;
        // MysqlConf
        $this->host = $conf['host'] ?? '127.0.0.1';
        $this->port = $conf['port'] ?? '3306';
        $this->db = $conf['db'] ?? 'default_db';
        $this->user = $conf['user'] ?? 'root';
        $this->pass = $conf['pass'] ?? '';
        $this->charset = $conf['charset'] ?? 'utf8mb4';
        // LoginConf
        $this->lang = $conf['lang'] ?? ['fr'];
        $this->loginUrlForm = $conf['loginUrlForm'] ?? '/';
        // MailConf
        $this->mailContactDest = $conf['mailContactDest'] ?? false;
        $this->mailFrom = $conf['mailFrom'] ?? false;
        $this->mailHost = $conf['mailHost'] ?? false;
        $this->mailPass = $conf['mailPass'] ?? false;
        $this->mailPort = $conf['mailPort'] ?? false;
        $this->mailSsl = $conf['mailSsl'] ?? false;
        $this->mailTls = $conf['mailTls'] ?? false;
        $this->mailUser = $conf['mailUser'] ?? false;
        $this->nonce = $conf['nonce'] ?? false;
        $this->passwordComplexity = $conf['passwordComplexity'] ?? '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/';
        $this->passwordMaxLength = $conf['passwordMaxLength'] ?? 255;
        $this->passwordMinLength = $conf['passwordMinLength'] ?? 8;
        $this->prodUrl = $conf['prodUrl'] ?? 'http://localhost';
        $this->salt = $conf['salt'] ?? '';
        $this->ban_file_path  = $conf['ban_file_path'] ?? '';
    }

    public static function getInstance(): self
    {
        if(!self::$instance) {
            throw new \Exception('AppConfig not initialized');
        }
        return self::$instance;
    }

    public static function get(string $key, $default = '', $safe=false) {

        $instance = self::getInstance();
        if(!$safe) {
            return Sec::h($instance->$key ?? $default);
        }
        return $instance->$key ?? $default;
    }

    public static function isDebug(): bool
    {
        return self::get('debug');
    }

}