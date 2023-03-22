API for minecraft laucher

API Routes
+--------+----------+----------------------------------------------+------+----------------------------------------------------------------+------------+ 
| Domain | Method   | URI                                          | Name | Action                                                         | Middleware | 
+--------+----------+----------------------------------------------+------+----------------------------------------------------------------+------------+ 
|        | GET|HEAD | /                                            |      | Closure                                                        | web        | 
|        | POST     | api/launcher/auth/FetchUserDataByAuth        |      | App\Http\Controllers\UserController@FetchUserDataByAuth        | api        | 
|        | POST     | api/launcher/auth/FetchUserDataByVisibleData |      | App\Http\Controllers\UserController@FetchUserDataByVisibleData | api        | 
|        | POST     | api/launcher/auth/KeepAlive                  |      | App\Http\Controllers\UserController@KeepAlive                  | api        | 
|        | GET|HEAD | api/launcher/auth/OAuth                      |      | App\Http\Controllers\UserController@OAuth                      | api        | 
|        | POST     | api/launcher/auth/localLogin                 |      | App\Http\Controllers\UserController@Login                      | api        | 
|        | POST     | api/launcher/auth/localRegister              |      | App\Http\Controllers\UserController@Register                   | api        | 
|        | GET|HEAD | api/launcher/ping                            |      | App\Http\Controllers\LauncherController@Ping                   | api        | 
|        | POST     | api/launcher/skin/UploadSkin                 |      | App\Http\Controllers\SkinController@UploadSkin                 | api        | 
|        | GET|HEAD | api/launcher/skin/{playername}.png           |      | App\Http\Controllers\SkinController@FetchSkin                  | api        | 
|        | GET|HEAD | sanctum/csrf-cookie                          |      | Laravel\Sanctum\Http\Controllers\CsrfCookieController@show     | web        | 
+--------+----------+----------------------------------------------+------+----------------------------------------------------------------+------------+
![image](https://user-images.githubusercontent.com/68060177/227020682-d6779dd8-47ef-4430-9413-fa5922af4e82.png)

![image](https://user-images.githubusercontent.com/68060177/227020961-f52e6392-7f3f-41a4-9ae1-140a1f7c95ee.png)
