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

class OrderController extends Controller
{
    public function list(Request $request)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $params = $_POST;

        $whereQuery = array();

        if(!empty($_POST["status"])) $whereQuery[] = ("sc_orders.status = '".addslashes($_POST["status"])."'");

        if($profile_details['user_type'] == "super_admin"  || $profile_details['user_type'] == "manager") {
            if(!empty($_POST["store_id"])) $whereQuery[] = ("sc_orders.store_id = '" . addslashes($_POST["store_id"]) . "'");
        }
        else
        {
            if(in_array("StoreController::list", $profile_details['permission_lists']))
            {
                if(!empty($_POST["store_id"])) $whereQuery[] = ("sc_orders.store_id = '" . addslashes($_POST["store_id"]) . "'");
                if(empty($_POST["store_id"])) $whereQuery[] = ("store.parent_store_id = '" . $profile_details['store_vendor_id'] . "' OR sc_orders.store_id = '".$profile_details['store_vendor_id']."'");
            } else {
                $whereQuery[] = "sc_orders.store_id = '".$profile_details['store_vendor_id']."'";
            }
        }

        $whereQuery = array_values(array_diff($whereQuery,array("")));

        $query = DB::select(DB::raw("SELECT sc_orders.*, inv_products.`name` AS product_name, store.store_name as store_name FROM sc_orders LEFT JOIN inv_products ON inv_products.id = sc_orders.product_id LEFT JOIN store ON sc_orders.store_id = store.store_id ".(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")." ORDER BY FIELD(sc_orders.status, 'pending') DESC, sc_orders.created_at DESC LIMIT ".$params['start'].", ".$params['length']));

        $queryCount = DB::select(DB::raw("SELECT count(sc_orders.id) as total FROM sc_orders LEFT JOIN store ON sc_orders.store_id = store.store_id ".(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")));

        //if(!empty($ires)) $add_balance = floatval($queryCount[0]->total);


        //$query = DB::connection('mysql')->table('sc_orders');
            //->orderBy("updated_at", "desc")
        //->where('status', 'enabled');

        /*foreach ($params as $key => $value)
        {
            if(!empty($value))
                $query->where($key, 'like', $value."%");
        }*/
        //$g = $query->toSql();


        /*$resultCount = $query->get();

        $query->offset($params['start']);
        $query->limit($params['length']);

        $result = $query->get();*/

        $data = array();
        $pos = ($params['start'] + 1);

        foreach($query as $row)
        {
            $ii = array(
                $row->order_serial,
                ($row->product_name." ".$row->quantity." Pcs"),
                date("F jS, Y h:i A", strtotime($row->created_at))
            );

            if(in_array("StoreController::list", $profile_details['permission_lists'])) $ii[] = $row->store_name;

            $ii[] = ucwords($row->status);
            $ii[] = $row->id."||".
                ((in_array("Simcard::reject_order", $profile_details['permission_lists']) && $row->status == 'pending'  && ($profile_details['store_vendor_id'] != $row->store_id))?"reject_button||":"").
                ((in_array("Simcard::remove_order", $profile_details['permission_lists']) && ($row->status == 'pending' || $row->status == 'rejected'))?"remove_button||":"").
                ((in_array("Simcard::approve_order", $profile_details['permission_lists']) && $row->status == 'pending' && ($profile_details['store_vendor_id'] != $row->store_id))?"approve_button||":"").
                ((in_array("Simcard::view_stock", $profile_details['permission_lists']) && $row->status == 'approved')?"view_stocked||":"").
                ((in_array("Simcard::view_sold", $profile_details['permission_lists']) && $row->status == 'approved')?"view_sold":"");


            $data[] = $ii;
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
            'store_id' => 'required|string|min:2|max:200',
            'product_id' => 'required|string',
            'quantity' => 'required|string',
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

        $sc_orders_id = uniqid('').bin2hex(random_bytes(8));

        $order_serial = DB::select(DB::raw("SELECT order_serial as serial FROM `sc_orders` ORDER BY created_at DESC LIMIT 1"));

        DB::connection('mysql')->table('sc_orders')->insert(array(
            'id'=>$sc_orders_id,
            'product_id' => $data["product_id"],
            'store_id' => $data["store_id"],
            'quantity' => $data["quantity"],
            'order_serial'=>((count($order_serial) > 0)?(intval($order_serial[0]->serial) + 1):1),
            'status' => "pending",
            'created_by'=>$profile_details["user_id"],
            'created_at'=>date("Y-m-d H:i:s"),
            'saved_simcard_numbers'=>json_encode(array())
        ));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }


    public function remove(Request $request, $order_id)
    {
        $product_dta = DB::connection('mysql')->table('sc_orders')->select("id")->where('id', $order_id)->first();

        if(!$product_dta)
        {
            $errorMessages = array('order Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        DB::connection('mysql')->table("sc_orders")->where('id', $order_id)->delete();

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function reject(Request $request, $order_id)
    {
        $product_dta = DB::connection('mysql')->table('sc_orders')->select("id")->where('id', $order_id)->first();

        if(!$product_dta)
        {
            $errorMessages = array('order Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        DB::connection('mysql')->table("sc_orders")
            ->where(array(
                'id'=>$order_id
            ))->update(array(
                "status"=>"rejected"
            ));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }


    public function update(Request $request, $order_id)
    {
        $data = $_POST;
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $sc_orders = DB::connection('mysql')->table('sc_orders')->where('id', $order_id)->first();

        if(!$sc_orders)
        {
            $errorMessages = array('Order Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        if(!empty($data['saved_sim_info']))
        {
            DB::connection('mysql')->table("sc_orders")
                ->where(array(
                    'id'=>$order_id
                ))->update(array(
                    'saved_simcard_numbers'=>$data['saved_sim_info']
                ));
        }

        if(!empty($data['sim_info']))
        {
            $data["sim_info"] = json_decode($data["sim_info"]);

            if((count($data["sim_info"]) != $sc_orders->quantity))
            {
                $errorMessages = array("Entered sim information quantity did not matched with order quantity.");

                return response()->json(array(
                    'right_now'=>date("Y-m-d H:i:s"),
                    'timestamp'=>time(),
                    'success'=>false,
                    'message'=>$errorMessages
                ), 406);
            }

            DB::connection('mysql')->table("sc_orders")
                ->where(array(
                    'id'=>$order_id
                ))->update(array(
                    'status'=>'approved'
                ));

            $total_due = 0;
            $pd = array();
            $i = 1;
            $sim_info = $data["sim_info"];

            //----- Checking if ICCID already exists on system (Start)
            $iccidForCheck = array();
            foreach($sim_info as $row) {
                $info = explode("|", urldecode($row));
                $id = 0;
                $qType = $info[3];
                if ($qType == "i") {
                    $iccidForCheck[] = $info[0];
                }
            }
            $detailInfo = DB::connection('mysql')->table('sc_sim_card')->selectRaw("sc_sim_card.sim_card_iccid, store.store_name, store.store_code, sc_sim_card.status, sc_sim_card.sales_status")
                ->whereIn('sc_sim_card.sim_card_iccid', $iccidForCheck)
                ->leftJoin('store', 'store.store_id', '=', 'sc_sim_card.store_id')
                ->get();

            $errorMessages = array();

            foreach($detailInfo as $row)
            {
                $mm = "";

                if($row->sales_status == "in_stock")
                {
                    $mm .= "SimCard (".$row->sim_card_iccid.") is in stock ".(!empty($row->store_name)?"of (".$row->store_name." [".$row->store_code."])":"")."\n";

                }else if($row->sales_status == "sold")
                {
                    $mm .= "SimCard (".$row->sim_card_iccid.") already sold ".(!empty($row->store_name)?"by (".$row->store_name." [".$row->store_code."])":"")."\n";
                } else {
                    $mm .= "SimCard (".$row->sim_card_iccid.") already in system \n";
                }

                $errorMessages[] = $mm;
            }

            if(!empty($errorMessages))
            {
                return response()->json(array(
                    'right_now'=>date("Y-m-d H:i:s"),
                    'timestamp'=>time(),
                    'success'=>false,
                    'message'=>$errorMessages
                ), 406);
            }
            //----- Checking if ICCID already exists on system (End)



            foreach($sim_info as $row)
            {
                $info = explode("|", urldecode($row));
                $id = 0;
                $qType = $info[3];
                if($qType=="i")
                {
                    $sc_sim_card_id = uniqid('').bin2hex(random_bytes(8));
                    DB::connection('mysql')->table('sc_sim_card')->insert(array(
                        'id'=>$sc_sim_card_id,
                        'order_id'=> $order_id,
                        'store_id'=>$sc_orders->store_id,
                        'product_id'=>$sc_orders->product_id,
                        'sim_card_iccid'=> $info[0],
                        'sim_card_mobile_number'=> $info[1],
                        'created_at'=>date("Y-m-d H:i:s"),
                        'ordered_at'=>$sc_orders->created_at,
                        'approved_at'=>date("Y-m-d H:i:s"),
                        'custom_product_offer'=>json_encode(array()),
                        'cost'=>$info[2],
                        'sales_status'=> 'in_stock',
                        'status'=> 'pending',


                        'other_operator_name'=>"",
                        'sur_name'=>"",
                        'activation_sms_mobile_number'=>"",
                        'codicifiscale'=>"",
                        'mnp_operator_name'=>"",
                        'mnp_iccid_number'=>"",
                        'mnp_iccid_mobile_number'=>"",
                        'mnp_notes'=>"",
                        'ricarica'=>"",
                        'reseller_price'=>"",
                        'activated_by'=>"",
                    ));
                    $id=$sc_sim_card_id;
                }
                else
                {
                    $UU=explode("_",$qType);
                    if(count($UU)>1)
                    {
                        DB::connection('mysql')->table("sc_sim_card")
                            ->where(array(
                                'id'=>$UU[1]
                            ))->update(array(
                                'sim_card_iccid'=> $info[0],
                                'sim_card_mobile_number'=> $info[1],
                                'store_id'=>$sc_orders->store_id,
                                'sales_status'=> 'in_stock',
                                'status'=> 'pending',
                                'locked'=>0,
                                'locked_by'=>0
                            ));

                        $id = $UU[1];
                    }
                }

                $pd[]=array(
                    'id'=>($i),
                    'sim_card_iccid'=> $info[0],
                    'sim_card_mobile_number'=> $info[1],
                    'cost'=>$info[2]
                );

                $total_due = $total_due + floatval($info[2]);
                $i = $i + 1;

                // Append SIM CARD Meta Data
                if($qType=="i")
                {
                    DB::connection('mysql')->table('sc_sim_card_meta_data')->insert(array(
                        'id'=>uniqid('').bin2hex(random_bytes(8)),
                        'meta_key' => 'created_at',
                        'meta_value' => date("Y-m-d H:i:s"),
                        'sim_card_id' => $id
                    ));
                }

                DB::connection('mysql')->table('sc_sim_card_meta_data')->insert(array(
                    'id'=>uniqid('').bin2hex(random_bytes(8)),
                    'meta_key' => 'order_approved_at',
                    'meta_value' => date("Y-m-d H:i:s"),
                    'sim_card_id' => $id
                ));

                DB::connection('mysql')->table("sc_sim_card")
                    ->where(array(
                        'id'=>$id
                    ))->update(array(
                        'approved_at'=>date("Y-m-d H:i:s")
                    ));

                if(!empty($id))
                {
                    $path = public_path().'/assets/sim_card/'.$id.'/general';
                    File::makeDirectory($path, $mode = 0777, true, true);

                    $path = public_path().'/assets/sim_card/'.$id.'/admin';
                    File::makeDirectory($path, $mode = 0777, true, true);
                }
            }

            Redis::set('message:success', 'Order Have Been Approved Successfully', 'EX', 5);
        }

        // Approve Order
        //Redis::set('message:error', 'User Created Successfully', 'EX', 5);
        //Redis::set('message:success', 'User Created Successfully', 'EX', 5);

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }



    public function appoint_sim_card(Request $request)
    {
        $token = $request->header('Authorization');
        $param = $_POST;
        $errorMessages = array();

        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        if(empty($param))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>true,
            ), 400);

        $sc_orders = DB::connection('mysql')->table('sc_orders')->selectRaw("id, created_at")->where('id', $param["order_id"])->first();

        $ids = explode("|", $param["ids"]);
        foreach ($ids as $id)
        {
            $uData = array(
                'order_id'=>$sc_orders->id,
                'ordered_at'=>$sc_orders->created_at,
                'sales_status'=>"in_stock",
                'status'=>"stocked",
                'country_name'=>"",
                'date_of_birth'=>"",
            );

            DB::connection('mysql')->table("sc_sim_card")
                ->where(array(
                    'id'=>$id
                ))->update($uData);
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }
}
