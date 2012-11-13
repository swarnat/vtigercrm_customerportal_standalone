<?
error_reporting(E_ALL | E_STRICT);
ini_set("display_errors", "1");

require_once("config/config.internal.php");
require_once("config/config.inc.php");

require_once("php/functions.php");
require_once("php/startup.inc.php");

if(empty($_POST["relay_key"]) || $_POST["relay_key"] != RELAY_KEY) {
    exit();
}

$connection = getConnection();

$returnurl = $connection->call("webformRelay", array("data" => serialize($_POST)), true);

if(!empty($returnurl["returnurl"])) {
    header("Location:http://".$returnurl["returnurl"]);
    exit();
}
