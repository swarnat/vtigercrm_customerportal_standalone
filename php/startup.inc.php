<?php
session_start();

/**
 * Simple Session Hijacking Check
 */
if (isset($_SESSION['HTTP_USER_AGENT'])) {

    if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
    {
        session_destroy();
        exit;
    }

    if ($_COOKIE['checkString'] != $_SESSION["checkString"] && HIGH_SESSION_SECURITY == true) {
        session_destroy();
        exit;
    }
} else {
    $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);

    /**
     * More complex Session Hijacking Check
     */
    $string = $_SERVER['HTTP_USER_AGENT'];
    $string .= '9eHT#4#F0mGZ&rR';

    setcookie("checkString", sha1($string), null, "/");
    $_SESSION["checkString"] = sha1($string);
}

set_error_handler("error_handler");
register_shutdown_function("shutdown_handler");

/**
 * Variables
 */

global $customerPortalConnection;

/**
 * Connection to CustomerPortal
 *
 * @var CustomerPortalConnection Vtiger_Customerportal
 */
$customerPortalConnection = false;

if(!empty($_GET["lang"])) {
	$_SESSION["lang"] = $_GET["lang"];
}
$locale = (isset($_SESSION["lang"]))? $_SESSION["lang"] : DEFAULT_LOCALE;
#var_dump($locale);exit();
$langArray = array();

foreach (glob("language/".$locale."/*.php") as $filename)
{

    $tmpLang = include $filename;
    $langArray = array_merge($langArray, $tmpLang);
}

/**
 * Initialize PluginHandler
 */
$pluginHandler = new Vtiger_Plugins();
$pluginHandler->loadPlugins(dirname(__FILE__)."/../plugins/");

$pluginHandler->do_action("init");

