<?php

namespace Phase\Router;

interface RouteInterface {
    public function get($route, $callback);
    public function post($route, $callback);
    public function controller($route, $controller);
    public function group(Array $settings);
}

