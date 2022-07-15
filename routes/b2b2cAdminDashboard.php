<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('adminDashboard', function () {
    return view('b2b2c.login');
});
Route::post('/b2b2c-admin-login', 'b2b2c\b2b2cAdminController@login');

Route::middleware(['B2b2cAdminLoginCheck'])->group(function () {
    Route::get('/dashboard', 'b2b2c\b2b2cAdminController@dashboard');
    Route::get('/customers', 'b2b2c\b2b2cAdminController@allCustomers');
    Route::get('/add-customer', 'b2b2c\b2b2cAdminController@addCustomer');
    Route::post('/store-customer', 'b2b2c\b2b2cAdminController@storeCustomer');
    Route::get('/edit-customer/{id}', 'b2b2c\b2b2cAdminController@editCustomer');
    Route::post('/update-customer/{id}', 'b2b2c\b2b2cAdminController@updateCustomer');
    Route::get('/card-delivery', 'b2b2c\b2b2cAdminController@cardDeliveryList');
    Route::post('/b2b2c_delivery_status', 'adminController@change_delivery_status');
    Route::post('/b2b2c_shipping_address', 'adminController@update_shipping_address');
    Route::get('/all-transactions', 'b2b2c\b2b2cAdminController@allTransactions');
    Route::resource('all-post', 'b2b2c\b2b2cPostController');
    Route::get('/adminLogout', 'b2b2c\b2b2cAdminController@logout');
});
