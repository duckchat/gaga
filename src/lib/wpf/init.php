<?php
/**
 * WEB应用程序初始化
 *
 * 应用程序必须自己预先在model文件夹定义一个BaseCtx的类
 */

define("WPF_ROOT_DIR", dirname(__FILE__) . '/../../');
define("WPF_START_TIME", microtime(true));
define("WPF_LIB_DIR", dirname(__FILE__) . '/../');

//设置配置项
$_ENV['WPF_CONTROLLER_NAME_SUFFIX'] = "Controller";    //Controller类名的后缀，默认为Controller
//$_ENV['WPF_URL_PATH_SUFFIX']，URL目录部分的前缀，前缀不参与路由

//防止服务器没有设置默认时间而报错
date_default_timezone_set('Asia/Shanghai');

//手动引入肯定需要的文件
require_once(WPF_LIB_DIR . '/wpf/Wpf_Loader.php');
require_once(WPF_LIB_DIR . '/wpf/Wpf_Web.php');
require_once(WPF_LIB_DIR . '/wpf/Wpf_Controller.php');
require_once(WPF_LIB_DIR . '/wpf/Wpf_Router.php');
require_once(WPF_LIB_DIR . '/wpf/Wpf_Ctx.php');
require_once(WPF_LIB_DIR . '/wpf/Wpf_Logger.php');


//激活自动加载
$autoloader = new Wpf_Autoloader();
$autoloader->addDir(WPF_LIB_DIR . '/../model/');
$autoloader->addDir(WPF_LIB_DIR);
$autoloader->addDir(WPF_LIB_DIR . "/proto/");
$autoloader->addDir(WPF_LIB_DIR . "/Util/");
$autoloader->addDir(WPF_LIB_DIR . "/Overtrue/");
$autoloader->addDir(WPF_LIB_DIR . "/PHPMailer/");
$autoloader->addDir(WPF_LIB_DIR . '/../config/');
$autoloader->addDir(WPF_LIB_DIR . '/../controller/');
spl_autoload_register(array($autoloader, 'load'));



