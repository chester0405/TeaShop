<?php

namespace App\Http\Controllers\api\backweb\order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use datetime;
use Illuminate\Support\Facades\Validator;




class orderController extends Controller
{
    // 訂單列表
    public function list(Request $request){
        date_default_timezone_set ('Asia/Taipei');

        $datetime = date("Y-m-d H:i:s");
        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));

        if($admins) {
            $tokenModifyTime = $admins[0] -> adminTokenModifyTime;
            $differencetime = (strtotime("now") - $tokenModifyTime);
            if ($differencetime > (60*60*24)){
                $statusCode = 0;
                $msg = "已超過登入時間，請重新登入";
                return response()->json(['statusCode'=> $statusCode , 'msg'=>$msg]);
            }

            $orders = DB::select('select o.id, o.status, m.name as buy_name, o.order_created_time, o.pay_created_time, o.send_time, o.amount, o.addressee as receive_name, o.address, o.phone from orders as o, member as m where status > 1 and o.u_id = m.uId');
            if (!$orders) {
                return response()->json(['status'=> 401 , 'msg'=> "查無近期訂單資料", 'datas' => []]);
            }

            return response()->json(['status'=> 200 , 'msg'=> "訂單列表獲取成功", 'datas' => $orders]);
        }else{
            $statusCode = 401;
            $msg = "驗證錯誤，請重新登入";
            return response()->json(['status'=> $statusCode , 'msg'=> $msg]);
        }
    }

    // 獲取訂單資料
    public function get(Request $request){
        date_default_timezone_set ('Asia/Taipei');

        $datetime = date("Y-m-d H:i:s");
        $token = $request -> token;
        $o_id = $request -> oId;

        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));

        if($admins) {
            $tokenModifyTime = $admins[0] -> adminTokenModifyTime;
            $differencetime = (strtotime("now") - $tokenModifyTime);
            if ($differencetime > (60*60*24)){
                $statusCode = 0;
                $msg = "已超過登入時間，請重新登入";
                return response()->json(['statusCode'=> $statusCode , 'msg'=>$msg]);
            }

            $order = DB::select('select o.id, o.status, m.name, o.order_created_time, o.pay_created_time, o.send_time, o.amount, o.addressee as receive_name, o.address, o.phone from orders as o, member as m where o.u_id = m.uId and o.id = ?', array($o_id));
            $order_products = DB::select('select op.id as opId, p.pId as pId, p.name as name, p.description as description , p.price as price, p.images as images, op.amount as amount from orders as o, order_product as op, product as p where p.pId = op.p_id and o.id = op.o_id and o.id = ?', array($o_id));

            if (!$order || !$order_products) {
                return response()->json(['status'=> 401 , 'msg'=> "訂單詳細資料查詢錯誤", 'datas' => []]);
            }

            return response()->json(['status'=> 200 , 'msg'=> "訂單列表獲取成功", 'order_detail' => $order, 'order_products' => $order_products]);
        }else{
            $statusCode = 401;
            $msg = "驗證錯誤，請重新登入";
            return response()->json(['status'=> $statusCode , 'msg'=> $msg]);
        }
    }

    // 寄送確認
    public function send_succeful(Request $request){
        date_default_timezone_set ('Asia/Taipei');

        $datetime = date("Y-m-d H:i:s");
        $token = $request -> token;
        $o_id = $request -> oId;

        $adminToken = (string)($request -> adminToken);
        $admins = DB::select('select * from admins where adminToken = ?', array($adminToken));

        if($admins) {
            $tokenModifyTime = $admins[0] -> adminTokenModifyTime;
            $differencetime = (strtotime("now") - $tokenModifyTime);
            if ($differencetime > (60*60*24)){
                $statusCode = 0;
                $msg = "已超過登入時間，請重新登入";
                return response()->json(['statusCode'=> $statusCode , 'msg'=>$msg]);
            }

            DB::update('update orders set status = 3, send_time = ? where id = ? and status = 2', [$datetime, $o_id]);
            return response()->json(['status'=> 200 , 'msg'=> "訂單狀態變更為已運送成功"]);
        }else{
            $statusCode = 401;
            $msg = "驗證錯誤，請重新登入";
            return response()->json(['status'=> $statusCode , 'msg'=> $msg]);
        }
    }
}
