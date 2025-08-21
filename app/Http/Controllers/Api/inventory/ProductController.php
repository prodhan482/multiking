<?php

namespace App\Http\Controllers\Api\inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use function base_path;
use function response;

class ProductController extends Controller
{
    public function list(Request $request)
    {
        //$params = json_decode($request->getContent(), true);

        $params = $_POST;

        $query = DB::connection('mysql')->table('inv_products')->orderBy("updated_at", "desc")->limit(50);
        //->where('status', 'enabled');

        /*foreach ($params as $key => $value)
        {
            if(!empty($value))
                $query->where($key, 'like', $value."%");
        }*/
        //$g = $query->toSql();
        $result = $query->get();

        $data = array();

        foreach($result as $key => $value)
        {
            $data[] = array(
                ($key + 1),
                $value->name,
                ucwords(implode(" ", explode("_", $value->type))),
                "&euro; ".$value->price,
                date("m/d/Y", strtotime($value->created_at)),
                $value->id
            );
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$data
        ), 200);
    }

    public function create(Request $request)
    {
        $token = $request->header('Authorization');
        $data = $_POST;//json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
            'name' => 'required|string|min:2|max:200',
            'type' => 'required|string',
            'price' => 'required|string',
        ]);

        if ($validator->fails()) {

            foreach(json_decode(json_encode($validator->messages())) as $key => $value)
            {
                foreach($value as $v)
                {
                    $errorMessages[] = $v;
                }
            }

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $product_id = uniqid('').bin2hex(random_bytes(8));

        DB::connection('mysql')->table('inv_products')->insert(array(
            'id'=>$product_id,
            'name' => $data['name'],
            'type' => $data['type'],
            'price' => $data['price'],
            'created_by' => $profile_details['user_id'],
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function info(Request $request, $product_id)
    {
        $product_dta = DB::connection('mysql')->table('inv_products')->where('id', $product_id)->first();

        if(!$product_dta)
        {
            $errorMessages = array('Product Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'data'=>$product_dta,
        ), 200);
    }

    public function update(Request $request, $product_id)
    {
        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $product_dta = DB::connection('mysql')->table('inv_products')->select("id")->where('id', $product_id)->first();

        if(!$product_dta)
        {
            $errorMessages = array('product Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $info = array();
        foreach($data as $key => $value){
            if(!empty($value))
                $info[$key] = $value;
        }

        DB::connection('mysql')->table("inv_products")
            ->where(array(
                'id'=>$product_id
            ))->update($info);

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }
}
