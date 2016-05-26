<?php

namespace Phase\Router;

class Route extends RouteCollection implements RouteInterface {      
    protected $prefix;
    protected $middleware;
    /**
     * Construct new object for creating routes
     * @return void
     */
    public function __construct() {
        $this->prefix = NULL;
        $this->middleware = NULL;
    }
    /**
     * Creating route available on GET method
     * and passing callback function that will be called
     * when router match requested URI
     * @param string $route
     * @param function $callback
     */
    public function get($route,$callback) {
        if(is_null($this->prefix)) {
            $route = explode("/", $route);
        } else {
            $route = explode("/", $this->prefix ."/". $route);
        }
        
        $this->collect([$route,"GET",$callback,$this->middleware]);
    }
    /**
     * Creating route available on POST method
     * and passing callback function that will be called
     * when router match requested URI
     * @param string $route
     * @param function $callback
     */
    public function post($route,$callback) {
        if(is_null($this->prefix)) {
            $route = explode("/", $route);
        } else {
            $route = explode("/", $this->prefix ."/". $route);
        }
        
        $this->collect([$route,"POST",$callback,$this->middleware]);
    }
    
    public function controller($route, $controller) {
        if(!class_exists($controller)) {
            return false;
        }
        
        $methods = get_class_methods($controller);
        
        foreach($methods as $method) {                
            if(strpos($method, "get") !== false) {
                $mth = "GET";
            } else {
                $mth = "POST";             
            }
            
            $num = strlen($mth);
            
            $rt = strtolower(substr($method, $num));
            if(is_null($this->prefix)) {
                $r = $route ."/". $rt;
            } else {
                $r = $this->prefix ."/". $route ."/". $rt;
            }
            
            $completedRoute = explode("/", $r);
            
            var_dump($completedRoute);
            
           $this->collect([$completedRoute, $mth,["controller" => $controller, "method" => $method],$this->middleware]);
        }
    }
    /**
     * Passing settings to set Route settings
     * @param array $settings
     */
    public function group(Array $settings, callable $callback) {
        foreach($settings as $key => $setting) {
            $this->$key = $setting;
        }
        
        $callback();
        $this->prefix = NULL;
    }
    
}
