<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function listar(){
        $users = User::all();
        return response()->json($users);
    }
}
