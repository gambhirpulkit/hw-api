<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input as Input;

use App\Http\Requests;


class ChatController extends Controller
{
    public function postMessage() {
    	$sender = Input::get('sender');
    	$user_id = 0;
    	$trainer_id = 0;
    	if($sender == 'user') {
	    	$user_id = \Authorizer::getResourceOwnerId();
	    	$trainer_id = Input::get('trainer_id');    		
    	} 
    	else {
	    	$trainer_id = \Authorizer::getResourceOwnerId();
	    	$user_id = Input::get('user_id');
    	}   	

    	$message = Input::get('message');
    	$type = Input::get('type');

    	$time = microtime(true);   	

    	$time_arr = list($sec, $usec) = explode('.', $time);

		$insertData = array(
			"user_id"	=>	$user_id,
			"trainer_id"	=>	$trainer_id,
			"message"	=>	$message,
			"type" =>	$type,
			"sender" => $sender,
			"sent_time" => $time
		);
		$time_format =  date('g:i A', $time);


		$save = \App\Chat::create($insertData);
    	
    	if($save) {
    		$val = NULL;
    		if($sender == 'user') {
    			$user = \App\User::find($user_id); 

			    $pusher = \App::make('pusher');
			    
			    $data = array();

			    $data['chat_id'] = $save->id;
			    $data['message'] = $message;
			    $data['type'] = 'text'; 
			    $data['sender'] = $sender; 
			    $data['sending_time'] = $time_format;
			    $data['name'] = $user->name;

			    $val = $pusher->trigger('messages', 'new_message', $data);

			    // echo $val;

    		}
    		else {
    			$trainer = \App\Trainer::find($trainer_id);

			    $pusher = \App::make('pusher');
			    
			    $data = array();

			    $data['chat_id'] = $save->id;
			    $data['message'] = $message;
			    $data['type'] = 'text'; 
			    $data['sender'] = $sender; 
			    $data['sending_time'] = $time_format;
			    $data['name'] = $trainer->name;

			    $val = $pusher->trigger('messages', 'new_message', $data);   		    		

			    // echo $val;	    			
    		}


			$responseArray = [
				'message' => 'Message sent to server',
				'status_code' => 200,
				'val' => $val
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

    public function previousMessages() {

    	
    	
    }
}
