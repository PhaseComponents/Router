<?php

use Phase\Router\Request\HeaderMessage;

class RouterTest extends PHPUnit_Framework_TestCase {
    private $routeCollection = [];

    private $headerMessage;

    public function __construct() {
        // on test construct put some default route to collection
        array_push($this->routeCollection, [["route","default"],"GET",function() {return true;}]);
        array_push($this->routeCollection, [["route","default"],"GET","RouterTest@controllerMethodTest"]);
        array_push($this->routeCollection, [["route","default"],"GET",["controller" => "RouterTest", "method" => "controllerMethodTest"]]);

        array_push($this->routeCollection, [["route","default"],"POST",function() {return true;}]);
        array_push($this->routeCollection, [["route","default"],"POST","RouterTest@controllerMethodTest"]);
        array_push($this->routeCollection, [["route","default"],"POST",["controller" => "RouterTest", "method" => "controllerMethodTest"]]);

        $this->headerMessage = new HeaderMessage();
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
                    $this->assertTrue($this->headerMessage->sendHeader405());
                }
            }
        }

          $this->assertTrue($this->headerMessage->sendHeader404());

    }

    public function test_run_withPOST() {
        $uri = ["route","default"];
        $url = implode("/", $uri);

        foreach($this->routeCollection as $collection) {
            $coll = implode("/", $collection[0]);

            if(substr($url, 0, strlen($coll)) === $coll && $collection[1] == "POST") {
                $args = array_splice($uri, count($collection[0]));

                if(is_callable($collection[2])) {
                    $this->assertTrue(call_user_func_array($collection[2], $args));
                }

                if(is_string($collection[2])) {
                    $this->distinctClass($collection[2], $args);
                }

                if(is_array($collection[2]) && isset($collection[2]["controller"])) {
                    $this->callClassMethod($collection[2], $args);
                }

            }

            if($collection[1] != "POST") {
                $this->assertTrue($this->headerMessage->sendHeader405());
            }

        }

          $this->assertTrue($this->headerMessage->sendHeader404());

    }

    private function callClassMethod(array $classFromCollection, array $args) {
      if(class_exists($classFromCollection["controller"])) {
          $controller = new $classFromCollection["controller"];
          $method =  $classFromCollection["method"];

          $this->assertTrue(call_user_func_array(array($controller,$method), $args));
      }
    }

    private function distinctClass($classFromCollection, array $args) {
        $explode = explode("@", $classFromCollection);
        $controller = new $explode[0];
        $method = $explode[1];

        call_user_func_array(array($controller,$method), $args);

        $this->assertTrue(call_user_func_array(array($controller,$method), $args));
    }


}
