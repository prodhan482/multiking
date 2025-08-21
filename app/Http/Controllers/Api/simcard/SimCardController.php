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

class SimCardController extends Controller
{
    public function all(Request $request)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $params = $_POST;
        $data = array();
        $where_query=array();
        $where_query[] = " sc_sim_card.status != 'trashed' ";

        if(in_array("StoreController::list", $profile_details['permission_lists']))
        {
            if(!empty($params["reseller_id"]))
            {
                $where_query[] = " sc_sim_card.store_id = '".$params["reseller_id"]."' ";
            } else {

                $storeListQ = DB::connection('mysql')->table('store')->selectRaw("store_id as id")->where(array(
                    'enable_simcard_access'=>"1",
                    'parent_store_id'=>$profile_details['store_vendor_id']
                ));
                $storeList = $storeListQ->get();
                $storeListQQ = array($profile_details['store_vendor_id']);
                foreach($storeList as $row)
                {
                    $storeListQQ[] = $row->id;
                }

                if($profile_details['user_type'] == "super_admin" || $profile_details['user_type'] == "manager") {
                    if(empty($params["search"])) $where_query[] = " sc_sim_card.store_id IN ('".join("', '", $storeListQQ)."') ";
                }
                else
                {
                    $where_query[] = " sc_sim_card.store_id IN ('".join("', '", $storeListQQ)."') ";
                }
            }
        }
        else
        {
            $where_query[] = " sc_sim_card.store_id = '".$profile_details['store_vendor_id']."'";
        }

        if(!empty($params["sales_status"]))
        {
            $where_query[] = " sc_sim_card.sales_status = '".$params["sales_status"]."' ";
        }

        if(!empty($params["lot_id"]))
        {
            $where_query[] = " sc_sim_card.lot_id = '".$params["lot_id"]."' ";
        }

        if(!empty($params["status"]))
        {
            $where_query[] = " sc_sim_card.status = '".$params["status"]."' ";
        }

        if(!empty($params["product_id"]))
        {
            $where_query[] = " sc_sim_card.product_id = '".$params["product_id"]."' ";
        }

        if(!empty($params["selected_mnp_number"]))
        {
            $where_query[] = " sc_sim_card.mnp_iccid_mobile_number = '".$params["selected_mnp_number"]."'";
        }

        if(!empty($params["search"]))
        {
            $where_query[] = " (sc_sim_card.sim_card_iccid LIKE '%".$params["search"]."%' OR sc_sim_card.sim_card_mobile_number LIKE '%".$params["search"]."%')";
        }

        $order_by = " ORDER BY activated_at DESC ";

        if(!empty($params["order"]))
        {
            if(!empty($params["order"][0]["column"]))
            {
                if(!empty($params["order"][0]["dir"]))
                {
                    $order_by = " ORDER BY ".$params["columns"][($params["order"][0]["column"])]["name"]." ".strtoupper($params["order"][0]["dir"]);
                }
            }
        }


