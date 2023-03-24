<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Minecraft mega launcher!</title>
    </head>
    <body id="body">
        <a id="hideBilly" style="color:white; font-size:50px" href="javascript:hideBilly()">Мне мешает ♂данжен мастер♂! и ♂вебмастер♂ который делал стиль этой страницы!!</a><br>
        <script>
            function hideBilly(){
                alert('Fucking slave!');
                document.getElementById("body").style = "background-image: none; background-color: black;"
                document.getElementById("hideBilly").style ="display:none;";
            }
        </script>
        <script>
            /*Proudly украдено из ESE-CRM */
            /*p.s. XMLHttpRequest - legacy. Может не взлетать на определенных браузерах */
            //GET
            function HTTPGet(URL){
                var xhr = new XMLHttpRequest(); 
                xhr.open("GET",URL,false);
                xhr.send(null);
                return xhr.responseText;          
            }
            //Post
            function HTTPPost(URL, params, isAsync){
                var xhr = new XMLHttpRequest(); 
                xhr.open("POST",URL,isAsync);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                if(isAsync==true){
                    xhr.onreadystatechange = function() {
                        if(this.readyState === XMLHttpRequest.DONE){ 
                            return xhr.responseText; 
                        }
                    }
                }

                paramsString = "";
                if(params.length>0){
                    paramsString = params[0].key+"="+params[0].value;
                    for(var i =1;i<params.length;i++){
                        paramsString = paramsString + "&"+params[i].key+"="+params[i].value
                    }
                }
                xhr.send(paramsString);
                if(isAsync==false){
                    if(xhr.status != null){
                        return xhr.responseText;
                    }
                }
            }
        </script>
        <script>
            function test_defaultRegister(){
                var playername = document.getElementById("reg_playername").value;
                var email = document.getElementById("reg_email").value;
                var password = document.getElementById("reg_password").value;
                
                let params = [
                    {
                        "key" : "playername",
                        "value" : playername
                    },
                    {
                        "key" : "email",
                        "value" : email
                    },
                    {
                        "key" : "password",
                        "value" : password
                    }
                ];
                var responseRaw = HTTPPost(window.location.origin+'/api/launcher/auth/localRegister',params,false);
                
                response = JSON.parse(responseRaw);
                if(response.status == 200){
                    console.log(response);
                    document.getElementById("reg_result").innerHTML = responseRaw;
                } else {
                    console.log(response.message);
                }
            }
            
            
            function test_defaultLogin(){
                var email = document.getElementById("auth_email").value;
                var password = document.getElementById("auth_password").value;
                let params = [
                    {
                        "key" : "email",
                        "value" : email
                    },
                    {
                        "key" : "password",
                        "value" : password
                    }
                ];
                var responseRaw = HTTPPost(window.location.origin+'/api/launcher/auth/localLogin',params,false);
                response = JSON.parse(responseRaw);
                if(response.status == 200){
                    document.getElementById("auth_result").innerHTML = responseRaw;
                } else {
                    console.log(response.message);
                }
            }
            function test_fetchDataByAuth(){
                var email = document.getElementById("data_Auth_email").value;
                var authtoken = document.getElementById("dataAuth_token").value;
                let params = [
                    {
                        "key" : "email",
                        "value" : email
                    },
                    {
                        "key" : "authtoken",
                        "value" : authtoken
                    }
                ];
                var responseRaw = HTTPPost(window.location.origin+'/api/launcher/auth/fetchUserDataByAuth',params,false);
                response = JSON.parse(responseRaw);
                if(response.status == 200){
                    document.getElementById("dataAuth_result").innerHTML = responseRaw;
                } else {
                    console.log(response.message);
                }
            }
            function test_fetchDataByVisible(){
                var visibleDataType = document.getElementById("data_Visible_visibleDataType").value;
                var someval = document.getElementById("data_Visible_value").value;
                let params = [
                    {
                        "key" : "visibleDataType",
                        "value" : visibleDataType
                    },
                    {
                        "key" : "value",
                        "value" : someval
                    }
                ];
                var responseRaw = HTTPPost(window.location.origin+'/api/launcher/auth/fetchUserDataByVisibleData',params,false);
                response = JSON.parse(responseRaw);
                if(response.status == 200){
                    document.getElementById("visibleData_result").innerHTML = responseRaw;
                } else {
                    console.log(response.message);
                }                
            }
            function test_keepAlive(){
                var email = document.getElementById("keepalive_email").value;
                var authtoken = document.getElementById("keepalive_token").value;
                let params = [
                    {
                        "key" : "email",
                        "value" : email
                    },
                    {
                        "key" : "authtoken",
                        "value" : authtoken
                    }
                ];
                var responseRaw = HTTPPost(window.location.origin+'/api/launcher/auth/keepAlive',params,false);
                response = JSON.parse(responseRaw);
                if(response.status == 200){
                    document.getElementById("keepAlive_result").innerHTML = responseRaw;
                } else {
                    console.log(response.message);
                }   
            }
            function test_fetchSkin(){
                var playername = document.getElementById("fetchSkin_playername").value;
                document.getElementById("skinPreview").src = window.location.origin+"/api/launcher/skin/"+playername+".png";

            }
            async function baseConvert(){
                let base64Str =  await new Promise((resolve) => {
                let fileReader = new FileReader();
                fileReader.onload = (e) => resolve(fileReader.result);
                fileReader.readAsDataURL(document.getElementById("file").files[0]);
            });
            document.getElementById("uploadskin_base64").value = base64Str;
            }
            function test_uploadSkin(){
                var email = document.getElementById("uploadskin_email").value;
                var authtoken = document.getElementById("uploadskin_authtoken").value;
                var skinBase64 = document.getElementById("uploadskin_base64").value;
                
                let params = [
                    {
                        "key" : "email",
                        "value" : email
                    },
                    {
                        "key" : "authtoken",
                        "value" : authtoken
                    },
                    {
                        "key" : "skinBase64",
                        "value" : skinBase64
                    }
                ];
                var responseRaw = HTTPPost(window.location.origin+'/api/launcher/skin/UploadSkin',params,false);
                response = JSON.parse(responseRaw);
                if(response.status == 200){
                    document.getElementById("UploadSkinResult").innerHTML = responseRaw;
                } else {
                    console.log(response.message);
                }   
            }
        </script>
        <style>
            h1, h2, h3{
                color:white;
                backdrop-filter: blur(10px) saturate(70%);
                display: inline-block;
            }
            body{
                background-image: url('./cover.jpg')
                
            }
            table, tr, th, td{
                border: 1px solid;
                width:100%;
                background-color:white;
            }
            th{
                background-color:gray;
            }
            textarea{
               min-width:1280px;
            }
        </style>
        <h1>Тут ничавойка нет!</h1><br>
        <h1>Никакого API для майнкрафт лаунчера! Правда!</h1><br>
        <h1>Ниже представлено демо обращения к API, но перед этим, список роутов прописанных, вдруг что-то добавится...</h1><br>
        <textarea style="width:100%; height:300px;" readonly>
        +--------+----------+----------------------------------------------+------+----------------------------------------------------------------+------------+ 
        | Domain | Method   | URI                                          | Name | Action                                                         | Middleware | 
        +--------+----------+----------------------------------------------+------+----------------------------------------------------------------+------------+ 
        |        | GET|HEAD | /                                            |      | Closure                                                        | web        | 
        |        | GET|HEAD | api/launcher/auth/OAuth                      |      | App\Http\Controllers\UserController@OAuth                      | api        | 
        |        | POST     | api/launcher/auth/fetchUserDataByAuth        |      | App\Http\Controllers\UserController@FetchUserDataByAuth        | api        | 
        |        | POST     | api/launcher/auth/fetchUserDataByVisibleData |      | App\Http\Controllers\UserController@FetchUserDataByVisibleData | api        | 
        |        | POST     | api/launcher/auth/keepAlive                  |      | App\Http\Controllers\UserController@KeepAlive                  | api        | 
        |        | POST     | api/launcher/auth/localLogin                 |      | App\Http\Controllers\UserController@Login                      | api        |
        |        | POST     | api/launcher/auth/localRegister              |      | App\Http\Controllers\UserController@Register                   | api        | 
        |        | GET|HEAD | api/launcher/game/getGameVersions            |      | App\Http\Controllers\LauncherController@GetGameVersions        | api        | 
        |        | GET|HEAD | api/launcher/game/getNews                    |      | App\Http\Controllers\LauncherController@GetNews                | api        | 
        |        | GET|HEAD | api/launcher/ping                            |      | App\Http\Controllers\LauncherController@Ping                   | api        | 
        |        | POST     | api/launcher/skin/UploadSkin                 |      | App\Http\Controllers\SkinController@UploadSkin                 | api        | 
        |        | GET|HEAD | api/launcher/skin/{playername}.png           |      | App\Http\Controllers\SkinController@FetchSkin                  | api        | 
        |        | GET|HEAD | sanctum/csrf-cookie                          |      | Laravel\Sanctum\Http\Controllers\CsrfCookieController@show     | web        | 
        +--------+----------+----------------------------------------------+------+----------------------------------------------------------------+------------+
        </textarea>
        <br>
        <h1>Важно! Статусы ошибок JS не кушает, поэтому смотрим консоль, там Console.Log()</h1>
        <br>
        <h1>Контроллер пользователей</h1><br>
        <h3>Default register POST: </h3>
        <table>
            <tr>
                <th>Параметры</th>
                <th>Ответ:</th>
            </tr>
            <tr>
                <td>
                <span>playername<input type="text" id="reg_playername"></span><br>
                <span>email<input type="text" id="reg_email"></span><br>
                <span>password<input type="text" id="reg_password"></span>
                </td>
                <td><textarea readonly id="reg_result"></textarea></td>
            </tr>
        </table>
        <input type="submit" onclick="test_defaultRegister()" value="TEST ME!">
        <hr>
        <h3>Default login POST: </h3>
        <table>
            <tr>
                <th>Параметры</th>
                <th>Ответ:</th>
            </tr>
            <tr>
                <td>
                <span>email<input type="text" id="auth_email"></span><br>
                <span>password<input type="text" id="auth_password"></span>
                </td>
                <td><textarea id="auth_result" readonly></textarea></td>
            </tr>
        </table>
        <input type="submit" onclick="test_defaultLogin()" value="TEST ME!">
        <hr>
        <h3>FetchUserDataByAuth POST: </h3>
        <table>
            <tr>
                <th>Параметры</th>
                <th>Ответ:</th>
            </tr>
            <tr>
                <td>
                <span>email<input type="text" id="data_Auth_email"></span><br>
                <span>authtoken<input type="text" id="dataAuth_token"></span>
                </td>
                <td><textarea id="dataAuth_result" readonly></textarea></td>
            </tr>
        </table>
        <input type="submit" onclick="test_fetchDataByAuth()" value="TEST ME!">
        <hr>
        <h3>FetchUserDataByVisibleData</h3>
        <table>
            <tr>
                <th>Параметры</th>
                <th>Ответ:</th>
            </tr>
            <tr>
                <td>
                <span>visibleDataType<input type="text" id="data_Visible_visibleDataType"></span><br>
                <span>value<input type="text" id="data_Visible_value"></span>
                </td>
                <td><textarea readonly id="visibleData_result"></textarea></td>
            </tr>
        </table>
        <input type="submit" onclick="test_fetchDataByVisible()" value="TEST ME!">
        <hr>
        <h3>KeepAlive</h3>
        <table>
            <tr>
                <th>Параметры</th>
                <th>Ответ:</th>
            </tr>
            <tr>
                <td>
                <span>email<input type="text" id="keepalive_email"></span><br>
                <span>authtoken<input type="text" id="keepalive_token"></span>
                </td>
                <td><textarea readonly id="keepAlive_result"></textarea></td>
            </tr>
        </table>
        <input onclick="test_keepAlive()" type="submit" value="TEST ME!">
        <hr>
        <h1>Контроллер скинов</h1><br>
        <h3>FetchSkin</h3>
        <table>
            <tr>
                <th>Параметры</th>
                <th>Ответ:</th>
            </tr>
            <tr>
                <td>
                <span>playername<input type="text" id="fetchSkin_playername"></span><br>
                </td>
                <td><img id="skinPreview" src=""/></td>
            </tr>
        </table>
        <input type="submit" onclick="test_fetchSkin()" value="TEST ME!">
        <hr>
        <h3>UploadSkin</h3>
        <table>
            <tr>
                <th>Параметры</th>
                <th>Ответ:</th>
            </tr>
            <tr>
                <td>
                <span>email<input type="text" id="uploadskin_email"></span><br>
                <span>authtoken<input type="text" id="uploadskin_authtoken"></span><br>
                <span>base64png<input type="text" id="uploadskin_base64"></span><hr>
                <span>файл который будет преобразован в base64<input type="file" id="file"></span>
                <input type="submit" onclick="baseConvert()" value="Преобразовать в BASE 64 строку">
                </td>
                <td><textarea readonly id="UploadSkinResult"></textarea></td>
            </tr>
        </table>
        <input onclick="test_uploadSkin()" type="submit" value="TEST ME!">
        <hr>
        <h1>Методы getNews, getGameVersions - GET, поэтому дебаг на них писать не буду</h1>
    </body>
</html>
