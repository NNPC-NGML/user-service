<?php

namespace App\Service\UserService;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;

class UserHandler
{

    public function create($email, $name, $password): bool
    {

        // Check if user exists in DB
        if (User::where('email', $email)->exists()) {
            return false;
        }

        $user = new user([
            'email' => $email,
            'name' => $name,
            'password' => bcrypt($password),
        ]);

        if ($user->save()) {
            return true;
        }else{
            return false;
        };
    }
}
