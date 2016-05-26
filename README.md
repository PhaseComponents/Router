# Router
Phase router component

[![Build Status](https://travis-ci.org/PhaseComponents/Router.svg?branch=master)](https://travis-ci.org/PhaseComponents/Router)

Phase\Router component is wraped for Rapid Application development (RAD). 

#Getting started

`composer require phase/router`


#How to use

Firstly we need to initilize our object that will handle route creation.

`$route = new Phase\Router\Route();`

Create simple route as easy as:

`$route->get("home", function() {});`

in this situation function is callback and will be called when route is reached. Everything that router find in requested URI after wanted route, will be regarded as parameter ex:

Requested uri is `/home/page`

Router will look for `/home` route, it will find it, and `page` will be regarded as parameter, which can be picked up like this `$route->get("home", function( $page ) {});`

*Note: `$page` parameter is for example purpose only, it can be any variable you want.*

## Defining controller

Defining controller with `phase/router` looks like this `$route->controller("test", "TestController")`.
Lets assume that our `TestController` looks like in the example under:


    use Phase\Router\Controller as BaseController;

    class TestController extends BaseController {

        public function getIndex() {};
    
        public function postSave() {};

    }  

Since we defined our controller,reaching route `/test/index` will run `getIndex` method in our controller we defined, on the other side,reaching `/test/save` will give us `405 Method Not Allowed` because we are trying to reach route through `GET` method, and its clearly stated in class that it can be reached through `POST` method.

*Note: Reaching any route that route isn't defined before, will throw header with`404 Not Found`.*

#Grouping

Router also provides possibility of grouping routes and controllers that will have same prefix.


    $route->group(["prefix" => "admin"], function() use ($route) {
    
        $route->get("home", function() {});
    
        $route->controller("test", 'TestController');
    });
    
All provided routes in group will be reachable prefixing them with provided `prefix` option.


