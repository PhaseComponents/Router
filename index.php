<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("vendor/autoload.php");

$route = new Phase\Router\Route();

$route->get("user/profile", function($id) {
  echo "TEBRA? {$id}";
});

$route->get("admin/dashboard", function() {
    echo "Welcome to admin dashboard";
});

// $route->get("home/test", "Phase\Controller\TestController@getIndex");
//
//
// $route->group(["prefix" => "admin"], function() use ($route) {
//
//
//     $route->controller("store", 'Phase\Controller\TestController');
// });

$router = new Phase\Router\Router($route);
$router->run();


?>
