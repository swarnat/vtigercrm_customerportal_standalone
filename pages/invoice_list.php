<?
define("CURRENT_PAGE", "invoice_list.html");

$connection = getConnection();

$result = $connection->getRelated("Contacts", getUserId(), "Invoice");

$currencies =  $connection->getCurrencies();
$newCur = array();
foreach($currencies as $cur) {
    $newCur[$cur["id"]] = $cur;
}

$hView->assign("currencies", $newCur);

foreach($result as $invoice) {
    $data[] = $connection->getRecord("Invoice", $invoice);
    #var_dump($connection->getRecord("Currency", $invoice["currency_id"]));
}

$hView->assign("invoices", $data);
echo $hView->render("pages/invoices_list.phtml");