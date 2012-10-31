<?php
if($argc < 2) {
    echo "Wrong Parameter Count";
}

$file = $argv[1];
$content = unserialize(file_get_contents($file));

require_once("../../config.inc.php");

$connection = getConnection();

$connection->_startUpload($content["module"], $content["crmID"], $content["fileArray"], $content["folderid"]);

#@unlink($file);