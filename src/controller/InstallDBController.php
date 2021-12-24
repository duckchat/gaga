<?php

/**
 *
 * 首次安装，初始化数据库 && 站点配置 && 小程序
 *
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 25/08/2018
 * Time: 8:06 PM
 */
class InstallDBController
{
    private $logger;
    private $_dbPath = ".";
    private $loginPluginIds = [101, 102];
    private $passportAccountSafePluginId = 105;
    private $configName = "config.php";
    private $sampleConfigName = "config.sample.php";
    private $sitePubkPem;

    private $lang = Zaly\Proto\Core\UserClientLangType::UserClientLangZH;
    /**
     * @var \PDO
     */
    private $db;
    private $curl;

    /**
     * @var ZalyHelper
     */
    private $helper;

    function __construct(BaseCtx $content)
    {
        $this->logger = $content->getLogger();
        $this->helper = new ZalyHelper();
        $this->curl = new ZalyCurl();
    }

    public function doIndex()
    {
        $result = [
            'errCode' => 'error'
        ];

        $configFileName = dirname(__FILE__) . "/../" . $this->configName;
        $sampleFileName = dirname(__FILE__) . "/../" . $this->sampleConfigName;

        if (file_exists($configFileName)) {
            $newConfig = require($configFileName);
            $dbType = $newConfig['dbType'];
            $sqliteName = $newConfig['sqlite']['sqliteDBName'];
            if ($dbType == "sqlite" && $sqliteName) {
                $sqliteName = $this->_dbPath . "/" . $sqliteName;
                $fileExists = file_exists($sqliteName);
                if ($newConfig['dbType'] == "sqlite" && !$fileExists) {
                    header("Content-Type: text/html; charset=UTF-8");
                    echo "sqlite DB 文件不存在, 请删除config.php文件，初始化站点";
                    return;
                }
            }
            $apiPageIndex = ZalyConfig::getConfig("apiPageIndex");
            header("Location:" . $apiPageIndex);
            exit();
        }

        $config = require($sampleFileName);
        $sqliteName = "";
        $this->lang = isset($_GET['lang']) ? $_GET['lang'] : "1";
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == 'POST') {
            //install site
            try {
                if (isset($_GET['for']) && $_GET['for'] == "test_connect_mysql") {
                    echo $this->testConnectMysql();
                    return;
                }

                $serverHost = $_SERVER['HTTP_HOST'];
                $port = $_SERVER['SERVER_PORT'];
                $dbType = $_POST['dbType'];

                $hosts = explode(":", $serverHost);
                $host = array_shift($hosts);
                $scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : "http";

                $siteAddress = $scheme . "://" . $serverHost;
                $loginPluginId = 102;

                if (isset($dbType) && "mysql" == $dbType) {
                    $config['dbType'] = "mysql";
                    if (isset($_POST['dbName'])) {
                        $config['mysql']['dbName'] = $_POST['dbName'];
                    }
                    if (isset($_POST['dbHost'])) {
                        $config['mysql']['dbHost'] = $_POST['dbHost'];
                    }
                    if (isset($_POST['dbPort'])) {
                        $config['mysql']['dbPort'] = $_POST['dbPort'];
                    } else {
                        $config['mysql']['dbPort'] = 3306;
                    }
                    if (isset($_POST['dbUserName'])) {
                        $config['mysql']['dbUserName'] = $_POST['dbUserName'];
                    }
                    if (isset($_POST['dbPassword'])) {
                        $config['mysql']['dbPassword'] = $_POST['dbPassword'];
                    }

                } else {
                    $config['dbType'] = "sqlite";

                    $dbNameKey = ZalyHelper::generateStrKey(8);
                    $sqliteName = "db." . md5($dbNameKey) . ".sqlite3";

                    if (isset($_POST['sqliteDbFile']) && $_POST['sqliteDbFile'] != "") {
                        $dbFileInfos = pathinfo($_POST['sqliteDbFile']);
                        $dbFileDir = $dbFileInfos['dirname'];
                        $dbFileName = $dbFileInfos['basename'];

                        if (isset($dbFileDir)) {
                            $config['sqlite']['sqliteDBPath'] = $dbFileDir;
                        }

                        if (isset($dbFileName)) {
                            $sqliteName = $dbFileName;
                        }
                        $config['sqlite']['sqliteDBName'] = $sqliteName;
                    } else {
                        //create new site db sqlite
                        $config['sqlite']['sqliteDBName'] = $sqliteName;
                    }

                }

                $config['loginPluginId'] = in_array($loginPluginId, $this->loginPluginIds) ? $loginPluginId : 101;

                $config['siteAddress'] = $siteAddress;
                $randomKey = ZalyHelper::generateStrKey('16');
                $config['errorLog'] = 'php_errors_' . $randomKey . '.log';
                $config['randomKey'] = $randomKey;
                $config['msectime'] = ZalyHelper::getMsectime();

                //write to file
                $contents = var_export($config, true);
                file_put_contents($configFileName, "<?php\n return {$contents};\n ");
                if (function_exists("opcache_reset")) {
                    opcache_reset();
                }

                $siteName = $host;

                if ("mysql" == $config['dbType']) {
                    $this->initSiteWithMysql($config, $siteName, $host, $port);
                } else {
                    $this->initSiteWithSqlite($sqliteName, $siteName, $host, $port);
                }

                $this->initSiteOwner($_POST['adminLoginName'], $_POST['adminPassword']);

                $result['errCode'] = "success";
                echo "success";
            } catch (Exception $ex) {
                $this->deleteConfigFile();
                $this->logger->error("do install site", $ex);
                $result['errCode'] = "error";
                $result['errInfo'] = $ex->getMessage() . " " . $ex->getTraceAsString();
                echo $ex->getMessage() . " " . $ex->getTraceAsString();
                return;
            }
        } else if ($method == "GET") {
            //check system
            if (isset($_GET['for']) && $_GET['for'] == 'test_curl') {
                echo "success";
                return;
            }

            if (isset($_GET['for']) && $_GET['for'] == 'test_curl_result') {
                echo $this->isCanUserCurl();
                return;
            }

            if (isset($_GET['for']) && $_GET['for'] == 'phpinfo') {
                phpinfo();
                return;
            }

            $permissionDirectory = is_writable(dirname(dirname(__FILE__)));
            $configFile = dirname(dirname(__FILE__)) . "/config.php";
            $attachDir = dirname(dirname(__FILE__)) . "/attachment";
            if (file_exists($configFile) && !is_writable($configFile)) {
                $permissionDirectory = false;
            }

            if (file_exists($attachDir) && !is_writable($attachDir)) {
                $permissionDirectory = false;
            }

            $testCanWriteFile = (dirname(dirname(__FILE__)) . "/test_write.duckchat");
            $flag = file_put_contents($testCanWriteFile, "duckchat");
            if ($flag === false) {
                $permissionDirectory = false;
            }
            @unlink($testCanWriteFile);

            //防止自己配置nginx的时候，多写一个/
            $requestUri = isset($_SERVER['REQUEST_URI']) ? str_replace(array("\\", "//"), array("/", "/"), $_SERVER['REQUEST_URI']) : "";
            $requestUris = explode("/", $requestUri);
            $isInstallRootPath = true;

            if (count($requestUris) > 2) {
                $isInstallRootPath = false;
            }
            $sampleFile = require(dirname(dirname(__FILE__)) . "/config.sample.php");

            if ($isInstallRootPath === false) {
                header("Content-Type: text/html; charset=UTF-8");
                echo $this->lang == 1 ? "目前只支持根目录运行" : "Currently only the root directory is supported.";
                return;
            }

            $params = [
                "isPhpVersionValid" => version_compare(PHP_VERSION, "5.6.0") >= 0,
                "isLoadOpenssl" => extension_loaded("openssl") && false != ZalyRsa::newRsaKeyPair(2048),
                "isLoadPDOSqlite" => extension_loaded("pdo_sqlite"),
                "isLoadPDOMysql" => extension_loaded("pdo_mysql"),
                "isLoadCurl" => extension_loaded("curl"),
                "isWritePermission" => $permissionDirectory,
                "siteVersion" => isset($sampleFile['siteVersionName']) ? $sampleFile['siteVersionName'] : "",
                "versionCode" => $sampleFile['siteVersionCode'],
                "isInstallRootPath" => $isInstallRootPath,
                "siteAddress" => ZalyHelper::getRequestAddressPath(),
                'phpinfo' => "./index.php?action=installDB&for=phpinfo",
            ];
            //get db file
            $dbDir = dirname(__DIR__);
            $dbFiles = scandir($dbDir);
            $phpInfoExist = false;
            if (!empty($dbFiles)) {
                $sqliteFiles = [];
                foreach ($dbFiles as $dbFile) {
                    $fileExt = pathinfo($dbFile, PATHINFO_EXTENSION);
                    if (isset($fileExt) && ($fileExt == "sqlite" || $fileExt == "sqlite3")) {
                        $sqliteFiles[] = $dbFile;
                    }
                }
                $params['dbFiles'] = json_encode($sqliteFiles);
            }

            echo $this->display("init_init", $params);
            return;
        }
    }

    private function isCanUserCurl()
    {
        $sampleFile = require(dirname(dirname(__FILE__)) . "/config.sample.php");
        $testCurlUrl = isset($sampleFile['testCurl']) ? $sampleFile['testCurl'] : $sampleFile['test_curl'];
        $testCurlUrl = ZalyHelper::getFullReqUrl($testCurlUrl);
        $curlResult = $this->curl->request($testCurlUrl, 'get');
        echo $curlResult;
    }

    private function display($viewName, $params = [])
    {
        // 自己实现实现一下这个方法，加载view目录下的文件
        // 自己实现实现一下这个方法，加载view目录下的文件
        ob_start();
        $fileName = str_replace("_", "/", $viewName);
        $path = dirname(__DIR__) . '/views/' . $fileName . '.php';
        if ($params) {
            extract($params, EXTR_SKIP);
        }
        include($path);
        $var = ob_get_contents();
        ob_end_clean();
        return $var;
    }

    /**
     * init site with mysql
     * @param array $config
     * @param $siteName
     * @param $siteHost
     * @param $sitePort
     * @throws Throwable
     */
    private function initSiteWithMysql(array $config, $siteName, $siteHost, $sitePort)
    {
        $dbName = $config['mysql']['dbName'];
        $dbHost = $config['mysql']['dbHost'];
        $dbPort = $config['mysql']['dbPort'];
        $dbUserName = $config['mysql']['dbUserName'];
        $dbPassword = $config['mysql']['dbPassword'];
        //check mysql args

        $dbDsn = "mysql:host=$dbHost;port=$dbPort;";//;dbname=$dbName
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
        );

        $this->db = new PDO($dbDsn, $dbUserName, $dbPassword, $options);//创建一个pdo对象

        if (!$this->db) {
            throw new Exception("connect mysql error");
        }

        $this->_createMysqlDatabaase($dbName);
        $this->_executeMysqlScript();
        $this->_checkConfigDefaultValue($siteName, $siteHost, $sitePort);
    }

    private function _createMysqlDatabaase($dbName)
    {
        $sql = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci; USE `$dbName`;";
        $result = $this->db->exec($sql);
    }

    private function _executeMysqlScript()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $mysqlScriptPath = dirname(__DIR__) . "/model/database-sql/site_mysql.sql";

        $this->logger->error("site.install.db", "mysql script=" . $mysqlScriptPath);

        $_sqlContent = file_get_contents($mysqlScriptPath);//写自己的.sql文件
        $_sqlArr = explode(';', $_sqlContent);

        try {
            $this->db->beginTransaction();
            foreach ($_sqlArr as $sql) {
                $this->db->exec($sql);
            }
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            $this->logger->error($tag, $e);
            throw $e;
        }

    }


    private function initSiteWithSqlite($sqliteName, $siteName, $siteHost, $Port)
    {
        $dbInfo = $this->_dbPath . "/" . $sqliteName;
        $this->db = new \PDO("sqlite:{$dbInfo}");

        $this->_executeSqliteScript();
        $this->_checkConfigDefaultValue($siteName, $siteHost, $Port);
    }

    private function _executeSqliteScript()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $mysqlScriptPath = dirname(__DIR__) . "/model/database-sql/site_sqlite.sql";
        $_sqlContent = file_get_contents($mysqlScriptPath);//写自己的.sql文件
        $_sqlArr = explode(';', $_sqlContent);

        try {
            $this->db->beginTransaction();
            foreach ($_sqlArr as $sql) {
                $this->db->exec($sql);
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->logger->error($tag, $e);
            throw $e;
        }

    }

    private function _checkConfigDefaultValue($siteName, $siteHost, $Port)
    {
        $loginPluginId = ZalyConfig::getConfig("loginPluginId");
        $this->_insertSiteConfig($siteName, $loginPluginId);

        $this->initPluginMiniProgram();
        return;
    }

    private function _insertSiteConfig($siteName, $loginPluginId)
    {
        $siteConfig = SiteConfig::$initSiteConfig;

        $siteConfig[SiteConfig::SITE_NAME] = $siteName;

        $siteConfig[SiteConfig::SITE_ENABLE_INVITATION_CODE] = 0;//init with uic when first user login

        $siteConfig[SiteConfig::SITE_LOGIN_PLUGIN_ID] = $loginPluginId;

        $siteConfig[SiteConfig::SITE_PLUGIN_PLBLIC_KEY] = (new ZalyHelper())->generateStrKey(32);
        $siteConfig[SiteConfig::SITE_PASSPORT_ACCOUNT_SAFE_PLUGIN_ID] = $this->passportAccountSafePluginId;

        $pubkAndPrikPems = SiteConfig::getPubkAndPrikPem();
        $siteConfig = array_merge($siteConfig, $pubkAndPrikPems);

        $this->sitePubkPem = $siteConfig[SiteConfig::SITE_ID_PUBK_PEM];

        $sqlStr = "";
        foreach ($siteConfig as $configKey => $configVal) {
            $sqlStr .= "('$configKey','$configVal'),";
        }
        $sqlStr = trim($sqlStr, ",");

        $sql = "insert into siteConfig(configKey, configValue) values $sqlStr;";
        $count = $this->db->exec($sql);

        $this->logger->error("site.install.db", "init config count=" . $count);
    }

    /**
     * 增加默认扩展小程序
     */
    private function initPluginMiniProgram()
    {
        $miniPrograms = [
            [
                'pluginId' => 100,
                'name' => "管理后台",
                'logo' => $this->getSiteManageIcon(),
                'sort' => 100,
                'landingPageUrl' => "index.php?action=manage.index",
                'landingPageWithProxy' => 1, //1 表示走site代理
                'usageType' => Zaly\Proto\Core\PluginUsageType::PluginUsageIndex,
                'loadingType' => Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage,
                'permissionType' => Zaly\Proto\Core\PluginPermissionType::PluginPermissionAdminOnly,
                'authKey' => "",
            ],
            [
                'pluginId' => 102,
                'name' => "密码登陆",
                'logo' => "",
                'sort' => 102, //order = 102
                'landingPageUrl' => "index.php?action=page.passport.login",
                'landingPageWithProxy' => 1, //1 表示走site代理
                'usageType' => Zaly\Proto\Core\PluginUsageType::PluginUsageLogin,
                'loadingType' => Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage,
                'permissionType' => Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll,
                'authKey' => "",
                'management' => "",
            ],
            [
                'pluginId' => 103,
                'name' => "DC文档",
                'logo' => "",
                'sort' => 1, //order = 2
                'landingPageUrl' => "https://duckchat.akaxin.com/wiki/",
                'landingPageWithProxy' => 0, //1 表示走site代理
                'usageType' => Zaly\Proto\Core\PluginUsageType::PluginUsageIndex,
                'loadingType' => Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage,
                'permissionType' => Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll,
                'authKey' => "",
            ],
            [
                'pluginId' => 104,
                'name' => "Gif表情",
                'logo' => $this->getSiteGifIcon(),
                'sort' => 2, //order = 2
                'landingPageUrl' => "index.php?action=miniProgram.gif.index",
                'landingPageWithProxy' => 1, //1 表示走site代理
                'usageType' => Zaly\Proto\Core\PluginUsageType::PluginUsageU2Message,
                'loadingType' => Zaly\Proto\Core\PluginLoadingType::PluginLoadingChatbox,
                'permissionType' => Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll,
                'authKey' => "",
                "management" => "index.php?action=miniProgram.gif.cleanGif",
            ],
            [
                'pluginId' => 104,
                'name' => "Gif表情",
                'logo' => $this->getSiteGifIcon(),
                'sort' => 2, //order = 2
                'landingPageUrl' => "index.php?action=miniProgram.gif.index",
                'landingPageWithProxy' => 1, //1 表示走site代理
                'usageType' => Zaly\Proto\Core\PluginUsageType::PluginUsageGroupMessage,
                'loadingType' => Zaly\Proto\Core\PluginLoadingType::PluginLoadingChatbox,
                'permissionType' => Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll,
                'authKey' => "",
                "management" => "index.php?action=miniProgram.gif.cleanGif"
            ],
            [
                'pluginId' => 105,
                'name' => "账户密码管理",
                'logo' => "",
                'sort' => 104, //order = 2
                'landingPageUrl' => "index.php?action=miniProgram.passport.account",
                'landingPageWithProxy' => 1, //1 表示走site代理
                'usageType' => Zaly\Proto\Core\PluginUsageType::PluginUsageAccountSafe,
                'loadingType' => Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage,
                'permissionType' => Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll,
                'authKey' => "",
            ],

//            [
//                'pluginId' => 106,
//                'name' => "开发工具",
//                'logo' => "",
//                'sort' => 106,
//                'landingPageUrl' => "index.php?action=miniProgram.test.tools",
//                'landingPageWithProxy' => 1, //1 表示走site代理
//                'usageType' => Zaly\Proto\Core\PluginUsageType::PluginUsageIndex,
//                'loadingType' => Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage,
//                'permissionType' => Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll,
//                'authKey' => "",
//            ],
            [
                'pluginId' => 107,
                'name' => "客服小程序",
                'logo' => "",
                'sort' => 107,
                'landingPageUrl' => "index.php?action=miniProgram.customerService.index",
                'landingPageWithProxy' => 1, //1 表示走site代理
                'usageType' => Zaly\Proto\Core\PluginUsageType::PluginUsageNone,
                'loadingType' => Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage,
                'permissionType' => Zaly\Proto\Core\PluginPermissionType::PluginPermissionAdminOnly,
                'authKey' => "",
                "management" => "index.php?action=miniProgram.customerService.manage"
            ],
            [
                'pluginId' => 199,  //200+ for user
                'name' => "用户广场",
                'logo' => $this->getSiteSquareIcon(),
                'sort' => 2, //order = 2
                'landingPageUrl' => "index.php?action=miniProgram.square.index",
                'landingPageWithProxy' => 1, //1 表示走site代理
                'usageType' => Zaly\Proto\Core\PluginUsageType::PluginUsageIndex,
                'loadingType' => Zaly\Proto\Core\PluginLoadingType::PluginLoadingNewPage,
                'permissionType' => Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll,
                'authKey' => "",
            ],
        ];

        $this->_insertSitePlugin($miniPrograms);

    }

    private function _insertSitePlugin($miniPrograms)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        $successParams = [];
        foreach ($miniPrograms as $miniProgram) {
            try {
                $success = $this->insertData("sitePlugin", $miniProgram);
                if ($success) {
                    $successParams[] = $miniProgram['name'];
                }
            } catch (Throwable $e) {
                $this->logger->error($tag, $e);
            }
        }
        $this->logger->info("site.install.db", "init miniPrograms finish success=" . json_encode($successParams));
    }

    public function insertData($tableName, $data)
    {
        $startTime = microtime(true);
        $tag = __CLASS__ . "-" . __FUNCTION__;
        $insertKeys = array_keys($data);
        $insertKeyStr = implode(",", $insertKeys);
        $placeholderStr = "";
        foreach ($insertKeys as $key => $val) {
            $placeholderStr .= ",:" . $val . "";
        }
        $placeholderStr = trim($placeholderStr, ",");
        if (!$placeholderStr) {
            throw new Exception("insert data fail with empty values");
        }
        $sql = " insert into  $tableName({$insertKeyStr}) values ({$placeholderStr});";
        $prepare = $this->db->prepare($sql);
        $this->handelPrepareError($prepare);
        foreach ($data as $key => $val) {
            $prepare->bindValue(":" . $key, $val);
        }
        $flag = $prepare->execute();
        $this->logger->writeSqlLog($tag, $sql, $data, $startTime);
        $count = $prepare->rowCount();

        $this->logger->error("site.install.db",
            "init mimiProgram or custom, name=" . $data['name'] .
            " count=" . $count .
            " errCode=" . $prepare->errorCode() .
            " errInfo=" . json_encode($prepare->errorInfo()));

        if ($flag) {
            return true;
        }
        return false;
    }

    function handelPrepareError($prepare)
    {
        $tag = __CLASS__ . ' - ' . __FUNCTION__;
        if (!$prepare) {
            $error = [
                "error_code" => $this->db->errorCode(),
                "error_info" => $this->db->errorInfo(),
            ];
            $this->logger->error($tag, "error_msg=" . json_encode($error));
        }
    }

    public function testConnectMysql()
    {
        if (isset($_POST['dbName'])) {
            $dbName = $_POST['dbName'];
        }
        if (isset($_POST['dbHost'])) {
            $dbHost = $_POST['dbHost'];
        }
        if (isset($_POST['dbPort'])) {
            $dbPort = $_POST['dbPort'];
        } else {
            $dbPort = 3306;
        }
        if (isset($_POST['dbUserName'])) {
            $dbUserName = $_POST['dbUserName'];
        }
        if (isset($_POST['dbPassword'])) {
            $dbPassword = $_POST['dbPassword'];
        }

        //check mysql args
        $dbDsn = "mysql:host=$dbHost;port=$dbPort;";//;dbname=$dbName
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
        );

        $this->db = new PDO($dbDsn, $dbUserName, $dbPassword, $options);//创建一个pdo对象

        if (!$this->db) {
            throw new Exception("connect mysql error");
        }
        return "success";
    }

    private function getSiteManageIcon()
    {
        $defaultIcon = WPF_ROOT_DIR . "/public/img/manage/site_manage.png";
        if (!file_exists($defaultIcon)) {
            return "";
        }

        $defaultImage = file_get_contents($defaultIcon);
        $fileManager = new File_Manager();
        $fileId = $fileManager->saveFile($defaultImage, "20180201");

        return $fileId;
    }

    private function getSiteSquareIcon()
    {
        $defaultIcon = WPF_ROOT_DIR . "/public/img/manage/site_square.png";
        if (!file_exists($defaultIcon)) {
            return "";
        }

        $defaultImage = file_get_contents($defaultIcon);
        $fileManager = new File_Manager();
        $fileId = $fileManager->saveFile($defaultImage, "20180201");
        return $fileId;
    }

    private function getSiteGifIcon()
    {
        $defaultIcon = WPF_ROOT_DIR . "/public/img/plugin/gif.png";
        if (!file_exists($defaultIcon)) {
            return "";
        }

        $defaultImage = file_get_contents($defaultIcon);
        $fileManager = new File_Manager();
        $fileId = $fileManager->saveFile($defaultImage, "20180201");
        return $fileId;
    }

    private function initSiteOwner($adminLoginName, $adminPassword)
    {
        if (empty($adminLoginName) || empty($adminPassword)) {
            throw new Exception("loginName or password error");
        }

        $passwordUserId = ZalyHelper::generateStrId();
        //register user
        $result = $this->registerSiteOwner($passwordUserId, $adminLoginName, $adminPassword);

        if (!$result) {
            throw new Exception("register site admin error");
        }

        if (empty($this->sitePubkPem)) {
            throw new Exception("site RSA Public Key error");
        }

        $siteUserId = sha1($passwordUserId . "@" . $this->sitePubkPem);
        //set site owner
        $siteConfig = new Site_Config(new BaseCtx());
        $siteConfig->updateConfigValue(SiteConfig::SITE_OWNER, $siteUserId);
        return true;
    }

    private function registerSiteOwner($userId, $loginName, $password)
    {
        $userInfo = [
            "userId" => $userId,
            "loginName" => $loginName,
            "password" => password_hash($password, PASSWORD_BCRYPT),
            "nickname" => $loginName,
            "timeReg" => ZalyHelper::getMsectime()
        ];
        $PassportPasswordTable = new PassportPasswordTable(new BaseCtx());
        return $PassportPasswordTable->insertUserInfo($userInfo);
    }

    private function deleteConfigFile()
    {
        $configFilePath = dirname(dirname(__FILE__)) . "/config.php";
        unlink($configFilePath);
    }
}