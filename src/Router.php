<?php

namespace Phase\Router;

class Router implements RouterInterface {
    protected $routes;
    protected $prefix;
    /**
     * Construct Router with RouteCollections
     * @return void
     */
    public function __construct(RouteCollection $routes) {
        $this->routes = $routes;
    }
    
    /**
     * Run router
     * @return void
     */
    final public function run() {
        $uri = $this->parseURI();
        $url = implode("/", $uri);
        foreach($this->routes->getCollection() as $collection) {
            $coll = implode("/", $collection[0]);
            if($url === $coll) {
                if($this->getRequestMethod() == $collection[1]) {
                    $args = array_splice($uri, count($collection[0])); 
                    
                    if(gettype($collection[2]) == "object") {
                        call_user_func_array($collection[2], $args);
                    } else if(gettype($collection[2]) == "string"){
                        $explode = explode("@", $collection[2]);
                        $controller = new $explode[0];
                        $method = $explode[1];
                        
                        call_user_func_array(array($controller,$method), $args);
                    } else {
                        $controller = new $collection[2]["controller"];
                        $method = $collection[2]["method"];
                        
                        call_user_func_array(array($controller,$method), $args);
                    }
                } else {
                    if (!headers_sent()) {
                        header('HTTP/1.1 405 Method Not Allowed');
                        exit;
                    }
                }
            }
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
     * Returns $_SERVER array
     * @return array
     */
    public function getRequest() {
        return $_SERVER;
    }
    
    /**
     * Get request time integer
     * @return int
     */
    public function getRequestTime() {
        $request = $this->getRequest();
        return $request["REQUEST_TIME"];
    }
    
    /**
     * Get request method 
     * @return string
     */
    public function getRequestMethod() {
        return $this->getRequest()["REQUEST_METHOD"];
    }
    /**
     * Get request URI
     * @return string
     */
    public function getRequestURI() {
        return $this->getRequest()["REQUEST_URI"];
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
   
    /**
     * Document root of requested path
     * @return string
     */
    public function getRequestDocumentRoot() {
        return $this->getRequest()["DOCUMENT_ROOT"];
    }
    
    /**
     * Get directory of project
     * @return string
     */
    public function getProjectDIR() {
        $documentRoot = explode("/", $this->getRequestDocumentRoot());
        return $documentRoot[count($documentRoot) - 1];
    }
}
