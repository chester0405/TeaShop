<?php

namespace App\Http\Controllers\api\web\productclassView;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
//use Hash;
use Illuminate\Support\Facades\DB;
//use \DB;
use datetime;
//use Validator;
use Illuminate\Support\Facades\Validator;




class productclassViewController extends Controller
{
    // use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /*// 瀏覽商品分類(前站)
    public function productclassViewFront(Request $request){
      date_default_timezone_set ('Asia/Taipei');

      // 顯示商品分類的名稱
      $query_pdcname = DB::select('select pcId, name, status from productclass ');
      $statuss = ('status = 1 顯示 ， status = 0 不顯示');
      //dd($query_pdcname);
      return response()->json(['status' => 200, 'msg' => 'Success viewdata','statuss' => $statuss, 'datas' => $query_pdcname]);

    }*/

    public function productclassViewFront(Request $request){
      date_default_timezone_set ('Asia/Taipei');
      $datas = $request->all();


      $productclass = $request -> productclass;
      $product = DB::select('select * from product where productclass = ?', array($productclass));

      if($productclass) {

          $productclasss = DB::select('select * from productclass');
          $producttypes = DB::select('select * from producttype');
          $news = ('1=顯示 0=不顯示');

          $data = DB::select('select * from product where productclass = ?', array($productclass));
          $statusCode = 200;
          $msg = "獲取資料成功";
          return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg , 'data'=> $data, 'productclass'=> $productclasss, 'producttypes'=> $producttypes, 'news'=> $news]);
      }else{
          $productclasss = DB::select('select * from productclass');
          $producttypes = DB::select('select * from producttype');
          $query_pd = DB::select('select * from product');
          $news = ('1=顯示 0=不顯示');
          $msg = "獲取資料成功";
          $statusCode = 200;


          return response()->json(['statusCode' => $statusCode, 'msg' => $msg, 'data' => $query_pd, 'productclass'=> $productclasss, 'producttypes'=> $producttypes, 'news'=> $news]);
      }


  }
    public function productInfo(Request $request){
        date_default_timezone_set ('Asia/Taipei');
        $datas = $request->all();

        $pId = $request -> pId;
        $product = DB::select('select * from product where pId = ?' , array($pId));

            if($product) {
                $pId = $product[0] -> pId;
                $name = $product[0] -> name;
                $price = $product[0] -> price;
                $description = $product[0] -> description;
                $stock = $product[0] -> stock;
                $images = $product[0] -> images;


                $data = DB::select('select pId, name, price, description, stock, images from product where pId = ?', array($pId));
                $statusCode = 200;
                $msg = "獲取資料成功";
                return response()->json(['statusCode'=> $statusCode , 'msg'=> $msg , 'data'=> $data]);
            }else{

            }


    }

    public function productHot(Request $request){
        date_default_timezone_set ('Asia/Taipei');

        // 顯示商品的資料
        $query_pd = DB::select('select pId, name, images, price from product where pId <=4 ');
        $status = 200;
        $msg = "獲取資料成功";

        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $query_pd]);

    }


}
