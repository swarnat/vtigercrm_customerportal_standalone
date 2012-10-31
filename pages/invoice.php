<?php
define("CURRENT_PAGE", "invoice_list.html");

$connection = getConnection();

$invoice = $connection->getRecord("Invoice", $_GET["id"]);

$fields = $connection->getFields("Invoice");

$hView->assign("invoice", $invoice);
$hView->assign("fields", $fields);
var_dump($invoice);

echo $hView->render("pages/invoice.phtml");