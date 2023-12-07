<?php

namespace App\Helper;

use Validator;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Password;
use App\Helper\UserService;

class UserServices{
    public $email, $password;

    public function __construct($email, $password){
        $this->email = $email;
        $this->password = $password;
    }
    public function validateInput()
    {
        $validator = Validator::make(['email' => $this->email, 'password'=> $this->password],
        [
         'email' =>['required', 'email:rfc,dns', 'unique:users'],
         'password'=> ['required','string', Password::mins(8)]
        ]
        );

        if($validator->fails())
        {
            return ['status' => false,'message'=> $validator->messages()->first(),'data'=> $validator->messages()];
        }
        else {
            return ['status'=> true];

        }
        
    }

    public function register($deviceName)
    {
        $validate = $this->validateInput();
        if($validate['status'] == false)
        {
            return $validate;
        }
        else {
            $user = User::create(['email'=>$this->email, 'password'=>hash::make($this->password)]);
            $token = $user->createToken($deviceName)->plaintexToken;
            return ['status'=> true,'token'=>$token,'user'=>$user];
        }
    }
}