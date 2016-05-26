<?php

class RouterTest extends PHPUnit_Framework_TestCase {
    private $routeCollection = [];
    
    public function __construct() {
        // on test construct put some default route to collection
        array_push($this->routeCollection, [["route","default"],"GET",function() {return true;}]);
        array_push($this->routeCollection, [["route","default"],"GET","RouterTest@controllerMethodTest"]);
        array_push($this->routeCollection, [["route","default"],"GET",["controller" => "RouterTest", "method" => "controllerMethodTest"]]);
        
        array_push($this->routeCollection, [["route","default"],"POST",function() {return true;}]);
        array_push($this->routeCollection, [["route","default"],"POST","RouterTest@controllerMethodTest"]);
        array_push($this->routeCollection, [["route","default"],"POST",["controller" => "RouterTest", "method" => "controllerMethodTest"]]);
    }
    
    public function controllerMethodTest() {
        return true;
    }
    
    public function test_parseURI() {
        $uri = explode("/", "/project/route/default/");
        $rootDIR = array_search("project", $uri);
        
        $this->assertTrue(!count(array_diff(["route","default"], array_splice($uri, ($rootDIR+1)))));
    }
    
    public function test_run_withGET() {
        $uri = ["route","default"];
        $url = implode("/", $uri);
        foreach($this->routeCollection as $collection) {
            $coll = implode("/", $collection[0]);
           
            
            if(substr($url, 0, strlen($coll)) === $coll) {
                if("GET" == $collection[1]) {
                    
                    $args = array_splice($uri, count($collection[0])); 
                    if(gettype($collection[2]) == "object") {
                        
                        $this->assertTrue(call_user_func_array($collection[2], $args));

                        
                    } else if(gettype($collection[2]) == "string"){
                        $explode = explode("@", $collection[2]);
                        $controller = new $explode[0];
                        $method = $explode[1];
                        
                        $this->assertTrue(call_user_func_array(array($controller,$method), $args));

                        
                    } else {
                        if(class_exists($collection[2]["controller"])) {
                            $controller = new $collection[2]["controller"];
                            $method = $collection[2]["method"];

                            $this->assertTrue(call_user_func_array(array($controller,$method), $args));
                        }
                        
                        
                    }
                } else {
                    if (!headers_sent()) {
                        header("HTTP/1.1 405 Method Not Allowed");
                        exit;
                    }
                }
            }
        }
        
        if (!headers_sent()) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }
        
    }
    
    public function test_run_withPOST() {
        $uri = ["route","default"];
        $url = implode("/", $uri);
        foreach($this->routeCollection as $collection) {
            $coll = implode("/", $collection[0]);
           
            
            if(substr($url, 0, strlen($coll)) === $coll) {
                if("POST" == $collection[1]) {
                    
                    $args = array_splice($uri, count($collection[0])); 
                    if(gettype($collection[2]) == "object") {
                        
                        $this->assertTrue(call_user_func_array($collection[2], $args));

                        
                    } else if(gettype($collection[2]) == "string"){
                        $explode = explode("@", $collection[2]);
                        $controller = new $explode[0];
                        $method = $explode[1];
                        
                        $this->assertTrue(call_user_func_array(array($controller,$method), $args));

                        
                    } else {
                        if(class_exists($collection[2]["controller"])) {
                            $controller = new $collection[2]["controller"];
                            $method = $collection[2]["method"];

                            $this->assertTrue(call_user_func_array(array($controller,$method), $args));
                        }
                        
                        
                    }
                } else {
                    if (!headers_sent()) {
                        header("HTTP/1.1 405 Method Not Allowed");
                        exit;
                    }
                }
            }
        }
        
        if (!headers_sent()) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }
        
    }
}

