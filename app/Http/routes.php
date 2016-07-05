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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

	$api->get('users', function() {
		return "hey";
	});	

    $api->post('oauth/access_token', function() {
        return Authorizer::issueAccessToken();
    });

    $api->post('oauth/refresh_token', function() {
        return Authorizer::issueAccessToken();
    });  

	$api->post('user/register', 'App\Http\Controllers\UserController@register');

    $api->post('user/login', 'App\Http\Controllers\UserController@login');  

});



$api->version('v1', ['middleware' => 'oauth'], function ($api) {

    $api->get('user/validate', 'App\Http\Controllers\UserController@validateUser');

    $api->get('user/otp/{code}', 'App\Http\Controllers\UserController@userOtp');

    $api->get('user/user_screen', 'App\Http\Controllers\UserController@userScreen');


});
