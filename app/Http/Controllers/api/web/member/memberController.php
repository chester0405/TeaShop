<?php

namespace App\Http\Controllers\api\web\member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Hash;
use \DB;
use datetime;
use Validator;


class memberController extends Controller
{
    //會員資料修改頁面顯示
    public function memberProfile(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $token = $request -> token;
       /* $users = DB::select('select * from member where token = ? and active = 1', array($token));*/
        $users = DB::select('select * from member where token = ? ', array($token));

        if($users) {
            $uId = $users[0] -> uId; 
            $tokenModifyTime = $users[0] -> tokenModifyTime;
            $differencetime = (strtotime("now") - $tokenModifyTime);
            // dd($differencetime);
            if ($differencetime > (60*60*24)){
                $statusCode = 0;
                $msg = "已超過登入時間，請重新登入";
                return response()->json(['statusCode'=> $statusCode , 'msg'=>$msg]);
            }
            
            $name=  $users[0] -> name;
            $competence = $users[0] -> competence;
            $email = $users[0] -> email;
            $countryCode = $users[0] -> countryCode;
            $phone = $users[0] -> phone;
            $sex = $users[0] -> sex;
            $birthday = $users[0] -> birthday;
            $age = $users[0] -> age;
            if (!$birthday){
                $birthday = "";
            }
            
            $countryCodes = DB::select('select * from countrycode');
            $sexs = "性別；0=未填寫、1=男、2=女、3=其他";
            $competences = "狀態 ; 0=註銷、1=一般會員、2=服務員、3=廠商、4=管理員";
            $data = array('uId' => $uId ,'name' => $name ,'competence' => $competence  , 'email' => $email , 'countrycode' => $countryCode ,'phone' => $phone ,'sex' => $sex ,'birthday' => $birthday,'age' => $age );
            $statusCode = 200;
            $msg = "獲取資料成功";
            return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg , 'data'=> $data , 'sexs'=> $sexs, 'competences'=> $competences, 'countryCodes'=> $countryCodes]);
        }else{
            $statusCode = 401;
            $msg = "此會員不存在";
            return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg]);
        }


    }
    //會員修改資料表單
    public function memberProfileChange(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $token = $request -> token;
        /*$users = DB::select('select * from users where token = ? and active = 1', array($token));*/
        $users = DB::select('select * from member where token = ?', array($token));

        if($users) {
            $uId = $users[0] -> uId;
            $tokenModifyTime = $users[0] -> tokenModifyTime;
            $user = DB::select('select * from member where uId = ?',array($uId));
            $differencetime = (strtotime("now") - $tokenModifyTime);
            // dd($differencetime);
            if ($differencetime > (60*60*24)){
                $statusCode = 0;
                $msg = "已超過登入時間，請重新登入";
                return response()->json(['statusCode'=> $statusCode , 'msg'=>$msg]);
            }
        }else{
            $statusCode = 401;
            $msg = "此會員不存在";
            return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg]);
        }
        $email = ($request -> email);
        $name = ($request -> name);
        $sex = ($request -> sex);
        $birthday = ($request -> birthday);
        $age = ($request -> age);
        // dd($referrer);


        if (empty($email)) {
            $email = $users[0] -> email;
        }/*else{
            DB::update('update users set email = ? where uId = ?', array($email,$uId));
            $statusCode = 200;
            $msg = "更改會員資料成功";
            return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg]);
        }*/
        
        if (empty($name)) {
            $name = $users[0] -> name;
        }

        $iage = 0;
        if (empty($birthday)) {
            $birthday = $users[0] -> birthday;
        }
        if (empty($iage)){
            $year = date('Y', strtotime($birthday));
            $month = date('m', strtotime($birthday));
            $day = date('d', strtotime($birthday));

            $now_year = date('Y');
            $now_month = date('m');
            $now_day = date('d');

            if ($now_year > $year) {
                $iage = $now_year - $year - 1;
                if ($now_month > $month) {
                    $iage++;
                } else if ($now_month == $month) {
                    if ($now_day >= $day) {
                        $iage++;
                    }
                }
            }
        }

        // dd($nickName);
        if (empty($name)) {
            $statusCode = 401;
            $msg = "姓名不能為空請填寫姓名";
            return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg]);
        }
        
        $validator = Validator::make($request->input(), 
        [
            'name' => 'required|max:30',
            //'email' => 'required|max:50|email|unique:users,email',
            //'email' => Rule::unique('email')->ignore($user->uId),
           
        ],[
            'name.required'=>'請填寫「姓名」',
            'name.max'=>'姓名最多30字',
            'email.required'=>'請填寫「電子郵件」',
            //'email.max'=>'「電子郵件」最多50字',
            //'email.email'=>'「電子郵件」格式不符',
            //'email.unique'=>'「電子郵件」已經註冊',

        ]);

        
        // dd($validator->errors()->messages());
        //錯誤處裡
        if($validator->fails()){
            $errorArray = array();
            $errorMsg = array();

            $errorArray = $validator->errors()->messages();
            $errorArrayKeys = array_keys($errorArray);

            $errorArrayCount = (int)count($errorArray);
            for ($i=0; $i < $errorArrayCount; $i++) {
                //看該筆錯誤訊息有幾個，如果超過兩個以上要用逗號接起來
                $tmpErrorArrayCount = (int)count($errorArray[$errorArrayKeys[$i]]);
                if ($tmpErrorArrayCount > 1) {
                    $tmpMsg = "";
                    for ($j=0; $j < $tmpErrorArrayCount; $j++) {
                        if ($j == 0) {
                            $tmpMsg = $errorArray[$errorArrayKeys[$i]][$j];
                        }else {
                            $tmpMsg = $tmpMsg . "，" . $errorArray[$errorArrayKeys[$i]][$j];
                        }
                    }
                    // $tmpArr = array($errorArrayKeys[$i] => $tmpMsg);
                    $tmpArr = array('error' => $errorArrayKeys[$i] , 'msg' => $tmpMsg);
                }else {
                    // $tmpArr = array($errorArrayKeys[$i] => $errorArray[$errorArrayKeys[$i]][0]);
                    $tmpArr = array('error' => $errorArrayKeys[$i] , 'msg' => $errorArray[$errorArrayKeys[$i]][0]);
                }
                array_push($errorMsg,$tmpArr);
                $tmpArr = array();
            }
            // dd($errorMsg);
            $statusCode = 402;
            return response()->json(['statusCode'=> $statusCode , 'errMsg'=> $errorMsg , 'msg'=> "格式錯誤"]);
            //return redirect()->back()->withErrors($validator->errors()->messages());
        }
        else {
            DB::update('update member set  email = ?, name = ?, birthday = ?,  sex = ?,  age = ? where uId = ?', array($email,$name,$birthday,$sex,$iage,$uId));
            $statusCode = 201;
            $msg = "更改會員資料成功";
            return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg]);
        } 
    }
    //會員密碼修改資料表單
    public function memberPasswordChange(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $token = $request -> token;
        /*$users = DB::select('select * from member where token = ? and active = 1', array($token));*/
        $users = DB::select('select * from member where token = ? ', array($token));
        
        if($users) {
            $uId = $users[0] -> uId;
            $tokenModifyTime = $users[0] -> tokenModifyTime;
            
            $differencetime = (strtotime("now") - $tokenModifyTime);
            // dd($differencetime);
            if ($differencetime > (60*60*24)){
                $statusCode = 0;
                $msg = "已超過登入時間，請重新登入";
                return response()->json(['statusCode'=> $statusCode , 'msg'=>$msg]);
            }
        }else{
            $statusCode = 401;
            $msg = "此會員不存在";
            return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg]);
        }
        $passwordOld = ($request -> passwordOld);
        $passwordNew = ($request -> password);
        $passwordNewCheck = ($request -> passwordNews);
        $passwordNew = Hash::make($passwordNew);
        
        $users = DB::select('select * from member where uId = ?', array($uId));
        if (!empty($passwordOld) && !empty($passwordNew) && !empty($passwordNewCheck)){
            $validator = Validator::make($request->input(), [
                'password' => 'regex:/^(?=.*\d)(?=.*[a-z])(?!.*[^\x00-\xff]).{8,}.*$/',
                'passwordOld' => 'regex:/^(?=.*\d)(?=.*[a-z])(?!.*[^\x00-\xff]).{8,}.*$/',
                'passwordNews' => 'required_with:password|same:password',
            ],[
                'password.regex'=>'「密碼」需包含一個英文及數字，且最少要8位以上',
                'passwordOld.regex'=>'「密碼」需包含一個英文及數字，且最少要8位以上',
                'passwordNews.required_with'=>'請填寫「密碼確認」',
                'passwordNews.same'=>'「密碼確認」與「密碼」不同，請重新輸入',
            ]);
            if($validator->fails()){
                $errorArray = array();
                $errorMsg = array();

                $errorArray = $validator->errors()->messages();
                $errorArrayKeys = array_keys($errorArray);

                $errorArrayCount = (int)count($errorArray);
                for ($i=0; $i < $errorArrayCount; $i++) {
                    $tmpErrorArrayCount = (int)count($errorArray[$errorArrayKeys[$i]]);
                    if ($tmpErrorArrayCount > 1) {
                        $tmpMsg = "";
                        for ($j=0; $j < $tmpErrorArrayCount; $j++) {
                            if ($j == 0) {
                                $tmpMsg = $errorArray[$errorArrayKeys[$i]][$j];
                            }else {
                                $tmpMsg = $tmpMsg . "，" . $errorArray[$errorArrayKeys[$i]][$j];
                            }
                        }
                        $tmpArr = array('error' => $errorArrayKeys[$i] , 'msg' => $tmpMsg);
                    }else {
                        $tmpArr = array('error' => $errorArrayKeys[$i] , 'msg' => $errorArray[$errorArrayKeys[$i]][0]);
                    }
                    array_push($errorMsg,$tmpArr);
                    $tmpArr = array();
                }
                $statusCode =401;
                return response()->json(['statusCode'=> $statusCode , 'msg'=> $errorMsg]);
            }
            if (Hash::check($passwordOld, $users[0] -> password) || $users[0] -> password == md5($passwordNew)) {
                DB::update('update member set password = ? where uId = ?', array($passwordNew,$uId));
                $statusCode = 200;
                $msg = "更改密碼成功";
                return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg]);
            
            }else {
                $statusCode = 401;
                $msg = "舊密碼輸入錯誤";
                return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg]);
            }
        }
        
            
        
    }
}





