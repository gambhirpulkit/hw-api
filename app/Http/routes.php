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


Route::get('/home', function () {

 
    return view('welcome');
});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $api->post('oauth/access_token', function() {
        return Authorizer::issueAccessToken();
    });

    $api->post('oauth/refresh_token', function() {
        return Authorizer::issueAccessToken();
    });  

	$api->post('user/register', 'App\Http\Controllers\UserController@register');

    $api->post('user/login', 'App\Http\Controllers\UserController@login');  

    $api->get('user/forgot_pwd/{phone}', 'App\Http\Controllers\UserController@forgotPassword');

    $api->post('user/verify_pwd', 'App\Http\Controllers\UserController@verifyPassword');

    // Trainer API's
    $api->post('trainer/login', 'App\Http\Controllers\TrainerController@login');  

    $api->get('trainer/forgot_pwd/{phone}', 'App\Http\Controllers\TrainerController@forgotPwd');

    $api->post('trainer/verify_pwd', 'App\Http\Controllers\TrainerController@verifyPwd');

    // $api->post('file_upload', 'App\Http\Controllers\ChatController@fileUpload');

});


$api->version('v1', ['middleware' => 'oauth'], function ($api) {

    // Patient API's
    $api->get('user/validate', 'App\Http\Controllers\UserController@validateUser');

    $api->get('user/otp/{code}', 'App\Http\Controllers\UserController@verifyOtp');

    $api->get('user/user_screen', 'App\Http\Controllers\UserController@userScreen');

    $api->get('user/resend_otp', 'App\Http\Controllers\UserController@resendOtp');

    $api->get('user/change_pwd', 'App\Http\Controllers\UserController@changePwd');

    $api->get('user/new_pwd', 'App\Http\Controllers\UserController@newPwd');

    $api->get('user/resend_otp', 'App\Http\Controllers\UserController@resendOtp');

    $api->post('user/change_email', 'App\Http\Controllers\UserController@changeEmail');

    $api->post('user/change_phone', 'App\Http\Controllers\UserController@changePhone');

    $api->get('user/verify_code/{code}/{new_phone}', 'App\Http\Controllers\UserController@verifyCode');
    
    $api->post('user/reg_id', 'App\Http\Controllers\UserController@updateRegId');


    // Trainer API's
    $api->get('trainer/change_pwd', 'App\Http\Controllers\TrainerController@changePwd');

    $api->get('trainer/new_pwd', 'App\Http\Controllers\TrainerController@newPwd'); 

    $api->post('trainer/reg_id', 'App\Http\Controllers\TrainerController@updateRegId');

    $api->get('trainer/list_users', 'App\Http\Controllers\TrainerController@listUsers');   


    // Common API's for trainer and patient
    $api->post('post_message', 'App\Http\Controllers\ChatController@postMessage');

    $api->post('file_upload', 'App\Http\Controllers\ChatController@fileUpload');

    $api->get('push_message', 'App\Http\Controllers\ChatController@pushMessage');




});