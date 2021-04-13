<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login','AuthController@login');
Route::post('register','AuthController@register');
Route::post('admin-login', 'AuthController@admin_login');
Route::group(['middleware' => "auth:api"], function(){
	Route::delete('logout','AuthController@logout');
});
//Project
Route::prefix('project')->group(function (){
	Route::post('create', 'ProjectController@create');
	Route::delete('remove/{id}', 'ProjectController@remove');
	Route::put('update', 'ProjectController@update');
	Route::get('/{page}','ProjectController@index');

	//Project Warning
	Route::prefix('warning')->group(function (){
		Route::post('create', 'ProjectController@addWarning');
		Route::delete("remove/{id}", 'ProjectController@removeWarning');
	});
});
// Customer
Route::prefix('customer')->group(function() {
	Route::get('/{page}', "CustomerController@index");
	Route::post('create', 'CustomerController@createCustomer');
	Route::delete('remove/{customer_id}', 'CustomerController@delete');
	Route::put('update', 'CustomerController@updateCustomer');


	//Customer Payment
	Route::prefix('payment')->group(function() {
		Route::post('create','CustomerController@createPayment');
		Route::delete('remove/{customer_id}', 'CustomerController@deletePayment');
		Route::put('update', 'CustomerController@updatePayment');
	});
});

//User
Route::prefix('user')->group(function (){
	Route::get('/{page}','UserController@index');
	Route::get('details/{id}', 'UserController@getUser');
	Route::delete("delete/{id}","UserController@remove");
	Route::group(['middleware' => "auth:api"], function (){
		Route::put('update-time/{isIn}','UserController@timeUpdate');
		Route::put('update', "UserController@update");
		Route::put("change-password", "UserController@changePassword");
		Route::put('notification/{isOn}', 'UserController@notificationControl');
	});
});

Route::prefix('messaging')
->group(function () {
	Route::group(['middleware' => 'auth:api'], function (){
		Route::get('/', 'MessageController@index');
		Route::post('send', "MessageController@send");
		Route::put('seen/{chat_id}', "MessageController@seeMessage");
	});
});
