<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /*Пока это говнище заточено только под лаунчер. Если мы хотим паралельную работу веб-сайта и лаунчера - рефактор 100% + добавление разделения токенов. 
    + вынос логики лаунчера/сайта в отдельные контроллеры */
    /*p.s.: не тестировалось, без понятия, работает или нет. В теории должно. */

    /*Функция - вызова действий после авторизации по OAUTH */
    function OAuth(Request $request){
        $referer = $request->input('provider');
        $code = $request->input('code');
        if(is_null($provider) || is_null($code)){
            return response() -> json(["state"=>"failed","status" => "422","message"=>['Missing param code or provider!']],422);
        }
        switch($provider){
            /*Получение данных из провайдера - Google Auth*/
            case "google": $userinfo = $this->GoogleUserDataProvider($code);  /*тут потом сверка с БД, по Google, ID, если есть учетка, то кул, даем токен уже приложения, если нет учетки, создаем и даем токен */  break;
            /*Получение данных из провайдера - Yandex Auth*/
            case "yandex": break;
            /*Получение данных из провайдера - VKontakte Auth*/
            case "vk": break;
            /*Ошибка. Несуществующий провайдер авторизации*/
            default: return response() -> json(["state"=>"failed","status" => "422","message"=>['Unknown OAuth Provider!']],422);
        }
    }

    /*Забирает данные по полученному коду послу авторизации гугл */
    /*TODO: добавить в .env параметры конфигурации авторизации */
    function GoogleUserDataProvider($code = null){      
        if(!is_null($code)){
            $params = [
                'client_id'     => 'ИНДИФИКАТОР_КЛИЕНТА',
                'client_secret' => 'СЕКРЕТ_КЛИЕНТА',
                'redirect_uri'  => 'https://example.com/login_google.php', 
                'grant_type'    => 'authorization_code',
                'code'          => $code
            ];	         
            $ch = curl_init('https://accounts.google.com/o/oauth2/token');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $data = curl_exec($ch);
            curl_close($ch);	
            $data = json_decode($data, true);
            if (!empty($data['access_token'])) {
                $params = array(
                    'access_token' => $data['access_token'],
                    'id_token'     => $data['id_token'],
                    'token_type'   => 'Bearer',
                    'expires_in'   => 3599
                );
                $info = file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?' . urldecode(http_build_query($params)));
                $info = json_decode($info, true);
                return $info;
            } else {
                return null;
            }
        } else {
            return null;
        }

    }
    
    
    /*Дефолтная регистрация */
    /*сделать по человечески + подтверждение по Email (аналогично делал в reshupdd.ru) */
    function Register(Request $request){
        return null;
    }
    /*Дефолтная авторизация*/
    function Login(Request $request){
        $inputData = $request->input();
        $validRules = [
           'email' => 'required|Email|max:256',
           'password' => 'required|max:256'
        ];
        $validator = Validator::make($inputData,$validRules);
        if($validator -> passes()){
            $hashedPassword = hash('sha256',$inputData["password"].$inputData["email"]); //TODO: вынести паттерн хэширования пароля в .env
            if(DB::table('player')->SELECT('email')->WHERE([['email','=',$inputData["email"]],['def_passwordhash','=',$hashedPassword]])->exists()){
                $authtoken = hash('sha256',date("ymdhis")); //ну тут немного кринж. TODO: вынести паттерн формирования токена в .env
                DB::table('player')->WHERE([['email','=',$inputData["email"]],['def_passwordhash','=',$hashedPassword]])->update(['authtoken' => $authtoken]);
                return response() -> json(["state"=>"success","status" => "200","message"=>["Вы успешно авторизовались!"], "authtoken"=>$authtoken, "email"=>$inputData["email"]],200);
            } else {
                return response() -> json(["state"=>"failed","status" => "401","message"=>["Неверная электронная почта или пароль!"]],401);
            }
        } else { return response() -> json(["state"=>"failed","status" => "422","message"=>$validator->messages()],422); }
    }
    /*Получение данных пользователя по сессии авторизации */
    function FetchUserDataByAuth(Request $request){
        $inputData = $request->input();
        $validRules = [
           'email' => 'required|Email|max:256',
           'authtoken' => 'required|max:256'
        ];
        $validator = Validator::make($inputData,$validRules);
        if($validator -> passes()){
            if(DB::table('player')->where([['email','=',$inputData["email"]], ['authtoken','=',$inputData["authtoken"]]])->exists()){
                $userdata = DB::table('player')->select('playername','email','verified','money')->where([['email','=',$inputData["email"]], ['authtoken','=',$inputData["authtoken"]]])->get()[0];
                return response() -> json(["state"=>"success","status" => "200","userdata"=>$userdata],200); 
            } else {
                return response() -> json(["state"=>"failed","status" => "401","message"=>["Сессия устарела или не существует"]],401);
            }
        } else { return response() -> json(["state"=>"failed","status" => "422","message"=>$validator->messages()],422); }
    }
    /*Получение данных пользователя по "доступным" данным */
    function FetchUserDataByVisibleData(Request $request){
        $inputData = $request->input();
        $validRules = [
            'visibleDataType' => 'required|max:256',
            'value' => 'required|max:256'
        ];
        $validator = Validator::make($inputData,$validRules);
        if($validator -> passes()){
            /*Список возможных полей*/
            $possibleFieldsRule = [
                'id',
                'playername',
                'email'
            ]
            if(in_array($inputData["visibleDataType"], $possibleFieldsRule)){
                if(DB::table('player')->where([['visibleDataType','=',$inputData["value"]]])->exists()){
                    $userdata = DB::table('player')->select('id','playername','email')->where([['visibleDataType','=',$inputData["value"]]])->get()[0];
                    return response() -> json(["state"=>"success","status" => "200","userdata"=>$userdata],200); 
                } else {
                    return response() -> json(["state"=>"failed","status" => "401","data"=>null],401);
                }
            } else {
                return response() -> json(["state"=>"failed","status" => "422","message"=>["Запрещенное поле для фильтрации: ".$inputData["visibleDataType"]]],422);
            }
        } else {
           return response() -> json(["state"=>"failed","status" => "422","message"=>$validator->messages()],422);
        }
    }
    /*проверка актуальности сессии авторизации */
    function KeepAlive(Request $request){
        $inputData = $request->input();
        $validRules = [
           'email' => 'required|Email|max:256',
           'authtoken' => 'required|max:256'
        ];
        $validator = Validator::make($inputData,$validRules);
        if($validator -> passes()){
            if(DB::table('player')->where([['email','=',$inputData["email"]], ['authtoken','=',$inputData["authtoken"]]])->exists()){
                return response() -> json(["state"=>"success","status" => "200"],200); 
            } else {
                return response() -> json(["state"=>"failed","status" => "401","message"=>["Сессия устарела или не существует"]],401);
            }
        } else { return response() -> json(["state"=>"failed","status" => "422","message"=>$validator->messages()],422); }
    }
}
