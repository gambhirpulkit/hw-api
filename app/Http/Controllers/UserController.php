<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input as Input;


use App\Http\Requests;

// use App\Helpers\Helper;


class UserController extends Controller
{

    public function register() {

    	// return "yes";

		if (\App\User::where('email', '=', Input::get('email'))->exists() || \App\User::where('phone', '=', Input::get('phone'))->exists()) {

			return response()->json(['error' => 'Already exists', 'status_code' => 406]);

		}

		else {
			
			$password = Input::get("password");

			$insertData = array(
				"name"	=>	Input::get("name"),
				"password"	=>	\Hash::make($password),
				"email"	=>	Input::get("email"),
				"phone" =>	Input::get("phone"),
			);
			// print_r($insertData); exit;		

			$save = \App\User::create($insertData);

			$rand = mt_rand(100000,999999); 

			$insertCode = array(
				"user_id" => $save->id,
				"codes" => $rand,
				"expires" => time() + 1800
			);	


			$saveCode = \App\OtpCode::create($insertCode);

		    $otp_prefix = ':';

		    //Your message to send, Add URL encoding here.
		    $msg = urlencode($rand.$otp_prefix." is your OTP. Welcome to HappyWise!");

			\Helper::generateOtp((string)Input::get("phone"), (string)$rand, (string)$msg);

			// return "saved";
			if($save) {

				$responseArray = [
					'message' => 'User created',
					'status_code' => 200
				];
				return response()->json($responseArray);
			}
			else {
				$responseArray = [
					'message' => 'Error creating account',
					'status_code' => 401
				];
				return response()->json($responseArray);
			}	

		} 	

		return false;
    }


    public function login() {

	        $username = Input::get('username');
	        $password = Input::get('password');

		if (\App\User::where('email', '=', $username)->exists() || \App\User::where('phone', '=', $username)->exists()) {

	       // \Helper::generateOtp();
	        // $gmail = Input::get('gmail');

	        if (\Auth::attempt(array('email' => $username, 'password' => $password)) || \Auth::attempt(array('phone' => $username, 'password' => $password))){

				$responseArray = [
					'message' => 'User authenticated',
					'data' => \Auth::user(),
					'status_code' => 200
				];
				return response()->json($responseArray);

	        }
	        else {        
				$responseArray = [
					'message' => 'Wrong credetials',
					'status_code' => 500
				];
				return response()->json($responseArray);
	        }			

		}
		else {

				$responseArray = [
					'message' => 'User not registered',
					'status_code' => 404
				];
				return response()->json($responseArray);

		}

    }

    public function verifyOtp($code) {

    	$user_id = \Authorizer::getResourceOwnerId();

    	// $code = Input::get('code');
    	$user_codes = \App\OtpCode::where('user_id', '=', $user_id)->first();
    	// echo $user_codes->expires - time(); exit;

    	if($code == $user_codes->codes) {

	    	if($user_codes->expires > time()) {

				$user = \App\User::find($user_id);

				$user->phone_flag = 1;
				$user->ques_flag = 1; // To be deleted
				$user->active = 1;

				$user->save();

				$responseArray = [
					'message' => 'Phone verified',
					'status_code' => 200
				];
				
				return response()->json($responseArray);	

	    	}
	    	else{

				$responseArray = [
					'message' => 'Code expires. Regenerate otp.',
					'status_code' => 400
				];
				
				return response()->json($responseArray);	
	    	}


    	}
    	else {

			$responseArray = [
				'message' => 'OTP Invalid',
				'status_code' => 400
			];
			
			return response()->json($responseArray);
    	}
    	

    	
	}

    public function userScreen() {

    	$user_id = \Authorizer::getResourceOwnerId();  

    	$user = \App\User::find($user_id);

    	$screen = "";

    	if($user->phone_flag == 0){
    		$screen = "otp";
    	}
    	else if(($user->ques_flag == 0) && ($user->phone_flag == 1)) {
    		$screen = "ques";
    	}
    	else if(($user->ques_flag == 1) && ($user->phone_flag == 1)) {
    		$screen = "home";
    	}
    	else {
    		return false;
    	}

		$responseArray = [
			'screen' => $screen,
			'status_code' => 200
		];
			
		return response()->json($responseArray);	

    }	

    public function forgotPassword($phone) {

    	$user = \App\User::where('phone', '=', $phone)->first();

    	if($user) {

	    	$user_codes = \App\OtpCode::where('user_id', '=', $user->id)->first();

	    	if($user_codes){
	    		if($user_codes->expires > time()) {
	    			$code = $user_codes->codes;

	    			$msg = urlencode($code." is your code to reset password for HappyWise. This code will last for 30min. Please do not share.");

					\Helper::generateOtp((string)$phone, (string)$code, (string)$msg);	    			

					$responseArray = [
						'message' => 'Verification code generated',
						'status_code' => 200
					];
						
					return response()->json($responseArray);	
	    		}
	    		else {

					$rand = mt_rand(100000,999999); 

					$msg = urlencode($rand." is your code to reset password for HappyWise. This code will last for 30min. Please do not share.");

					\Helper::generateOtp((string)$phone, (string)$rand, (string)$msg);				

					$user_codes->codes = $rand;


					$user_codes->save();

					$responseArray = [
						'message' => 'Verification code generated',
						'status_code' => 200
					];
						
					return response()->json($responseArray);						

				}   		

	    	}
	    	else {
					$rand = mt_rand(100000,999999); 

					$msg = urlencode($rand." is your code to reset password for HappyWise. This code will last for 30min. Please do not share.");

					\Helper::generateOtp((string)$phone, (string)$rand, (string)$msg);				

					$insertCode = array(
						"user_id" => $user->id,
						"codes" => $rand,
						"expires" => time() + 1800
					);	


					$saveCode = \App\OtpCode::create($insertCode);

					$responseArray = [
						'message' => 'Verification code generated',
						'status_code' => 200
					];
						
					return response()->json($responseArray);		    		
	    	}

    	}
    	else {
			$responseArray = [
				'message' => 'User does not exist',
				'status_code' => 404
			];
				
			return response()->json($responseArray);	    		
    	}

    }

    public function verifyPassword() {

		
		$password = Input::get("password");
		$code = Input::get('code');
		$phone = Input::get('phone');

    	$user = \App\User::where('phone', '=', $phone)->first();

    	$user_codes = \App\OtpCode::where('user_id', '=', $user->id)->first();
    	// echo $user_codes->expires - time(); exit;

    	if($code == $user_codes->codes) {

	    	if($user_codes->expires > time()) {

	    		$user->password = \Hash::make($password);

	    		$user->save();

				$responseArray = [
					'message' => 'Phone verified and password changed',
					'status_code' => 200
				];
				
				return response()->json($responseArray);	

	    	}
	    	else{

				$responseArray = [
					'message' => 'Code expires. Regenerate otp.',
					'status_code' => 403
				];
				
				return response()->json($responseArray);	
	    	}


    	}
    	else {

			$responseArray = [
				'message' => 'Invalid code',
				'status_code' => 400
			];
			
			return response()->json($responseArray);
    	}
    	

    	
	}




}