// For performance
//
// autoload: the most commonly used files from autoload "file_exists" Stat.
$preAutoloadFiles = array(
    'Api_Group_BaseController' => '/controller/Api/Group/Api_Group_BaseController.php',
    'Api_Group_ProfileController' => '/controller/Api/Group/Api_Group_ProfileController.php',
    'BaseController' => '/controller/BaseController.php',
    'GPBMetadata\\Core\Net' => '/lib/proto/GPBMetadata/Core/Net.php',
    'GPBMetadata\\Google\\Protobuf\\Any' => '/lib/proto/GPBMetadata/Google/Protobuf/Any.php',
    'GPBMetadata\\Google\\Protobuf\\Internal\\Descriptor' => '/lib/proto/GPBMetadata/Google/Protobuf/Internal/Descriptor.php',
    'Google\\Protobuf\\Any' => '/lib/proto/Google/Protobuf/Any.php',
    'Google\\Protobuf\\Descriptor' => '/lib/proto/Google/Protobuf/Descriptor.php',
    'Google\\Protobuf\\EnumDescriptor' => '/lib/proto/Google/Protobuf/EnumDescriptor.php',
    'Google\\Protobuf\\EnumValueDescriptor' => '/lib/proto/Google/Protobuf/EnumValueDescriptor.php',
    'Google\\Protobuf\\FieldDescriptor' => '/lib/proto/Google/Protobuf/FieldDescriptor.php',
    'Google\\Protobuf\\Internal\\CodedInputStream' => '/lib/proto/Google/Protobuf/Internal/CodedInputStream.php',
    'Google\\Protobuf\\Internal\\CodedOutputStream' => '/lib/proto/Google/Protobuf/Internal/CodedOutputStream.php',
    'Google\\Protobuf\\Internal\\Descriptor' => '/lib/proto/Google/Protobuf/Internal/Descriptor.php',
    'Google\\Protobuf\\Internal\\DescriptorPool' => '/lib/proto/Google/Protobuf/Internal/DescriptorPool.php',
    'Google\\Protobuf\\Internal\\DescriptorProto' => '/lib/proto/Google/Protobuf/Internal/DescriptorProto.php',
    'Google\\Protobuf\\Internal\\EnumBuilderContext' => '/lib/proto/Google/Protobuf/Internal/EnumBuilderContext.php',
    'Google\\Protobuf\\Internal\\EnumDescriptor' => '/lib/proto/Google/Protobuf/Internal/EnumDescriptor.php',
    'Google\\Protobuf\\Internal\\EnumDescriptorProto' => '/lib/proto/Google/Protobuf/Internal/EnumDescriptorProto.php',
    'Google\\Protobuf\\Internal\\EnumValueDescriptorProto' => '/lib/proto/Google/Protobuf/Internal/EnumValueDescriptorProto.php',
    'Google\\Protobuf\\Internal\\FieldDescriptor' => '/lib/proto/Google/Protobuf/Internal/FieldDescriptor.php',
    'Google\\Protobuf\\Internal\\FieldDescriptorProto' => '/lib/proto/Google/Protobuf/Internal/FieldDescriptorProto.php',
    'Google\\Protobuf\\Internal\\FileDescriptor' => '/lib/proto/Google/Protobuf/Internal/FileDescriptor.php',
    'Google\\Protobuf\\Internal\\FileDescriptorProto' => '/lib/proto/Google/Protobuf/Internal/FileDescriptorProto.php',
    'Google\\Protobuf\\Internal\\FileDescriptorSet' => '/lib/proto/Google/Protobuf/Internal/FileDescriptorSet.php',
    'Google\\Protobuf\\Internal\\FileOptions' => '/lib/proto/Google/Protobuf/Internal/FileOptions.php',
    'Google\\Protobuf\\Internal\\GPBJsonWire' => '/lib/proto/Google/Protobuf/Internal/GPBJsonWire.php',
    'Google\\Protobuf\\Internal\\GPBLabel' => '/lib/proto/Google/Protobuf/Internal/GPBLabel.php',
    'Google\\Protobuf\\Internal\\GPBType' => '/lib/proto/Google/Protobuf/Internal/GPBType.php',
    'Google\\Protobuf\\Internal\\GPBUtil' => '/lib/proto/Google/Protobuf/Internal/GPBUtil.php',
    'Google\\Protobuf\\Internal\\GPBWire' => '/lib/proto/Google/Protobuf/Internal/GPBWire.php',
    'Google\\Protobuf\\Internal\\GetPublicDescriptorTrait' => '/lib/proto/Google/Protobuf/Internal/GetPublicDescriptorTrait.php',
    'Google\\Protobuf\\Internal\\HasPublicDescriptorTrait' => '/lib/proto/Google/Protobuf/Internal/HasPublicDescriptorTrait.php',
    'Google\\Protobuf\\Internal\\MapField' => '/lib/proto/Google/Protobuf/Internal/MapField.php',
    'Google\\Protobuf\\Internal\\MapFieldIter' => '/lib/proto/Google/Protobuf/Internal/MapFieldIter.php',
    'Google\\Protobuf\\Internal\\Message' => '/lib/proto/Google/Protobuf/Internal/Message.php',
    'Google\\Protobuf\\Internal\\MessageBuilderContext' => '/lib/proto/Google/Protobuf/Internal/MessageBuilderContext.php',
    'Google\\Protobuf\\Internal\\MessageOptions' => '/lib/proto/Google/Protobuf/Internal/MessageOptions.php',
    'Google\\Protobuf\\Internal\\RawInputStream' => '/lib/proto/Google/Protobuf/Internal/RawInputStream.php',
    'Google\\Protobuf\\Internal\\RepeatedField' => '/lib/proto/Google/Protobuf/Internal/RepeatedField.php',
    'Google\\Protobuf\\Internal\\RepeatedFieldIter' => '/lib/proto/Google/Protobuf/Internal/RepeatedFieldIter.php',
    'HttpBaseController' => '/controller/HttpBaseController.php',
    'Im_BaseController' => '/controller/Im/Im_BaseController.php',
    'Im_Cts_SyncController' => '/controller/Im/Cts/Im_Cts_SyncController.php',
    'ZalyConfig' => '/lib/Util/ZalyConfig.php',
    'ZalyErrorBase' => '/lib/Util/ZalyErrorBase.php',
    'ZalyErrorZh' => '/lib/Util/ZalyErrorZh.php',
    'ZalyHelper' => '/lib/Util/ZalyHelper.php',
    'Zaly\\Proto\\Core\\TransportData' => '/lib/proto/Zaly/Proto/Core/TransportData.php',
    'Zaly\\Proto\\Core\\TransportDataHeaderKey' => '/lib/proto/Zaly/Proto/Core/TransportDataHeaderKey.php',
    'Zaly\\Proto\\Core\\UserClientLangType' => '/lib/proto/Zaly/Proto/Core/UserClientLangType.php',
);
$autoloader->registerClass($preAutoloadFiles);


if (!ini_get("error_log") ) {
    $phpErrorLog = ZalyConfig::getConfig("errorLog");
    if($phpErrorLog) {
        $logDirName = WPF_ROOT_DIR . "/logs";
        if (!is_dir($logDirName)) {
            mkdir($logDirName, 0755, true);
        }
        ini_set("log_errors", "On");
        ini_set("error_log", $logDirName . "/" . $phpErrorLog);
    }
} else {
    ini_set("log_errors", "On");
}

////生成WEB程序管理器，开始执行逻辑
$web = new Wpf_Web();
$web->run();
//
////其他
define("WPF_END_TIME", microtime(true));
////printf("\n<br />request time:%fms\n", (WPF_END_TIME-WPF_START_TIME)*1000);
