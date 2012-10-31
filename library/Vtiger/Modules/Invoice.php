<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan
 * Date: 07.09.12
 * Time: 14:35
 */
class Vtiger_Modules_Invoice extends Vtiger_Modules_Base
{
    protected $moduleName = "Invoice";

    public function getRelated($module, $crmID, $targetModule) {
        $parent = parent::getRelated($module, $crmID, $targetModule);
        if($parent !== false) {
            return $parent;
        }

    }

    public function getInvoiceNumber($data) {
        return $data[$this->_settings["field_invoice_number"]];
    }

    public function getRecord($crmID) {
        $invoice = parent::getRecord($crmID);

        $invoice["internal"]["invoice_number"] = $this->getInvoiceNumber($invoice);

        return $invoice;
    }
}
