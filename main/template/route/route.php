<?php

/* route without controller*/
$route->addroute('post','/','',function(){
    echo "hello";
});
$route->addroute('post','/direct','',function(){
    echo "hello direct";
});
/* 
route with controller and function
controller : UserController
function : testFun
*/
$route->addroute('get','/test','UserController','testFun');

/* 
route prefix
*/
$route->routePrefix("/demo",function($route){
    /* /demo/d1 */
    $route->addroute('get','/d1','DemoController','d1Fun');
    /* /demo/d2 */
    $route->addroute('get','/d2','DemoController','d2Fun');

    //nested route prefix
    $route->routePrefix("/dx",function($route){
         /* /demo/dx/dx1 */
        $route->addroute('get','/dx1','DemoController','dx1Fun');
    });
    $route->routePrefix("/dy",function($route){
        /* /demo/dy/dy1 */
        $route->addroute('get','/dy1','DemoController','dy1Fun');
    });
});
$route->addroute('get','hello','DemoController','d1Fun');
?>