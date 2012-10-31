<p class="headline">
    <? echo T_("Your Company informations"); ?>
</p>
<p>
    <?php echo T_("Please keep this values correct, to help us to contact you, if you have a question."); ?>
</p>
<?php
define("CURRENT_PAGE", "organization.html");

$connection = getConnection();

$related = $connection->getRelated("Contacts", getUserId(), "Accounts");

if(empty($related)) {
    echo "<p class='hint info'>".T_("No Account is associated to your account.")."</p>";
    return;
}
require_once 'HTML/QuickForm2.php';
require_once 'HTML/QuickForm2/Renderer.php';

$form = new HTML_QuickForm2('elements');
$form->setAttribute('action', "#");

$form->addDataSource(new HTML_QuickForm2_DataSource_Array($related));

$fields = $connection->getFields("Accounts");

addFields($fields, $form);

// submit buttons in nested fieldset
$fsSubmit = $form->addElement('fieldset')->setLabel('These buttons can submit the form');

$form->addElement('hidden', 'account_id')->setValue($related["id"]);

$fsSubmit->addElement(
    'submit', 'testSubmit', array('value' => 'Test Submit')
);
$fsSubmit->addElement(
    'button', 'testSubmitButton', array('type' => 'submit'),
     array('content' => '<img src="http://pear.php.net/gifs/pear-icon.gif" '.
        'width="32" height="32" alt="pear" />This button submits')
);

if ('POST' == $_SERVER['REQUEST_METHOD'] && $form->isSubmitted()) {
    echo "<pre>\n";

    $values = $form->getValue();
    $connection->setRecord("Accounts", $related["id"], $values);
    echo "</pre>\n<hr />";
    // let's freeze the form and remove the reset button
#    $fsButton->removeChild($testReset);
 #   $form->toggleFrozen(true);
}

$renderer = HTML_QuickForm2_Renderer::factory('default');
$form->render($renderer);
// Output javascript libraries, needed by hierselect
#echo $renderer->getJavascriptBuilder()->getLibraries(true, true);
echo $renderer;

