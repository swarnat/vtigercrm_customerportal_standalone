<?php
$connection = Vtiger_Customerportal::getInstance();

$connection->add_filter("get_related_documents", "get_product_documents", 10);
#$connection->add_filter("document_access", "document_access", 10);

function get_product_documents($documents, $module) {
    if($module == "Products")
        return $documents;

    $connection = Vtiger_Customerportal::getInstance();

    $products = $connection->getRelated("Contacts", getUserId(), "Products");

    foreach($products as $product) {
        $relDoc = $connection->getRelated("Products", $product["id"], "Documents");
        foreach($relDoc as $doc) {

            $documents[] = $doc;
        }
    }

    return $documents;
}

function document_access($access, $fileid) {
    if($access == true) {
        return true;
    }

    #$records = get_product_documents(array());

    foreach($records as $doc) {
        if($doc["attachmentsid"] == $fileid) {
            return true;
        }
    }
}