<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/ihelpers'], function (Router $router) {

    $router->get('clearcache', [
        'as' => 'admin.ihelpers.clearcache',
        'uses' => 'IhelpersController@clearcache',
        'middleware' => 'can:page.pages.edit',
    ]);


});
