<?php

namespace App\Http\Controllers\api\web\productView;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
//use Hash;
use Illuminate\Support\Facades\DB;
//use \DB;
use datetime;
//use Validator;
use Illuminate\Support\Facades\Validator;




class productViewController extends Controller
{
    // use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // 顯示茶種(前端)
    public function productViewFront(Request $request){
        date_default_timezone_set ('Asia/Taipei');

        // 顯示商品的資料
        $query_pd = DB::select('select * from producttype ');
        $statuss = ('status = 1 顯示 ， status = 0 不顯示');
        $statusCode = 200;
        $msg = "獲取資料成功";

        return response()->json(['statusCode' => $statusCode, 'msg' => $msg, 'status' => $statuss, 'data' => $query_pd]);

    }

    public function addCart(Request $request)
    {
        date_default_timezone_set ('Asia/Taipei');

        $datetime = date("Y-m-d H:i:s");
        $token = $request -> token;
        $pId = $request -> pId;
        $amount = $request -> amount;

        $users = DB::select('select * from member where token = ? ', array($token));

        if($users) {
            $uId = $users[0] -> uId;
            $tokenModifyTime = $users[0] -> tokenModifyTime;
            $differencetime = (strtotime("now") - $tokenModifyTime);
            if ($differencetime > (60*60*24)){
                $statusCode = 0;
                $msg = "已超過登入時間，請重新登入";
                return response()->json(['statusCode'=> $statusCode , 'msg'=>$msg]);
            }

            $order = DB::select('select * from orders where u_id = ? and status = 0', array($uId));
            if (!$order) {
                DB::insert('insert into orders (u_id, cart_created_time) values (?, ?)', [$uId, $datetime]);
                $order = DB::select('select * from orders where u_id = ? and status = 0', array($uId));
            }
            $o_id = $order[0] -> id;

            $order_products = DB::select('select * from order_product where o_id = ? and p_id = ?', array($o_id, $pId));
            $product = DB::select('select * from product where pId = ?', array($pId));

            if (!$product) return response()->json(['status'=> 401 , 'msg'=> "查無此商品，請重新加入購物車"]);

            $price = $product[0] -> price;

            if ($order_products) {
                $amount = $order_products[0]->amount + $amount;
                $result = DB::update('update order_product set amount = ?, price = ? where id = ?', [$amount, $price, $order_products[0]->id]);
            }else{
                DB::insert('insert into order_product (o_id, p_id, amount, price) values (?, ?, ?, ?)', [$o_id, $pId, $amount, $price]);
            }

            return response()->json(['status'=> 200 , 'msg'=> "成功加入購物車"]);
        }else{
            $statusCode = 401;
            $msg = "請登入再加入購物車";
            return response()->json(['status'=> $statusCode , 'msg'=> $msg]);
        }
    }

   

}
