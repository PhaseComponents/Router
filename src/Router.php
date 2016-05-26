<?php

namespace Phase\Router;

class Router extends Request implements RouterInterface {
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
     * Dispatcher
     * @return void
     */
    final public function run() {
        $uri = $this->parseURI();
        $url = implode("/", $uri);
        foreach($this->routes->getCollection() as $collection) {
            $coll = implode("/", $collection[0]);

            if(substr($url, 0, strlen($coll)) === $coll) {
                if($this->getRequestMethod() == $collection[1]) {

                    $args = array_splice($uri, count($collection[0]));
                    if(is_callable($collection[2])) {
                        call_user_func_array($collection[2], $args);

                        return 1;

                    } else if(is_string($collection[2])){
                        $explode = explode("@", $collection[2]);
                        $controller = new $explode[0];
                        $method = $explode[1];

                        call_user_func_array(array($controller,$method), $args);

                        return 1;

                    } else {
                        if(class_exists($collection[2]["controller"])) {
                            $controller = new $collection[2]["controller"];
                            $method = $collection[2]["method"];

                            call_user_func_array(array($controller,$method), $args);
                        }

                        return 1;

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
