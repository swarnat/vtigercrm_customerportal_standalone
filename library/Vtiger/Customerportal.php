<?php
require_once("Plugins.php");

class Vtiger_Customerportal extends Vtiger_Plugins
{
    private $_config = array(
        "url" => "",
        "username" => "",
        "accesskey" => ""
    );
    /**
     * @var nusoap_client
     */
    private $_client = false;

    private $_customerportal_id = false;

    private static $instance = false;

    public static $fieldCache = array();

    public static $loggedInUser = false;

    private $_pluginHandler = false;

    /**
     * @var bool
     */
    private static $_debug = false;

    /**
     * Dynamic Cache
     * @var array
     */
    private $_recordCache = array();

    private $_sessionId = false;
    /**
     * @static
     * @param string $url
     * @param string $username
     * @param string $accesskey
     * @param string $customerportal_id
     * @return Vtiger_Customerportal
     */
    public static function getInstance($url = "", $username = "", $accesskey = "", $customerportal_id = "", $loggedInUser = false) {

        if(Vtiger_Customerportal::$instance === false && $url != "") {
            Vtiger_Customerportal::$instance = new Vtiger_Customerportal($url, $username, $accesskey, $customerportal_id);
            Vtiger_Customerportal::$instance->initPlugins();

            Vtiger_Customerportal::$loggedInUser = $loggedInUser;
        }

        return Vtiger_Customerportal::$instance;

    }

    /**
     * @param $value bool
     */
    public static function setDebug($value) {
        self::$_debug = $value;
    }

    /**
     * @param $url URL zum Vtiger System
     * @param $username Username für Webservice
     * @param $accesskey Remote Key für Webservice
     */
    private function __construct($url, $username, $accesskey, $customerportal_id) {
        $this->_config = array(
            "url" => $url,
            "username" => $username,
            "accesskey" => $accesskey
        );

        $this->_customerportal_id = $customerportal_id;
    }

    private function _connect() {
        global $http_auth, $http_auth_credentials, $http_proxy;

        if($this->_client !== false) {
            return;
        }

        require_once("Nusoap/nusoap.php");

        $url = $this->_config["url"]."/modules/Customerportal2/server.php?wsdl";

        $this->_client = new nusoap_client($url, true);

        if(isset($http_proxy)) {
            // Enable nuSOAP Proxy if configured
            $this->_client->setHTTPProxy(
                $http_proxy["url"],
                $http_proxy["port"],
                $http_proxy["proxy_user"],
                $http_proxy["proxy_password"]
            );
        }

        switch($http_auth) {
            case "basic":
                $this->_client->setCredentials($http_auth_credentials["username"], $http_auth_credentials["password"]);
            break;
            case "digest":
                $this->_client->setCredentials($http_auth_credentials["username"], $http_auth_credentials["password"], "digest");
            break;
            case "certificate":
                $this->_client->setCredentials("user", "password", "certificate",
                    array(
                        "sslcertfile" => $http_auth_credentials["sslcertfile"],
                        "sslkeyfile" => $http_auth_credentials["sslkeyfile"],
                        "passphrase" => $http_auth_credentials["passphrase"]
                    )
                );
            break;
        }
		
        $err = $this->_client->getError();

        $loginResult = $this->call("ws_login", array("username" => $this->_config["username"], "accesskey" => $this->_config["accesskey"]));

        $err = $this->_client->getError();

        if($err || $loginResult["result"] == false) {
            if($loginResult["result"] === false) {
                $err = $loginResult["error"];
            }
            error_handler(E_ERROR, "Couldn't connect to vtigerCRM (".$err.")", "", "");

            $this->_client = false;
            return false;
        }

        $cookies = $this->_client->getCookies();
        foreach($cookies as $cookie) {
            if($cookie["name"] == VTIGER_SESSION_VARIABLE) {
                $sessionID = $cookie["value"];
            }
        }

        $this->_sessionId = $sessionID;

    }

    public function call($method, $args, $json_decode = true) {
        $args["WS_SESS_ID"] = $this->_sessionId;

        if(!empty($this->_sessionId)) {
            $this->_client->setCookie(VTIGER_SESSION_VARIABLE, $this->_sessionId);
        }

        $result = $this->_client->call($method, $args);

        if(is_string($result) && $json_decode == true) {
            $result = json_decode($result, true);
        }

        return $result;
    }

