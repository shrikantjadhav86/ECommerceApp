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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/v1/update/cart/{updateData}', function ($updateData) {
    return view('edit-cart');
});

/*Route::get('/api/v1/remove/cart/{line}', function ($line) {
    return view('remove-cart');
});*/

Route::get('/api/v1/clear/cart', function () {
    return view('clear-cart');
});


Route::get('/api/v1/remove/cart/{line}', 'ProductController@cartRemoveProducts');
Route::get('/api/v1/cart', 'ProductController@cartProducts');
Route::get('/api/v1/products', 'ProductController@getAllProducts');
Route::get('/api/v1/product/{id}', 'ProductController@showDetails');
Route::get('/api/v1/users', 'ProductController@getAllCustomers');
Route::get('/api/v1/collections', 'ProductController@getAllCollections');
Route::get('/api/v1/collections/{collection_id}', 'ProductController@getCollectionProducts');
Route::get('/api/v1/checkout', 'ProductController@getCheckout');
Route::get('/api/v1/product/shipped/{order_id}', 'ProductController@getAllShippedProducts');
Route::get('/api/v1/order', 'ProductController@getOrder');
Route::post('/api/v1/createOrder', 'ProductController@createOrder');
Route::get('/api/v1/add/cart/{id}', 'ProductController@addToCartProducts');
Route::get('/api/v1/order/{order_id}', 'ProductController@getSingleOrders');
Route::post('/api/v1/cardPayment', 'ProductController@cardPay');
Route::post('/api/v1/paypalPayment', 'ProductController@paypalPayment');
Route::post('/api/v1/address_validate', 'ProductController@address_validate');
Route::post('/api/v1/register', 'ProductController@registration');
