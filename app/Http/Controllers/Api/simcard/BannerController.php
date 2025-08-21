<?php

namespace App\Http\Controllers\Api\simcard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    public function list(Request $request)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);

        $params = $_POST;
        $data = array();
        $whereQuery = array();

        if(!empty($_POST["product_id"])) $whereQuery[] = ("sc_product_promotion.product_id = '".addslashes($params["product_id"])."'");

        $whereQuery = array_values(array_diff($whereQuery,array("")));

        $query = DB::select(DB::raw("SELECT sc_product_promotion.*, inv_products.`name` AS product_name FROM sc_product_promotion LEFT JOIN inv_products ON sc_product_promotion.product_id = inv_products.id ".(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")." ORDER BY sc_product_promotion.id DESC LIMIT ".$params['start'].", ".$params['length']));

        $queryCount = DB::select(DB::raw("SELECT count(sc_product_promotion.id) as total FROM sc_product_promotion ".(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")));

        $i = ($params['start'] + 1);;

        foreach($query as $row)
        {
            $data[] = array(
                $i++,
                $row->title,
                $row->product_name,
                $row->description,
                $row->id,
                $row->status,
                $row->id."||".($row->status=='inactive'?'active':'inactive'),
            );
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$data,
            'draw'=>$params['draw'],
            'recordsFiltered'=>$queryCount[0]->total,
            'recordsTotal'=>$queryCount[0]->total
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
            'title' => 'required|string|min:2|max:200',
            'product_id' => 'required|string',
            'description' => 'required|string',
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

        $id = uniqid('').bin2hex(random_bytes(8));

        DB::connection('mysql')->table('sc_product_promotion')->insert(array(
            'id'=>$id,
            'product_id' => $data["product_id"],
            'title' => $data["title"],
            'description' => $data["description"],
            'size'=>'full_width',
            'file_name'=>'',
            'status' => "active",
            'created_by'=>$profile_details["user_id"],
            'created_at'=>date("Y-m-d H:i:s")
        ));

        if(!empty($request->file)) {
            $fileName = "simcard_banner_" . time() . '.' . $request->file->extension();
            $request->file->move(base_path('public/assets/banners'), $fileName);

            DB::connection('mysql')->table("sc_product_promotion")
                ->where(array(
                    'id' => $id
                ))->update(array(
                    'file_name'=>'assets/banners/'.$fileName
                ));
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function update(Request $request, $promo_id)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);
        $data = $_POST;

        $mnp_operators_dta = DB::connection('mysql')->table('sc_product_promotion')->select("id")->where('id', $promo_id)->first();

        if (!$mnp_operators_dta) {
            $errorMessages = array('Banner Not Exists');

            return response()->json(array(
                'right_now' => date("Y-m-d H:i:s"),
                'timestamp' => time(),
                'success' => false,
                'message' => $errorMessages
            ), 406);
        }

        $updateDtaa = array();

        if(!empty($data['title'])) $updateDtaa['title'] = $data['title'];
        if(!empty($data['description'])) $updateDtaa['description'] = $data['description'];
        if(!empty($data['status'])) $updateDtaa['status'] = $data['status'];

        if(!empty($updateDtaa))
        {
            DB::connection('mysql')->table("sc_product_promotion")
                ->where(array(
                    'id' => $promo_id
                ))->update($updateDtaa);
        }

        if(!empty($request->file)) {
            $fileName = "simcard_banner_" . time() . '.' . $request->file->extension();
            $request->file->move(base_path('public/assets/banners'), $fileName);

            DB::connection('mysql')->table("sc_product_promotion")
                ->where(array(
                    'id' => $promo_id
                ))->update(array(
                    'file_name'=>'assets/banners/'.$fileName
                ));
        }


        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function info(Request $request, $promo_id)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);
        $data = $_POST;

        $mnp_operators_dta = DB::connection('mysql')->table('sc_product_promotion')->where('id', $promo_id)->first();

        if (!$mnp_operators_dta) {
            $errorMessages = array('Banner Not Exists');

            return response()->json(array(
                'right_now' => date("Y-m-d H:i:s"),
                'timestamp' => time(),
                'success' => false,
                'message' => $errorMessages
            ), 406);
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$mnp_operators_dta
        ), 200);
    }
}
