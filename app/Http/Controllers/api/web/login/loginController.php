<?php

namespace App\Http\Controllers\api\web\login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use \DB;
use datetime;
use Validator;


class loginController extends Controller
{
    // use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //登入
    public function login(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $account = (int)($request -> account);
        $password= (string)($request -> password);
        /*$users = DB::select('select * from users where phone = ? and active = 1', array($account));*/
        $users = DB::select('select * from member where phone = ?', array($account));
        if($users) {
            if (Hash::check($password, $users[0] -> password)){
                $token = $users[0] -> token;
                $uId = $users[0] -> uId;
                $token = Hash::make('plat'. $users[0] -> uId. $users[0] -> phone. $users[0] -> name. strtotime("now"). 'form');
                $nowtime = strtotime(date("Y-m-d H:i:s"));
                DB::update('update member set token = ?, tokenModifyTime = ? where uId = ?', array($token,$nowtime,$uId));
                $statusCode = 203;
                $name = $users[0] -> name;
                $msg = $users[0] -> name . "！您好～" . "已成功登入";
                return response()->json(['statusCode' => $statusCode , 'Name'=> $name , 'msg'=> $msg , 'token'=> $token]);
            }else{
                $statusCode = 401;
                $msg = "帳號或密碼輸入錯誤，請重新登入";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
            }
        }else{
            $statusCode = 401;
            $msg = "此用戶不存在，請重新登入";
            return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
        }
    }
    
}