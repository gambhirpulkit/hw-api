<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input as Input;

use App\Http\Requests;


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
				"pushy_id" =>	Input::get("pushy_id")
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
	        $pushy_id = Input::get('pushy_id');

		if (\App\User::where('email', '=', $username)->exists() || \App\User::where('phone', '=', $username)->exists()) {

	        if (\Auth::attempt(array('email' => $username, 'password' => $password)) || \Auth::attempt(array('phone' => $username, 'password' => $password))){

	        	$user = \App\User::where('email', '=', $username)->first();

	        	$user->pushy_id = $pushy_id;
	        	$user->save();

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


    public function resendOtp() {

    	$user_id = \Authorizer::getResourceOwnerId();

    	// $code = Input::get('code');
    	$user_codes = \App\OtpCode::where('user_id', '=', $user_id)->first();

		$user = \App\User::find($user_id);
		$phone = $user->phone;

		if($user_codes) {

		    if($user_codes->expires > time()) {

			    $otp_prefix = ':';
			    $code = $user_codes->codes;

			    //Your message to send, Add URL encoding here.
			    $msg = urlencode($code.$otp_prefix." is your OTP. Welcome to HappyWise!");

				\Helper::generateOtp((string)$phone, (string)$code, (string)$msg);	    		

				$responseArray = [
					'message' => 'Otp Resent',
					'status_code' => 200
				];
					
				return response()->json($responseArray);	

		    }

		    else{

		    	// echo $user_codes->codes; exit;
		    	$rand = mt_rand(100000,999999);

				$user_codes->codes = $rand;
				$user_codes->expires = time() + 1800;

				$user_codes->save();

			    $otp_prefix = ':';

			    //Your message to send, Add URL encoding here.
			    $msg = urlencode($rand.$otp_prefix." is your OTP. Welcome to HappyWise!");

				\Helper::generateOtp((string)$phone, (string)$rand, (string)$msg);

				$responseArray = [
					'message' => 'Otp resent.',
					'status_code' => 200
				];
					
				return response()->json($responseArray);	
		    }

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

	public function changeEmail(){
		$new_email = Input::get("new_email");

		$user_id = \Authorizer::getResourceOwnerId();

		// echo $user_id; exit;
		$user = \App\User::find($user_id);

		$check_user = \App\User::where('email', '=', $new_email)->exists();

		// echo $check_user; exit;

		if($check_user && $new_email != $user->email) {
			$responseArray = [
				'message' => 'Email already exist',
				'status_code' => 400
			];
			
			return response()->json($responseArray);			
		}
		else {

			$user->email = $new_email;
			$user->save();

			$responseArray = [
				'message' => 'Email changed',
				'status_code' => 200
			];
			
			return response()->json($responseArray);					
		}

	}

	public function changePhone() {

		$new_phone = Input::get("new_phone");

		$user_id = \Authorizer::getResourceOwnerId();

		$user = \App\User::find($user_id);

		$check_user = \App\User::where('phone', '=', $new_phone)->exists();

		if($check_user) {
			$responseArray = [
				'message' => 'Phone no. already exist',
				'status_code' => 400
			];
			
			return response()->json($responseArray);	
		}
		else {
			


			$user_codes = \App\OtpCode::where('user_id', '=', $user_id)->first();

			if($user_codes) {
				if($user_codes->expires > time()) {

					$msg = urlencode($user_codes->codes." is your code to change mobile number for HappyWise. This code will last for 30min. Please do not share.");							
					\Helper::generateOtp((string)$new_phone, (string)$user_codes->codes, (string)$msg);

			

					$responseArray = [
						'message' => 'Verification code generated',
						'status_code' => 200
					];
					
					return response()->json($responseArray);					
				}
				else {
					$rand = mt_rand(100000,999999); 
					$user_codes->codes = $rand;
					$user_codes->expires = time() + 1800;
					$msg = urlencode($rand." is your code to change mobile number for HappyWise. This code will last for 30min. Please do not share.");					

					$user_codes->save();

					\Helper::generateOtp((string)$new_phone, (string)$rand, (string)$msg);

					$responseArray = [
						'message' => 'Verification code generated',
						'status_code' => 200
					];
					
					return response()->json($responseArray);
				}
			}
			else {
					$rand = mt_rand(100000,999999); 				

					$insertCode = array(
						"user_id" => $user_id,
						"codes" => $rand,
						"expires" => time() + 1800
					);	
					$msg = urlencode($rand." is your code to change mobile number for HappyWise. This code will last for 30min. Please do not share.");	

					\Helper::generateOtp((string)$new_phone, (string)$rand, (string)$msg);

					$saveCode = \App\OtpCode::create($insertCode);

					$responseArray = [
						'message' => 'Verification code generated',
						'status_code' => 200
					];
					return response()->json($responseArray);					

			}

								
		}
	}

	public function verifyCode($code, $new_phone) {

    	$user_id = \Authorizer::getResourceOwnerId();

    	// $code = Input::get('code');
    	$user_codes = \App\OtpCode::where('user_id', '=', $user_id)->first();
    	// echo $user_codes->expires - time(); exit;

    	if($code == $user_codes->codes) {

	    	if($user_codes->expires > time()) {

				$user = \App\User::find($user_id);

				$user->phone = $new_phone;

				$user->save();

				$responseArray = [
					'message' => 'Phone Number changed',
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
}

	public function updateRegId() {

		$user_id = \Authorizer::getResourceOwnerId();

		$user = \App\User::find($user_id);

		$pushy_id = Input::get('pushy_id');

		$user->pushy_id = $pushy_id;

		$save = $user->save();

		if($save) {
			$responseArray = [
				'message' => 'Registration id updated',
				'status_code' => 200
			];
				
			return response()->json($responseArray);	
		}
		else {
			$responseArray = [
				'message' => 'Error updating registration id',
				'status_code' => 400
			];
				
			return response()->json($responseArray);	
		}

	}


}
