<?php
if(!empty($_FILES["file"])) {
    # var_dump($_FILES);  exit();
    $connection = getConnection();

#        Vtiger_Customerportal::setDebug(true);

    $connection->createDocument($_POST["module"], $_POST["crmid"], $_FILES["file"], intval($_POST["folderid"]));
    exit();
}