<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input as Input;

use App\Http\Requests;


class UserChatController extends Controller
{
    public function postMessage() {

    	$user_id = \Authorizer::getResourceOwnerId();
    	$trainer_id = Input::get('trainer_id');
    	$message = Input::get('message');
    	$type = Input::get('type');

    	$user = \App\User::find($user_id);

		$insertData = array(
			"user_id"	=>	$user_id,
			"trainer_id"	=>	$trainer_id,
			"message"	=>	$message,
			"type" =>	$type,
			"sent_time" => microtime()
		);

		$save = \App\Chat::create($insertData);
    	
    	if($save) {

		    $pusher = \App::make('pusher');

		    $data = array();
		    $data['text'] = $message;   
		    $data['time'] = time();
		    $data['name'] = $user->name;

		    $val = $pusher->trigger( 'messages', 'new_message', $data);   		

			$responseArray = [
				'message' => 'Message sent to server',
				'status_code' => 200
			];
			return response()->json($responseArray);

    	}
    	else {

			$responseArray = [
				'message' => 'Error sending message.',
				'status_code' => 502
			];
			return response()->json($responseArray);    		
    	
    	}
    	return false;
    }
}