    public function doUpdate($data) {
        $return = $this->call("doUpdate", array("data" => json_encode($data)));

        if($return["result"] == "error") {
            error_handler(E_ERROR, $return["message"], __FILE__, __LINE__);
        }
        return $return["return"];
    }
    public function doCreate($module, $data) {
        return $this->call("doCreate", array("module" => $module, "data" => json_encode($data)));
    }
    public function doQuery($query) {
        return $this->call("doQuery", array("query" => $query));
    }

    /**
     * Does Login the User into CustomerPortal Records
     * @param $username
     * @param $passwort
     * @return array|bool
     */
    public function login($username, $passwort) {
        $this->_connect();

        $result = $this->call("login", array("username" => $username, "passwort" => $this->crypt_password($passwort)));
var_dump($result);
        #self::setDebug(true);

        if($result["result"] === false) {
            return false;
        } else {
            $return = array("id" => $result["contact_id"], "wsid" => $result["wscontact_id"], "firstname" => $result["firstname"], "lastname" => $result["lastname"], "module" => $result["module"]);

            Vtiger_Customerportal::$loggedInUser = $return;
            return $return;
        }
    }

    public function getFields($module, $create = false) {
        $this->do_action("before_get_fields", $module, $create);

        if(!empty(Vtiger_Customerportal::$fieldCache[$module])) {
            return Vtiger_Customerportal::$fieldCache[$module];
        }
        $this->_connect();

        $result = $this->call("getFields", array("module" => $module, "cp_id" => $this->_customerportal_id, "create" => $create?"1":"0"), true);

        if($result === false) return false;

        Vtiger_Customerportal::$fieldCache[$module] = $result;

        $this->do_action("after_get_fields", $module, $create);

        return $result;
    }

    public function getObj($moduleName) {
        $this->_connect();

		require_once("Vtiger/Modules/".ucfirst(strtolower($moduleName)).".php");
        $className = "Vtiger_Modules_".ucfirst(strtolower($moduleName));
        $obj = new $className($this);

        if(Vtiger_Customerportal::$_debug === true) {
            $obj->setDebug(true);
        }

        return $obj;
    }

    /**
     * create record
     * @param $module module of new record
     * @param $values values of the new record
     * @return bool
     */
    public function createRecord($module, $values) {
        $values = $this->prepareDataForTransfer($module, $values);

        $values = $this->do_filter("before_createRecord", $values, $module);

        if($values === false) {
            return false;
        }

        $obj = $this->getObj($module);
        $result = $obj->createRecord($values);

        $this->do_action("after_createRecord", $module, $result);

        return $result;
    }

    public function searchRecords($module, $values) {

        $obj = $this->getObj($module);
        $result = $obj->searchRecords($values);

        return $result;
    }

    /**
     * @param $module Module of the Record
     * @param $crmID ID for the record
     * @return mixed
     */
    public function getRecord($module, $crmID) {

        $obj = $this->getObj($module);

        $result = $obj->getRecord($crmID);

        return $result;
    }

    /**
     * function get comments, related to a record
     *
     * @param $module module of the record
     * @param $crmID id of the record
     * @return mixed
     */
    public function getComments($module, $crmID) {

        $obj = $this->getObj($module);

        $result = $obj->getRelated($module, $crmID, "ModComments");

        return $result;
    }

    public function prepareDataForTransfer($module, $values) {
        // Checkboxes will be set to "no", if no submit was done, because HTML don't submit unchecked checkboxes
        $fields = $this->getFields($module);
        foreach($fields as $block) {
            foreach($block as $fieldName => $field) {
                if($field["type"] == "checkbox" && !isset($values[$fieldName])) {
                    $values[$fieldName] = "no";
                } elseif($field["type"] == "date" && !empty($values[$fieldName])) {
                    $values[$fieldName] = strtotime($values[$fieldName]);
                }
            }
        }

        return $values;
    }

    /**
     * @param $module module of record
     * @param $crmID crmid of the record
     * @param $values value-array
     * @return bool
     */
    public function setRecord($module, $crmID, $values) {
        $values = $this->prepareDataForTransfer($module, $values);

        $values = $this->do_filter("before_setRecord", $values, $module, $crmID);

        $obj = $this->getObj($module);

        $result = $obj->setRecord($crmID, $values);

        return true;
    }

