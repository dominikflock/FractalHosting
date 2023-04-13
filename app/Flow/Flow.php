<?php

namespace App\Flow;

define('BY_ID', 'modifyById');
define('BY_CLASS', 'modifyByClass');
define('BY_QUERY', 'modifyByQuery');
define('BY_QUERY_ALL', 'modifyByQueryAll');

define('OUTER_HTML', 'outerhtml');
define('INNER_HTML', 'innerhtml');
define('TEXT', 'text');
define('ATTRIBUTE', 'attribute');
define('ADD_CLASS', 'addClass');
define('REMOVE_CLASS', 'removeClass');
define('TOGGLE_CLASS', 'toggleClass');
define('INNER_APPEND', 'innerappend');
define('INNER_PREPEND', 'innerprepend');
define('OUTER_APPEND', 'outerappend');
define('OUTER_PREPEND', 'outerprepend');
define('VALUE', 'value');
define('REMOVE', 'remove');

define('CONSOLE_LOG', 'log');
define('CONSOLE_WARN', 'warn');
define('CONSOLE_ERROR', 'error');

class Flow {
    function __construct($request) {
        $this->request = $request;
        $this->response = [];
    }

    function getData() {
        return json_decode($this->request->input('data'));
    }

    function reload() {
        array_push($this->response, [
            "action" => "reload"
        ]);
        return $this;
    }

    function popup($content, $duration = 5000) {
        array_push($this->response, [
            "action" => "popup",
            "content" => $content,
            "duration" => $duration
        ]);
        return $this;
    }

    function redirect($url) {
        array_push($this->response, [
            "action" => "redirect",
            "url" => $url
        ]);
        return $this;
    }
    
    function modify($action, $method, $selector, $content) {
        array_push($this->response, [
            "action" => $action,
            "method" => $method,
            "selector" => $selector,
            "content" => $content
        ]);
        return $this;
    }

    function alert($content) {
        array_push($this->response, [
            "action" => "alert",
            "content" => $content
        ]);
        return $this;
    }

    function console($content, $type = CONSOLE_LOG) {
        array_push($this->response, [
            "action" => "console",
            "content" => $content,
            "type" => $type
        ]);
        return $this;
    }



    function response() {
        return json_encode($this->response);
    }

    static function generateUID($prefix = null, $length = 10) { // Length includes the Prefix!
        $charset = "0123456789BbCcDdFfGgHhJjKkLlMmNnPpQqRrSsTtVvWwXxYyZz";
        $buffer = $prefix ?? "";
        for ($i = 0; $i < ($length - strlen($prefix)); $i++) {
            $buffer .= $charset[mt_rand(0, strlen($charset)-1)];
        }
        return $buffer;
    }
}