        if(empty($params["sales_status"]) && empty($params["lot_id"]) && empty($params["status"]) && empty($params["product_id"]) && empty($params["selected_mnp_number"]) && empty($params["search"]) && empty($params["reseller_id"]))
        {
            // if all filters are empty. send a empty response with no query.
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>true,
                'data'=>[],
                'draw'=>$params['draw'],
                'pendingOrders'=>[],
                'recordsFiltered'=>0,
                'recordsTotal'=>0
            ), 200);
        }


        $SQL="SELECT sc_sim_card.*, inv_products.`name` AS product_name, sc_sim_card_lot.name AS lot_name, store.store_name AS reseller_name, sc_orders.order_serial FROM sc_sim_card LEFT JOIN store ON store.store_id = sc_sim_card.store_id LEFT JOIN sc_orders ON sc_orders.ID = sc_sim_card.order_id LEFT JOIN inv_products ON inv_products.id = sc_sim_card.product_id LEFT JOIN sc_sim_card_lot ON sc_sim_card_lot.id = sc_sim_card.lot_id  ".(!empty($where_query)?(" WHERE ".implode(" AND ", $where_query)):"")." ".(!empty($order_by)?$order_by:"")." LIMIT ".$params['start'].", ".$params['length'];

        //echo $SQL;

        $query_50_result = DB::select(DB::raw($SQL));

        foreach($query_50_result as $row)
        {
            $sim_card_current_status = "";
            $action = "";
            $allowView = "NO_VIEW";

            if($row->sales_status == "sold")
            {
                $allowView = "VIEW";

                if($row->status == "pending"){$action="MV_STOCK";$sim_card_current_status = "Sold > Pending";}
                if($row->status == "rejected"){$action="MV_STOCK";$sim_card_current_status = "Sold > Activation Rejected";}
                if($row->status == "approved"){$action="MV_STOCK";$sim_card_current_status = "Sold > Approved";}
            }
            else
            {
                if($row->status == "archived" && empty($row->order_id)){$action="MV_TRASH";$sim_card_current_status = "Archived";}
                if($row->status == "stocked" && empty($row->order_id)){$action="MV_ARCHIVE";$sim_card_current_status = "In Stock";}
                if($row->status == "stocked" && !empty($row->order_id)){$action="MV_STOCK";$sim_card_current_status = "Moved @ Order #".$row->order_serial;}
                if($row->status == "pending" && !empty($row->order_id)){$action="MV_STOCK";$sim_card_current_status = "Approved @ Order #".$row->order_serial;}
            }

            $data[]=array(
                $row->id,
                $row->sim_card_iccid,
                $row->sim_card_mobile_number,
                $row->product_name,
                $row->lot_name,
                $row->reseller_name,
                $sim_card_current_status,
                str_replace("0000-00-00 00:00:00","", $row->created_at),
                str_replace("0000-00-00 00:00:00","", $row->ordered_at),
                str_replace("0000-00-00 00:00:00","", $row->approved_at),
                $row->sold_at,
                $row->activated_at,
                ($action.'|'.$allowView."|".$row->id)
            );
        }


        $queryCount = DB::select(DB::raw("SELECT COUNT(sc_sim_card.id) as total FROM sc_sim_card ".(!empty($where_query)?(" WHERE ".implode(" AND ", $where_query)):"")));


        if($profile_details['user_type'] == "super_admin" || $profile_details['user_type'] == "manager") {
            $pending_orders = DB::select(DB::raw("SELECT sc_orders.id, sc_orders.order_serial, (SELECT COUNT(*) FROM sc_sim_card WHERE sc_sim_card.order_id = sc_orders.id) as apointed, sc_orders.quantity FROM sc_orders WHERE sc_orders.`status` = 'pending' ORDER BY sc_orders.created_by DESC"));
        }
        else
        {
            if(in_array("StoreController::list", $profile_details['permission_lists']))
            {
                $storeListQ = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc")->where(array(
                    'enable_simcard_access'=>"1",
                    'parent_store_id'=>$profile_details['store_vendor_id']
                ));
                $storeList = $storeListQ->get();

                $storeIDs = array();
                foreach($storeList as $row){
                    $storeIDs[] = $row->id;
                }

                $pending_orders = DB::select(DB::raw("SELECT sc_orders.id, sc_orders.order_serial, (SELECT COUNT(*) FROM sc_sim_card WHERE sc_sim_card.order_id = sc_orders.id) as apointed, sc_orders.quantity FROM sc_orders WHERE sc_orders.`status` = 'pending' AND sc_orders.store_id IN ('".join("', '", $storeIDs)."') ORDER BY sc_orders.created_by DESC"));

            } else {

                $pending_orders = DB::select(DB::raw("SELECT sc_orders.id, sc_orders.order_serial, (SELECT COUNT(*) FROM sc_sim_card WHERE sc_sim_card.order_id = sc_orders.id) as apointed, sc_orders.quantity FROM sc_orders WHERE sc_orders.`status` = 'pending' AND sc_orders.store_id = '".$profile_details['store_vendor_id']."' ORDER BY sc_orders.created_by DESC"));
            }
        }


        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$data,
            'draw'=>$params['draw'],
            'pendingOrders'=>$pending_orders,
            'recordsFiltered'=>$queryCount[0]->total,
            'recordsTotal'=>$queryCount[0]->total
        ), 200);
    }

    public function add(Request $request)
    {
        $token = $request->header('Authorization');
        $param = $_POST;//json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($param))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
            'lot_name' => 'required|string|min:2|max:200',
            'product_id' => 'required|string',
            'saved_sim_info' => 'required|string',
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

        $sim_info = json_decode($param["saved_sim_info"]);

        $product_dta = DB::connection('mysql')->table('inv_products')->where('id', $param["product_id"])->first();

        $sim_card_lot_id = uniqid('').bin2hex(random_bytes(8));

        DB::connection('mysql')->table('sc_sim_card_lot')->insert(array(
            'id'=>$sim_card_lot_id,
            'name'=> $param["lot_name"],
        ));

        foreach($sim_info as $row)
        {
            $info = explode("|", urldecode($row));
            $id = 0;

            $sc_sim_card_id = uniqid('').bin2hex(random_bytes(8));
            DB::connection('mysql')->table('sc_sim_card')->insert(array(
                'id'=>$sc_sim_card_id,
                'order_id'=> "",
                'store_id'=>"",
                'lot_id'=>$sim_card_lot_id,
                'product_id'=>$param["product_id"],
                'sim_card_iccid'=> $info[0],
                'sim_card_mobile_number'=> $info[1],
                'created_at'=>date("Y-m-d H:i:s"),
                'ordered_at'=>null,
                'approved_at'=>null,
                'activated_by'=>'',
                'custom_product_offer'=>json_encode(array()),
                'cost'=>$product_dta->price,
                'sales_status'=> 'in_stock',
                'status'=> 'stocked',


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

                'country_name'=>"",
                'date_of_birth'=>"",
            ));
            $id=$sc_sim_card_id;

            // Append SIM CARD Meta Data
            DB::connection('mysql')->table('sc_sim_card_meta_data')->insert(array(
                'id'=>uniqid('').bin2hex(random_bytes(8)),
                'meta_key' => 'created_at',
                'meta_value' => date("Y-m-d H:i:s"),
                'sim_card_id' => $id
            ));
        }

        Redis::set('message:success', 'Sim Card Have Been Added Successfully', 'EX', 5);

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function list(Request $request)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $params = $_POST;

        $whereQuery = array();

        $whereQuery[] = ("sc_sim_card.status != 'trashed'");

        if(!empty($_POST["status"])) $whereQuery[] = ("sc_sim_card.status = '".addslashes($_POST["status"])."'");

        if(!empty($_POST["order_id"])) $whereQuery[] = ("sc_sim_card.order_id = '".addslashes($_POST["order_id"])."'");

        if(!empty($_POST["sales_status"])) $whereQuery[] = ("sc_sim_card.sales_status = '".addslashes($_POST["sales_status"])."'");

        if(in_array("StoreController::list", $profile_details['permission_lists']) || $profile_details['user_type'] == "manager") {
        //if($profile_details['user_type'] == "super_admin"){
            if(in_array("Simcard::activate", $profile_details['permission_lists'])){
                if(!empty($_POST["store_id"])) $whereQuery[] = ("sc_sim_card.store_id = '" . addslashes($_POST["store_id"]) . "'");
                if($_POST["sales_status"] == "in_stock" && empty($_POST["store_id"]))
                {
                    if($profile_details['user_type'] == "super_admin" || $profile_details['user_type'] == "manager") {
                        if(empty($params["sim_card_mobile_number"]) && empty($params["sim_card_iccid"])){
                            $storeListQQ = self::getSubStoreList($profile_details['store_vendor_id']);
                            $whereQuery[] = " sc_sim_card.store_id IN ('".join("', '", $storeListQQ)."') ";
                        }
                    }
                    else
                    {
                        $storeListQQ = self::getSubStoreList($profile_details['store_vendor_id']);
                        $whereQuery[] = " sc_sim_card.store_id IN ('".join("', '", $storeListQQ)."') ";
                    }
                }
            }
            else {
                if(!empty($_POST["store_id"])) $whereQuery[] = ("sc_sim_card.store_id = '" . addslashes($_POST["store_id"]) . "'");
                if(empty($_POST["store_id"]))
                {
                    $storeListQQ = self::getSubStoreList($profile_details['store_vendor_id']);
                    $whereQuery[] = " sc_sim_card.store_id IN ('".join("', '", $storeListQQ)."') ";
                }
            }
        }
        else
        {
            $whereQuery[] = "sc_sim_card.store_id = '".$profile_details['store_vendor_id']. "'";
        }

        $whereQuery[] = ((!empty($_POST["start_date"]) || !empty($_POST["end_date"]))?("(sc_sim_card.sold_at BETWEEN '".date("Y-m-d 00:00:00", strtotime($_POST["start_date"]))."' AND '".date("Y-m-d 23:59:59", strtotime($_POST["end_date"]))."')"):"");

        $whereQuery[] = (!empty($_POST["sim_card_status"])?("sc_sim_card.status = '".addslashes($_POST["sim_card_status"])."'"):"");

        $whereQuery[] = (!empty($_POST["sim_card_sales_status"])?("sc_sim_card.sales_status = '".addslashes($_POST["sim_card_sales_status"])."'"):"");

        $whereQuery[] = (!empty($_POST["sim_card_iccid"])?("sc_sim_card.sim_card_iccid LIKE '%".addslashes($_POST["sim_card_iccid"])."%'"):"");

        $whereQuery[] = (!empty($_POST["sim_card_mobile_number"])?("sc_sim_card.sim_card_mobile_number LIKE '%".addslashes($_POST["sim_card_mobile_number"])."%' OR sc_sim_card.mnp_iccid_mobile_number LIKE '%".addslashes($_POST["sim_card_mobile_number"])."%'"):"");

        $whereQuery[] = (!empty($_POST["product_id"])?("sc_sim_card.product_id = '".addslashes($_POST["product_id"])."'"):"");

        $whereQuery[] = (!empty($_POST["order_id"])?(" sc_sim_card.order_id = '".($_POST["order_id"]."'").((in_array("StoreController::list", $profile_details['permission_lists']))?"": " AND sc_sim_card.store_id = '".$profile_details['store_vendor_id']."'")):"");

        $whereQuery = array_values(array_diff($whereQuery,array("")));


        $queryS="SELECT sc_sim_card.mnp_iccid_number, sc_sim_card.mnp_iccid_mobile_number, sc_sim_card.store_id, sc_sim_card.id, sc_sim_card.sold_at, sc_sim_card.cost, sc_sim_card.sales_price, sc_sim_card.sales_status, sc_sim_card.status, sc_sim_card.sim_card_iccid, sc_sim_card.sim_card_mobile_number, sc_sim_card.locked, inv_products.`name` AS product_name, sc_simcard_offer.title as simcard_offer ".((in_array("StoreController::list", $profile_details['permission_lists']))?", store.`store_name` AS store_name, parent_store.`store_name` AS parent_store_name ":"")." FROM sc_sim_card LEFT JOIN inv_products ON sc_sim_card.product_id = inv_products.id LEFT JOIN sc_simcard_offer ON sc_sim_card.product_offer_id = sc_simcard_offer.id ".((in_array("StoreController::list", $profile_details['permission_lists']))?" LEFT JOIN store ON store.store_id = sc_sim_card.store_id LEFT JOIN store as parent_store ON store.parent_store_id = parent_store.store_id ":"").(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")." ORDER BY FIELD(sc_sim_card.status, 'pending') DESC, FIELD(sc_sim_card.sales_status, 'sold') DESC, sc_sim_card.sold_at DESC LIMIT ".$_POST['start'].", ".($_POST['length'] == -1?"50":$_POST['length']);

        $query = DB::select(DB::raw($queryS));

        $queryCount = DB::select(DB::raw("SELECT count(sc_sim_card.id) AS total FROM sc_sim_card ".(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")." LIMIT 1"));


        $data = array();
        $pos = ($params['start'] + 1);

        foreach($query as $row)
        {
            $ii = array(
                $pos++,
                ($row->product_name),
                $row->sim_card_iccid,
                $row->sim_card_mobile_number
            );

            if($row->sales_status == "sold")
            {
                $ii[] = $row->mnp_iccid_number;
                $ii[] = $row->mnp_iccid_mobile_number;
            }

            if(in_array("StoreController::list", $profile_details['permission_lists'])) $ii[] = $row->store_name." ".(!empty($row->parent_store_name)?("(Parent: ".$row->parent_store_name.")"):"");

            if(!in_array("StoreController::list", $profile_details['permission_lists'])){
                $ii[] = number_format($row->cost, 2, ".", ",");
                $ii[] = number_format($row->sales_price, 2, ".", ",");
                $ii[] = (($row->sales_price > $row->cost)?number_format($row->sales_price - $row->cost, 2, ".", ","):("(".number_format($row->cost - $row->sales_price, 2, ".", ",").")"));
            }

            if(!empty($_POST["sales_status"]) && $_POST["sales_status"] != "in_stock") $ii[] = (($row->sales_status == "sold")?date("F jS, Y, g:i a", strtotime($row->sold_at)):"");

            if($row->sales_status == "in_stock")
            {
                if($row->status == "pending")
                {
                    $ii[] = "In Stock";
                }
                else
                {
                    $ii[] = "In Stock";
                }
            }
            else
            {
                $ii[] = (!empty($row->simcard_offer)?$row->simcard_offer:"Other Offer");

                if($row->status == "pending")
                {
                    $ii[] = "Approval pending";
                }
                else
                {
                    $ii[] = ucwords($row->status);
                }
            }


            $ii[] = $row->id."||".($row->product_name." [ICCID: ".$row->sim_card_iccid." , Mobile Number: ".$row->sim_card_mobile_number." ]")."||".
                ((in_array("Simcard::sale", $profile_details['permission_lists']) && $row->sales_status == 'in_stock')?"sale_button||":"").
                ((($row->status == 'pending' || $row->status == 'rejected') && $row->sales_status == 'sold' && in_array("Simcard::upload", $profile_details['permission_lists']))?"upload_button||":"").

                (($row->status == 'rejected' && $row->sales_status == 'sold' && !in_array("Simcard::upload", $profile_details['permission_lists']) && $profile_details['store_vendor_id'] == $row->store_id)?"upload_button||":"").

                ((!($row->status == 'pending' || $row->status == 'rejected') && $row->sales_status == 'sold')?"view_button||":"").

                (($row->locked == '1' && in_array("Simcard::emergency_unlock", $profile_details['permission_lists']))?"unlock_button||":"").
                (($row->locked == '0' && $row->status != "approved" && $row->sales_status == "sold" && in_array("Simcard::lock", $profile_details['permission_lists']))?"lock_button||":"").

                ((($row->status == 'pending' || $row->status == 'rejected') && $row->locked == '1' && $row->sales_status == "sold" && in_array("Simcard::activate", $profile_details['permission_lists']))?"activate_button||":"").
                (($row->status == 'pending' && $row->sales_status == "sold" && $row->locked == '1' &&  in_array("Simcard::reject", $profile_details['permission_lists']))?"reject_button":"");


            $data[] = $ii;
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>$queryS,
            'data'=>$data,
            'draw'=>$params['draw'],
            'recordsFiltered'=>$queryCount[0]->total,
            'recordsTotal'=>$queryCount[0]->total
        ), 200);
    }

    private function getSubStoreList($parent_store_id)
    {
        $storeListQ = DB::connection('mysql')->table('store')->selectRaw("store_id as id")->where(array(
            'enable_simcard_access'=>"1",
            'parent_store_id'=>$parent_store_id
        ));
        $storeList = $storeListQ->get();
        $storeListQQ = array($parent_store_id);
        foreach($storeList as $row)
        {
            $storeListQQ[] = $row->id;
        }

        return $storeListQQ;
    }

    public function sale(Request $request, $sim_card_id)
    {
        $token = $request->header('Authorization');
        $param = $_POST;
        //json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($param))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
            'sur_name' => 'required|string',
            'reseller_price' => 'required|string',
            //'codicifiscale' => 'required|string',
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

        if(!empty($param["mnp_operator_name"]) && !$param["mnp_operator_name"] == "Others")
        {
            $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
                'mnp_iccid_number' => 'required|string',
                'mnp_iccid_mobile_number' => 'required|string',
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
        }

        if(!empty($param["mnp_operator_name"]) && $param["mnp_operator_name"] == "Others")
        {
            $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
                'other_operator_name' => 'required|string'
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
        }

        $sim_card_info = DB::connection('mysql')->table('sc_sim_card')->selectRaw("*")
            ->where(array(
                'sales_status'=>"in_stock",
                'id'=>$sim_card_id,
            ))
            ->first();

        if(!$sim_card_info)
        {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>array("No Sim Card Found")
            ), 404);
        }

        if(empty($request->file)){
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>array("Proper Documents needs to be upload.")
            ), 406);
        }

        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        DB::connection('mysql')->table("sc_sim_card")
            ->where(array(
                'id'=>$sim_card_id
            ))->update(array(
                'sales_status'=>'sold',
                'product_offer_id'=>$param["product_offer_id"],
                'sales_price'=>"0",
                'sold_at'=>date("Y-m-d H:i:s"),
                'custom_product_offer'=>$param["custom_product_offer"],
                'other_operator_name'=>$param["other_operator_name"],


                'sur_name'=>$param["sur_name"],
                'activation_sms_mobile_number'=>"",
                'codicifiscale'=>(!empty($param["codicifiscale"])?$param["codicifiscale"]:""),
                'mnp_operator_name'=>$param["mnp_operator_name"],
                'mnp_iccid_number'=>$param["mnp_iccid_number"],
                'mnp_iccid_mobile_number'=>$param["mnp_iccid_mobile_number"],
                'mnp_notes'=>$param["mnp_notes"],

                'country_name'=>$param["country_name"],
                'date_of_birth'=>$param["date_of_birth"],

                'ricarica'=>"",
                'reseller_price'=>$param["reseller_price"]
            ));


        DB::connection('mysql')->table('sc_sim_card_meta_data')->insert(array(
            'id'=>uniqid('').bin2hex(random_bytes(8)),
            'meta_key' => 'other_information',
            'meta_value' => $param["other_information"],
            'sim_card_id' => $sim_card_id
        ));

        $pos = 1;
        if(!empty($request->file)){
            foreach ($request->file as $file) {
                $fileName = "simcard_".time().$pos.'.'.$file->extension();
                $file->move(base_path('public/assets/sim_card/'.$sim_card_id.'/general'), $fileName);

                $row_id = uniqid('').bin2hex(random_bytes(8));

                DB::connection('mysql')->table('sc_sim_card_files')->insert(array(
                    'row_id'=>$row_id,
                    'file_path' => (('assets/sim_card/'.$sim_card_id.'/general').'/'.$fileName),
                    'sc_sim_card_id' => $sim_card_id,
                    'created_at' => date("Y-m-d H:i:s")
                ));

                self::sendUploadRequestToDigOcenSpace((base_path('public/assets/sim_card/'.$sim_card_id.'/general').'/'.$fileName), (('assets/sim_card/'.$sim_card_id.'/general').'/'.$fileName), "sc_sim_card_files", array("row_id"), array($row_id));

                $pos = $pos + 1;
            }
        }

        Redis::set('message:success', 'Sim Card Have Been Sold Successfully', 'EX', 5);

        // Send Firebase Notification
        $admin_users = DB::connection('mysql')
            ->table('aauth_users')
            ->selectRaw('fcm_token')
            ->where('user_type', '=', 'manager')->orWhere('user_type', '=', 'super_admin');

        $admin_result = $admin_users->get();
        foreach($admin_result as $value)
        {
            if(!empty($value->fcm_token))
            {
                $n = new \App\Libs\FirebaseMessaging($value->fcm_token);
                $message = "Sim Card (ICCID: ".$sim_card_info->sim_card_iccid.", Mobile Number: ".$sim_card_info->sim_card_mobile_number.") have been sold.";
                $n->setMessage("Sim Card Sold", $message, config('constants.SIMCARD_SOLD'))->sendMessage();
            }
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function reject(Request $request, $sim_card_id)
    {
        $token = $request->header('Authorization');
        $param = $_POST;
        //json_decode($request->getContent(), true);
        $errorMessages = array();

        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        if(empty($param))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>true,
            ), 400);

        $sim_card_info = DB::connection('mysql')->table('sc_sim_card')->where(array(
                'status'=>"pending",
                'id'=>$sim_card_id,
            ))
            ->first();

        if(!$sim_card_info)
        {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>array("No Sim Card Found")
            ), 404);
        }

        DB::connection('mysql')->table("sc_sim_card")
            ->where(array(
                'id'=>$sim_card_id
            ))->update(array(
                'status'=>'rejected',
                'locked'=>0,
                'locked_by'=>0
            ));

        DB::connection('mysql')->table('sc_sim_card_history')->insert(array(
            'id'=>uniqid('').bin2hex(random_bytes(8)),
            'sim_card_id' => $sim_card_id,
            'status' => 'rejected',
            'cause' => $param["full_cause"],
            'created_by'=>$profile_details['user_id'],
            'created_at'=>date("Y-m-d H:i:s")
        ));

        Redis::set('message:success', 'Sim Card Have Been Rejected Successfully', 'EX', 5);

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function remove_file(Request $request, $sim_card_id)
    {
        $token = $request->header('Authorization');
        $param = json_decode($request->getContent(), true);
        $errorMessages = array();

        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);

        if (empty($param))
            return response()->json(array(
                'right_now' => date("Y-m-d H:i:s"),
                'timestamp' => time(),
                'success' => $_POST,
            ), 400);

        Storage::disk('public-folder')->delete([$param["file_url"]]);

        DB::connection('mysql')->table("sc_sim_card_files")->where('row_id', $param["file_id"])->delete();


        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function remove_sim_card(Request $request)
    {
        $token = $request->header('Authorization');
        $param = json_decode($request->getContent(), true);
        $errorMessages = array();

        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);

        if (empty($param))
            return response()->json(array(
                'right_now' => date("Y-m-d H:i:s"),
                'timestamp' => time(),
                'success' => false,
            ), 400);

        $simcardIDs = explode("|", $param["id"]);
        foreach ($simcardIDs as $ID) {
            DB::connection('mysql')->table("sc_sim_card")->where('id', $ID)->delete();
            DB::connection('mysql')->table("sc_sim_card_files")->where('sc_sim_card_id', $ID)->delete();
            DB::connection('mysql')->table("sc_sim_card_history")->where('sim_card_id', $ID)->delete();
            DB::connection('mysql')->table("sc_sim_card_meta_data")->where('sim_card_id', $ID)->delete();
        }

        //Storage::disk('public-folder')->delete([$param["file_url"]]);
        //DB::connection('mysql')->table("sc_sim_card_files")->where('row_id', $param["file_id"])->delete();

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function update(Request $request, $sim_card_id)
    {
        $token = $request->header('Authorization');
        $param = json_decode($request->getContent(), true);

        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);

        $sim_card_info = DB::connection('mysql')->table('sc_sim_card')->selectRaw("sc_sim_card.id")->where(array(
            'sc_sim_card.id' => $sim_card_id,
        ))->first();

        if (!$sim_card_info) {
            return response()->json(array(
                'right_now' => date("Y-m-d H:i:s"),
                'timestamp' => time(),
                'success' => false,
                'message' => array("No Sim Card Found")
            ), 404);
        }

        if (empty($param)) {
            return response()->json(array(
                'right_now' => date("Y-m-d H:i:s"),
                'timestamp' => time(),
                'success' => false,
                'message' => array("Invalid Request")
            ), 404);
        }

        DB::connection('mysql')->table("sc_sim_card")
            ->where(array(
                'id'=>$sim_card_id
            ))->update(array(
                'sim_card_iccid'=>$param["sim_card_iccid"],
                'sim_card_mobile_number'=>$param["sim_card_mobile_number"],
                'codicifiscale'=>$param["codicifiscale"],
                'sur_name'=>$param["sur_name"],
                'mnp_iccid_number'=>$param["mnp_iccid_number"],
                'mnp_iccid_mobile_number'=>$param["mnp_iccid_mobile_number"],
                'mnp_notes'=>$param["mnp_notes"]
            ));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }


    public function upload_file(Request $request, $sim_card_id)
    {
        $token = $request->header('Authorization');
        $param = $_POST;
        //json_decode($request->getContent(), true);
        $errorMessages = array();

        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);

        $sim_card_info = DB::connection('mysql')->table('sc_sim_card')->selectRaw("sc_sim_card.id")->where(array(
            'sc_sim_card.id' => $sim_card_id,
        ))->first();

        if (!$sim_card_info) {
            return response()->json(array(
                'right_now' => date("Y-m-d H:i:s"),
                'timestamp' => time(),
                'success' => false,
                'message' => array("No Sim Card Found")
            ), 404);
        }

        $pos = 1;
        if(!empty($request->file)){
            foreach ($request->file as $file) {
                $fileName = "simcard_".time().$pos.'.'.$file->extension();
                $file->move(base_path('public/assets/sim_card/'.$sim_card_id.'/'.($profile_details['user_type'] == "super_admin"?"admin":"general")), $fileName);
                $pos = $pos + 1;

                $row_id = uniqid('').bin2hex(random_bytes(8));

                DB::connection('mysql')->table('sc_sim_card_files')->insert(array(
                    'row_id'=>$row_id,
                    'file_path' => (('assets/sim_card/'.$sim_card_id.'/'.($profile_details['user_type'] == "super_admin"?"admin":"general")).'/'.$fileName),
                    'sc_sim_card_id' => $sim_card_id,
                    'created_at' => date("Y-m-d H:i:s")
                ));

                self::sendUploadRequestToDigOcenSpace((base_path('public/assets/sim_card/'.$sim_card_id.'/'.($profile_details['user_type'] == "super_admin"?"admin":"general")).'/'.$fileName), (('assets/sim_card/'.$sim_card_id.'/'.($profile_details['user_type'] == "super_admin"?"admin":"general")).'/'.$fileName), "sc_sim_card_files", array("row_id"), array($row_id));
            }
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }


    public function change_lock_status(Request $request, $sim_card_id)
    {
        $token = $request->header('Authorization');
        $errorMessages = array();

        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);

        $sim_card_info = DB::connection('mysql')->table('sc_sim_card')->where(array(
            'id'=>$sim_card_id,
        ))->first();

        if(!$sim_card_info)
        {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>array("No Sim Card Found")
            ), 404);
        }

        DB::connection('mysql')->table("sc_sim_card")
            ->where(array(
                'id'=>$sim_card_id
            ))->update(array(
                'locked'=>($sim_card_info->locked=="1"?0:1),
                'locked_by'=>$profile_details['user_id']
            ));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function emergency_unlock(Request $request, $sim_card_id)
    {
        $token = $request->header('Authorization');
        $errorMessages = array();

        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);

        $sim_card_info = DB::connection('mysql')->table('sc_sim_card')->where(array(
            'id'=>$sim_card_id,
        ))->first();

        if(!$sim_card_info)
        {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>array("No Sim Card Found")
            ), 404);
        }

        DB::connection('mysql')->table("sc_sim_card")
            ->where(array(
                'id'=>$sim_card_id
            ))->update(array(
                'locked'=>0,
                'locked_by'=>""
            ));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function activate(Request $request, $sim_card_id)
    {
        $token = $request->header('Authorization');
        $param = $_POST;
        //json_decode($request->getContent(), true);
        $errorMessages = array();

        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        if(empty($param))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>true,
            ), 400);

        $sim_card_info = DB::connection('mysql')->table('sc_sim_card')->selectRaw("sc_sim_card.*, inv_products.name as product_name")->where(array(
            'sc_sim_card.id'=>$sim_card_id,
        ))
        ->leftJoin('inv_products', 'inv_products.id', '=', 'sc_sim_card.product_id')
        ->first();

        if(!$sim_card_info)
        {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>array("No Sim Card Found")
            ), 404);
        }

        $approvedCount = DB::select(DB::raw("SELECT COUNT(sc_sim_card.id) AS total FROM sc_sim_card WHERE sc_sim_card.status = 'approved'"));

        DB::connection('mysql')->table("sc_sim_card")
        ->where(array(
           'id'=>$sim_card_id
        ))->update(array(
           'activated_at'=>date("Y-m-d H:i:s"),
           'activation_id'=>($approvedCount[0]->total + 1),
           'status'=>'approved',
           'activated_by'=>$profile_details['user_id'],
           'locked'=>0,
           'locked_by'=>0
        ));

        // Add Due to Reseller
        $ricarica = $sim_card_info->reseller_price;

        DB::statement("UPDATE store SET store.simcard_due_amount = (store.simcard_due_amount + ".$ricarica.") WHERE store.store_id ='".$sim_card_info->store_id."'");
        self::addDueToParentReseller($sim_card_info->store_id, $sim_card_info->product_offer_id, $sim_card_info);

        DB::connection('mysql')->table('adjustment_history')->insert(array(
            'row_id'=>uniqid('').bin2hex(random_bytes(8)),
            'created_on' => date("Y-m-d H:i:s"),
            'type'=>'store',
            'adjustment_type'=>'simcard',
            'store_vendor_id'=>$sim_card_info->store_id,
            'adjustment_type_id'=>$sim_card_id,
            'adjusted_amount'=>0,
            'adjustment_percent'=>0,
            'received_amount'=>$ricarica,
            'note'=> ("Recharge added for ".$sim_card_info->product_name." Sim Card ICCID: ".$sim_card_info->sim_card_iccid.", Sim Card Mobile Number: ".$sim_card_info->sim_card_mobile_number),
            'euro_amount'=>0,
            'new_balance'=>'0',
            'conversion_rate'=>0,
            'commission'=>0,
            'new_balance_euro'=>0,
        ));

        // Send Firebase Notification
        $admin_users = DB::connection('mysql')
            ->table('aauth_users')
            ->selectRaw('fcm_token')
            ->where('store_vendor_id', '=', $sim_card_info->store_id);

        $admin_result = $admin_users->get();
        foreach($admin_result as $value)
        {
            if(!empty($value->fcm_token))
            {
                $n = new \App\Libs\FirebaseMessaging($value->fcm_token);
                $message = "Sim Card (ICCID: ".$sim_card_info->sim_card_iccid.", Mobile Number: ".$sim_card_info->sim_card_mobile_number.") have been activated. Due added Euro ".$ricarica."/=";
                $n->setMessage("Sim Card Activated", $message, config('constants.SIMCARD_ACTIVATED'))->sendMessage();
            }
        }

        self::updateSimCardBalance($sim_card_info->store_id);

        $pos = 1;
        if(!empty($request->file)){
            foreach ($request->file as $file) {
                $fileName = "simcard_".time().$pos.'.'.$file->extension();
                $file->move(base_path('public/assets/sim_card/'.$sim_card_id.'/'.($profile_details['user_type'] == "super_admin"?"admin":"general")), $fileName);
                $pos = $pos + 1;

                $row_id = uniqid('').bin2hex(random_bytes(8));

                DB::connection('mysql')->table('sc_sim_card_files')->insert(array(
                    'row_id'=>$row_id,
                    'file_path' => (('assets/sim_card/'.$sim_card_id.'/'.($profile_details['user_type'] == "super_admin"?"admin":"general")).'/'.$fileName),
                    'sc_sim_card_id' => $sim_card_id,
                    'created_at' => date("Y-m-d H:i:s")
                ));

                self::sendUploadRequestToDigOcenSpace((base_path('public/assets/sim_card/'.$sim_card_id.'/'.($profile_details['user_type'] == "super_admin"?"admin":"general")).'/'.$fileName), (('assets/sim_card/'.$sim_card_id.'/'.($profile_details['user_type'] == "super_admin"?"admin":"general")).'/'.$fileName), "sc_sim_card_files", array("row_id"), array($row_id));
            }
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    private function addDueToParentReseller($reseller_id, $offer_id, $sim_card_info)
    {
        /*
         * Steps:
         * 1. Search reseller's parent reseller
         * 2. Get Parent Reseller offer by Parent Reseller's parent reseller.
         * 3. If there is no selected price for parent reseller. due will be reseller price.
         * 4. DO adjust.
         */
        $parentReseller = DB::connection('mysql')->table('store')->selectRaw("parent_store_id")->where(array(
            'store_id'=>$reseller_id
        ))->first();

        //Log::channel('single')->info('addDueToParentReseller:: ID -> '.$reseller_id);

        if(empty($parentReseller)) return;

        $svi = $parentReseller->parent_store_id;

        $parentResellerS_parentReseller = DB::connection('mysql')->table('store')->selectRaw("parent_store_id")->where(array(
            'store_id'=>$parentReseller->parent_store_id
        ))->first();

        if($parentResellerS_parentReseller)
        {
            //Log::channel('single')->info('addDueToParentReseller:: ParentID -> '.$parentResellerS_parentReseller->parent_store_id);
            $svi = $parentResellerS_parentReseller->parent_store_id;
        }

        $reseller_price = 0;

        //Log::channel('single')->info('addDueToParentReseller:: $svi -> '.$svi);

        $SimCardOffer = DB::connection('mysql')->table('sc_simcard_offer')
            ->selectRaw("
                IFNULL(sc_simcard_offer_reseller.title, sc_simcard_offer.title) as title,
                IFNULL(sc_simcard_offer_reseller.description, sc_simcard_offer.description) as description,
                IFNULL(sc_simcard_offer_reseller.bonus, sc_simcard_offer.bonus) as bonus,
                IFNULL(sc_simcard_offer_reseller.reseller_price, sc_simcard_offer.reseller_price) as reseller_price,
                IFNULL(sc_simcard_offer_reseller.status, sc_simcard_offer.status) as status,
                sc_simcard_offer.id,
                sc_simcard_offer_reseller.reseller_offer as sc_simcard_resellers_reseller_offer,
                sc_simcard_offer.reseller_offer as sc_simcard_reseller_offer
                ")
            ->leftJoin('sc_simcard_offer_reseller', function($join) use ($svi)
            {
                $join->on('sc_simcard_offer_reseller.sc_simcard_offer_id', '=', 'sc_simcard_offer.id');
                $join->on('sc_simcard_offer_reseller.store_id','=',DB::raw("'".$svi."'"));
            })
            ->where('sc_simcard_offer.id', DB::raw("'".$offer_id."'"))->first();

        if(!empty($SimCardOffer))
        {
            $reseller_price = $SimCardOffer->reseller_price;

            //Log::channel('single')->info('sc_simcard_resellers_reseller_offer -> '.$SimCardOffer->sc_simcard_resellers_reseller_offer);
            //Log::channel('single')->info('sc_simcard_reseller_offer -> '.$SimCardOffer->sc_simcard_reseller_offer);
            //Log::channel('single')->info('sc_simcard_reseller_offer -> '.json_encode($SimCardOffer));

            $sc_simcard_resellers_reseller_offer = json_decode($SimCardOffer->sc_simcard_resellers_reseller_offer);
            if(!empty($sc_simcard_resellers_reseller_offer))
            {
                foreach ($sc_simcard_resellers_reseller_offer as $offer_id)
                {
                    if(str_contains($offer_id, ($parentReseller->parent_store_id."|")))
                    {
                        $reseller_price = explode("|", $offer_id)[1];
                        break;
                    }
                }
            }

            $sc_simcard_reseller_offer = json_decode($SimCardOffer->sc_simcard_reseller_offer);
            if(!empty($sc_simcard_reseller_offer))
            {
                foreach ($sc_simcard_reseller_offer as $offer_id)
                {
                    if(str_contains($offer_id, ($parentReseller->parent_store_id."|")))
                    {
                        $reseller_price = explode("|", $offer_id)[1];
                        break;
                    }
                }
            }

            DB::statement("UPDATE store SET store.simcard_due_amount = (store.simcard_due_amount + ".$reseller_price.") WHERE store.store_id ='".$parentReseller->parent_store_id."'");

            $reseller = DB::connection('mysql')->table('store')->selectRaw("store_name")->where(array(
                'store_id'=>$reseller_id
            ))->first();

            DB::connection('mysql')->table('adjustment_history')->insert(array(
                'row_id'=>uniqid('').bin2hex(random_bytes(8)),
                'created_on' => date("Y-m-d H:i:s"),
                'type'=>'store',
                'adjustment_type'=>'simcard',
                'store_vendor_id'=>$parentReseller->parent_store_id,
                'adjusted_amount'=>0,
                'adjustment_percent'=>0,
                'received_amount'=>$reseller_price,
                'note'=> ("Reseller: (".$reseller->store_name."). Recharge added for ".$sim_card_info->product_name." Sim Card ICCID: ".$sim_card_info->sim_card_iccid.", Sim Card Mobile Number: ".$sim_card_info->sim_card_mobile_number),
                'euro_amount'=>0,
                'new_balance'=>'0',
                'conversion_rate'=>0,
                'commission'=>0,
                'new_balance_euro'=>0,
            ));

            // Send Firebase Notification
            $admin_users = DB::connection('mysql')
                ->table('aauth_users')
                ->selectRaw('fcm_token')
                ->where('store_vendor_id', '=', $parentReseller->parent_store_id);

            $admin_result = $admin_users->get();
            foreach($admin_result as $value)
            {
                if(!empty($value->fcm_token))
                {
                    $n = new \App\Libs\FirebaseMessaging($value->fcm_token);
                    $message = "Sim Card (ICCID: ".$sim_card_info->sim_card_iccid.", Mobile Number: ".$sim_card_info->sim_card_mobile_number.") have been activated. Due added Euro ".$reseller_price."/=";
                    $n->setMessage("Sim Card Activated", $message, config('constants.SIMCARD_ACTIVATED'))->sendMessage();
                }
            }

            self::updateSimCardBalance($parentReseller->parent_store_id);
        }
    }

    private function updateSimCardBalance($store_id)
    {
        // Update Sim-Card Balance
        $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, base_currency, due_euro, simcard_due_amount")->where('store_id', $store_id)->first();

        if($store_dta){
            $currentBalance = number_format($store_dta->balance, 2);
            $currentBalanceCurrency = strtoupper($store_dta->base_currency);

            Redis::set('user:current_balance:'.$store_id, json_encode(
                array(
                    'currency'=>$currentBalanceCurrency,
                    'amount'=>$currentBalance,
                    'due_euro'=>$store_dta->due_euro,
                    'simcard_due_amount'=>$store_dta->simcard_due_amount
                )
            ), 'EX', (60 * 60 * 24 * 7));
            // Update Sim-Card Balance
        }
    }


    public function change_status(Request $request)
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

        $ids = explode("|", $_POST["ids"]);
        $status = $_POST["status"];
        foreach ($ids as $id)
        {
            $uData = array(
                'activated_at'=>NULL,
                'activation_id'=>NULL,
                'order_id'=>"",
                'sales_price'=>NULL,
                'sales_status'=>"in_stock",
                'cost'=>NULL,
                'locked'=>"0",
                'locked_by'=>"0",
                'country_name'=>"",
                'date_of_birth'=>"",
            );

            if($status=="stock")
            {
                $uData['sold_at'] = NULL;
                $uData['store_id'] = "";
                $uData['status'] = "stocked";
            }

            if($status=="archived")
            {
                $uData['sold_at'] = NULL;
                $uData['store_id'] = "";
                $uData['status'] = "archived";
            }

            if($status=="trash")
            {
                $uData['sold_at'] = NULL;
                $uData['store_id'] = "";
                $uData['status'] = "trashed";
            }

            if($status=="reseller_own_stock")
            {
                $uData['status'] = "pending";
            }


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

    private function sendUploadRequestToDigOcenSpace($local, $remote, $table, $column=array(), $val=array())
    {
        DB::connection('mysql')->table('pending_dig_ocn_spc')->insert(array(
            'id'=>uniqid('').bin2hex(random_bytes(8)),
            'upload_absolute_path' => $local,
            'remote_file_name' => $remote,
            'table_name' => $table,
            'column_name' => json_encode($column),
            'table_primary_key_val' => json_encode($val)
        ));
    }
}
