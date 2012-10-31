<?php

 require_once('Curlclient.php');
 require_once('Zend/Json.php');
 require_once('Zend/Exception.php');
 require_once('Zend/Json/Exception.php');

class Vtiger_Client_Http_Client extends Vtiger_Client_HTTP_Curlclient {
	var $_serviceurl = '';

	public function __construct($url) {
		if(!function_exists('curl_exec')) {
			die('Vtiger_HTTP_Client: Curl extension not enabled!');
		}
		parent::__construct();
		$this->_serviceurl = $url;
		$useragent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
		$this->set_user_agent($useragent);

		// Escape SSL certificate hostname verification
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);	
	}

	public function doPost($postdata=false, $decodeResponseJSON=false, $timeout=20, $debug=false) {
        if($postdata === false) $postdata = Array();

        if($debug) {
            $debugId = md5(microtime().$this->_serviceurl);
            echo "<hr><div onclick=\"document.getElementById('".$debugId."').style.display=(document.getElementById('".$debugId."').style.display=='none'?'block':'none');\"><strong>Client: POST</strong></div><br>";
        }

		$resdata = $this->send_post_data($this->_serviceurl, $postdata, null, $timeout);

        if($debug) {
            echo "<div id='".$debugId."' style='display:none;'>";
            echo "<pre>";
            var_dump(array("postdata" => $postdata,"returndata" => $resdata));
            echo "</pre>";
            echo "</div>";
        }
	
        try {
		    if($resdata && $decodeResponseJSON) $resdata = $this->__jsondecode($resdata);
        } catch(Zend_Json_Exception $exp) {
            return false;
        }

		return $resdata;
	}

	public function doGet($getdata=false, $decodeResponseJSON=false, $timeout=20, $debug=false) {
        if($getdata === false) $getdata = Array();
		$queryString = '';
		foreach($getdata as $key=>$value) {
			$queryString .= '&' . urlencode($key)."=".urlencode($value);
		}

        if($debug) {
            $debugId = md5(microtime().$this->_serviceurl);
            echo "<hr><div onclick=\"document.getElementById('".$debugId."').style.display=(document.getElementById('".$debugId."').style.display=='none'?'block':'none');\"><strong>Client: GET</strong> [".$this->_serviceurl."?".$queryString."]</div><br>";
        }

        $resdata = $this->fetch_url("$this->_serviceurl?$queryString", null, $timeout);

        if($debug) {
            echo "<div id='".$debugId."' style='display:none;'>";
            echo "<pre>";
            var_dump(array("getdata" => $getdata,"returndata" => $resdata));
            echo "</pre>";
            echo "</div>";
        }

        try {
		    if($resdata && $decodeResponseJSON) $resdata = $this->__jsondecode($resdata);
        } catch(Zend_Json_Exception $exp) {
            return false;
        }

        return $resdata;
	}

	public function __jsondecode($indata) {
		return Zend_Json::decode($indata);
	}

	public function __jsonencode($indata) {
		return Zend_Json::encode($indata);
	}
}

?>
