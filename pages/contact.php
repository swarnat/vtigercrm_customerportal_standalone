<p class="headline">
    <? echo T_("Your Contact informations"); ?>
</p>
<p>
    <?php echo T_("Please keep this values correct, to help us to contact you, if you have a question."); ?>
</p>
<?
define("CURRENT_PAGE", "contact.html");

$connection = getConnection();

$contact = $connection->getRecord("Contacts", $_SESSION["cp_user"]["id"]);

require_once 'HTML/QuickForm2.php';
require_once 'HTML/QuickForm2/Renderer.php';

$form = new HTML_QuickForm2('elements');
$form->setAttribute('action', "#");

$form->addDataSource(new HTML_QuickForm2_DataSource_Array($contact));

$fields = $connection->getFields("Contacts");

addFields($fields, $form);

// submit buttons in nested fieldset
$form->addElement(
    'submit', 'testSubmit', array('value' => 'Save this values')
);

if ('POST' == $_SERVER['REQUEST_METHOD'] && $form->isSubmitted()) {
    echo "<pre>\n";

    $values = $form->getValue();
    $connection->setRecord("Contacts", $_SESSION["cp_user"]["id"], $values);
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

// Change Userlogin
    $form = new HTML_QuickForm2('change_user', "post", null, true);
   	$form->setAttribute('action', "#");

   	$form->addDataSource(new HTML_QuickForm2_DataSource_Array($_POST));

    $fieldset = $form->addElement('fieldset')->setLabel(__("Change your Login Informations"));

    $field1 = $fieldset->addElement(
        'text', "username", null, array('label' =>  __("Login eMail"))
    );
    $field1->addRule('required', __('Login eMail is required'), null,
                       HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
    $field1->addRule('email', 'Email address is invalid', null,
                    HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);

    $field1 = $fieldset->addElement(
        'password', "password1", null, array('label' =>  __("New Password"))
    );
    $field2 = $fieldset->addElement(
        'password', "password2", null, array('label' =>  __("Password Repeat"))
    );

    $field1->addRule('required', __('Password is required'), null,
                       HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
    $field2->addRule('required', __('Please Repeat your password'), null,
                       HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);
    $field1->addRule('eq', __("Passwords don't match"), $field2,
                       HTML_QuickForm2_Rule::ONBLUR_CLIENT_SERVER);

    $fieldset->addElement(
		'submit', 'testSubmit', array('value' => 'Test Submit')
	);

	if ('POST' == $_SERVER['REQUEST_METHOD'] && $form->isSubmitted()) {
        if($form->validate()) {

            $values = $form->getValue();

           # $values["portal"] = "1";
            $connection = getConnection();
            Vtiger_Customerportal::setDebug(true);

            $return = $connection->changeLogin($_SESSION["cp_user"]["id"], $values["username"], $values["password1"]);

            echo "<p class='hint success'>".__("Your Login Information are successfully changed!")."</p>";
        }

		// let's freeze the form and remove the reset button
	#    $fsButton->removeChild($testReset);
	 #   $form->toggleFrozen(true);
	}

	$renderer = HTML_QuickForm2_Renderer::factory('default');
    $jsRenderer = new HTML_QuickForm2_JavascriptBuilder('/data/js');
    $renderer->setJavascriptBuilder($jsRenderer);

	$form->render($renderer);
	// Output javascript libraries, needed by hierselect
	#echo $jsRenderer->getLibraries(true, true);
	echo $renderer;

?>