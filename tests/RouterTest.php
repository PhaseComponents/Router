<?php

use Phase\Router\Http\HeaderMessage;
use Phase\Router\Http\RequestMethods as Method;

class RouterTest extends PHPUnit_Framework_TestCase {
    private $routeCollection = [];

    private $headerMessage;

    public function __construct() {
        // on test construct put some default route to collection
        array_push($this->routeCollection, [["route","default"],"GET",function() {return true;},MiddlewareTest::class]);
        array_push($this->routeCollection, [["route","default"],"GET","RouterTest@controllerMethodTest",MiddlewareTest::class]);
        array_push($this->routeCollection, [["route","default"],"GET",["controller" => "RouterTest", "method" => "controllerMethodTest"],MiddlewareTest::class]);

        array_push($this->routeCollection, [["route","default"],"POST",function() {return true;},MiddlewareTest::class]);
        array_push($this->routeCollection, [["route","default"],"POST","RouterTest@controllerMethodTest",MiddlewareTest::class]);
        array_push($this->routeCollection, [["route","default"],"POST",["controller" => "RouterTest", "method" => "controllerMethodTest"],MiddlewareTest::class]);

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
            $args = array_splice($uri, count($collection[0]));
            $match = addcslashes($coll,'/');

            if(preg_match("/($match)/i", $url)) {
              // construct middleware
                if(class_exists(end($collection))) {
                  $middleware = new $collection[3];
                  // middleware handle didnt passed,
                  // dont pass request further to router
                  if( ! $middleware->test_handle()) {
                      return false;
                  }

                  if($collection[1] != "GET") {
                      $this->assertTrue($this->headerMessage->sendHeader405());
                      return true;
                  }

                if(count($args) === 0) {
                    if("GET" === Method::GET)
                        $args = $_GET;

                    if("GET" === Method::POST)
                        $args = $_POST;

                }

                if(is_callable($collection[2])) {
                    call_user_func_array($collection[2], $args);
                    return true;
                }

                if(is_string($collection[2])){
                    $this->distinctClass($collection[2], $args);
                    return true;
                }

                if(is_array($collection[2]) && isset($collection[2]["controller"])) {
                    $this->callClassMethod($collection[2], $args);
                    return true;
                }
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
          $args = array_splice($uri, count($collection[0]));

          if(preg_match("@($coll)?([^\s/?\.#-]+\.?)+(/[^\s]*)?$@iS", $url)
            && ! count(array_diff($collection[0], $uri))
          ) {
            // construct middleware
              if(class_exists(end($collection))) {
                $middleware = new $collection[3];
                // middleware handle didnt passed,
                // dont pass request further to router
                if( ! $middleware->test_handle()) {
                    return false;
                }

                if($collection[1] != "POST") {
                    $this->assertTrue($this->headerMessage->sendHeader405());
                    return true;
                }

              if(count($args) === 0) {
                  if("POST" === Method::GET)
                      $args = $_GET;

                  if("POST" === Method::POST)
                      $args = $_POST;

              }

              if(is_callable($collection[2])) {
                  call_user_func_array($collection[2], $args);
                  return true;
              }

              if(is_string($collection[2])){
                  $this->distinctClass($collection[2], $args);
                  return true;
              }

              if(is_array($collection[2]) && isset($collection[2]["controller"])) {
                  $this->callClassMethod($collection[2], $args);
                  return true;
              }
          }
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
