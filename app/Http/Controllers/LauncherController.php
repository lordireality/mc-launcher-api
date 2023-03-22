<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LauncherController extends Controller
{
    function Ping(){
        return response() -> json(["state"=>"success","status" => "200","message"=>["Pong!"]],200);
    }
}
