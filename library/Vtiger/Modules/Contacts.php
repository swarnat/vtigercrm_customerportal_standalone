<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan
 * Date: 07.09.12
 * Time: 14:35
 */
 
 require_once("Base.php");
class Vtiger_Modules_Contacts extends Vtiger_Modules_Base
{
    protected $moduleName = "Contacts";

    public function getRelated($module, $crmID, $targetModule) {
        $parent = parent::getRelated($module, $crmID, $targetModule);
        if($parent !== false) {
            return $parent;
        }

		switch($targetModule) {
            case "Applicants":
                return $this->_client->doInvoke("cp.get_related", array("module" => $module, "crmid" => $crmID, "target_module" => $targetModule), "GET", $this->_debug);
                break;
		}
    }
}
