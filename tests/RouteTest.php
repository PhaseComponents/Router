<?php

class RouteTest extends PHPUnit_Framework_TestCase {
    private $defaultRoute = "default";
    private $prefix = "route";
    
    public function test_controller() {
//         if(!class_exists($controller)) {
//            return false;
//        }
        
        $methods = ["getTest"];
        
        foreach($methods as $method) {                
            if(strpos($method, "get") !== false) {
                $mth = "GET";
            } else {
                $mth = "POST"; 
            }
            
            $num = strlen($mth);
            
            $rt = strtolower(substr($method, $num));
            if(is_null($this->prefix)) {
                $r = $this->defaultRoute ."/". $rt;
            } else {
                $r = $this->prefix ."/". $this->defaultRoute ."/". $rt;
            }
            
            $completedRoute = explode("/", $r);
                   
            $diff = count(array_diff($completedRoute, ["route","default","test"]));
            if($diff === 0) {
                $this->assertTrue(true);
            } else {
                $this->assertTrue(false);
            }
            
           
        }
    }
    
    
    
    
}

