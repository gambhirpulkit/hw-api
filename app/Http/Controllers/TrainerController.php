<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input as Input;

use App\Http\Requests;

class TrainerController extends Controller
{
    public function login() {

	    $username = Input::get('username');
	    $password = Input::get('password');    	

	    if (\App\Trainer::where('email', '=', $username)->exists()) {

	    	$trainer = \App\Trainer::where('email', '=', $username)->first();

	        $hashedPassword = $trainer->password;

	        if (\Hash::check($password, $hashedPassword)) {
				$responseArray = [
					'message' => 'User authenticated',
					'data' => $trainer,
					'status_code' => 200
				];
				return $this->response()->json($responseArray);

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
				'message' => 'User not registered',
				'status_code' => 404
			];
			return response()->json($responseArray);

	    }
    }
}
