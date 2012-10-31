<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan
 * Date: 07.09.12
 * Time: 14:35
 */
class Vtiger_Modules_Applicants extends Vtiger_Modules_Base
{
    protected $moduleName = "Applicants";

    public function getRelated($module, $crmID, $targetModule) {
        $parent = parent::getRelated($module, $crmID, $targetModule);
        if($parent !== false) {
            return $parent;
        }

    }
}
