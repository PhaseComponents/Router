<?php

namespace Phase\Router;

abstract class RouteCollection {
    /**
     * Collection holds all available routes
     * @var array
     */
    protected $collection = [];

    /**
     * Push route to collection of routes
     * @param \Phase\Router\RouteInterface $route
     */
    public function collect(Array $route) {
        array_push($this->collection, $route);
    }
    /**
     * Returns collection of routes
     * @return array
     */
    public function getCollection() {
        return $this->collection;
    }
}
