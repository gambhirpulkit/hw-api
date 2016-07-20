<?php 

namespace App\Verifiers;

use Illuminate\Support\Facades\Auth;

class PasswordGrantVerifier
{
    public function verify($email, $password)
    {
        $credentials = array();
            $credentials = [
                'email'    => $email,
                'password' => $password,
            ];        
 

        if (Auth::once($credentials)) { 
            return Auth::user()->id;
        }

        return false;
    }

    public function verifyTrainer($email, $password)
    {
        $credentials = array();
            $credentials = [
                'email'    => $email,
                'password' => $password,
            ];        

        $trainer = \App\Trainer::where('email', $email)->first();
        $hashedPassword = $trainer->password;
        // echo $hashedPassword; exit;
        // echo \Hash::check($password, $hashedPassword); exit;
        if (\Hash::check($password, $hashedPassword)) {
            return $trainer->id;
        }

        return false;
    }

}
