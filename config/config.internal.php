<?
define("SERVER_ROOT", realpath(dirname(__FILE__)."/..")."/");

define("VERSION", 1.0);

define('LANGUAGE_DIR', SERVER_ROOT .'/language/');

define('L18N_DOMAIN', "customerPortal");

function SW__autoload($klasse) {

	// die falschen zeichen in klassennamen mal sicherheitshalber verbieten
	if (strpos ( $klasse, '.' ) !== false || strpos ( $klasse, '/' ) !== false || strpos ( $klasse, '\\' ) !== false || strpos ( $klasse, ':' ) !== false) {
		return;
	}
    if(substr($klasse, 0, 5) == "HTML_") {
        return;
    }
	$path = str_replace(" ","/", ucwords(strtolower(str_replace("_"," ", $klasse))));

    include_once($path  . ".php");

    if(!class_exists($klasse) && !interface_exists($klasse)) {
        echo "[ERROR]ClassNotFound (".$klasse.")<br>";
    }
}

spl_autoload_register('SW__autoload');

set_include_path(SERVER_ROOT."/library/".PATH_SEPARATOR.get_include_path() );
$hView = new Plib_Template();