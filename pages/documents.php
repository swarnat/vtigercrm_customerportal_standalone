<?php
define("CURRENT_PAGE", "documents.html");

$connection = getConnection();

if(!empty($_GET["fileid"])) {
    $document = $connection->outputDocument($_GET["fileid"], $_GET["hash"]);

    exit();
}

$documents = $connection->getRelated($_SESSION["cp_user"]["module"], $_SESSION["cp_user"]["id"], "Documents");

$documents = $pluginHandler->do_filter("document_list", $documents);

$hView->assign("documents", $documents);

echo $hView->render("pages/documents.phtml");