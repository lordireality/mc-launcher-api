<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

class LauncherController extends Controller
{
    function Ping(){
        return response() -> json(["state"=>"success","status" => "200","message"=>["Pong!"]],200);
    }
    function GetGameVersions(){
        //p.s. где data из таблицы - скорее всего ссылка на репозиторий с файлами игры.
        return response() -> json(["state"=>"success","status" => "200","data"=>DB::table('versions')->select('ver','last-updated-at','data')->get()],200);
    }
}
