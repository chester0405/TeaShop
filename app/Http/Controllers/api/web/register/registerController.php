<?php

namespace App\Http\Controllers\api\web\register;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use \DB;
use datetime;
use Validator;


class registerController extends Controller
{
    // use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
   
    
    //註冊
    public function register(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        
        $email = (string)($request -> email);
        $password= (string)($request -> password);
        $passwordCheck= (string)($request -> passwordCheck);
        $name= (string)($request -> name);
        $countryCode= (string)($request -> countryCode);
        $phone= (int)($request -> phone);
        //$uuId= (string)($request -> uuId);
        //$code = (string)($request -> code);
        $sex= (string)($request -> sex);
        $birthday= (string)($request -> birthday);
        $age= (string)($request -> age);
        $createTime = strtotime(date("Y-m-d H:i:s"));
        $competence = 1;//權限

        //--------UUID----------
        //前7碼數字+最後一個英文字
        //$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        //generate a pin based on 2 * 7 digits + a random character
        //$uuId = mt_rand(1000000, 9999999)
        //. mt_rand(1000000, 9999999)%2
        //. $characters[rand (0,(strlen($characters)))];
        //$string = str_shuffle($uuId);
        //$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        //$str_len=(8);
        //$uuId = '';
        //for ($i = 0; $i < $str_len; $i++) {
        //$uuId .= $characters[rand(0, strlen($characters) - 1)];
        //}
        //---------UUID END-------
       
        if (empty($countryCode)) {
            $statusCode = 401;
            return response()->json(['statusCode'=> $statusCode , 'msg'=> "沒有拿到contrycode"]);
        }

        $validator = Validator::make($request->input(), [
            'email' => 'required|max:50|email',
            'password' => 'required|regex:/^(?=.*\d)(?=.*[a-z])(?!.*[^\x00-\xff]).{8,}.*$/',
            'passwordCheck' => 'required_with:password|same:password',
            'phone' => 'regex:/[0-9]{8,}/',
        ],[
            'nickname.max'=>'姓名最多30字',
            'email.required'=>'請填寫「電子郵件」',
            'email.max'=>'「電子郵件」最多50字',
            'email.email'=>'「電子郵件」格式不符',
            'password.required'=>'請填寫「密碼」',
            'password.regex'=>'「密碼」需包含一個英文及數字，且最少要8位以上',
            'passwordCheck.required_with'=>'請填寫「密碼確認」',
            'passwordCheck.same'=>'「密碼確認」與「密碼」不同，請重新輸入',
            'phone.regex'=>'「電話」格式不符，開頭沒有0',
            

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
        
        //到此代表格式錯誤開始註冊流程
        $password = Hash::make($password);
        $users = DB::select('select * from member where phone = ?', array($phone));
        if(empty($users)){
            $users = DB::select('select * from member where email = ?', array($email));
            if(empty($users)){
                DB::insert('insert into member (email, password, name, phone, createTime, competence, countryCode, birthday, sex) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', array($email, $password, $name,  $phone, $createTime, $competence, $countryCode, $birthday, $sex));
                $users = DB::select('select * from member where phone = ?', array($phone));
               //$users['uId'];
                $uId = $users[0] -> uId;
               // DB::insert('insert into referrer_code (uId) values (?)', array($uId));
                //$referrer_code = DB::select('select * from referrer_code where uId = ?' , array($uId));
                //$codes = $referrer_code[0] -> id;
                //dechex($codes);
                //$code16 = dechex($codes);
                //DB::update('update referrer_code set code = ? where id = ?', array($code16,$codes));
                $countryCodes = DB::select('select * from countrycode');
                $data = array('countrycodes' => $countryCodes );
                $statusCode = 201;
                $msg = "註冊成功";
                return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg, 'data'=> $data]);
                
            }else {
                $statusCode = 401;
                $msg = "此email已被使用，請更換其他email或不填寫email直接註冊";
                return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg]);  
            }     
        }else {
            $statusCode = 401;
            $msg = "此手機號碼已被使用，請更換其他電話進行註冊";
            return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg]);
        }
        
    }

}