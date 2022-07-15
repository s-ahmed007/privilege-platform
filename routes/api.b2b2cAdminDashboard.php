<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('admin_login', 'b2b2c\ApiController@authenticate');

Route::group(['middleware' => ['auth:b2b2c_admin_api']], function () {
    Route::get('dashboard', 'b2b2c\ApiController@getAuthenticatedUser');
    Route::get('customers', 'b2b2c\ApiController@allCustomers');
    Route::post('create-customer', 'b2b2c\ApiController@createCustomer');
    Route::post('update-customer', 'b2b2c\ApiController@updateCustomer');
    Route::get('card-delivery-list', 'b2b2c\ApiController@cardDeliveryList');
    Route::post('user-transactions', 'b2b2c\ApiController@transactions');
    Route::post('create-post', 'b2b2c\ApiController@createPost');
    Route::get('posts', 'b2b2c\ApiController@posts');
    Route::post('update-post', 'b2b2c\ApiController@updatePost');
    Route::post('delete-post', 'b2b2c\ApiController@deletePost');
});
