<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan
 * Date: 07.09.12
 * Time: 14:35
 */
 
 require_once("Base.php");
class Vtiger_Modules_Helpdesk extends Vtiger_Modules_Base
{
    protected $moduleName = "HelpDesk";

    public function getRelated($module, $crmID, $targetModule) {
        $parent = parent::getRelated($module, $crmID, $targetModule);
        if($parent !== false) {
            return $parent;
        }

    }

}
