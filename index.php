<?
error_reporting(E_ALL | E_STRICT);
ini_set("display_errors", "1");

require_once("config/config.internal.php");
require_once("config/config.inc.php");

require_once("php/functions.php");
require_once("php/startup.inc.php");

if(empty($_GET["ajax"])) {
    $ajax = false;
} else {
    $ajax = true;
}
define("TEMPLATE_ROOT", CUSTOMER_PORTAL_URL."templates/".TEMPLATE_DIR."/");

// Open Homepage, if non page was given
if(!isset($_GET["page"]) || empty($_GET["page"])) {
    $_GET["page"] = "home";
}

$page = ($ajax?"ajax/":"").preg_replace("/[^a-zA-Z0-9_]/", "", $_GET["page"]);

$page = $pluginHandler->do_filter("get_page", $page);

ob_start();

if(empty($_SESSION["cp_user"]) && $page != "login") {
    header("Location:login.html");
    exit();
}
if($page == "404") {
    readfile("404.html");
    exit();
}

if($page != "login" && !$ajax) {
    $mainNavigation = $pluginHandler->do_filter("main_navigation", $mainNavigation);

    $hView->assign("navigation", $mainNavigation);
}

$hView->assign("current_page", $page);

if(file_exists(SERVER_ROOT."/pages/".$page.".php")) {
    require_once(SERVER_ROOT."/pages/".$page.".php");
} else {
    header("Location:404.html");
    exit();
}

if(!empty($_POST["comment"])) {
    // SecurityHash should permit write comments to other records
    $module = $_POST["comment"]["module"];
    $crmID = $_POST["comment"]["crmid"];
    $securityCheck = $_POST["comment"]["secure"];

    if($securityCheck == sha1($module."#".$crmID."#".SECURITY_SALT."#".$_SESSION["cp_user"]["id"])) {
        $connection = getConnection();
        $connection->writeComment($module, $crmID, $_POST["comment"]["content"]);
    }
}

$content = ob_get_clean();

if(!$ajax) {
    $hView->assign("templateSettings", $templateSettings);

    $hView->assign("supported_locales", $supported_locales);

    $hView->assign("mainContent", $content);

    echo $hView->render("layout.phtml");
} else {
    echo $content;
}
