<p class="headline">
    <? echo T_("Your Contact informations"); ?>
</p>
<p>
    <?php echo T_("Please keep this values correct, to help us to contact you, if you have a question."); ?>
</p>
<?
define("CURRENT_PAGE", "contact.html");

$module = "Contacts";
$crmid = $_SESSION["cp_user"]["id"];

    // Get CustomerPortal Connection
    $connection = getConnection();

    // Load Contact Record
    $record = $connection->getRecord($module, $crmid);

    $form = insertForm($module, $record);

    if ('POST' == $_SERVER['REQUEST_METHOD'] && $form->isSubmitted()) {
        $values = $form->getValue();

        $connection->setRecord($module, $crmid, $values);
    }


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
		'submit', 'testSubmit', array('value' => 'change login')
	);

	if ('POST' == $_SERVER['REQUEST_METHOD'] && $form->isSubmitted()) {
        if($form->validate()) {

            $values = $form->getValue();

           # $values["portal"] = "1";
            $connection = getConnection();
            Vtiger_Customerportal::setDebug(true);

            $return = $connection->changeLogin($_SESSION["cp_user"]["id"], $values["username"], $values["password1"]);

            $successMessage = true;
        }
	}

	$renderer = HTML_QuickForm2_Renderer::factory('default');
    $jsRenderer = new HTML_QuickForm2_JavascriptBuilder('/data/js');
    $renderer->setJavascriptBuilder($jsRenderer);

	$form->render($renderer);
	// Output javascript libraries, needed by hierselect
	#echo $jsRenderer->getLibraries(true, true);
	echo $renderer;

    if($successMessage === true) {
        echo "<div style='clear:both;'></div><p class='hint success'>".__("Your Login Information are successfully changed!")."</p>";
    }

?>