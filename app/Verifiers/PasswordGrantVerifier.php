<?php 

namespace App\Verifiers;

use Illuminate\Support\Facades\Auth;

class PasswordGrantVerifier
{
    public function verify($email, $password , $phone = null)
    {
        $credentials = array();
            $credentials = [
                'email'    => $email,
                'password' => $password,
            ];        
        // if($phone = null) {
        //     $credentials = [
        //         'email'    => $email,
        //         'password' => $password,
        //     ];
        // }
        // else {
        //     $credentials = [
        //         'phone'    => $phone,
        //         'password' => $password,
        //     ];            
        // }      

        if (Auth::once($credentials)) { 
            return Auth::user()->id;
        }

        return false;
    }
}
