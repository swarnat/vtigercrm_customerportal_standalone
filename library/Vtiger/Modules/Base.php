<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan
 * Date: 07.09.12
 * Time: 14:28
 */
class Vtiger_Modules_Base
{
    protected $moduleName = "";

    protected $_client = false;

    protected $_settings = array();

    protected $_debug = false;
    /**
     * @param $connection Vtiger_Client_WSClient
     */
    public function __construct($connection) {
        global $moduleConfigurations;

        $this->_client = $connection;

        if(!empty($moduleConfigurations) && isset($moduleConfigurations[$this->moduleName])) {
            $this->_settings = $moduleConfigurations[$this->moduleName];
        }
    }

    /**
     * @param $value bool
     */
    public function setDebug($value) {
        $this->_debug = $value;
    }

    public function createRecord($values) {
        return $this->_client->doCreate($this->moduleName, $values);

    }

    /**
     * @TODO not yet implemented
     * @param $values
     * @return bool
     */
    public function searchRecords($values) {
        return false;

//        $where = array();
//        foreach($values as $key => $value) {
//            $where[] = "";
//        }
//
//        $result = $this->_client->doQuery("SELECT * FROM ".$this->moduleName." WHERE id = '".""."'");
//
//        return $result;
    }

    /**
     * @param $crmID string [moduleIDxCrmID]
     * @return mixed
     */
    public function getRecord($crmID) {

        $result = ($this->_client->call("doQuery", array("query" => "SELECT * FROM ".$this->moduleName." WHERE id = '".$crmID."'")));

        return $result[0];
    }

    /**
     * @param $crmID string [moduleIDxCrmID]
     * @param $values mixed
     */
    public function setRecord($crmID, $values) {
        $record = $this->getRecord($crmID);

        foreach($values as $key => $value) {
            $record[$key] = $value;
        }

        $this->_client->doUpdate($record);

    }

    public function getRelated($module, $crmID, $targetModule) {

        switch($targetModule) {
            case "Documents":
                return $this->_client->call("relatedDocuments", array("crmid" => $crmID));
                break;
            case "Invoice":
                switch($module) {
                    case "Contacts":
                        $result = $this->_client->doQuery("SELECT id FROM Invoice WHERE contact_id = '".$crmID."'", $this->_debug);
                        $return = array();
                        for($i = 0; $i < count($result); $i++) {
                            $return[] = $result[$i]["id"];
                        }
                        return $return;
                        break;
                }
                break;
            case "Accounts":
                $result = $this->_client->doQuery("SELECT account_id FROM ".$module." WHERE id = '".$crmID."'", $this->_debug);

                if(count($result) == 0 || empty($result[0]["account_id"])) {
                    return false;
                }

                $cp = Vtiger_Customerportal::getInstance();
                return $cp->getRecord("Accounts", $result[0]["account_id"]);

                break;
            case "Products":
            case "ModComments":
            case "HelpDesk":
                return $this->_client->call("getRelated", array("module" => $module, "crmid" => $crmID, "target_module" => $targetModule));
                break;

        }

        return false;

    }

    public function writeComment($crmID, $content) {
        return $this->_client->call("createComment", array("crmid" => $crmID, "comment" => $content, "authorid" => $_SESSION["cp_user"]["id"]));
    }

}
