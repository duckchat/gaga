<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 03/09/2018
 * Time: 1:41 PM
 */

return array(
    'siteVersionName' => '1.1.7',
    'siteVersionCode' => '10107',
    'siteId' => "",//sha1(pubk),获取此值，请从Site_Config中获取
    'apiPageIndex' => './index.php?action=page.index',
    'apiPageLogin' => './index.php?action=page.login',
    'apiPageLogout' => './index.php?action=page.logout',
    'apiPageJump' => "./index.php?action=page.jump",
    'loginPluginId' => '102',
    'apiPageWidget' => './index.php?action=page.widget',
    'apiPageSiteInit' => "./index.php?action=installDB",
    'sessionVerify102' => './index.php?action=api.session.verify&body_format=base64pb',
    'testCurl' => "./index.php?action=installDB&for=test_curl",
    'apiPagePassportLogin' => "./index.php?action=page.passport.login",
    'errorLog' => '',
    'dbType' => 'sqlite',
    'dbVersion' => '2',
    'sqlite' =>
        array(
            'sqliteDBPath' => '.',
            'sqliteDBName' => '',
        ),
    'mysql' =>
        array(
            'dbName' => '',
            'dbHost' => '127.0.0.1',
            'dbPort' => '3306',
            'dbUserName' => '',
            'dbPassword' => '',
        ),
    'mysqlSlave' => array(
//        'slave_0' => array(
//            'dbName' => 'duckchat_site',
//            'dbHost' => '127.0.0.1',
//            'dbPort' => '3306',
//            'dbUserName' => 'duckchat',
//            'dbPassword' => '1234567890',
//        ),
    ),
    'logPath' => '.',
    'randomKey' => '',
    "debugMode" => false,
    'msectime' => 1535945699185.0,
);
