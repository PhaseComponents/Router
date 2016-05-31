<?php

namespace Phase\Router;

use Phase\Router\Http\HeaderMessage;
use Phase\Router\Http\Request;
use Phase\Router\Http\RequestMethods as Method;

class Router extends Request implements RouterInterface {
    protected $routes;
    protected $prefix;
    /**
    * Phase\Router\Request\HeaderMessage instance
    */
    protected $headerMessage;
    /**
     * Construct Router
     * @return void
     */
    public function __construct(RouteCollection $routes) {
        $this->routes = $routes;
        $this->headerMessage = new HeaderMessage();
    }
    /**
     * Dispatcher
     * @return void
     */
    final public function run() {
        $uri = $this->parseURI();
        $url = implode("/", $uri);

        foreach($this->routes->getCollection() as $route => $collection) {
                $coll = implode("/", $collection[0]);
                $args = array_splice($uri, count($collection[0]));
                $match = addcslashes($coll,'/');

                if(preg_match("/($match)/i", $url)) {
                  // construct middleware
                    if(class_exists(end($collection))) {
                      $middleware = new $collection[3];
                      // middleware handle didnt passed,
                      // dont pass request further to router
                      if( ! $middleware->handle()) {
                          return false;
                      }

                      if($collection[1] != $this->getRequestMethod()) {
                          $this->headerMessage->sendHeader405();
                          return true;
                      }

                    if(count($args) === 0) {
                        if($this->getRequestMethod() === Method::GET)
                            $args = $_GET;

                        if($this->getRequestMethod() === Method::POST)
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

        $this->headerMessage->sendHeader404();

    }
    /**
    * Assigning class method to route this method does disctinction
    * which class and method to pick
    *
    * @param String $classFromCollection
    * @param Array $args
    * @return void
    */
    private function distinctClass($classFromCollection, array $args) {
        $explode = explode("@", $classFromCollection);
        $controller = new $explode[0];
        $method = $explode[1];

        call_user_func_array(array($controller,$method), $args);
    }
    /**
    * Method calls method of passed class
    *
    * @param String $classFromCollection
    * @param Array $args
    * @return void
    */
    private function callClassMethod(array $classFromCollection, array $args) {
      if(class_exists($classFromCollection["controller"])) {
          $controller = new $classFromCollection["controller"];
          $method = $classFromCollection["method"];

          call_user_func_array(array($controller,$method), $args);
      }
    }

    /**
     * Returns Route object, that holds all routes for this routes
     * @return Phase\Router\Route
     */
    public function getRoutes() {
        return $this->routes;
    }
    /**
     * Gets route path excluding project directory
     * @return array
     */
    public function parseURI() {
        $uri = explode("/", $this->getRequestURI());
        $rootDIR = array_search($this->getProjectDIR(), $uri);

        return array_splice($uri, ($rootDIR+1));
    }
}
