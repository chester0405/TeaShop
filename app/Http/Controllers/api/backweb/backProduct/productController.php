<?php

namespace App\Http\Controllers\api\backweb\backProduct;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
//use Hash;
use Illuminate\Support\Facades\DB;
//use \DB;
use datetime;
//use Validator;
use Illuminate\Support\Facades\Validator;




class productController extends Controller
{

    //瀏覽商品(後端)
    public function productView(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $datas = $request->all();
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));

        //接資料()
        $adminToken = $datas['adminToken'];

        // 確認是否登入
        if(!$admins)
        {
            return response()->json(['statusCode' => 400, 'msg' => '請重新登入帳號!']);
        }

        $producttype = $request -> producttype;
        $product = DB::select('select * from product where producttype = ?', array($producttype));

        if($producttype) {
            $pId = $product[0] -> pId;
            $newest = $product[0] -> newest;
            $productclass=  $product[0] -> productclass;
            $producttype=  $product[0] -> producttype;
            $name = $product[0] -> name;
            $price = $product[0] -> price;
            $description = $product[0] -> description;
            $stock = $product[0] -> stock;
            $remark = $product[0] -> remark;
            $status = $product[0] -> status;
            $purchaser = $product[0] -> purchaser;
            $image = $product[0] -> purchaser;
            $productontime = $product[0] -> purchaser;
            $productclasss = DB::select('select * from productclass');
            $producttypes = DB::select('select * from producttype');

            $data = DB::select('select * from product where producttype = ?', array($producttype));
            $statusCode = 201;
            $msg = "獲取資料成功";
            return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg , 'data'=> $data, 'productclass'=> $productclasss, 'producttypes'=> $producttypes]);
        }else{
            $productclasss = DB::select('select * from productclass');
            $producttypes = DB::select('select * from producttype');
            $query_pd = DB::select('select * from product');

            return response()->json(['statusCode' => 200, 'msg' => 'Success viewdata', 'data' => $query_pd, 'productclasss'=> $productclasss, 'producttypes'=> $producttypes]);
        }


    }
    //新增商品
    public function productInsert(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $datas = $request->all();
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));

        //接資料()

        $newest = $datas['newest'];
        $productclass = $datas['productclass'];
        $producttype = $datas['producttype'];
        $name = $datas['name'];
        $price = $datas['price'];
        $description = $datas['description'];
        $stock = $datas['stock'];
        $remark = $datas['remark'];
        $status = $datas['status'];
        $purchaser = $datas['purchaser'];
        $images = $datas['images'];
        $productontime = $datas['productontime'];
        $adminToken = $datas['adminToken'];
        $createTime = date("Y-m-d H:i:s");

        $dataArray = array();
        if($request->hasFile('images')){
            // return 0;
            $file = request('images');
            //$picstatus = 1;//1是標題圖片 0是內容圖片
            $allowed_extensions = ["png", "jpg", "gif","mp4"];
            //if ($file->getClientOriginalExtension() && !in_array($file->getClientOriginalExtension(), $allowed_extensions)) {
                //return ['error' => 'You may only upload png, jpg , gif or mp4.'];
            //}
            // dd($file->getClientOriginalName());
            //檔案名稱
            $picname = $file->getClientOriginalName();

            //檔案副檔名
            $picname2 = $file->getClientOriginalExtension();
            // 實際儲存的檔案名稱+副檔名
            //$filename = time()  .$picname2;
            $filename = $picname;
            //$filename1 = $picname . $picname2;
            // dd($picname2);
            //檔案預設儲存路徑
            $picDefaultPath = $file->getRealPath();
            //儲存路徑縮寫/images/filename.jpg
            $picaddr = '/images/';
            // 設定要儲存的路徑
            $picSavePath = base_path() . "/public" . $picaddr;
            $picall = $picSavePath . $filename;

            //$ids = $platform_cooperation -> id ;
            //$order = "";

            //Move Uploaded File
            $destinationPath = 'uploads';
            $file->move($picSavePath,$filename);
            $data['data'] = array('path' => $picaddr, 'filename' => $picname, 'Type' => $picname2);
            array_push($dataArray,$data);
            $dataJsonCode = json_encode($data);
            //return $dataArray;
        }
        // 確認是否登入
        if(!$admins)
        {
            return response()->json(['statusCode' => 400, 'msg' => '請重新登入帳號!']);
        }
        // 檢查商品名稱
        if($name)
        {
            // 新增商品分類
            $query_insertpdclass = DB::insert('insert into product (newest, productclass, producttype, name, images, stock, price, status, description, remark, productontime, purchaser) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [$newest, $productclass, $producttype, $name, $dataJsonCode, $stock, $price, $status, $description, $remark, $createTime, $purchaser]);

            //dd($query_insertpdclass);
            return response()->json(['statusCode' => 200, 'msg' => '新增商品資料成功', 'data' => $query_insertpdclass]);
        }else
        {
            return response()->json(['statusCode' => 400, 'msg' => '請輸入需新增的商品資料!']);
        }

    }
    //修改商品
    public function productEdit(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $datas = $request->all();
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));

        //接資料()
        $pId = $datas['pId'];
        $newest = $datas['newest'];
        $productclass = $datas['productclass'];
        $producttype = $datas['producttype'];
        $name = $datas['name'];
        $price = $datas['price'];
        $description = $datas['description'];
        $stock = $datas['stock'];
        $remark = $datas['remark'];
        $status = $datas['status'];
        $purchaser = $datas['purchaser'];
        $productontime = $datas['productontime'];
        $createTime = date("Y-m-d H:i:s");

        // 確認是否登入
        if(!$admins)
        {
            return response()->json(['status' => 400, 'msg' => '請重新登入帳號!']);
        }

        // 搜尋商品ID
        $query_pId = DB::select('select * from product where pId = ?',array ($pId));

        if($query_pId)
        {
            // 更新商品分類名稱
            $query_updatepdclass = DB::update('update product set newest = ?, productclass = ?, producttype = ?, name = ?, stock = ?, price = ?, status = ?, description = ?, remark = ?, purchaser = ?, productontime = ? where pId = ?',  array($newest, $productclass, $producttype, $name, $stock, $price, $status, $description, $remark, $purchaser, $createTime, $pId));

            //dd($query_insertpdclass);
            return response()->json(['status' => 200, 'msg' => '修改商品資料成功', 'data' => $query_updatepdclass]);
        }else
        {
            return response()->json(['status' => 400, 'msg' => '請輸入需更新的商品資料!']);
        }

    }

    //刪除商品
    public function productDelete(Request $request)
    {
        date_default_timezone_set ('Asia/Taipei');
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));
        if ($admins) {
            $user_admin_permission = $admins[0] -> admin_permission;
            $adminPermission = DB::select('select * from admin_permission where id = ?', array($user_admin_permission));
            $product_delete = $adminPermission[0] -> product_delete;
            if ($product_delete == 0) {
                $statusCode = 401;
                $msg = "獲取頁面資料錯誤，將自動導回首頁";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
            }elseif ($product_delete == 1) {
                $pId = $request -> pId;
                $product = DB::select("select * from product where pId = ?", array($pId));
                $name = $request -> name;
                DB::delete('delete from product where pId = ? ', array($pId));
                $statusCode = 201;
                $msg = "刪除商品資料成功";
                return response()->json(['statusCode' => $statusCode , 'msg'=> $msg, 'pId'=> $pId ]);
            }
        }else{
            $statusCode = 401;
            $msg = "請重新登入";
            return response()->json(['statusCode' => $statusCode , 'msg'=> $msg]);
        }

    }

    //上傳檔案

    //新增商品
    public function uploadImage(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $datas = $request->all();
        $adminToken = (string)($request -> adminToken);
        $pId = (string)($request -> pId);

        $admins = DB::select('select * from admins where adminToken = ?', [$adminToken]);

        // 確認是否登入
        if(!$admins)
        {
            return response()->json(['status' => 400, 'msg' => '請重新登入帳號!']);
        }

        $createTime = date("Y-m-d H:i:s");

        $product = DB::select('select * from product where pId = ?', [$pId]);

        if (!$product) {
            return response()->json(['status' => 400, 'msg' => '此商品不存在請稍後再試']);
        }

        if($request->hasFile('images')){
            $file = request('images');
            //$picstatus = 1;//1是標題圖片 0是內容圖片
            $allowed_extensions = ["png", "jpg", "gif","mp4"];
            //if ($file->getClientOriginalExtension() && !in_array($file->getClientOriginalExtension(), $allowed_extensions)) {
                //return ['error' => 'You may only upload png, jpg , gif or mp4.'];
            //}
            // dd($file->getClientOriginalName());
            //檔案名稱
            $picname = $file->getClientOriginalName();

            //檔案副檔名
            $picname2 = $file->getClientOriginalExtension();
            // 實際儲存的檔案名稱+副檔名
            //$filename = time()  .$picname2;
            $filename = $picname;
            //$filename1 = $picname . $picname2;
            // dd($filename);
            //檔案預設儲存路徑
            $picDefaultPath = $file->getRealPath();
            //儲存路徑縮寫/images/filename.jpg
            $picaddr = '/images/';
            // 設定要儲存的路徑
            $picSavePath = base_path() . "/public" . $picaddr;
            $picall = $picSavePath . $filename;

            //Move Uploaded File
            $destinationPath = 'uploads';
            $file->move($picSavePath,$filename);
            $data['data'] = array('path' => $picaddr, 'filename' => $picname, 'Type' => $picname2);
            $dataJsonCode = json_encode($data);
            DB::update('update product set images = ? where pId = ?', [$dataJsonCode, $pId]);

            return response()->json(['status' => 200, 'msg' => '更新商品圖片成功']);
            //return $dataArray;
        }

        return response()->json(['status' => 400, 'msg' => '請輸入需新增的商品資料!']);


    }

}
