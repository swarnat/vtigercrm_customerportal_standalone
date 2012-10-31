<?php

class Plib_Template {
	public function assign($spec, $value = null, $append = false)
    {
        // which strategy to use?
        if (is_string($spec)) {
            // assign by name and value
            if(!$append) {
	            $this->$spec = $value;
            } else {
	            $this->$spec .= $value;
            }
        } elseif (is_array($spec)) {
            // assign from associative array
            // $error = false;
            foreach ($spec as $key => $val) {
				if(!$append) {
					$this->$key = $val;
				} else {
					$this->$key .= $val;
				}
            }
        }

        return $this;
    }

    public function __get($value) {
        return "";
    }
    
    public function __call($value, $arg) {
		$class = "Plib_Template_Helper_".ucfirst(strtolower($value));
		$function = strtolower($value);

        array_unshift($arg, $this);

		$return = call_user_func_array("$class::$function", $arg);

        return $return;
    }
    private function includeStatic($file) {
    	return file_get_contents($file);
    }

	protected function _render($value) {

        ob_start();
		// Zend_Debug::Dump($value);
        try {
		    eval ( "?".">".$value."<"."?");
        } catch(Exception $ex) {
            echo "Error";
        }

		$strOutput = ob_get_contents();
		ob_clean();

		// Zend_Debug::Dump($strOutput);
		return $strOutput;

	}

    public function render($filename, $path = "") {
           if(empty($path)) {
               $content = file_get_contents(SERVER_ROOT.DIRECTORY_SEPARATOR."templates/".TEMPLATE_DIR.DIRECTORY_SEPARATOR.$filename);
           } else {
               $content = file_get_contents($path.$filename);
           }

           #echo strlen($content)."\n";
           return $this->_render($content);

   	}
	public function renderString(&$value) {
		return $this->_render($value);
	}

}
