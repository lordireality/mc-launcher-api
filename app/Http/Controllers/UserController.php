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
    
    function GoogleUserDataProvider($code = null){      
        if(!is_null($code)){
            $params = [
                'client_id'     => config('app.OAuth_Google_ClientID'),
                'client_secret' => config('app.OAuth_Google_SecretId'),
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
            //https://snipp.ru/php/oauth-google
        } else {
            return null;
        }

    }
    /*Забирает данные по полученному коду послу авторизации ВК */
    function VKUserDataProvider($code = null){      
        if(!is_null($code)){
            $params = [
                'client_id'     => config('app.OAuth_VK_ClientID'),
                'client_secret' => config('app.OAuth_VK_SecretID'),
                'redirect_uri'  => 'https://example.com/login_vk.php', 
                'code'          => $code
            ];	 
            $ch = curl_init('https://oauth.vk.com/access_token?'.http_build_query($params));
            $data = curl_exec($ch);
            curl_close($ch);	
            $data = json_decode($data, true);
            if (!empty($data['access_token'])) {
                return $data;
            } else {
                return null;
            }
            //https://oauth.vk.com/access_token
        }else {
            return null;
        }

    }
    
    
    /*Дефолтная регистрация */
    function Register(Request $request){
        $inputData = $request->input();
        $validRules = [
            'playername' => 'required|max:256',
            'email' => 'required|Email|max:256',
            'password' => 'required'
        ];
        $validator = Validator::make($inputData,$validRules);
        if($validator -> passes()){
            if(DB::table('player')->where([['playername'],'=',$inputData["playername"]])-exists()){
                return response() -> json(["state"=>"failed","status" => "422","message"=>['Указанный ник-нейм уже зарегистрирован!']],422);
            }
            if(DB::table('player')->where([['email'],'=',$inputData["email"]])-exists()){
                return response() -> json(["state"=>"failed","status" => "422","message"=>['Указанный Email уже зарегистрирован!']],422);
            }
            $isPasswordStrong = true;
            $passwordValidationErrorMessage = [];
            if(!preg_match('@[A-Z]@', $password)){
                $isPasswordStrong = false;
                array_push($passwordValidationErrorMessage, "В пароле должен быть, хотя бы один символ заглавного регистра!");                
            }
            if(!preg_match('@[a-z]@', $password)){
                $isPasswordStrong = false;
                array_push($passwordValidationErrorMessage, "В пароле должен быть, хотя бы один символ нижнего регистра!");                
            }
            if(!preg_match('@[0-9]@', $password)){
                $isPasswordStrong = false;
                array_push($passwordValidationErrorMessage, "В пароле должно быть хотя бы одно число!");                
            }
            if(strlen($password) < 6){
                $isPasswordStrong = false;
                array_push($passwordValidationErrorMessage, "Пароль должен состоять минимум из 6 символов!");                
            }
            if($isPasswordStrong == false){
                return response() -> json(["state"=>"failed","status" => "422","message"=>$passwordValidationErrorMessage],422);
            }
            $hashedPassword = hash('sha256',$inputData["password"].$inputData["email"]); //TODO: вынести паттерн хэширования пароля в .env
            $uid = DB::table('player')->insertgetid(['playername'=>$inputData["playername"], 'email'=>$inputData["email"],'def_passwordhash'=>$hashedPassword]);
            return response() -> json(["state"=>"success","status" => "200","message"=>['Учетная запись создана!']],422);
        } else { return response() -> json(["state"=>"failed","status" => "422","message"=>$validator->messages()],422); }
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
            ];
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
