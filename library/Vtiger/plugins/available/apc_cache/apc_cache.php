<?php
$connection = Vtiger_Customerportal::getInstance();

#$connection->add_action("before_get_fields", "before_get_apc_fields", 10);
#$connection->add_action("after_get_fields", "after_get_apc_fields", 10);

function before_get_apc_fields($module) {
    $fetch = apc_fetch("cp_get_fields_".$module);

    if($fetch !== false) {
        Vtiger_Customerportal::$fieldCache[$module] = $fetch;
    }
}

function after_get_apc_fields($module) {
    $fetch = apc_fetch("cp_get_fields_".$module);

    if($fetch === false) {
        apc_store("cp_get_fields_".$module, Vtiger_Customerportal::$fieldCache[$module], 86400);
    }
}