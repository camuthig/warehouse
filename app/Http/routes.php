<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$app->get('/', function() use ($app) {
    return $app->welcome();
});

/*
Warehouse routes
 */
$app->group(['prefix' => 'api/warehouses'], function () use ($app){
    $app->get('/', ['as' => 'warehouses', 'uses' => 'App\Http\Controllers\WarehouseController@index']);
    $app->post('/', ['as' => 'warehouses_create', 'uses' => 'App\Http\Controllers\WarehouseController@create']);
});

/*
Products routes
 */
$app->group(['prefix' => 'api/products'], function () use ($app){
    $app->get('/', ['as' => 'products', 'uses' => 'App\Http\Controllers\ProductController@index']);
    $app->post('/', ['as' => 'products_create', 'uses' => 'App\Http\Controllers\ProductController@create']);
});

/*
Products routes
 */
$app->group(['prefix' => 'api/stock'], function () use ($app){
    $app->get('/', ['as' => 'stock', 'uses' => 'App\Http\Controllers\StockController@index']);
    $app->post('/', ['as' => 'stock_create', 'uses' => 'App\Http\Controllers\StockController@create']);
});