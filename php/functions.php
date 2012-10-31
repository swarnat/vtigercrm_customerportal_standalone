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