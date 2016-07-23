<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input as Input;

use App\Http\Requests;

class TrainerController extends Controller
{
    public function login() {

	    $email = Input::get('email');
	    $password = Input::get('password'); 
	    $pushy_id = Input::get('pushy_id');

	    // echo \App\Trainer::where('email', '=', $email)->exists(); exit;
	    if (\App\Trainer::where('email', '=', $email)->exists()) {

	    	$trainer = \App\Trainer::where('email', '=', $email)->first();
	        


	        $hashedPassword = $trainer->password;
	        // echo $hashedPassword; exit;

	        if (\Hash::check($password, $hashedPassword)) {
	        	// echo "hey"; exit;
	        	$trainer->pushy_id = $pushy_id;
	        	$trainer->save();	    		        	
	        	
				$responseArray = [
					'message' => 'Trainer authenticated',
					'data' => $trainer,
					'status_code' => 200
				];
				return response()->json($responseArray);

	        }
	        else {
				$responseArray = [
					'message' => 'Wrong credentials',
					'data' => $trainer,
					'status_code' => 404
				];
				return response()->json($responseArray);
	        }

	    }	
	    else {

			$responseArray = [
				'message' => 'Trainer not registered',
				'status_code' => 400
			];
			return response()->json($responseArray);

	    }
    }

    public function forgotPwd($phone) {

    	$trainer = \App\Trainer::where('phone', '=', $phone)->first();

    	if($trainer) {

	    	$trainer_codes = \App\TrainerCodes::where('trainer_id', '=', $trainer->id)->first();
    	
	    	if($trainer_codes){

	    		if($trainer_codes->expires > time()) {
	    			$code = $trainer_codes->codes;

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

					$trainer_codes->codes = $rand;


					$trainer_codes->save();

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
						"trainer_id" => $trainer->id,
						"codes" => $rand,
						"expires" => time() + 1800
					);	


					$saveCode = \App\TrainerCodes::create($insertCode);

					$responseArray = [
						'message' => 'Verification code generated',
						'status_code' => 200
					];
						
					return response()->json($responseArray);

	    	}

    	}
    	else {
			$responseArray = [
				'message' => 'Trainer does not exist',
				'status_code' => 404
			];
				
			return response()->json($responseArray);	    		
    	}

    }

	public function verifyPwd () {
		$password = Input::get("password");
		$code = Input::get('code');
		$phone = Input::get('phone');

    	$trainer = \App\Trainer::where('phone', '=', $phone)->first();

    	$trainer_codes = \App\TrainerCodes::where('trainer_id', '=', $trainer->id)->first();
    	// echo $user_codes->expires - time(); exit;

    	if($code == $trainer_codes->codes) {

	    	if($trainer_codes->expires > time()) {

	    		$trainer->password = \Hash::make($password);

	    		$trainer->save();

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
