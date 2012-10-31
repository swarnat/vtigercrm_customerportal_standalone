<?php
class Vtiger_Plugins
{
    private $_actions = array();

    private $_sorted = array();

    public function loadPlugins($path = false) {
        if($path === false) {
            $path = dirname(__FILE__)."/plugins/";
        }

        $plugins = scandir($path."/enabled/");

        foreach($plugins as $plugin) {

            if(!empty($plugin) && $plugin != "." && $plugin != "..") {
                require_once($path."/enabled/".$plugin."/".$plugin.".php");
            }

        }

    }

    public function add_filter($filter, $handler, $priority = 1) {
        $this->add_action($filter, $handler, $priority);
    }
    public function add_action($action, $handler, $priority = 1) {
        if(!isset($this->_actions[$action])) {
            $this->_actions[$action] = array();
        }
        if(!isset($this->_actions[$action][$priority])) {
            $this->_actions[$action][$priority] = array();
        }

        $this->_actions[$action][$priority][] = $handler;
    }

    public function do_action($action) {
        if(!isset($this->_actions[$action])) {
            return;
        }
        $parameter = array();
        for ( $i = 1; $i < func_num_args(); $i++ ) {
            $parameter[] = func_get_arg($i);
        }

        // Sort
        if ( !isset( $this->_sorted[$action] ) ) {
            ksort($this->_actions[$action]);
            $this->_sorted[$action] = true;
        }

        foreach($this->_actions[$action] as $actionList) {
            foreach($actionList as $handler) {
                call_user_func_array($handler, $parameter);
            }
        }

    }
    public function do_filter($action, $value) {
        if(!isset($this->_actions[$action])) {
            return $value;
        }
        $extraParameter = array();
        for ( $i = 2; $i < func_num_args(); $i++ ) {
            $extraParameter[] = func_get_arg($i);
        }

        // Sort
        if ( !isset( $this->_sorted[$action] ) ) {
            ksort($this->_actions[$action]);
            $this->_sorted[$action] = true;
        }

        foreach($this->_actions[$action] as $actionList) {
            foreach($actionList as $handler) {
                $value = call_user_func_array($handler, array_merge(array($value), $extraParameter));
            }
        }

        return $value;
    }

}