    /**
     * @param $module source module
     * @param $crmID source crmid
     * @param $targetModule module, which is related to source record
     * @return mixed
     */
    public function getRelated($module, $crmID, $targetModule) {
        if(isset($this->_recordCache[$module."#-#".$crmID."#-#".$targetModule])) {
            return $this->_recordCache[$module."#-#".$crmID."#-#".$targetModule];
        }

        $obj = $this->getObj($module);
        $result = $obj->getRelated($module, $crmID, $targetModule);

        $result = $this->do_filter("get_related_".strtolower($targetModule), $result, $module);

        $this->_recordCache[$module."#-#".$crmID."#-#".$targetModule] = $result;

        return $result;
    }

    protected function crypt_password($password) {

        // Encryption Algorithm
        $alg = MCRYPT_RIJNDAEL_256;
        // Create the initialization vector for increased security.
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($alg, MCRYPT_MODE_ECB), MCRYPT_RAND);

        $encrypted_string = base64_encode(mcrypt_encrypt($alg, SECURITY_SALT, $password, MCRYPT_MODE_CBC, $iv));

        return base64_encode($iv)."#~#~#".$encrypted_string;
    }

    public function changeLogin($crmid, $username, $password) {

        $result = $this->call("changeLogin", array("crmid" => $crmid, "username" => $username, "password" => $this->crypt_password($password)));

        return $result;
    }

    public function writeComment($module, $crmID, $content) {
        $obj = $this->getObj($module);

        return $obj->writeComment($crmID, $content);
    }
//    public function getRelatedDocuments($crmID) {
//        $this->_connect();
//
//        $module = "Base";
//        $obj = $this->getObj($module);
//
//        $result = $obj->getRelatedDocuments($crmID);
//
//        $result = $this->_pluginHandler->do_filter("document_list", $result);
//
//        return $result;
//    }

    public function getDocument($attachmentID, $hash) {
        $this->_connect();

        $this->do_action("get_document", intval($attachmentID));

        $hash = explode(";", $hash);
        $result = $this->call("getDocument", array("attachmentid" => $attachmentID, "crmid" => $hash[1],"hash" => $hash[0]));

        return $result;
    }

    public function outputDocument($attachmentID, $hash) {
        $document = $this->getDocument(intval($attachmentID), $hash);

        if($document !== false) {
            header('Content-Length:' . $document["filesize"]);
            header("Content-Type: ".$document["type"]);
            header('Content-Disposition: attachment; filename="'.$document["filename"].'"');

            $filecontent = ($document["data"]);
            echo $filecontent;
        } else {
            return false;
        }
    }

    public function createDocument($module, $crmID, $fileArray, $folderid) {
        if(!function_exists("execs")) {
            #$tmpFile = tempnam(sys_get_temp_dir(), "CPupload");
            #file_put_contents($tmpFile, serialize(array("module" => $fileArray, "crmID" => $crmID, "fileArray" => $fileArray, "folderid" => $folderid)));

            #exec("php ".dirname(__FILE__)."/uploader.php $tmpFile"); // >/dev/null 2>&1 &
        } else {
            #$this->_startUpload($module, $crmID, $fileArray);
        }

        $this->_startUpload($module, $crmID, $fileArray, $folderid);

        $this->writeComment($module, $crmID, "<em>".T_(sprintf("Document '%s' added ", $fileArray["name"]))."</em>");
    }

    public function getCurrencies() {
        return $this->_client->doInvoke("cp.get_currencies", array(), "GET");
    }
    /**
     * Function will be called by Customerportal.php (synchron) or uploader.php (asynchron)
     * @param $module
     * @param $crmID
     * @param $fileArray
     */
    public function _startUpload($module, $crmID, $fileArray, $folderid) {
        $this->_connect();

        $values = array(
            "filename" => $fileArray["name"],
            "type" => $fileArray["type"],
            "filesize" => $fileArray["size"],
            "data" => base64_encode(file_get_contents($fileArray["tmp_name"])),
        );

        $result = $this->call("createDocument", array("crmid" => $crmID, "folderid" => $folderid, "file" => ($values)));

        var_dump($result);
        #@unlink($fileArray["tmp_name"]);
    }

    /** Plugin Bridge  **/
    public function initPlugins() {

        $this->loadPlugins(dirname(__FILE__)."/plugins/");

    }

}
