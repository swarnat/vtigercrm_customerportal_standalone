<?php
/**
 * @return Vtiger_Customerportal
 */
function getConnection() {
    global $customerPortalConnection, $connectionSettings;


    if($customerPortalConnection !== false) {
        return $customerPortalConnection;
    }

    $customerPortalConnection = Vtiger_Customerportal::getInstance($connectionSettings["url"], $connectionSettings["username"], $connectionSettings["accesskey"], CUSTOMERPORTAL_ID);

    return $customerPortalConnection;
}

/**
 * @param $fields
 * @param $form HTML_QuickForm2
 */
function addFields($fields, &$form) {
    $saveable = false;

    foreach($fields as $legend => $fieldsetArray) {
        $fieldset = $form->addElement('fieldset');
        if(!empty($legend)) {
            $fieldset->setLabel(__($legend));
        }

        foreach($fieldsetArray as $key => $field) {

            // Don't show field!
            if($field["show"] == "0") {
                continue;
            }

            switch($field["type"]) {
                case "picklist":
					$options = array();
					foreach($field["options"] as $option) {
						$options[$option] = T_($option);
					}
					$tmp = $fieldset->addElement(
						'select', $key, null, array('options' => $options, 'label' =>  T_($field["label"]))
					);
				break;
				case "text":

                    $tmp = $fieldset->addElement(
                        'text', $key, array(), array('label' => T_($field["label"]))
                    );
                    break;
				case "textarea":

                    $tmp = $fieldset->addElement(
                        'textarea', $key, array(), array('label' => T_($field["label"]))
                    );
                    break;
                case "firstname":
                    $options = array();
                    foreach($field["options"] as $option) {
                        $options[$option] = T_($option);
                    }
                    $tmp = $fieldset->addElement(
                        'select', "salutationtype", null, array('options' => $options, 'label' =>  T_("Salutation"))
                    );

                    $tmp = $fieldset->addElement(
                        'text', "firstname", array(), array('label' => T_($field["label"]))
                    );
                    break;
                case "checkbox":

                    $tmp = $fieldset->addElement(
                        'checkbox', $key, array(), array('label' => T_($field["label"]))
                    );
                    break;
                case "date":
                    if(!HTML_QuickForm2_Factory::isElementRegistered("Html5Date")) {
                        HTML_QuickForm2_Factory::registerElement("Html5Date", "HTML_CustomerPortal_Element_Html5Date", "HTML/CustomerPortal/Element/Html5Date.php");
                    }

                    $tmp = $fieldset->addElement(
                        'Html5Date', $key, array(), array('label' => T_($field["label"]))
                    );
                    break;
                default:
                    var_dump($field["type"]);
            }

            if($field["readonly"] == "1") {
                $tmp->toggleFrozen(true);
            } else {
                $saveable = true;
            }
        }
	}

    return $saveable;
}


/**
 * Get the logged in UserID in CustomerPortal
 * @return string
 */
function getUserId() {
    return $_SESSION["cp_user"]["id"];
}
function getWSUserId() {
    return $_SESSION["cp_user"]["wsid"];
}

function initJsTree($url) {
    global $hView;

    $hView->assign("jstree", true);
    $hView->assign("jstreeURL", CUSTOMER_PORTAL_URL."/".$url);
}

function __($key) {
    global $langArray;

    return !empty($langArray[$key])?$langArray[$key]:$key;
}
function T_($key) {
    return __($key);
}

function insertForm($module, $record) {
    $connection = getConnection();

    require_once 'HTML/QuickForm2.php';
    require_once 'HTML/QuickForm2/Renderer.php';

    $form = new HTML_QuickForm2('elements');
    $form->setAttribute('action', "#");

    if(!empty($record) && is_array($record)) {
        $form->addDataSource(new HTML_QuickForm2_DataSource_Array($record));
    }

    $fields = $connection->getFields($module);

    addFields($fields, $form);

    $form->addElement(
        'submit', 'testSubmit', array('value' => 'Save this values')
    );

    $renderer = HTML_QuickForm2_Renderer::factory('default');
    $form->render($renderer);
    // Output javascript libraries, needed by hierselect
    #echo $renderer->getJavascriptBuilder()->getLibraries(true, true);
    echo $renderer;

    return $form;
}

function error_handler($errno, $errstr, $errfile, $errline) {
    global $current_user;

    switch ($errno){
        case E_ERROR: // 1 //
            $typestr = 'E_ERROR'; break;
        case E_WARNING: // 2 //
            $typestr = 'E_WARNING'; break;
        case E_PARSE: // 4 //
            $typestr = 'E_PARSE'; break;
        case E_CORE_ERROR: // 16 //
            $typestr = 'E_CORE_ERROR'; break;
        case E_CORE_WARNING: // 32 //
            $typestr = 'E_CORE_WARNING'; break;
        case E_COMPILE_ERROR: // 64 //
            $typestr = 'E_COMPILE_ERROR'; break;
        case E_CORE_WARNING: // 128 //
            $typestr = 'E_COMPILE_WARNING'; break;
        case E_USER_ERROR: // 256 //
            $typestr = 'E_USER_ERROR'; break;
        case E_USER_WARNING: // 512 //
            $typestr = 'E_USER_WARNING'; break;
        case E_RECOVERABLE_ERROR: // 4096 //
            $typestr = 'E_RECOVERABLE_ERROR'; break;
        default:
            return true;
    }

    ob_clean();

    $html = "<html>";
    $html .= "<body style='font-family:Arial;'>";
    $html .= "<h2>Customerportal Error occurred</h2>";

    $htmlx = "<table style='font-size:14px;font-family:Courier;'>";
    $htmlx .= "<tr><td width=100>ERROR:</td><td><strong>".$typestr."</strong></td></tr>";
    $htmlx .= "<tr><td>LOCATION:</td><td><em>".$errfile." [".$errline."]</td></tr>";
    $htmlx .= "<tr><td>URL:</td><td><em>".$_SERVER["REQUEST_URI"]."</em></td></tr>";
    $htmlx .= "</table>";
    $htmlx .= "<br>";
    $htmlx .= $errstr;
    if(DEBUG) {
        $html .= $htmlx;
    }
    $html .= "</body>";
    $html .= "</html>";
    echo $html;
    echo "<br><br><strong>The Systemadministrator has been notified!</strong>";

    $subject = 'Customerportal error';

    $headers = "From: " . ADMIN_MAIL_SENDER . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    mail(ADMIN_MAIL, $subject, $html.$htmlx, $headers);
    exit();
    return true;
}

function shutdown_handler() {
    if ($error = error_get_last()) {
        error_handler($error['type'], $error['message'], $error['file'], $error['line']);
    }

}