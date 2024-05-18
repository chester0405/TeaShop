<?php

namespace App\Http\Controllers\api\backweb\backProductClass;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
//use Hash;
use Illuminate\Support\Facades\DB;
// use \DB;
use datetime;
// use Validator;
use Illuminate\Support\Facades\Validator;




class productclassController extends Controller
{
    // use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // 新增商品分類
    public function productclassInsert(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $datas = $request->all();
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));
        
        // 接資料()
        $name = $datas['name'];
        $status = $datas['status'];
        $pcdate = $datas['pcdate'];
        $adminToken = $datas['adminToken'];
        $createTime = date("Y-m-d H:i:s");

        // 確認是否登入
        if(!$admins)
        {   
            return response()->json(['statusCode' => 400, 'msg' => '請重新登入帳號!']);
        }
        
        $query_name_select = DB::select('select name from productclass where name = ?',[$name]);
        //dd($query_name_select);

        // 檢查商品類別名稱
        if($name)
        {   
            if(!empty($query_name_select))
            {
                return response()->json(['statusCode' => 400, 'msg' => '請勿重複輸入相同商品類別名稱!']);
            }else
            {
                // 新增商品分類名稱
                $query_insertpdclass = DB::insert('insert into productclass (name, status, pcdate) values (?, ?, ?)', [$name, $status, $createTime]);
                //dd($query_insertpdclass);
                return response()->json(['statusCode' => 200, 'msg' => '新增產品類別資料成功', 'data' => $query_insertpdclass]);
            }
            
            
        }else
        {
            return response()->json(['statusCode' => 400, 'msg' => '請輸入需新增的商品資料!']);
        }
        
    }

    // 瀏覽商品分類(後站)
    public function productclassView(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $datas = $request->all();
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));
        
        // 接資料()
        $adminToken = $datas['adminToken'];

        // 確認是否登入
        if(!$admins)
        {
            return response()->json(['statusCode' => 400, 'msg' => '請重新登入帳號!']);
        }

        // 顯示商品分類的名稱
        $query_pdcname = DB::select('select pcId,name,status from productclass');

        return response()->json(['statusCode' => 200, 'msg' => '成功查看商品分類資料', 'data' => $query_pdcname]);
        
    }

    

    // 修改商品分類
    public function productclassEdit(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $datas = $request->all();
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));
        
        // 接資料()
        $pcId = $datas['pcId'];
        $name = $datas['name'];
        $status = $datas['status'];
        $pcdate = $datas['pcdate'];
        $adminToken = $datas['adminToken'];
        $createTime = date("Y-m-d H:i:s");


        // 確認是否登入
        if(!$admins)
        {   
            return response()->json(['statusCode' => 400, 'msg' => '請重新登入帳號!']);
        }

        // 搜尋商品類別ID
        $query_pcId = DB::select('select * from productclass where pcId = ?',[$pcId]);
        //dd($query_pcId);

        if($query_pcId)
        {     
            // 更新商品分類名稱
            $query_updatepdclass_name = DB::update('update productclass set name = ? where pcId = ?', [$name,$pcId]);

            // 更新商品分類上架的狀態
            $query_updatepdclass_status = DB::update('update productclass set status = ? where pcId = ?', [$status,$pcId]);

            // 更新商品分類上架時間
            $query_updatepdclass_pcdate = DB::update('update productclass set pcdate = ? where pcId = ?', [$createTime,$pcId]);

            return response()->json(['statusCode' => 200, 'msg' => '更新商品分類資料成功']);
        
        }else{
            return response()->json(['statusCode' => 400, 'msg' => '請重新輸入商品類別名稱!!']);
        }
    }

    //刪除商品分類
    public function productclassDelete(Request $request)
    {
        date_default_timezone_set ('Asia/Taipei');
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));
        if ($admins) {
            $user_admin_permission = $admins[0] -> admin_permission;
            $adminPermission = DB::select('select * from admin_permission where id = ?', array($user_admin_permission));
            $productclass_delete = $adminPermission[0] -> productclass_delete;
            if ($productclass_delete == 0) {
                $statusCode = 401;
                $msg = "獲取頁面資料錯誤，將自動導回首頁";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
            }elseif ($productclass_delete == 1) {
                $pcId = $request -> pcId;
                $productclass = DB::select("select * from productclass where pcId = ?", array($pcId));
                $name = $request -> name;
                DB::delete('delete from productclass where pcId = ? ', array($pcId));
                $statusCode = 201;
                $msg = "刪除商品資料成功";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg, 'pcId'=> $pcId ]);
            }
        }else{
            $statusCode = 401;
            $msg = "請重新登入";
            return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
        }
        
    }

    // 新增茶葉種類
    public function producttypeInsert(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $datas = $request->all();
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));
        
        // 接資料()
        $name = $datas['name'];
        $status = $datas['status'];
        $ptdate = $datas['ptdate'];
        $adminToken = $datas['adminToken'];
        $createTime = date("Y-m-d H:i:s");

        // 確認是否登入
        if(!$admins)
        {   
            return response()->json(['statusCode' => 400, 'msg' => '請重新登入帳號!']);
        }
        
        $query_name_select = DB::select('select name from producttype where name = ?',[$name]);
        //dd($query_name_select);

        // 檢查商品類別名稱
        if($name)
        {   
            if(!empty($query_name_select))
            {
                return response()->json(['statusCode' => 400, 'msg' => '請勿重複輸入相同商品類別名稱!']);
            }else
            {
                // 新增商品分類名稱
                $query_insertptclass = DB::insert('insert into producttype (name, status, ptdate) values (?, ?, ?)', [$name, $status, $createTime]);
                //dd($query_insertpdclass);
                return response()->json(['statusCode' => 200, 'msg' => '新增商品茶種成功', 'data' => $query_insertptclass]);
            }
            
            
        }else
        {
            return response()->json(['status' => 400, 'msg' => '請輸入需新增的商品資料!']);
        }
    }


    // 瀏覽商品分類(後站)
    public function producttypeView(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $datas = $request->all();
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));
        
        // 接資料()
        $adminToken = $datas['adminToken'];

        // 確認是否登入
        if(!$admins)
        {
            return response()->json(['statusCode' => 400, 'msg' => '請重新登入帳號!']);
        }

        // 顯示商品分類的名稱
        $query_pdcname = DB::select('select ptId,name,status from producttype');

        return response()->json(['statusCode' => 200, 'msg' => '瀏覽商品茶種成功', 'data' => $query_pdcname]);
        
    }

    

    // 修改商品分類
    public function producttypeEdit(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $datas = $request->all();
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));
        
        // 接資料()
        $ptId = $datas['ptId'];
        $name = $datas['name'];
        $status = $datas['status'];
        $ptdate = $datas['ptdate'];
        $adminToken = $datas['adminToken'];
        $createTime = date("Y-m-d H:i:s");


        // 確認是否登入
        if(!$admins)
        {   
            return response()->json(['statusCode' => 400, 'msg' => '請重新登入帳號!']);
        }

        // 搜尋商品類別ID
        $query_ptId = DB::select('select * from producttype where ptId = ?',[$ptId]);
        //dd($query_pcId);

        if($query_ptId)
        {     
            // 更新商品分類名稱
            $query_producttype_name = DB::update('update producttype set name = ? where ptId = ?', [$name,$ptId]);

            // 更新商品分類上架的狀態
            $query_producttype_status = DB::update('update producttype set status = ? where ptId = ?', [$status,$ptId]);

            // 更新商品分類上架時間
            $query_producttype_ptdate = DB::update('update producttype set ptdate = ? where ptId = ?', [$createTime,$ptId]);

            return response()->json(['statusCode' => 200, 'msg' => '修改商品茶種成功']);
        
        }else{
            return response()->json(['statusCode' => 400, 'msg' => '請重新輸入商品類別名稱!!']);
        }
    }

    //刪除商品分類
    public function producttypeDelete(Request $request)
    {
        date_default_timezone_set ('Asia/Taipei');
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));
        if ($admins) {
            $user_admin_permission = $admins[0] -> admin_permission;
            $adminPermission = DB::select('select * from admin_permission where id = ?', array($user_admin_permission));
            $productclass_delete = $adminPermission[0] -> productclass_delete;
            if ($productclass_delete == 0) {
                $statusCode = 401;
                $msg = "獲取頁面資料錯誤，將自動導回首頁";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
            }elseif ($productclass_delete == 1) {
                $ptId = $request -> ptId;
                $producttype = DB::select("select * from producttype where ptId = ?", array($ptId));
                $name = $request -> name;
                DB::delete('delete from producttype where ptId = ? ', array($ptId));
                $statusCode = 201;
                $msg = "刪除商品茶種資料成功";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg, 'ptId'=> $ptId ]);
            }
        }else{
            $statusCode = 401;
            $msg = "請重新登入";
            return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
        }
        
    }

       
}