<p class="headline">
    <? echo T_("Your Company informations"); ?>
</p>
<p>
    <?php echo T_("Please keep this values correct, to help us to contact you, if you have a question."); ?>
</p>
<?php
define("CURRENT_PAGE", "organization.html");

// Get CustomerPortal Connection
$connection = getConnection();

// get record, related to the userID
$related = $connection->getRelated("Contacts", getUserId(), "Accounts");

// return of false, if there are no related records
if(empty($related)) {
    echo "<p class='hint info'>".T_("No Account is associated to your account.")."</p>";
    return;
}

// print form and recognize configured fields
$form = insertForm("Accounts", $related);

// Save values if submit
if ('POST' == $_SERVER['REQUEST_METHOD'] && $form->isSubmitted()) {
    $values = $form->getValue();
    $connection->setRecord("Accounts", $related["id"], $values);
}


