<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

class Helper
{
    public static function generateOtp($mobile, $otp, $msg)
    {


			//Your authentication key
			$authKey = "98631A9FuU8Ips577a56fb";

			//Multiple mobiles numbers separated by comma
			$mobileNumber = $mobile;

			//Sender ID,While using route4 sender id should be 6 characters long.
			$senderId = "BHAPPY";

			// Your message to send, Add URL encoding here.
		    $otp_prefix = ':';

		    //Your message to send, Add URL encoding here.
		    $message = $msg;

			//Define route 
			$route = "4";
			//Prepare you post parameters
			$postData = array(
			    'authkey' => $authKey,
			    'mobiles' => $mobileNumber,
			    'message' => $message,
			    'sender' => $senderId,
			    'route' => $route
			);

			//API URL
			$url="https://control.msg91.com/api/sendhttp.php";

			// init the resource
			$ch = curl_init();
			curl_setopt_array($ch, array(
			    CURLOPT_URL => $url,
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_POST => true,
			    CURLOPT_POSTFIELDS => $postData
			    //,CURLOPT_FOLLOWLOCATION => true
			));


			//Ignore SSL certificate verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


			//get response
			$output = curl_exec($ch);

			//Print error if any
			if(curl_errno($ch))
			{
			    echo 'error:' . curl_error($ch);
			}

			curl_close($ch);

			// echo $output;	
			// echo "hey";    
        
    }

    public static function sendPushNotification( $data, $ids, $sender ) {

		$apiKey = '';

    	if($sender != 'user') {
    		$apiKey = '0b08951311e19d18b21c0461127a5b9bbb7b97243f0ef1a27f4fbedaa90be447';
    	}
    	else {
    		$apiKey = '71fe72d90ed6b90bf4847f3741319c147ff5f5ed1643df5163efbd66a8b968d1';
    	}
        // Insert your Secret API Key here
        // echo $apiKey;

        // Set post variables
        $post = array(
            'data'              => $data,
            'registration_ids'  => $ids,
        );

        // Set Content-Type header since we're sending JSON
        $headers = array(
            'Content-Type: application/json'
        );

        // Initialize curl handle
        $ch = curl_init();

        // Set URL to Pushy endpoint
        curl_setopt($ch, CURLOPT_URL, 'https://api.pushy.me/push?api_key=' . $apiKey);

        // Set request method to POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Set our custom headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Get the response back as string instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set post data as JSON
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));

        // Actually send the push
        $result = curl_exec($ch);

        // Display errors
        if (curl_errno($ch)) {
            echo curl_error($ch);
        }

        // Close curl handle
        curl_close($ch);

        // Debug API response
        // echo $result;
    }	    


}