<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    private $is_mobile_app_request = false;

    public function list(Request $request)
    {
        $token = $request->header('Authorization');
        //$params = json_decode($request->getContent(), true);
        $this->is_mobile_app_request = $request->hasHeader('mobile-app');

        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $params = $_POST;

        $query = DB::connection('mysql')->table('store')->orderBy("modified_at", "desc");

            //->where('status', 'enabled');

        if(!empty($params['search']) && !empty($params['search']['value']))
        {
            /*$query->orWhere('store_name', 'like', "%".$params['search']['value']."%");
            $query->orWhere('store_owner_name', 'like', "%".$params['search']['value']."%");
            $query->orWhere('store_phone_number', 'like', "%".$params['search']['value']."%");
            $query->orWhere('store_code', 'like', "%".$params['search']['value']."%");*/

            $query->whereRaw("((store_name like '%".$params['search']['value']."%') or (store_owner_name like '%".$params['search']['value']."%') or (store_phone_number like '%".$params['search']['value']."%') or (store_code like '%".$params['search']['value']."%'))");
        }

        if($this->is_mobile_app_request)
        {
            if(!empty($params['search_by']))
            {
                $query->whereRaw("((store_name like '%".$params['search_by']."%') or (store_owner_name like '%".$params['search_by']."%') or (store_phone_number like '%".$params['search_by']."%') or (store_code like '%".$params['search_by']."%'))");

                /*$query->orWhere('store_name', 'like', "%".$params['search_by']."%");
                $query->orWhere('store_owner_name', 'like', "%".$params['search_by']."%");
                $query->orWhere('store_phone_number', 'like', "%".$params['search_by']."%");
                $query->orWhere('store_code', 'like', "%".$params['search_by']."%");*/
            }
        }


        if($profile_details['user_type'] == "super_admin" || $profile_details['user_type'] == "manager")
        {
            $profile_details['store_vendor_id'] = "by_admin";
        }

        if(!empty($params['parent_store_id']))
        {
            $profile_details['store_vendor_id'] = $params['parent_store_id'];
        }

        //if($profile_details['user_type'] != "super_admin")
        //{
        $query->whereRaw("(parent_store_id = '".$profile_details['store_vendor_id']."' OR created_by = '".$profile_details['user_id']."')");
        //}

        if(!empty($params['simcard_view'])) $query->where("enable_simcard_access", '=', "1");
        //if(empty($params['simcard_view'])) $query->where("enable_simcard_access", '=', "0");

        //parent_store_id

        /*foreach ($params as $key => $value)
        {
            if(!empty($value))
                $query->where($key, 'like', $value."%");
        }*/
        //$g = $query->toSql();
        //$query->where("status", "=", "enabled");

        $queryCount = $query->count();

        $query->orderByRaw("store.store_code ASC, FIELD(store.status, 'enabled') DESC");

        $query->offset($params['start'])->limit(($params['length'] == -1?"50":$params['length']));

        $result = $query->get();

        $data = array();
        $storeList = array();
        $allStoreList = array();


        if(empty($params['simcard_view']))
        {
            foreach($result as $key => $value)
            {
                $sn = $value->store_name." [".$value->store_code."] ". (true?("  ".($this->is_mobile_app_request?"":("(Pin: ".$value->transaction_pin.")"))):"");
                $data[] = array(
                    ($key + 1),
                    (!$this->is_mobile_app_request?(!empty($value->image_path)?'<img style="height:50px;" src="/'.$value->image_path.'" class="img-fluid" alt="Responsive image">':''):(!empty($value->image_path)?$value->image_path:"")),
                    ("<a href='/reseller/".$value->store_id."/update'>".$sn."</a>"),
                    strtoupper($value->base_currency)." ".number_format($value->balance, 2),
                    //ucwords($value->base_currency)." ".number_format($value->loan_balance, 3),
                    //ucwords($value->base_currency)." ".number_format($value->loan_slab, 3),
                    //number_format($value->conversion_rate, 3),

                    (!$this->is_mobile_app_request?(floatval($value->due_euro) > 0 ?"<span style='color: red'>&euro; ".number_format($value->due_euro, 2)."</span>":"<span style='color: green'> &euro;".number_format((floatval($value->due_euro) * (-1)), 2)."</span>"):$value->due_euro),

                    $value->status,
                    date("m/d/Y", strtotime($value->created_at)),
                    $value->store_id."|".$value->status."|".$value->conversion_rate."|".$value->base_currency."|".$value->base_add_balance_commission_rate,
                );

                $storeList[] = array(
                    'store_id' => $value->store_id,
                    'store_name' => $value->store_name." [".$value->store_code."] "
                );
            }
        }
        else
        {
            foreach($result as $key => $value)
            {
                $data[] = array(
                    ($key + 1),
                    (!$this->is_mobile_app_request?(!empty($value->image_path)?'<img style="height:50px;" src="/'.$value->image_path.'" class="img-fluid" alt="Responsive image">':''):(!empty($value->image_path)?$value->image_path:"")),
                    $value->store_name." [".$value->store_code."] ",
                    "€ ".number_format($value->simcard_due_amount, 2),
                    $value->status,
                    date("m/d/Y", strtotime($value->created_at)),
                    $value->store_id."|".$value->status,
                );

                $storeList[] = array(
                    'store_id' => $value->store_id,
                    'store_name' => $value->store_name." [".$value->store_code."] "
                );
            }
        }

        $storeListQ = DB::connection('mysql')->table('store')->orderBy("modified_at", "desc");

        if($profile_details['user_type'] == "super_admin")
        {}
        else
        {
            $storeListQ->where(array(
                'parent_store_id'=>$profile_details['store_vendor_id']
            ));
        }

        if(!empty($params['simcard_view'])) $storeListQ->where("enable_simcard_access", '=', "1");

        $__storeList = $storeListQ->get();


        foreach($__storeList as $value)
        {
            $allStoreList[] = array(
                'store_id' => $value->store_id,
                'store_name' => $value->store_name." [".$value->store_code."] "
            );
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$data,
            'store_list'=>$storeList,
            'allStoreList'=>$allStoreList,
            'draw'=>$params['draw'],
            'recordsFiltered'=>$queryCount,
            'recordsTotal'=>$queryCount
        ), 200);
    }

    public function create(Request $request)
    {
        $this->is_mobile_app_request = $request->hasHeader('mobile-app');


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
            'store_name' => 'required|string|min:2|max:200',
            'manager_user_name' => 'required|string|min:3|max:30',
            'manager_user_password' => 'required|string|min:3|max:30',
            'commission' => 'required',
            'conversion_rate' => 'required',
            'store_code' => 'required',
            'transaction_pin' => 'required|min:4|max:4',
            'baseCurrency' => 'required'
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

        $search_for_store_code = DB::connection('mysql')->table('store')->where('store_code', $data['store_code'])->first();
        if($search_for_store_code)
        {
            $errorMessages = array('This reseller code already found in '.$search_for_store_code->store_name);

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        if(!empty($data['manager_user_name']) && !empty($data['manager_user_password']))
        {
            $aauth_users_dta = DB::connection('mysql')->table('aauth_users')->where('username', $data['manager_user_name'])->first();

            if($aauth_users_dta)
            {
                $errorMessages = array('User Name Already Exists');

                return response()->json(array(
                    'right_now'=>date("Y-m-d H:i:s"),
                    'timestamp'=>time(),
                    'success'=>false,
                    'message'=>$errorMessages
                ), 406);
            }
        }

        $store_id = uniqid('').bin2hex(random_bytes(8));

        $store_commission_percent = json_decode($data['mfsList']);
        foreach($store_commission_percent as $key => &$value)
        {
            $value->value = '0.0';
        }

        $store_dta = DB::connection('mysql')->table('store')->selectRaw("default_conv_rate_json, notice_meta")->first();
        if(!$store_dta)
        {
            $default_conv_rate_json = '[{"type":"bdt","name":"BDT","conv_amount":102},{"type":"usd","name":"USD","conv_amount":1.19},{"type":"gbp","name":"GBP","conv_amount":0.86},{"type":"cfa_franc","name":"CFA Franc","conv_amount":655.96}]';
            $store_conv_rate_json = '[{"type":"bdt","name":"BDT","conv_amount":102},{"type":"usd","name":"USD","conv_amount":1.19},{"type":"gbp","name":"GBP","conv_amount":0.86},{"type":"cfa_franc","name":"CFA Franc","conv_amount":655.96}]';
            $notice_meta = json_encode(array());
        }
        else
        {
            $default_conv_rate_json = $store_dta->default_conv_rate_json;
            $store_conv_rate_json = $store_dta->default_conv_rate_json;
            $notice_meta = $store_dta->notice_meta;
        }

        if(empty($data['service_charge_slabs']))
        {
            $data['service_charge_slabs'] = json_encode(array(
                array('from'=>'0', 'to'=>'50', 'charge'=>'3'),
                array('from'=>'50', 'to'=>'100', 'charge'=>'4'),
                array('from'=>'100', 'to'=>'150', 'charge'=>'5'),
                array('from'=>'150', 'to'=>'200', 'charge'=>'6'),
                array('from'=>'200', 'to'=>'250', 'charge'=>'7'),
            ));
        }

        if(empty($data['service_charge_slabs_t2']))
        {
            $data['service_charge_slabs_t2'] = json_encode(array(
                array('from'=>'0', 'to'=>'50', 'charge'=>'3'),
                array('from'=>'50', 'to'=>'100', 'charge'=>'4'),
                array('from'=>'100', 'to'=>'150', 'charge'=>'5'),
                array('from'=>'150', 'to'=>'200', 'charge'=>'6'),
                array('from'=>'200', 'to'=>'250', 'charge'=>'7'),
            ));
        }

        $parent_store_id = (!empty($data['parent_store_id'])?$data['parent_store_id']:"by_admin");

        DB::connection('mysql')->table('store')->insert(array(
            'store_id'=>$store_id,
            'store_name' => $data['store_name'],
            'store_code' => $data['store_code'],
            'parent_store_id' => (!empty($profile_details['store_vendor_id'])?$profile_details['store_vendor_id']:$parent_store_id),
            'status' => 'enabled',
            'created_by' => $data['user_id'],
            'created_at' => date("Y-m-d H:i:s"),
            'modified_at' => date("Y-m-d H:i:s"),
            'balance' => '0.0',
            'loan_slab'=>$data['loan_slab'],
            'transaction_pin'=>$data['transaction_pin'],
            'loan_balance'=>'0.0',
            'store_commission_percent'=>json_encode($store_commission_percent),
            'default_conv_rate_json'=>$default_conv_rate_json,
            'store_conv_rate_json'=>$store_conv_rate_json,
            'mfs_slab'=>($data['mfsSlab']),
            'service_charge_slabs'=>$data['service_charge_slabs'],
            'service_charge_slabs_t2'=>$data['service_charge_slabs_t2'],
            'notice_meta'=>$notice_meta,
            'd4'=>'',
            'note'=>(!empty($data['note'])?$data['note']:""),
            'allowed_products'=>($data['allowed_products']),
            'commission_percent' => ($data['mfsList']),
            'conversion_rate' => $data['conversion_rate'],
            'store_owner_name'=>$data['store_owner_name'],
            'store_address'=>$data['store_address'],
            'store_phone_number'=>$data['store_phone_number'],
            'base_currency'=>$data['baseCurrency'],
            'base_add_balance_commission_rate'=>$data['base_add_balance_commission_rate'],
            'pending_balance'=>'0.0',
            'due_euro'=>'0.0',
            'simcard_due_amount'=>'0.0',
            'enable_simcard_access'=>((!empty($data['allow_simcard_management']) && $data['allow_simcard_management'] == "Yes")?"1":"0")
        ));

        if(!empty($data['manager_user_name']) && !empty($data['manager_user_password']))
        {
            $userId = uniqid('').bin2hex(random_bytes(8));
            DB::connection('mysql')->table('aauth_users')->insert(array(
                'id'=>$userId,
                'email' => $data['manager_user_name'],
                'username' => $data['manager_user_name'],
                'pass' => base64_encode($data['manager_user_password']),
                'date_created' => date("Y-m-d H:i:s"),
                'store_vendor_admin' => 'true',
                'store_vendor_id'=>$store_id,
                'user_type'=>'store',
                'insecure' => $data['manager_user_password'],
                'fcm_token' => ''
            ));

            if(!empty($data['allow_reseller_creation']) && $data['allow_reseller_creation'] == "Yes")
            {
                foreach(array(7, //StoreController::list
                            8, //RechargeController::create
                            9, //RechargeController::update
                        ) as $key)
                {
                    DB::connection('mysql')->table('aauth_perm_to_user')->insert(array(
                        'perm_id'=>$key,
                        'user_id' => $userId
                    ));
                }
            }

            if(!empty($data['allow_simcard_management']) && $data['allow_simcard_management'] == "Yes")
            {
                foreach(array(59, //Simcard::view_orders
                            52, //Simcard::create_order
                            40, //Simcard::list
                            42, //Simcard::view_stock
                            43, //Simcard::view_sold
                            44, //Simcard::sale
                            49, //SimCardReport::sales_report
                            50, //SimCardReport::recharge_report
                            51, //SimCardReport::adjustment_report
                        ) as $key)
                {
                    DB::connection('mysql')->table('aauth_perm_to_user')->insert(array(
                        'perm_id'=>$key,
                        'user_id' => $userId
                    ));
                }

                if(!empty($data['allow_reseller_creation']) && $data['allow_reseller_creation'] == "Yes")
                {
                    foreach(array(53, //Simcard::approve_order
                                55, //Simcard::reject_order
                                65, //Simcard::appoint_sim_card
                                45, //Simcard::promo
                            ) as $key)
                    {
                        DB::connection('mysql')->table('aauth_perm_to_user')->insert(array(
                            'perm_id'=>$key,
                            'user_id' => $userId
                        ));
                    }
                }
            }

            if(!empty($store_commission_percent))
            {
                foreach(array(
                            15, //RechargeController::list
                            34, //RechargeController::mfs_summery
                            35, //RechargeController::reseller_balance_recharge
                            36, //RechargeController::reseller_due_adjust
                            37, //RechargeController::reseller_due_statement
                            38, //ReportController::payment_doc_upload_statement
                            39  //RechargeController::upload_payment_doc
                        ) as $key)
                {
                    DB::connection('mysql')->table('aauth_perm_to_user')->insert(array(
                        'perm_id'=>$key,
                        'user_id' => $userId
                    ));
                }
            }

            /*
             * foreach(array(15, //RechargeController::list
                        16, //RechargeController::create
                    ) as $key)
            {
                DB::connection('mysql')->table('aauth_perm_to_user')->insert(array(
                    'perm_id'=>$key,
                    'user_id' => $userId
                ));
            }*/
        }

        if(!empty($request->file)){
            $fileName = "store_logo_".time().'.'.$request->file->extension();
            //$request->file->move(storage_path('app/public/store_logo'), $fileName);
            $request->file->move(base_path('public/assets/store_logo'), $fileName);
            //Storage::disk('local')->put('public/file.txt', 'Contents');

            DB::connection('mysql')->table("store")
            ->where(array(
                'store_id'=>$store_id
            ))->update(array(
                 'image_path'=>'assets/store_logo/'.$fileName
            ));
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function info(Request $request, $store_id)
    {
        $store_dta = DB::connection('mysql')->table('store')->selectRaw("*")->where('store_id', $store_id)->first();

        if(!$store_dta)
        {
            $errorMessages = array('Store Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $store_dta->{"allow_reseller_creation"} = "No";

        $store_list_permission = DB::connection('mysql')->table('aauth_perm_to_user')
            ->selectRaw("aauth_perm_to_user.perm_id")
            ->leftJoin('aauth_users', 'aauth_users.id', '=', 'aauth_perm_to_user.user_id')
            ->where('aauth_perm_to_user.perm_id', '=', '7')
            ->where('aauth_users.store_vendor_id', '=', $store_id)
            ->first();

        if($store_list_permission)
        {
            $store_dta->{"allow_reseller_creation"} = "Yes";
        }

        $store_dta->{"allow_simcard_management"} = "No";

        $store_list_permission = DB::connection('mysql')->table('aauth_perm_to_user')
            ->selectRaw("aauth_perm_to_user.perm_id")
            ->leftJoin('aauth_users', 'aauth_users.id', '=', 'aauth_perm_to_user.user_id')
            ->where('aauth_perm_to_user.perm_id', '=', '59')
            ->where('aauth_users.store_vendor_id', '=', $store_id)
            ->first();

        if($store_list_permission)
        {
            $store_dta->{"allow_simcard_management"} = "Yes";
        }

        $store_dta->{"default_mfs_list"} = DB::connection('mysql')->table('mfs')->selectRaw("mfs_name as name, mfs_id as id, '0.0' as commission, '0.0' as charge")->get();

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'data'=>$store_dta,
        ), 200);
    }

    public function adjust(Request $request, $store_id)
    {
        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, commission_percent, conversion_rate, loan_slab, loan_balance, base_currency")->where('store_id', $store_id)->first();

        if(!$store_dta)
        {
            $errorMessages = array('Store Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $data['new_balance'] = abs((floatval($data['selected_currency_rate']) * floatval($data['euro_amount'])) * ((100 - floatval($data['commission'])) / 100));

        if($profile_details['user_type'] != "super_admin")
        {
            // Lets check logged user have balance on his account.
            $myStore_dta = DB::connection('mysql')->table('store')->selectRaw("balance")->whereRaw("balance >= ".floatval($data['new_balance']))->where('store_id', $profile_details['store_vendor_id'])->first();

            if(!$myStore_dta)
            {
                $errorMessages = array("You don't have sufficient balance to add balance for this reseller.");

                return response()->json(array(
                    'right_now'=>date("Y-m-d H:i:s"),
                    'timestamp'=>time(),
                    'success'=>false,
                    'message'=>$errorMessages
                ), 402);
            }
        }

        if(!empty($data['new_balance'])){

            $deductLoan = ((floatval($store_dta->loan_balance) < floatval($data['new_balance']))?floatval($store_dta->loan_balance):floatval($data['new_balance']));
            $addCb = ((floatval($store_dta->loan_balance) < floatval($data['new_balance']))?(floatval($data['new_balance']) - floatval($store_dta->loan_balance)):0);

            DB::statement("UPDATE `store` SET `balance` = (store.balance + ".$addCb."), `due_euro` = (store.due_euro + ".floatval($data['euro_amount'])."), `loan_balance` = (store.loan_balance - ".$deductLoan.") WHERE `store`.`store_id` = '".$store_id."'");

            if($profile_details['user_type'] != "super_admin")
            {
                DB::statement("UPDATE store SET store.balance = (store.balance - ".floatval($data['new_balance']).") WHERE store.store_id = '".$profile_details['store_vendor_id']."'");

                $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, base_currency, due_euro, simcard_due_amount")->where('store_id', $profile_details['store_vendor_id'])->first();

                $currentBalance = number_format($store_dta->balance, 2);
                $currentBalanceCurrency = strtoupper($store_dta->base_currency);

                Redis::set('user:current_balance:'.$profile_details['store_vendor_id'], json_encode(
                    array(
                        'currency'=>$currentBalanceCurrency,
                        'amount'=>$currentBalance,
                        'due_euro'=>$store_dta->due_euro,
                        'simcard_due_amount'=>$store_dta->simcard_due_amount
                    )
                ), 'EX', (60 * 60 * 24 * 7));
            }

            $recharge_id = uniqid('').bin2hex(random_bytes(8));


            DB::connection('mysql')->table('recharge')->insert(array(
                'recharge_id'=>$recharge_id,
                'mfs_name' => 'Balance Refill',
                'phone_number' => '',
                'recharge_amount' => $data['new_balance'],
                'mfs_number_type'=> "",
                'base_currency'=>$store_dta->base_currency,
                'sending_currency'=>$store_dta->base_currency,
                'b1'=>'0',
                'locked_by'=>"",
                'recharge_type'=>'store_refill',
                'processed_vendor_id'=>"",
                'recharge_meta'=>json_encode($data, true),
                'recharge_status'=>'approved',
                'created_by' => $store_id,
                'created_at' => date("Y-m-d H:i:s"),
                'modified_at' => date("Y-m-d H:i:s"),

                'commission_amount'=>0,
                'store_conversion_rate'=>0,
                'vendor_balance'=>0,
                'store_balance'=>0,
                'store_loan_balance'=>0,
                'due_euro'=>0,
                'b2'=>0,
                'b3'=>0
            ));

            //DB::statement("UPDATE `recharge`, (SELECT COUNT(*) as total FROM `recharge`) AS `recharge_row_count` SET `recharge`.`serial_number` = `recharge_row_count`.total WHERE `recharge`.`recharge_id` = '".$recharge_id."'");

            DB::statement("UPDATE `recharge`, (SELECT serial_number FROM `recharge` ORDER BY serial_number DESC LIMIT 1) AS `recharge_last_row` SET `recharge`.`serial_number` = (`recharge_last_row`.serial_number + 1) WHERE `recharge`.`recharge_id` = '".$recharge_id."'");

            DB::statement("UPDATE recharge INNER JOIN store ON store.store_id = recharge.created_by SET recharge.store_balance = store.balance, recharge.store_loan_balance = store.loan_balance, recharge.due_euro = store.due_euro WHERE recharge.recharge_id = '".$recharge_id."'");

            $token = $request->header('Authorization');

            $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, commission_percent, conversion_rate, loan_slab, loan_balance, base_currency, due_euro, simcard_due_amount")->where('store_id', $store_id)->first();

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

            DB::connection('mysql')->table('adjustment_history')->insert(array(
                'row_id'=>uniqid('').bin2hex(random_bytes(8)),
                'created_on' => date("Y-m-d H:i:s"),
                'type'=>'store',
                'store_vendor_id'=>$store_id,
                'adjusted_amount'=>floatval($data['new_balance']),
                'adjustment_percent'=>0,
                'adjustment_type_id'=>$recharge_id,
                'received_amount'=>0,
                'conversion_rate'=>$data['selected_currency_rate'],
                'new_balance'=>$store_dta->balance,
                'new_balance_euro'=>$store_dta->due_euro,
                'note'=> $data['note'],
                'commission'=>$data['commission'],
                'euro_amount'=>$data['euro_amount'],
                'created_by'=>(($profile_details['user_type'] != "super_admin")?$profile_details['store_vendor_id']:"system")
            ));
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function receive_euro(Request $request, $store_id)
    {
        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, commission_percent, conversion_rate, loan_slab, loan_balance, base_currency")->where('store_id', $store_id)->first();

        if(!$store_dta)
        {
            $errorMessages = array('Store Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $validator = Validator::make($data, [//json_decode($request->getContent(), true), [
            'store_id' => 'required',
            'euro_amount' => 'required'
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

        if(!empty($data['euro_amount'])){

            if(!empty($data['simcard']))
            {
                // Sim Card Adjustment

                DB::statement("UPDATE `store` SET `simcard_due_amount` = (store.simcard_due_amount - ".floatval($data['euro_amount']).") WHERE `store`.`store_id` = '".$store_id."'");

                $token = $request->header('Authorization');

                $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, commission_percent, conversion_rate, loan_slab, loan_balance, base_currency, due_euro, simcard_due_amount")->where('store_id', $store_id)->first();

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


                DB::connection('mysql')->table('adjustment_history')->insert(array(
                    'row_id'=>uniqid('').bin2hex(random_bytes(8)),
                    'created_on' => date("Y-m-d H:i:s"),
                    'type'=>'store',
                    'adjustment_type'=>'simcard',
                    'store_vendor_id'=>$store_id,
                    'adjusted_amount'=>$data['euro_amount'],
                    'adjustment_percent'=>0,
                    'received_amount'=>0,
                    'adjustment_type_id'=>'none',
                    'note'=> $data['note'],
                    'euro_amount'=>0,
                    'new_balance'=>'0',
                    'conversion_rate'=>0,
                    'commission'=>0,
                    'new_balance_euro'=>0,
                ));
            }

            if(empty($data['simcard']))
            {
                DB::statement("UPDATE `store` SET `due_euro` = (store.due_euro - ".floatval($data['euro_amount'])."), last_payment_received_amount = '".floatval($data['euro_amount'])."', last_payment_received = '".date("Y-m-d H:i:s")."' WHERE `store`.`store_id` = '".$store_id."'");

                $token = $request->header('Authorization');

                $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, commission_percent, conversion_rate, loan_slab, loan_balance, base_currency, due_euro, simcard_due_amount")->where('store_id', $store_id)->first();

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

                DB::connection('mysql')->table('adjustment_history')->insert(array(
                    'row_id'=>uniqid('').bin2hex(random_bytes(8)),
                    'created_on' => date("Y-m-d H:i:s"),
                    'type'=>'store',
                    'store_vendor_id'=>$store_id,
                    'adjusted_amount'=>0,
                    'adjustment_percent'=>0,
                    'received_amount'=>$data['euro_amount'],
                    'adjustment_type_id'=>'none',
                    'note'=> $data['note'],
                    'euro_amount'=>$data['euro_amount'],
                    'new_balance'=>'0',
                    'conversion_rate'=>0,
                    'commission'=>0,
                    'new_balance_euro'=>$store_dta->due_euro,
                ));

                // Due to strange Issue. Appending Again
                DB::statement("UPDATE `store` SET last_payment_received_amount = '".floatval($data['euro_amount'])."', last_payment_received = '".date("Y-m-d H:i:s")."' WHERE `store`.`store_id` = '".$store_id."'");
            }
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function return_amount(Request $request, $store_id)
    {
        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, commission_percent, conversion_rate, loan_slab, loan_balance, base_currency")->where('store_id', $store_id)->first();

        if(!$store_dta)
        {
            $errorMessages = array('Store Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);


        if(!empty($data['new_balance'])){

            $conversation_rate = (!empty($data['conv_rate'])?floatval($data['conv_rate']):1);

            DB::statement("UPDATE `store` SET `balance` = (store.balance - ".(floatval($data['new_balance']) * $conversation_rate).") WHERE `store`.`store_id` = '".$store_id."'");

            if($profile_details['user_type'] == "store") DB::statement("UPDATE `store` SET `balance` = (store.balance + ".(floatval($data['new_balance']) * $conversation_rate).") WHERE `store`.`store_id` = '".$profile_details['store_vendor_id']."'");

            $recharge_id = uniqid('').bin2hex(random_bytes(8));

            DB::connection('mysql')->table('recharge')->insert(array(
                'recharge_id'=>$recharge_id,
                'mfs_name' => 'Balance Return',
                'phone_number' => '',
                'recharge_amount' => (floatval($data['new_balance']) * $conversation_rate),
                'recharge_meta'=>json_encode($data, true),
                'note' => $data['note'],
                'mfs_number_type'=> "",
                'base_currency'=>$store_dta->base_currency,
                'sending_currency'=>$store_dta->base_currency,
                'b1'=>'0',
                'locked_by'=>"",
                'recharge_type'=>'store_return',
                'processed_vendor_id'=>"",
                'recharge_status'=>'approved',
                'created_by' => $store_id,
                'created_at' => date("Y-m-d H:i:s"),
                'modified_at' => date("Y-m-d H:i:s"),

                'commission_amount'=>0,
                'store_conversion_rate'=>0,
                'vendor_balance'=>0,
                'store_balance'=>0,
                'store_loan_balance'=>0,
                'due_euro'=>0,
                'b2'=>0,
                'b3'=>0
            ));

            //DB::statement("UPDATE `recharge`, (SELECT COUNT(*) as total FROM `recharge`) AS `recharge_row_count` SET `recharge`.`serial_number` = `recharge_row_count`.total WHERE `recharge`.`recharge_id` = '".$recharge_id."'");

            DB::statement("UPDATE `recharge`, (SELECT serial_number FROM `recharge` ORDER BY serial_number DESC LIMIT 1) AS `recharge_last_row` SET `recharge`.`serial_number` = (`recharge_last_row`.serial_number + 1) WHERE `recharge`.`recharge_id` = '".$recharge_id."'");

            DB::statement("UPDATE recharge INNER JOIN store ON store.store_id = recharge.created_by SET recharge.store_balance = store.balance, recharge.store_loan_balance = store.loan_balance, recharge.due_euro = store.due_euro WHERE recharge.recharge_id = '".$recharge_id."'");

            $token = $request->header('Authorization');

            $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, commission_percent, conversion_rate, loan_slab, loan_balance, base_currency, due_euro, simcard_due_amount")->where('store_id', $store_id)->first();

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

            if($profile_details['user_type'] != "super_admin")
            {
                //DB::statement("UPDATE store SET store.balance = (store.balance + ".floatval($data['new_balance']).") WHERE store.store_id = '".$profile_details['store_vendor_id']."'");

                $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, base_currency, due_euro, simcard_due_amount")->where('store_id', $profile_details['store_vendor_id'])->first();

                $currentBalance = number_format($store_dta->balance, 2);
                $currentBalanceCurrency = strtoupper($store_dta->base_currency);

                Redis::set('user:current_balance:'.$profile_details['store_vendor_id'], json_encode(
                    array(
                        'currency'=>$currentBalanceCurrency,
                        'amount'=>$currentBalance,
                        'due_euro'=>$store_dta->due_euro,
                        'simcard_due_amount'=>$store_dta->simcard_due_amount
                    )
                ), 'EX', (60 * 60 * 24 * 7));
            }
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function update(Request $request, $store_id)
    {
        $data = $_POST;
        if(!empty($request->getContent()))
        {
            $data = json_decode($request->getContent(), true);
        }
        //$data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>$_POST,
                'success'=>$request->getContent(),
            ), 400);

        $store_dta = DB::connection('mysql')->table('store')->select("store_id")->where('store_id', $store_id)->first();

        if(!$store_dta)
        {
            $errorMessages = array('Store Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
            'transaction_pin' => 'min:4|max:4',
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

        if(!empty($data['mfsList']))
        {
            $store_commission_percent = json_decode($data['mfsList']);
            foreach($store_commission_percent as $key => &$value)
            {
                $value->value = '0.0';
            }
            $data['store_commission_percent'] = json_encode($store_commission_percent);
        }



        $info = array();
        if(!empty($data['store_name']))
            $info['store_name'] = $data['store_name'];

        if(!empty($data['status']))
            $info['status'] = $data['status'];

        /*if(empty($data['mfsList']))
            $info['commission_percent'] = '{}';*/
        if(!empty($data['mfsList']))
            $info['commission_percent'] = $data['mfsList'];

        if(!empty($data['allowed_products']))
            $info['allowed_products'] = $data['allowed_products'];

        /*if(empty($data['conversion_rate']))
            $info['conversion_rate'] = '0.0';*/
        if(!empty($data['conversion_rate']))
            $info['conversion_rate'] = $data['conversion_rate'];

        /*if(empty($data['store_commission_percent']))
            $info['store_commission_percent'] = '{}';*/
        if(!empty($data['store_commission_percent']))
            $info['store_commission_percent'] = $data['store_commission_percent'];


        if(!empty($data['store_owner_name']))
            $info['store_owner_name'] = $data['store_owner_name'];
        if(!empty($data['store_address']))
            $info['store_address'] = $data['store_address'];
        if(!empty($data['store_phone_number']))
            $info['store_phone_number'] = $data['store_phone_number'];

        if(!empty($data['transaction_pin']))
            $info['transaction_pin'] = $data['transaction_pin'];

        if(!empty($data['mfsSlab']))
            $info['mfs_slab'] = $data['mfsSlab'];

        if(!empty($data['service_charge_slabs']))
            $info['service_charge_slabs'] = $data['service_charge_slabs'];

        if(!empty($data['service_charge_slabs_t2']))
            $info['service_charge_slabs_t2'] = $data['service_charge_slabs_t2'];

        if(!empty($data['base_add_balance_commission_rate']))
            $info['base_add_balance_commission_rate'] = $data['base_add_balance_commission_rate'];

        if(!empty($data['allow_simcard_management']) && $data['allow_simcard_management'] == "Yes")
        {
            $info['enable_simcard_access'] = (($data['allow_simcard_management'] == "Yes")?"1":"0");
        } else {
            $info['enable_simcard_access'] = "0";
        }

        $info['note'] = (!empty($data['note'])?$data['note']:"");

        /*if(!empty($data['store_code']))
            $info['store_code'] = $data['store_code'];*/

        if(!empty($info))
        {
            DB::connection('mysql')->table("store")
            ->where(array(
                'store_id'=>$store_id
            ))->update($info);
        }

        if(!empty($data['status'])) {
            DB::connection('mysql')->table('aauth_users')
                ->where('store_vendor_id', $store_id)
                ->update(array(
                    'banned' => ($data['status'] == 'disabled' ? '1' : '0')
                ));
        }

        $aauth_users = DB::connection('mysql')->table('aauth_users')
            ->selectRaw("aauth_users.id")
            ->where('aauth_users.store_vendor_id', '=', $store_id)
            ->first();

        if(empty($data['mfsList']))
        {
            $store_commission_percent = [];
        } else {
            $store_commission_percent = json_decode($data['mfsList']);
        }

        if(!empty($store_commission_percent))
        {
            DB::connection('mysql')->table("aauth_perm_to_user")->where('user_id', $aauth_users->id)->whereIn('perm_id', array(
                15, //RechargeController::list
                34, //RechargeController::mfs_summery
                35, //RechargeController::reseller_balance_recharge
                36, //RechargeController::reseller_due_adjust
                37, //RechargeController::reseller_due_statement
                38, //ReportController::payment_doc_upload_statement
                39  //RechargeController::upload_payment_doc
            ))->delete();

            foreach(array(
                        15, //RechargeController::list
                        34, //RechargeController::mfs_summery
                        35, //RechargeController::reseller_balance_recharge
                        36, //RechargeController::reseller_due_adjust
                        37, //RechargeController::reseller_due_statement
                        38, //ReportController::payment_doc_upload_statement
                        39  //RechargeController::upload_payment_doc
                    ) as $key)
            {
                DB::connection('mysql')->table('aauth_perm_to_user')->insert(array(
                    'perm_id'=>$key,
                    'user_id' => $aauth_users->id
                ));
            }
        }

        if(empty($store_commission_percent))
        {
            DB::connection('mysql')->table("aauth_perm_to_user")->where('user_id', $aauth_users->id)->whereIn('perm_id', array(
                15, //RechargeController::list
                34, //RechargeController::mfs_summery
                35, //RechargeController::reseller_balance_recharge
                36, //RechargeController::reseller_due_adjust
                37, //RechargeController::reseller_due_statement
                38, //ReportController::payment_doc_upload_statement
                39  //RechargeController::upload_payment_doc
            ))->delete();
        }



        if(!empty($data['allow_reseller_creation']) && $data['allow_reseller_creation'] == "No")
        {
            DB::connection('mysql')->table("aauth_perm_to_user")->where('user_id', $aauth_users->id)->whereIn('perm_id', array(7, //StoreController::list
                8, //RechargeController::create
                9, //RechargeController::update,
                53, //Simcard::approve_order
                55, //Simcard::reject_order
            ))->delete();
        }

        if(!empty($data['allow_reseller_creation']) && $data['allow_reseller_creation'] == "Yes")
        {
            DB::connection('mysql')->table("aauth_perm_to_user")->where('user_id', $aauth_users->id)->whereIn('perm_id', array(7, //StoreController::list
                8, //RechargeController::create
                9, //RechargeController::update
            ))->delete();

            foreach(array(7, //StoreController::list
                        8, //RechargeController::create
                        9, //RechargeController::update
                    ) as $key)
            {
                DB::connection('mysql')->table('aauth_perm_to_user')->insert(array(
                    'perm_id'=>$key,
                    'user_id' => $aauth_users->id
                ));
            }
        }

        if(!empty($data['allow_simcard_management']) && $data['allow_simcard_management'] == "No")
        {
            DB::connection('mysql')->table("aauth_perm_to_user")->where('user_id', $aauth_users->id)->whereIn('perm_id', array(
                59, //Simcard::view_orders
                52, //Simcard::create_order
                40, //Simcard::list
                42, //Simcard::view_stock
                43, //Simcard::view_sold
                44, //Simcard::sale
                65, //Simcard::appoint_sim_card
                45, //Simcard::promo
                49, //SimCardReport::sales_report
                50, //SimCardReport::recharge_report
                51, //SimCardReport::adjustment_report
            ))->delete();
        }

        if(!empty($data['allow_simcard_management']) && $data['allow_simcard_management'] == "Yes")
        {
            DB::connection('mysql')->table("aauth_perm_to_user")->where('user_id', $aauth_users->id)->whereIn('perm_id', array(
                59, //Simcard::view_orders
                52, //Simcard::create_order
                40, //Simcard::list
                42, //Simcard::view_stock
                43, //Simcard::view_sold
                44, //Simcard::sale
                65, //Simcard::appoint_sim_card
                45, //Simcard::promo
                49, //SimCardReport::sales_report
                50, //SimCardReport::recharge_report
                51, //SimCardReport::adjustment_report
                53, //Simcard::approve_order
                55, //Simcard::reject_order
            ))->delete();

            foreach(array(59, //Simcard::view_orders
                        52, //Simcard::create_order
                        40, //Simcard::list
                        42, //Simcard::view_stock
                        43, //Simcard::view_sold
                        44, //Simcard::sale
                        49, //SimCardReport::sales_report
                        50, //SimCardReport::recharge_report
                        51, //SimCardReport::adjustment_report
                    ) as $key)
            {
                DB::connection('mysql')->table('aauth_perm_to_user')->insert(array(
                    'perm_id'=>$key,
                    'user_id' => $aauth_users->id
                ));
            }

            if(!empty($data['allow_reseller_creation']) && $data['allow_reseller_creation'] == "Yes")
            {
                foreach(array(53, //Simcard::approve_order
                            55, //Simcard::reject_order
                            65, //Simcard::appoint_sim_card
                            45, //Simcard::promo
                        ) as $key)
                {
                    DB::connection('mysql')->table('aauth_perm_to_user')->insert(array(
                        'perm_id'=>$key,
                        'user_id' => $aauth_users->id
                    ));
                }
            }
        }

        if(!empty($request->file)){
            $fileName = "store_logo_".time().'.'.$request->file->extension();
            //$request->file->move(storage_path('app/public/store_logo'), $fileName);
            $request->file->move(base_path('public/public/store_logo'), $fileName);
            //Storage::disk('local')->put('public/file.txt', 'Contents');

            DB::connection('mysql')->table("store")
                ->where(array(
                    'store_id'=>$store_id
                ))->update(array(
                    'image_path'=>'store_logo/'.$fileName
                ));
        }

            return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function remove(Request $request, $store_id)
    {
        $store_dta = DB::connection('mysql')->table('store')->where('store_id', $store_id)->first();

        if(!$store_dta)
        {
            $errorMessages = array('Store Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        DB::connection('mysql')->table("store")->where('store_id', $store_id)->delete();

        $user_dta = DB::connection('mysql')->table('aauth_users')->select("id")->where('store_vendor_id', $store_id)->first();

        DB::connection('mysql')->table("aauth_users")->where('store_vendor_id', $store_id)->delete();

        if($user_dta)
            DB::connection('mysql')->table("aauth_perm_to_user")->where('user_id', $user_dta->id)->delete();

        if(!empty($store_dta->image_path))
            Storage::disk('local')->delete('public/'.$store_dta->image_path);

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function list_saved_phone_number(Request $request, $store_id)
    {
        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>DB::connection('mysql')->table("store_phone_numbers")
                ->selectRaw("name as label, phone_number as descption")
                ->where('store_id', $store_id)->get(),
        ), 200);
    }

    public function save_phone_number(Request $request, $store_id)
    {
        $data = json_decode($request->getContent(), true);

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        DB::connection('mysql')->table('store_phone_numbers')->insert(array(
            'row_id'=>uniqid('').bin2hex(random_bytes(8)),
            'store_id'=>$store_id,
            'name'=>$data['name'],
            'phone_number'=>$data['phone_number'],
            'created_at' => date("Y-m-d H:i:s"),
        ));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function load_configuration(Request $request)
    {
        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'mfs_list'=>DB::connection('mysql')->table('mfs')->selectRaw("mfs_name as name, mfs_id as id, default_commission as commission, default_charge as charge")->get(),
            'success'=>true,
        ), 200);
    }

    public function save_store_currency(Request $request)
    {
        $token = $request->header('Authorization');
        $data = json_decode($request->getContent(), true);
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $info = array();

        if(!empty($data['default_conv_rate_json']))
            $info['default_conv_rate_json'] = $data['default_conv_rate_json'];

        if(!empty($data['store_conv_rate_json']))
            $info['store_conv_rate_json'] = $data['store_conv_rate_json'];

        if(!empty($data['notice_meta']))
            $info['notice_meta'] = $data['notice_meta'];

        if(!empty($data) && $profile_details['user_type'] == "store" && !empty($profile_details['store_vendor_id']))
        {
            DB::connection('mysql')->table("store")
                ->where(array(
                    'store_id'=>$profile_details['store_vendor_id']
                ))->update($info);

            DB::connection('mysql')->table("store")
                ->where(array(
                    'parent_store_id'=>$profile_details['store_vendor_id']
                ))->update($info);
        }

        if(!empty($data) && $profile_details['user_type'] !== "store" && empty($profile_details['store_vendor_id']))
        {
            DB::connection('mysql')->table("store")->where(array(
                'parent_store_id'=>'by_admin'
            ))->update($info);
        }

        if($profile_details['user_type'] == "store")
        {
            if(!empty($data['store_conv_rate_json'])){
                $profile_details['currency_conversions_list'] = json_decode($data['store_conv_rate_json']);
                Redis::set('user:token:'.$token, json_encode($profile_details), 'EX', (60 * 60 * 24 * 7));
            }
        }
        else
        {
            if(!empty($data['default_conv_rate_json'])) {
                $profile_details['currency_conversions_list'] = json_decode($data['default_conv_rate_json']);
                Redis::set('user:token:' . $token, json_encode($profile_details), 'EX', (60 * 60 * 24 * 7));
            }
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function save_store_conversion_rate(Request $request)
    {
        $token = $request->header('Authorization');
        $data = json_decode($request->getContent(), true);
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $info = array();

        if(!empty($data) && $profile_details['user_type'] == "store" && !empty($profile_details['store_vendor_id']))
        {
            if(!empty($data['conversion_rate']))
                $info['conversion_rate'] = $data['conversion_rate'];

            if(!empty($info))
            {
                DB::connection('mysql')->table("store")
                    ->where(array(
                        'store_id'=>$profile_details['store_vendor_id']
                    ))->update($info);
            }
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }
}
