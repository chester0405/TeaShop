<?php

namespace App\Http\Controllers\api\backweb\backLogin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use \DB;
use datetime;
use Validator;


class backLoginController extends Controller
{
    // use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //後台登入
    public function backLogin(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $account = (int)($request -> account);
        $password= (string)($request -> password);
        $admin = DB::select('select * from admins where account = ?', array($account));
        if($admin) {
            if (Hash::check($password, $admin[0] -> password)){
                $adminToken = $admin[0] -> adminToken;
                $id = $admin[0] -> id;
                $adminToken = Hash::make('plat'. $admin[0] -> id. $admin[0] -> account. $admin[0] -> name.  strtotime("now"). 'form');
                $nowtime = strtotime(date("Y-m-d H:i:s"));
                DB::update('update admins set adminToken = ? , adminTokenModifyTime = ? where id = ?', array($adminToken,$nowtime,$id));
                $name = $admin[0] -> name;
                $admin_permissions = $admin[0] -> admin_permission; 
                $admin_permission = DB::select('select * from admin_permission where id = ?', array($admin_permissions));
                $statusCode = 203;
                $msg = $admin[0] -> name . "！您好～" . "已成功登入";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg , 'name' => $name, 'adminToken'=>$adminToken ,  'admin_permission'=> $admin_permission]  );
            }else{
                $statusCode = 401;
                $msg = "帳號或密碼輸入錯誤，請重新登入";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
            }
        }else{
            $statusCode = 401;
            $msg = "此用戶不存在，請重新登入";
            return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
            //dd($admin);
        }
    }

    // use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //顯示會員資料
    public function backShowMember(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));
        if ($admins) {
            $user_admin_permission = $admins[0] -> admin_permission;
            $adminPermission = DB::select('select * from admin_permission where id = ?', array($user_admin_permission));
            $member_view = $adminPermission[0] -> member_view;
            if ($member_view == 0) {
                $statusCode = 401;
                $msg = "獲取頁面資料錯誤，將自動導回首頁";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
            }elseif ($member_view == 1) {
                $member = DB::select("select U.uId, U.name, UP.role as `competence`, U.email, C.code AS `countryCode`, U.phone, U.sex, U.birthday, U.age,DATE_FORMAT(FROM_UNIXTIME(`createTime`), '%Y-%m-%d %H:%i:%s') AS `createTime` from member U, countrycode C, admin_permission UP WHERE U.countryCode = C.id AND U.competence = UP.id");
                //$createTime = date("Y-m-d H:i:s", $users[0] -> createTime);
                $statusCode = 201;
                $msg = "獲取資料成功";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg , 'member'=> $member]);
            }
        }else{
            $statusCode = 401;
            $msg = "請重新登入";
            return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
        }
    }

    //讀取修改會員資料
    public function backLoadMember(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));
        if ($admins) {
            $user_admin_permission = $admins[0] -> admin_permission;
            $adminPermission = DB::select('select * from admin_permission where id = ?', array($user_admin_permission));
            $member_update = $adminPermission[0] -> member_update;
            if ($member_update == 0) {
                $statusCode = 401;
                $msg = "獲取頁面資料錯誤，將自動導回首頁";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
            }elseif ($member_update == 1) {
                $uId = $request -> uId;
                $users = DB::select('select * from member where uId = ?', array($uId));
               
                if($users) {
                    $uId = $users[0] -> uId; 
                    $name=  $users[0] -> name;
                    $competence = $users[0] -> competence;
                    $email = $users[0] -> email;
                    $countryCode = $users[0] -> countryCode;
                    $phone = $users[0] -> phone;
                    $sex = $users[0] -> sex;
                    $birthday = $users[0] -> birthday;
                    $age = $users[0] -> age;
                    
                    $countryCodes = DB::select('select * from countrycode');
                    $sexs = "性別；0=未填寫、1=男、2=女、3=其他";
                    $competences = "狀態 ; 0=註銷、1=一般會員、2=服務員、3=廠商、4=管理員";
                    $data = array('uId' => $uId ,'name' => $name ,'competence' => $competence , 'email' => $email , 'countryCode' => $countryCode ,'phone' => $phone ,'sex' => $sex ,'birthday' => $birthday,'age' => $age);
                    $statusCode = 201;
                    $msg = "獲取資料成功";
                    return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg , 'data'=> $data, 'sexs'=> $sexs, 'competences'=> $competences, 'countryCodes'=> $countryCodes]);
                }else{
                    $statusCode = 401;
                    $msg = "此會員不存在";
                    return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg]);
                }
            }
        }else{
            $statusCode = 401;
            $msg = "請重新登入";
            return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
        }
        
    }


    //管理員修改會員資料
    public function backupdateMember(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));
        if ($admins) {
            $user_admin_permission = $admins[0] -> admin_permission;
            $adminPermission = DB::select('select * from admin_permission where id = ?', array($user_admin_permission));
            $member_update = $adminPermission[0] -> member_update;
            if ($member_update == 0) {
                $statusCode = 401;
                $msg = "獲取頁面資料錯誤，將自動導回首頁";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
            }elseif ($member_update == 1) {
                $uId = $request -> uId;
                $users = DB::select('select * from member where uId = ?', array($uId));
                $email = ($request -> email);
                $name = ($request -> name);
                $sex = ($request -> sex);
                $birthday = ($request -> birthday);
                
                

                if (empty($email)) {
                    $email = $users[0] -> email;
                }
                
                if (empty($name)) {
                    $name = $users[0] -> name;
                }

                if (empty($sex)) {
                    $sex = $users[0] -> sex;
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

                if (empty($name)) {
                    $statusCode = 401;
                    $msg = "姓名不能為空請填寫姓名";
                    return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg]);
                }
                $validator = Validator::make($request->input(), [
                    'name' => 'required|max:30',
                    'email' => 'required|max:50|email',
                ],[
                    'name.required'=>'請填寫「姓名」',
                    'name.max'=>'姓名最多30字',
                    'email.required'=>'請填寫「電子郵件」',
                    'email.max'=>'「電子郵件」最多50字',
                    'email.email'=>'「電子郵件」格式不符',
                ]);
            }
        }else{
            $statusCode = 401;
            $msg = "請重新登入";
            return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
        }
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
            DB::update('update member set  email = ?, name = ?, birthday = ?, sex = ?, age = ? where uId = ?', array($email,$name,$birthday,$sex,$iage,$uId));
            $statusCode = 201;
            $msg = "管理員更改會員資料成功";
            return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg]);
        } 
    }

    //刪除會員
    public function backdeleteMember(Request $request)
    {
        date_default_timezone_set ('Asia/Taipei');
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));
        if ($admins) {
            $user_admin_permission = $admins[0] -> admin_permission;
            $adminPermission = DB::select('select * from admin_permission where id = ?', array($user_admin_permission));
            $member_delete = $adminPermission[0] -> member_delete;
            if ($member_delete == 0) {
                $statusCode = 401;
                $msg = "獲取頁面資料錯誤，將自動導回首頁";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
            }elseif ($member_delete == 1) {
                $uId = $request -> uId;
                $member = DB::select("select * from member where uId = ?", array($uId));
                $name = $request -> name;
                DB::delete('delete from member where uId = ? ', array($uId));
                $statusCode = 201;
                $msg = "刪除資料成功";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
            }
        }else{
            $statusCode = 401;
            $msg = "請重新登入";
            return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
        }
        
    }

}