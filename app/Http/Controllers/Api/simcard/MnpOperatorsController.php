<?php

namespace App\Http\Controllers\Api\simcard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use function base_path;
use function response;

class MnpOperatorsController extends Controller
{
    public function list(Request $request)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $params = $_POST;

        $whereQuery = array();

        /*if(!empty($_POST["status"])) $whereQuery[] = ("sc_orders.status = '".addslashes($_POST["status"])."'");

        if($profile_details['user_type'] == "super_admin") {
            if(!empty($_POST["store_id"])) $whereQuery[] = ("sc_orders.store_id = '" . addslashes($_POST["store_id"]) . "'");
        }
        else
        {
            $whereQuery[] = "sc_orders.store_id = ".$profile_details['store_vendor_id'];
        }*/

        $whereQuery = array_values(array_diff($whereQuery,array("")));

        $query = DB::select(DB::raw("SELECT * FROM sc_mnp_operator_list ".(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")." ORDER BY FIELD(sc_mnp_operator_list.status, 'enable') DESC, sc_mnp_operator_list.created_at DESC LIMIT ".$params['start'].", ".$params['length']));

        $queryCount = DB::select(DB::raw("SELECT count(sc_mnp_operator_list.id) as total FROM sc_mnp_operator_list ".(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")));

        $data = array();
        $pos = ($params['start'] + 1);

        foreach($query as $row)
        {
            $pTitle = "";
            if(!empty($row->product_id))
            {
                $product_ids = json_decode($row->product_id);
                if(!empty($product_ids))
                {
                    foreach($product_ids as $product)
                    {
                        if(!empty($product) && !empty($product->text)) $pTitle .= (empty($pTitle)?"":", ").$product->text;
                    }
                }
            }

            $data[] = array(
                $pos++,
                $row->title,
                $row->description,
                $pTitle,
                $row->reseller_bonus,
                ($row->status),
                ($row->id."||".($row->status == "enable"?"disable":"enable")),
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
            'title' => 'required|string',
            'product_id' => 'required|string',
            'reseller_bonus' => 'required|string',
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

        $sc_mnp_operator_list = uniqid('').bin2hex(random_bytes(8));

        DB::connection('mysql')->table('sc_mnp_operator_list')->insert(array(
            'id'=>$sc_mnp_operator_list,
            'title' => $data["title"],
            'product_id' => $data["product_id"],
            'status' => "enable",
            'reseller_bonus' => $data["reseller_bonus"],
            'reseller_offer' => json_encode(array()),
            'description' =>  $data["description"],
            'created_by'=>$profile_details["user_id"],
            'created_at'=>date("Y-m-d H:i:s")
        ));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function change_status(Request $request, $mnp_operators_id)
    {
        $token = $request->header('Authorization');
        $data = json_decode($request->getContent(), true);

        $mnp_operators_dta = DB::connection('mysql')->table('sc_mnp_operator_list')->select("id")->where('id', $mnp_operators_id)->first();

        if(!$mnp_operators_dta)
        {
            $errorMessages = array('order Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        DB::connection('mysql')->table("sc_mnp_operator_list")
            ->where(array(
                'id'=>$mnp_operators_id
            ))->update(array(
                "status"=>$data['status']
            ));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function update(Request $request, $mnp_operators_id)
    {
        $token = $request->header('Authorization');
        $data = $_POST;

        $mnp_operators_dta = DB::connection('mysql')->table('sc_mnp_operator_list')->select("id")->where('id', $mnp_operators_id)->first();

        if(!$mnp_operators_dta)
        {
            $errorMessages = array('order Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
            'title' => 'required|string',
            'product_id' => 'required|string',
            'reseller_bonus' => 'required|string',
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

        DB::connection('mysql')->table("sc_mnp_operator_list")
            ->where(array(
                'id'=>$mnp_operators_id
            ))->update(array(
                'title' => $data["title"],
                'product_id' => $data["product_id"],
                'reseller_bonus' => $data["reseller_bonus"],
                'reseller_offer' => $data["reseller_offer"],
                'description' =>  $data["description"]
            ));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }
}
