<?php

namespace App\Http\Controllers\api\web\member;

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
        $token = $request -> token;

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

            $orders = DB::select('select id, status, order_created_time, pay_created_time, send_time, amount, addressee as receive_name, address, phone from orders where u_id = ? and status > 0', array($uId));
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

            $order = DB::select('select id, status, order_created_time, pay_created_time, send_time, amount, addressee as receive_name, address, phone from orders where u_id = ? and id = ?', array($uId, $o_id));
            $order_products = DB::select('select op.id as opId, p.pId as pId, p.name as name, p.description as description , p.price as price, p.images as images, op.amount as amount from orders as o, order_product as op, product as p where p.pId = op.p_id and o.id = op.o_id and o.u_id = ? and o.id = ?', array($uId, $o_id));

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

    // 完成訂單
    public function orderComplete(Request $request){
        date_default_timezone_set ('Asia/Taipei');

        $datetime = date("Y-m-d H:i:s");
        $token = $request -> token;
        $o_id = $request -> oId;

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

            DB::update('update orders set status = 4 where id = ? and status = 3', [$o_id]);

            return response()->json(['status'=> 200 , 'msg'=> "完成訂單"]);
        }else{
            $statusCode = 401;
            $msg = "驗證錯誤，請重新登入";
            return response()->json(['status'=> $statusCode , 'msg'=> $msg]);
        }
    }

}
