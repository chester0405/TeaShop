<?php

namespace App\Http\Controllers\api\web\cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use datetime;
use Illuminate\Support\Facades\Validator;




class cartController extends Controller
{

    // 購物車列表
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

            $order = DB::select('select * from orders where u_id = ? and status = 0', array($uId));
            if (!$order) {
                return response()->json(['status'=> 401 , 'msg'=> "購物車無任何商品", 'datas' => []]);
            }
            $o_id = $order[0] -> id;

            $order_products = DB::select('select op.id as opId, p.pId as pId, p.name as name, p.description as description , p.price as price, p.images as images, op.amount as amount from orders as o, order_product as op, product as p where p.pId = op.p_id and o.id = op.o_id and o.u_id = ? and o.id = ?', array($uId, $o_id));

            if (!$order_products) return response()->json(['status'=> 401 , 'msg'=> "購物車無任何商品", 'datas' => $order_products]);

            return response()->json(['status'=> 200 , 'msg'=> "購物車資料獲取成功", 'datas' => $order_products]);
        }else{
            $statusCode = 401;
            $msg = "您尚未登入，無法使用購物車";
            return response()->json(['status'=> $statusCode , 'msg'=> $msg]);
        }
    }

    // 更新商品數量
    public function update_product_amount(Request $request){
        date_default_timezone_set ('Asia/Taipei');

        $datetime = date("Y-m-d H:i:s");
        $token = $request -> token;
        $opId = $request -> opId;
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

            $order_products = DB::select('select * from order_product where id = ?', array($opId));
            $order = DB::select('select * from orders where u_id = ? and status = 0', array($uId));
            if (!$order) {
                return response()->json(['status'=> 401 , 'msg'=> "購物車無任何商品", 'datas' => []]);
            }
            $o_id = $order[0] -> id;
            if (!$order_products) {
                $order_products = DB::select('select op.id as opId, p.pId as pId, p.name as name, p.description as description , p.price as price, p.images as images, op.amount as amount from orders as o, order_product as op, product as p where p.pId = op.p_id and o.id = op.o_id and o.u_id = ? and o.id = ?', array($uId, $o_id));
                return response()->json(['status'=> 401 , 'msg'=> "查無此商品，無法更改商品數量", 'datas' => $order_products]);
            }
            $product = DB::select('select * from product where pId = ?', array($order_products[0]->p_id));

            if (!$product){
                $order_products = DB::select('select op.id as opId, p.pId as pId, p.name as name, p.description as description , p.price as price, p.images as images, op.amount as amount from orders as o, order_product as op, product as p where p.pId = op.p_id and o.id = op.o_id and o.u_id = ? and o.id = ?', array($uId, $o_id));
                return response()->json(['status'=> 401 , 'msg'=> "查無此商品，無法更改商品數量", 'order_products' => $order_products]);
            }

            $price = $product[0] -> price;

            if ($order_products) {
                $result = DB::update('update order_product set amount = ?, price = ? where id = ?', [$amount, $price, $order_products[0]->id]);
            }else{
                DB::insert('insert into order_product (o_id, p_id, amount, price) values (?, ?, ?, ?)', [$o_id, $pId, $amount, $price]);
            }

            $order_products = DB::select('select op.id as opId, p.pId as pId, p.name as name, p.description as description , p.price as price, p.images as images, op.amount as amount from orders as o, order_product as op, product as p where p.pId = op.p_id and o.id = op.o_id and o.u_id = ? and o.id = ?', array($uId, $o_id));

            if (!$order_products) return response()->json(['status'=> 401 , 'msg'=> "購物車無任何商品", 'datas' => $order_products]);

            return response()->json(['status'=> 200 , 'msg'=> "成功更改商品數量", 'datas' => $order_products]);
        }else{
            $statusCode = 401;
            $msg = "您尚未登入，無法使用購物車";
            return response()->json(['status'=> $statusCode , 'msg'=> $msg]);
        }
    }

    // 購物車刪除商品
    public function delete(Request $request){
        date_default_timezone_set ('Asia/Taipei');

        $datetime = date("Y-m-d H:i:s");
        $token = $request -> token;
        $opId = $request -> opId;

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

            $order_products = DB::select('select * from order_product where id = ?', array($opId));
            $order = DB::select('select * from orders where u_id = ? and status = 0', array($uId));
            if (!$order) {
                return response()->json(['status'=> 401 , 'msg'=> "購物車無任何商品", 'datas' => []]);
            }
            $o_id = $order[0] -> id;
            if (!$order_products) {
                $order_products = DB::select('select op.id as opId, p.pId as pId, p.name as name, p.description as description , p.price as price, p.images as images, op.amount as amount from orders as o, order_product as op, product as p where p.pId = op.p_id and o.id = op.o_id and o.u_id = ? and o.id = ?', array($uId, $o_id));
                return response()->json(['status'=> 401 , 'msg'=> "購物車中查無此商品，無法刪除", 'datas' => $order_products]);
            }

            DB::delete('delete from order_product where id = ?', [$opId]);

            $order_products = DB::select('select op.id as opId, p.pId as pId, p.name as name, p.description as description , p.price as price, p.images as images, op.amount as amount from orders as o, order_product as op, product as p where p.pId = op.p_id and o.id = op.o_id and o.u_id = ? and o.id = ?', array($uId, $o_id));

            if (!$order_products) return response()->json(['status'=> 401 , 'msg'=> "購物車無任何商品", 'datas' => $order_products]);

            return response()->json(['status'=> 200 , 'msg'=> "刪除成功", 'datas' => $order_products]);
        }else{
            $statusCode = 401;
            $msg = "您尚未登入，無法使用購物車";
            return response()->json(['status'=> $statusCode , 'msg'=> $msg]);
        }
    }

    // 購物車建立訂單
    public function createOrder(Request $request){
        date_default_timezone_set ('Asia/Taipei');

        $datetime = date("Y-m-d H:i:s");
        $token = $request -> token;
        $name = $request -> name;
        $address = $request -> address;
        $phone = $request -> phone;

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
                return response()->json(['status'=> 401 , 'msg'=> "您未將商品加入購物車，請確認是否有購買商品", 'datas' => []]);
            }
            $o_id = $order[0] -> id;
            $total_price = DB::select('select SUM(price * amount) as total_price from order_product where o_id = ?', array($o_id));
            DB::update('update orders set status = 2, order_created_time = ?, pay_created_time = ?, amount = ?, addressee = ?, address = ?, phone = ? where id = ? and u_id = ?', [$datetime, $datetime, $total_price[0] -> total_price, $name, $address, $phone, $o_id, $uId]);

            $order_products = DB::select('select op.id as opId, p.pId as pId, p.name as name, p.description as description , p.price as price, p.images as images, op.amount as amount from orders as o, order_product as op, product as p where p.pId = op.p_id and o.id = op.o_id and o.u_id = ? and o.id = ?', array($uId, $o_id));
            $order = DB::select('select id, status, order_created_time, pay_created_time, amount, addressee as receive_name, address, phone from orders where u_id = ? and id = ? and status > 0', array($uId, $o_id));

            if (!$order_products || !$order) return response()->json(['status'=> 401 , 'msg'=> "訂單成立失敗，請至會員中心-訂單管理中查詢訂單"]);

            return response()->json(['status'=> 200 , 'msg'=> "訂單已成立", 'order_detail' => $order, 'order_products' => $order_products]);
        }else{
            $statusCode = 401;
            $msg = "您尚未登入，無法使用購物車";
            return response()->json(['status'=> $statusCode , 'msg'=> $msg]);
        }
    }
}
