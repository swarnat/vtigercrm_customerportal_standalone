<?php
define("CURRENT_PAGE", "tickets.html");

if(!empty($_POST["create"]) && $_POST["create"] == "1") {
    $connection = getConnection();

    #Vtiger_Customerportal::setDebug(true);

    $values = $_POST;
    $values["parent_id"] = getWSUserId();
    $connection->createRecord("HelpDesk", $values);
}

initJsTree("ajax/tickets.html");
?>
<a href="tickets.html?operation=create"><?=__("Create Ticket") ?></a>
