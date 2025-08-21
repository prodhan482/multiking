<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class RechargeController extends Controller
{
    private $is_mobile_app_request = false;

    public function recent_activity(Request $request)
    {
        //$params = json_decode($request->getContent(), true);
        $params = $_POST;

        $token = $request->header('Authorization');
        $this->is_mobile_app_request = $request->hasHeader('mobile-app');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $query = DB::connection('mysql')->table('recharge')
            ->selectRaw('recharge.*, store.store_name, IFNULL(locker.vendor_name, processed_vendor.vendor_name) as vendor_name, parent.store_name AS parent_store_name, mfs_package.package_name AS mfs_package_name')
            ->leftJoin('store', 'store.store_id', '=', 'recharge.created_by')
            ->leftJoin('store as parent', 'parent.store_id', '=', 'store.parent_store_id')
            ->leftJoin('vendor as processed_vendor', 'processed_vendor.vendor_id', '=', 'recharge.processed_vendor_id')
            ->leftJoin('vendor as locker', 'locker.vendor_id', '=', 'recharge.locked_by')
            ->leftJoin('mfs_package', 'mfs_package.row_id', '=', 'recharge.mfs_package_id')
            ->orderBy("created_at", "desc");

        if($profile_details['user_type'] == "store")
        {
            if(empty($params['store_id']))
            {
                $query->where('recharge.created_by', '=', $profile_details['store_vendor_id']);
            }
            else
            {
                $query->where('recharge.created_by', '=', $params['store_id']);
            }

            $store_dta = DB::connection('mysql')->table('store')->selectRaw("balance, loan_balance, store_commission_percent, commission_percent, conversion_rate, mfs_slab, service_charge_slabs, service_charge_slabs_t2")->where('store_id', $profile_details['store_vendor_id'])->first();

            if(!empty($params['date_from']) && !empty($params['date_to']))
                $query->whereBetween("recharge.created_at", array(
                    date("Y-m-d 00:00:00", strtotime($params['date_from'])),
                    date("Y-m-d 23:59:59", strtotime($params['date_to']))
                ));

            if(!empty($params['mfs_id']))
                $query->where('recharge.mfs_id', '=', $params['mfs_id']);

            if(!empty($params['recharge_status']))
                $query->where('recharge.recharge_status', '=', $params['recharge_status']);

            if(!empty($params['phone_number']))
                $query->where('recharge.phone_number', '=', $params['phone_number']);
        }

        if($profile_details['user_type'] == "vendor" && !empty($profile_details['allowed_mfs_ids']))
        {
            if(!empty($_GET['report']))
            {
                $query->where("recharge_type", "=", "mfs_recharge");
                $query->where("processed_vendor_id", "=", $profile_details['store_vendor_id']);

                if(!empty($params['date_from']) && !empty($params['date_to']))
                    $query->whereBetween("recharge.modified_at", array(
                        date("Y-m-d 00:00:00", strtotime($params['date_from'])),
                        date("Y-m-d 23:59:59", strtotime($params['date_to']))
                    ));

            } else {
                $query->where(function($query) use ($profile_details)
                {
                    $query->whereRaw("recharge.mfs_id IN ('".join("', '", $profile_details['allowed_mfs_ids'])."') AND recharge.recharge_status IN ('pending', 'requested')");
                    $query->orWhere("processed_vendor_id", "=", $profile_details['store_vendor_id']);
                    $query->orWhere("locked_by", "=", $profile_details['store_vendor_id']);
                });

                if(!empty($params['date_from']) && !empty($params['date_to']))
                    $query->whereBetween("recharge.created_at", array(
                        date("Y-m-d 00:00:00", strtotime($params['date_from'])),
                        date("Y-m-d 23:59:59", strtotime($params['date_to']))
                    ));

                if(!empty($params['phone_number']))
                    $query->where('recharge.phone_number', '=', $params['phone_number']);

                if(!empty($params['mfs_id']))
                    $query->where('recharge.mfs_id', '=', $params['mfs_id']);
            }
        }

        if(in_array($profile_details['user_type'], array('manager','super_admin')))
        {
            $query->where('recharge.recharge_type', '=', 'mfs_recharge');

            if(!empty($params['date_from']) && !empty($params['date_to']))
                $query->whereBetween("recharge.created_at", array(
                    date("Y-m-d 00:00:00", strtotime($params['date_from'])),
                    date("Y-m-d 23:59:59", strtotime($params['date_to']))
                ));

            if(!empty($params['store_id']))
                $query->where('recharge.created_by', '=', $params['store_id']);

            if(!empty($params['vendor_id']))
            {
                $query->whereRaw("recharge.processed_vendor_id = '".$params['vendor_id']."' OR recharge.locked_by = '".$params['vendor_id']."'");
            }

            if(!empty($params['mfs_id']))
                $query->where('recharge.mfs_id', '=', $params['mfs_id']);

            if(!empty($params['recharge_status']))
                $query->where('recharge.recharge_status', '=', $params['recharge_status']);

            if(!empty($params['phone_number']))
                $query->where('recharge.phone_number', '=', $params['phone_number']);

        }

        if(!empty($params['limit']))
        {
            $query->limit($params['limit']);
        } else {
            $query->limit(200);
        }

        if(!empty($params['query_type']) && $params['query_type'] == "show_only_recharges")
        {
            $query->where('recharge.recharge_type', '=', 'mfs_recharge');
        }

        $result = $query->get();

        $data = array();
        $totalRechargeAmount = 0;
        $sm = 0;
        $rm = 0;

        foreach($result as $key => $value)
        {
            $recharge_meta = json_decode($value->recharge_meta);
            $value->base_currency = strtoupper($value->base_currency);

            switch ($profile_details['user_type']) {
                case "store":

                    //$amount = (intval($value->recharge_amount)==0?("&#8364; ".number_format($value->recharge_euro, 3)):("&#2547; ".number_format($value->recharge_amount, 3)." / &#8364;".$value->recharge_euro));

                    $amount = $value->base_currency." ".number_format($value->b1, 3);

                    if($value->recharge_type == "store_refill")
                    {
                        $amount = $value->base_currency." ".number_format($value->recharge_amount, 3);
                    }

                    $data[] = array(
                        (string) $value->serial_number,
                        date("Y-m-d H:i:s", strtotime($value->created_at)),
                        (($value->recharge_type == "mfs_recharge" && $value->recharge_status == "approved")?$value->recharge_id:""),
                        $value->phone_number,
                        $amount,
                        //($value->base_currency." ".number_format($value->recharge_amount, 3)),
                        //(intval($value->recharge_amount)==0?"&#8364; ":"&#2547; ").number_format((intval($value->recharge_amount)==0?($value->recharge_euro):$value->recharge_amount), 3),
                        $value->base_currency." ".number_format($value->store_balance, 3).(!$this->is_mobile_app_request?(floatval($value->store_loan_balance) > 0?('&nbsp;&nbsp;&nbsp;<span class="text-danger">'.$value->base_currency.' '.number_format($value->store_loan_balance, 3).'</span>'):""):""),
                        $value->mfs_name.(intval($value->recharge_amount)==0?"":" (".ucfirst($value->mfs_number_type).")"),
                        $value->note,
                        $value->vendor_note,
                        ucfirst($value->recharge_status),
                        date("d/m/Y", strtotime($value->modified_at))
                    );
                    break;
                case "vendor":
                    if(!empty($_GET['report']))
                    {
                        $data[] = array(
                            (string) $value->serial_number,
                            date("Y-m-d H:i:s", strtotime($value->modified_at)),
                            $value->mfs_name.(!empty($value->mfs_number_type)?" (".ucfirst($value->mfs_number_type).")":"").(!empty($value->mfs_package_name)?" {".($value->mfs_package_name)."}":""),
                            $value->phone_number,
                            $value->base_currency." ".number_format(round($value->recharge_amount), 0),
                            $value->vendor_note,
                        );
                    }
                    else
                    {
                        $data[] = array(
                            (string) $value->serial_number,
                            $value->mfs_name.(!empty($value->mfs_number_type)?" (".ucfirst($value->mfs_number_type).")":"").(!empty($value->mfs_package_name)?" {".($value->mfs_package_name)."}":""),
                            $value->phone_number,


                            ($recharge_meta->sending_currency != "EURO" ?($value->base_currency." ".number_format(round($recharge_meta->send_money), 0)):($value->base_currency." ".number_format(round($recharge_meta->visualSendMoney), 0))),


                            //(floatval($value->vendor_balance) > 0 ?($value->base_currency." ".number_format($value->vendor_balance, 3)):""),
                            $value->recharge_id."|".$value->recharge_status,
                            ucfirst($value->recharge_status),
                            date("Y-m-d H:i:s", strtotime($value->created_at)).(!$this->is_mobile_app_request?($value->locked=="1"?'&nbsp;&nbsp;&nbsp;<i class="icon-lock mr-3 icon-1x text-danger"></i>':''):""),
                            date("Y-m-d H:i:s", strtotime($value->modified_at)),
                            $value->store_name,
                            (!empty($value->parent_store_name)?$value->parent_store_name:""),
                            $value->vendor_note,
                        );
                    }
                    $totalRechargeAmount = $totalRechargeAmount + floatval($value->recharge_amount);
                    break;
                default:
                    $data[] = array(
                        (string) $value->serial_number,
                        date("Y-m-d H:i:s", strtotime($value->created_at)).(!$this->is_mobile_app_request?($value->locked=="1"?'&nbsp;&nbsp;&nbsp;<i class="icon-lock mr-3 icon-1x text-danger"></i>':''):""),
                        $value->phone_number,
                        number_format($value->b1, 3),
                        //($value->base_currency." ".number_format($value->recharge_amount, 3)),
                        $value->mfs_name." (".ucfirst($value->mfs_number_type).")",
                        $value->note,
                        $value->vendor_note,
                        $value->store_name,
                        $value->vendor_name,
                        ucfirst($value->recharge_status),
                        date("d/m/Y", strtotime($value->modified_at)),
                        $value->parent_store_name,
                        $value->recharge_id."|".$value->recharge_status
                    );
            }

            if($value->recharge_status == "approved")
            {
                $sm = $sm + $value->b1;
                $rm = $rm + $value->recharge_amount;
            }
        }

        if(!empty($_GET['report']) && !empty($data))
        {
            switch ($profile_details['user_type']) {
                case "store":
                    $data[] = array(
                        "",
                        "",
                        "",
                        "<b style='color: #0f9d58'>Total: </b>",
                        number_format($sm, 3),
                        "",
                        "",
                        "",
                        //number_format($rm, 3),
                        "",
                        "",
                        ""
                    );
                    break;
                case "vendor":
                    $data[] = array(
                        "",
                        "",
                        "",
                        "<b style='color: #0f9d58'>Total: </b>",
                        "<u><b style='color: #0f9d58'>&#2547; ".number_format($totalRechargeAmount, 3)."</b></u>",
                        ""
                    );
                    break;
                default:
                    $data[] = array(
                        "",
                        "",
                        "<b style='color: #0f9d58'>Total: </b>",
                        number_format($sm, 3),
                        //number_format($rm, 3),
                        "",
                        "",
                        "",
                        "",
                        "",
                        "",
                        "",
                        "",
                        ""
                    );
            }
        }

        $current_balance = 0;
        $loan_balance = 0;
        $conversion_rate = 2;
        $store_commission_percent = "{}";
        $store_mfs_slab = array();
        $commission_percent = "{}";

        if($this->is_mobile_app_request)
        {
            $store_commission_percent = "[]";
            $commission_percent = "[]";
        }

        $storeList = array();
        $vendorList = array();
        $saved_numbers = array();
        $euroServiceChargeList_1 = array(
            array('from'=>'0', 'to'=>'50', 'charge'=>'3'),
            array('from'=>'51', 'to'=>'100', 'charge'=>'4'),
            array('from'=>'101', 'to'=>'150', 'charge'=>'5'),
            array('from'=>'151', 'to'=>'200', 'charge'=>'6'),
            array('from'=>'201', 'to'=>'250', 'charge'=>'7'),
        );
        $euroServiceChargeList_2 = $euroServiceChargeList_1;
        $mfs_list = DB::connection('mysql')->table('mfs')->get();

        switch ($profile_details['user_type']) {
            case "store":
                if($store_dta)
                {
                    $current_balance = $store_dta->balance;
                    $loan_balance =  $store_dta->loan_balance;
                    $store_commission_percent = $store_dta->store_commission_percent;
                    $commission_percent =  $store_dta->commission_percent;
                    $conversion_rate = $store_dta->conversion_rate;
                    $store_mfs_slab = array();

                    if(!empty($store_dta->store_commission_percent))
                    {
                        $store_mfs_slab = json_decode($store_dta->store_commission_percent, true);
                        /*foreach($o as $oo)
                        {
                            $store_mfs_slab[$oo["id"]][] = array(
                                "name"=>$oo["name"],
                                "charge"=>floatval($oo["charge"]),
                                "commission"=>floatval($oo["commission"])
                            );
                        }*/
                    }

                    if(!empty($store_dta->service_charge_slabs)){
                        $euroServiceChargeList_1 = json_decode($store_dta->service_charge_slabs);
                    }
                    if(!empty($store_dta->service_charge_slabs_t2)){
                        $euroServiceChargeList_2 = json_decode($store_dta->service_charge_slabs_t2);
                    }
                }

                $mfs_list = DB::connection('mysql')->table('mfs')->whereIn('mfs_id', ($profile_details['allowed_mfs_ids']))->get();

                if(empty($params['storeListLoaded']))
                {
                    $storeList = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc")->where(array(
                        'parent_store_id'=>(($profile_details['user_type'] == "super_admin")?'by_admin':$profile_details['store_vendor_id'])
                    ))->get();
                }

                $saved_numbers = DB::connection('mysql')->table('store_phone_numbers')->selectRaw('name, phone_number')->where('store_id','=', $profile_details['store_vendor_id'])->get();

                break;
            case "vendor":
                $vendor_dta = DB::connection('mysql')->table('vendor')->selectRaw("b1")->where('vendor_id', $profile_details['store_vendor_id'])->first();
                if($vendor_dta)
                {
                    $current_balance = $vendor_dta->b1;
                }

                $mfs_list = DB::connection('mysql')->table('mfs')->whereIn('mfs_id', ($profile_details['allowed_mfs_ids']))->get();
                break;
            default:

                if(empty($params['storeListLoaded']))
                {
                    $storeList = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc")
                        /*->where(array(
                        'parent_store_id'=>(($profile_details['user_type'] == "super_admin")?'by_admin':$profile_details['store_vendor_id'])
                    ))*/
                        ->get();
                }

                if(empty($params['vendorListLoaded']))
                {
                    $vendorList = DB::connection('mysql')->table('vendor')->selectRaw("vendor_id as id, vendor_name AS name")->orderBy("modified_at", "desc")->get();
                }
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>(string) time(),
            'success'=>true,
            'data'=>$data,
            //'sql'=>$query->toSql(),
            'euroServiceChargeList_1'=>$euroServiceChargeList_1,
            'euroServiceChargeList_2'=>$euroServiceChargeList_2,
            'storeList'=>$storeList,
            'vendorList'=>$vendorList,
            'current_balance'=>number_format($current_balance, 3),
            'loan_balance'=>number_format($loan_balance, 3),
            'conversion_rate'=>number_format($conversion_rate, 3),
            'mfs_list'=>$mfs_list,
            'mfs_package_list'=>DB::connection('mysql')->table('mfs_package')->where('enabled', '1')->orderByRaw('amount ASC, created_at DESC')->get(),
            'commission_percent'=>json_decode($commission_percent),
            'store_commission_percent'=>json_decode($store_commission_percent),
            'store_mfs_slab'=>$store_mfs_slab,
            'saved_numbers'=>$saved_numbers
        ), 200);
    }

    public function create(Request $request)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $validator = Validator::make(json_decode($request->getContent(), true), [
            'mfs_id' => 'required',
            'mobile_number' => 'required',
            'send_money' => 'required',
            'transaction_pin'=>'required',
            'recharge_amount'=>'required|numeric'
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

        $required_euro = 0;
        $base_currency = "";

        if($profile_details['user_type'] === "store")
        {
            $store_dta = DB::connection('mysql')->table('store')
                ->selectRaw("balance, commission_percent, conversion_rate, loan_slab, loan_balance, store_commission_percent, mfs_slab, base_currency")
                ->where('store_id', $profile_details['store_vendor_id'])
                ->where('transaction_pin', '=', $data['transaction_pin'])
                ->first();

            if(!$store_dta)
            {
                $errorMessages[] = "No Store found or Invalid Transaction Pin";

                return response()->json(array(
                    'right_now'=>date("Y-m-d H:i:s"),
                    'timestamp'=>time(),
                    'success'=>false,
                    'message'=>$errorMessages
                ), 400);
            }
            $base_currency = $store_dta->base_currency;
            $required_euro = $data['receive_money'];


            if(strtolower($base_currency) != strtolower($data['sending_currency']))
            {
                $required_euro = $data['visualSendMoney'];
            }

            $store_dta->loan_slab = 0;

            if(empty($required_euro) || (!(floatval($required_euro) > 0))) {
                return response()->json(array(
                    'right_now'=>date("Y-m-d H:i:s"),
                    'timestamp'=>time(),
                    'success'=>false,
                    'message'=>"Invalid Request. Try again later."
                ), 400);
            }

            if(floatval($required_euro) > floatval($store_dta->balance))
            {
                // Lets check loan limit.
                /*if((floatval($required_euro) + floatval($store_dta->loan_balance)) > floatval($store_dta->loan_slab))
                {

                }*/
                return response()->json(array(
                    'right_now'=>date("Y-m-d H:i:s"),
                    'timestamp'=>time(),
                    'success'=>true,
                    'message'=>array("You have crossed your limit. Please decrease recharge amount or contact administrator for refill.")
                ), 403);
                /*return response()->json(array(
                    'right_now'=>date("Y-m-d H:i:s"),
                    'timestamp'=>time(),
                    'success'=>true,
                    'message'=>"You only have ".floatval($store_dta->balance)." Euro. Please decrease recharge amount or contact administrator for refill."
                ), 403);*/
            }
        }

        $recharge_id = uniqid('').bin2hex(random_bytes(8));
        $mfs_dta = DB::connection('mysql')->table('mfs')->selectRaw("mfs_id, mfs_name")->where('mfs_id', $data['mfs_id'])->first();

        DB::connection('mysql')->table('recharge')->insert(array(
            'recharge_id'=>$recharge_id,
            'mfs_id'=>$data['mfs_id'],
            'mfs_name' => $mfs_dta->mfs_name,
            'phone_number' => $data['mobile_number'],
            'base_currency' => $base_currency,
            'recharge_amount' => $data['recharge_amount'],
            'mfs_number_type'=> $data['mfs_type'],
            'note'=>(!empty($data['note'])?$data['note']:" "),
            'b1'=>$required_euro,
            'b2'=> ((strtolower($base_currency) != strtolower($data['sending_currency']))?$data['send_money']:$required_euro),
            'sending_currency'=> $data['sending_currency'],
            'b3'=>(!empty($mcl[$data['mfs_id']])?floatval($mcl[$data['mfs_id']]):0.0),
            'recharge_type'=>'mfs_recharge',
            'locked_by'=>"",
            'processed_vendor_id'=>"",
            'mfs_package_id'=>(!empty($data['selected_mfs_package'])?$data['selected_mfs_package']:""),
            'recharge_meta'=>json_encode($data, true),
            'recharge_status'=>'requested',
            'created_by' => $profile_details['store_vendor_id'],
            'created_at' => date("Y-m-d H:i:s"),
            'modified_at' => date("Y-m-d H:i:s"),


            'commission_amount'=>0,
            'store_conversion_rate'=>0,
            'vendor_balance'=>0,
            'store_balance'=>0,
            'store_loan_balance'=>0,
            'due_euro'=>0,
        ));

        //DB::statement("UPDATE `recharge`, (SELECT COUNT(*) as total FROM `recharge`) AS `recharge_row_count` SET `recharge`.`serial_number` = `recharge_row_count`.total WHERE `recharge`.`recharge_id` = '".$recharge_id."'");

        DB::statement("UPDATE `recharge`, (SELECT serial_number FROM `recharge` ORDER BY serial_number DESC LIMIT 1) AS `recharge_last_row` SET `recharge`.`serial_number` = (`recharge_last_row`.serial_number + 1) WHERE `recharge`.`recharge_id` = '".$recharge_id."'");

        //serial_number

        if($profile_details['user_type'] === "store")
        {
            if(floatval($required_euro) > floatval($store_dta->balance))
            {
                $addLoan = ((floatval($store_dta->balance) < floatval($required_euro))?(floatval($required_euro) - floatval($store_dta->balance)):0);
                $deductCb = ((floatval($store_dta->balance) < floatval($required_euro))?$store_dta->balance:$required_euro);

                DB::statement("UPDATE store SET store.balance = (store.balance - ".$deductCb."), store.loan_balance = (store.loan_balance + ".$addLoan."), store.pending_balance = (store.pending_balance + ".$required_euro.") WHERE store.store_id = '".$profile_details['store_vendor_id']."'");
            }
            else
            {
                DB::statement("UPDATE store SET store.balance = (store.balance - ".$required_euro."), store.pending_balance = (store.pending_balance + ".$required_euro.") WHERE store.store_id = '".$profile_details['store_vendor_id']."'");
            }

            DB::statement("UPDATE recharge INNER JOIN store ON store.store_id = recharge.created_by SET recharge.store_balance = store.balance, recharge.store_loan_balance = store.loan_balance WHERE recharge.recharge_id = '".$recharge_id."'");
        }


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

        // Send All Vendor a Messaging Knock.
        $vendors_dta = DB::connection('mysql')
            ->table('aauth_users')
            ->selectRaw('fcm_token')
            ->where('user_type', '=', 'vendor');
        $vendors_result = $vendors_dta->get();
        foreach($vendors_result as $value)
        {
            if(!empty($value->fcm_token))
            {
                $n = new \App\Libs\FirebaseMessaging($value->fcm_token);
                $n->setMessage("New Recharge Request (".$mfs_dta->mfs_name.")", ((strtoupper($base_currency)." ".number_format(round($data['recharge_amount']), 0))."/= on ".$data['mobile_number']." Recharge Request Received"), config('constants.RECHARGE_REQUEST_RECEIVED'))->sendMessage();
            }
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function lock(Request $request, $recharge_id)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);

        $data = json_decode($request->getContent(), true);

        /*if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);*/

        /*$vendor_dta = DB::connection('mysql')->table('vendor')
            ->selectRaw("*")
            ->where('vendor_id', $profile_details['store_vendor_id'])
            ->where('transaction_pin', $data['transaction_pin'])
            ->first();

        if(!$vendor_dta)
        {
            $errorMessages = array('Invalid Vendor Pin');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }*/

        DB::connection('mysql')->table("recharge")
            ->where(array(
                'recharge_id'=>$recharge_id,
            ))->update(array(
                'locked'=>1,
                'locked_by'=>$profile_details['store_vendor_id'],
                'recharge_status'=>'progressing',
                'modified_at' => date("Y-m-d H:i:s")
            ));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function unlock(Request $request, $recharge_id)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);

        DB::connection('mysql')->table("recharge")
            ->where(array(
                'recharge_id'=>$recharge_id,
                'locked'=>1,
            ))->update(array(
                'locked'=>0,
                'locked_by'=>'',
                'recharge_status'=>'pending',
                'modified_at' => date("Y-m-d H:i:s")
            ));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function reinit(Request $request, $recharge_id)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);

        $recharge_dta = DB::connection('mysql')
            ->table('recharge')
            ->where('recharge_id', '=', $recharge_id)->first();

        if(!$recharge_dta)
        {
            $errorMessages = array('Recharge Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $recharge_meta = json_decode($recharge_dta->recharge_meta);

        $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, base_currency, due_euro")->where('store_id', $recharge_dta->created_by)->first();

        if(floatval($recharge_meta->visualSendMoney) > floatval($store_dta->balance))
        {
            $errorMessages = array("Reseller don't have enough balance to create this recharge.");

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        DB::connection('mysql')
            ->table("recharge")
            ->where(array(
                'recharge_id'=>$recharge_id
            ))
            ->update(array(
                'recharge_status'=>'requested',
            ));

        DB::statement("UPDATE store SET store.balance = (store.balance - ".$recharge_meta->visualSendMoney."), store.pending_balance = (store.pending_balance + ".$recharge_meta->visualSendMoney.") WHERE store.store_id = '".$recharge_dta->created_by."'");

DB::statement("UPDATE recharge INNER JOIN store ON store.store_id = recharge.created_by SET recharge.store_balance = store.balance, recharge.store_loan_balance = store.loan_balance WHERE recharge.recharge_id = '".$recharge_id."'");

        $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, base_currency, due_euro, simcard_due_amount")->where('store_id', $recharge_dta->created_by)->first();

        $currentBalance = number_format($store_dta->balance, 2);
        $currentBalanceCurrency = strtoupper($store_dta->base_currency);

        Redis::set('user:current_balance:'.$recharge_dta->created_by, json_encode(
            array(
                'currency'=>$currentBalanceCurrency,
                'amount'=>$currentBalance,
                'due_euro'=>$store_dta->due_euro,
                'simcard_due_amount'=>$store_dta->simcard_due_amount
            )
        ), 'EX', (60 * 60 * 24 * 7));


        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function getHtml_receipt(Request $request, $recharge_id)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);

        $recharge_dta = DB::connection('mysql')
            ->table('recharge')
            ->selectRaw('recharge.*, store.store_owner_name, store.store_phone_number, store.store_address, mfs.mfs_name, mfs.image_path')
            ->leftJoin("store", function($join)
            {
                $join->on('store.store_id', '=', 'recharge.created_by');
            })
            ->leftJoin("mfs", function($join)
            {
                $join->on('mfs.mfs_id', '=', 'recharge.mfs_id');
            })
            ->where('recharge_id', '=', $recharge_id);

        $recharge_dta = $recharge_dta->first();

        if(!$recharge_dta)
        {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false
            ), 406);
        }

        $recharge_meta = json_decode($recharge_dta->recharge_meta);

        $reseller_charge = 0;

        if(!empty($recharge_meta) && $recharge_meta->sending_currency == "EURO")
        {
            if($recharge_meta->send_money_type == "without_charge")
            {
                $reseller_charge = floatval($recharge_meta->reseller_servie_charge) * (-1);
            }
            else
            {
                //$reseller_charge = floatval($recharge_meta->reseller_servie_charge) * (+1);
            }
        }


        $html = '<h4 style="text-align: center">'.$recharge_dta->store_owner_name.'</h4>
                    <p style="text-align: center">
                        '.$recharge_dta->store_phone_number.'<br>
                        '.$recharge_dta->store_address.'
                    </p>
                    <p style="font-size: 18px;text-align: center">
                        <img src="'."/".$recharge_dta->image_path.'" class="img-fluid" style="max-height: 100px;"><br><br>
                        '.$recharge_dta->mfs_name.'<br>
                        <span style="font-weight: bold;">'.$recharge_dta->phone_number.'</span>
                    </p>
                    <h3 style="text-align: center">'.strtoupper($recharge_dta->sending_currency).' '.number_format((floatval($recharge_dta->b2) - ($reseller_charge)), 2).'</h3><br>
                    <table class="table">
                        <tr><td>Delivered Amount</td><td>'.strtoupper($recharge_dta->base_currency).' '.number_format($recharge_dta->recharge_amount, 2).'/=</td></tr>
                        <tr><td>Receiver</td><td>'.$recharge_dta->phone_number.'</td></tr>
                        <tr><td>Created At</td><td>'.date("Y-m-d h:i A", strtotime($recharge_dta->created_at)).'</td></tr>
                        <tr><td>Trans. #</td><td>'.sprintf('%010d', $recharge_dta->serial_number).'</td></tr>
                        <tr><td>Pin No</td><td>'.$recharge_dta->vendor_note.'</td></tr>
                    </table>';


        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'html'=>$html
        ), 200);
    }

    public function updateNote(Request $request, $recharge_id)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);

        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if (empty($data))
            return response()->json(array(
                'right_now' => date("Y-m-d H:i:s"),
                'timestamp' => time(),
                'success' => false,
            ), 400);

        $whereQuery = array();
        $dataUpdate = array();

        $whereQuery['recharge_id'] = $recharge_id;
        $dataUpdate['vendor_note'] = $data['note'];

        DB::connection('mysql')
            ->table("recharge")
            ->where($whereQuery)
            ->update($dataUpdate);

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function approveReject(Request $request, $recharge_id)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);

        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if (empty($data))
            return response()->json(array(
                'right_now' => date("Y-m-d H:i:s"),
                'timestamp' => time(),
                'success' => false,
            ), 400);

        if($profile_details['user_type'] == "vendor")
        {
            if(empty($data['note']))
            {
                return response()->json(array(
                    'right_now'=>date("Y-m-d H:i:s"),
                    'timestamp'=>time(),
                    'success'=>false,
                    'message'=>array('Please enter Note')
                ), 406);
            }
        }

        /*$vendor_dta = DB::connection('mysql')->table('vendor')
            ->selectRaw("*")
            ->where('vendor_id', $profile_details['store_vendor_id'])
            ->where('transaction_pin', $data['transaction_pin'])
            ->first();

        if(!$vendor_dta)
        {
            $errorMessages = array('Invalid Vendor Pin');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }*/

        $recharge_dta = DB::connection('mysql')
            ->table('recharge')
            ->selectRaw("recharge_amount, b1, created_by, mfs_id, base_currency, recharge_amount, phone_number")
            ->where('recharge_id', '=', $recharge_id);
            //->where('recharge_status', '=', 'progressing');

        if($profile_details['user_type'] == "vendor")
        {
            $recharge_dta->where('locked_by', '=', $profile_details['store_vendor_id']);
        }

        $recharge_dta = $recharge_dta->first();


        if(!$recharge_dta)
        {
            $errorMessages = array('Recharge Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        // Blocking user for 30 second to protect multi transection
        $lastActivity = Redis::get('vendor:last_recharge:' . $profile_details['store_vendor_id']);
        if(!empty($lastActivity) && !(floatval($lastActivity) < (time() + 30)))
        {
            $errorMessages = array('Please try after 30 second.');
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }
        Redis::set('vendor:last_recharge:' . $profile_details['store_vendor_id'], time(), 'EX', (60 * 60 * 2));


        $whereQuery = array();

        $dataUpdate = array(
            'recharge_status'=>$data['recharge_status'],
            'locked'=>0,
            'locked_by'=>""
        );

        $whereQuery['recharge_id'] = $recharge_id;

        if($profile_details['user_type'] == "vendor")
        {
            $dataUpdate['vendor_note'] = $data['note'];
            $dataUpdate['processed_vendor_id'] = $profile_details['store_vendor_id'];
            $dataUpdate['modified_at'] = date("Y-m-d H:i:s");

            $whereQuery['locked_by'] = $profile_details['store_vendor_id'];
        }

        DB::connection('mysql')
            ->table("recharge")
            ->where($whereQuery)
            ->update($dataUpdate);

        if($data['recharge_status'] == "approved")
        {
            //DB::statement("UPDATE vendor SET vendor.b1 = (vendor.b1 - ".floatval($recharge_dta->recharge_amount)."), last_transection_time = '".time()."' WHERE vendor.vendor_id = '".$profile_details['store_vendor_id']."' AND last_transection_time < ".(time() + 30));

            //DB::statement("UPDATE vendor SET vendor.b1 = (vendor.b1 - ".floatval($recharge_dta->recharge_amount).") WHERE vendor.vendor_id = '".$profile_details['store_vendor_id']."'");

            $vendor_dta = DB::connection('mysql')->table('vendor')->selectRaw("*")->where('vendor_id', $profile_details['store_vendor_id'])->first();
            $currentBalanceCurrency = 'BDT';
            $currentBalance = number_format($vendor_dta->b1, 2);

            Redis::set('user:current_balance:'.$profile_details['store_vendor_id'], json_encode(
                array(
                    'currency'=>$currentBalanceCurrency,
                    'amount'=>$currentBalance,
                )
            ), 'EX', (60 * 60 * 24 * 7));



            DB::statement("UPDATE store SET store.pending_balance = (store.pending_balance - ".$recharge_dta->b1.") WHERE store.store_id = '".$recharge_dta->created_by."'");

            DB::statement("UPDATE recharge INNER JOIN vendor ON vendor.vendor_id = recharge.processed_vendor_id SET recharge.vendor_balance = vendor.b1 WHERE recharge.recharge_id = '".$recharge_id."'");
        }

        if($data['recharge_status'] == "rejected")
        {
            $store_dta = DB::connection('mysql')->table('store')->selectRaw("balance, loan_balance")->where('store_id', $recharge_dta->created_by)->first();
            if($store_dta)
            {
                if(floatval($store_dta->loan_balance) > 0)
                {

                    $deductLoan = ((floatval($store_dta->loan_balance) < floatval($recharge_dta->b1))?$store_dta->loan_balance:$recharge_dta->b1);
                    $addCB = ((floatval($store_dta->loan_balance) < floatval($recharge_dta->b1))?(floatval($recharge_dta->b1) - floatval($store_dta->loan_balance)):0);

                    DB::statement("UPDATE store SET store.balance = (store.balance + ".$addCB."), store.loan_balance = (store.loan_balance - ".$deductLoan."), store.pending_balance = (store.pending_balance - ".$recharge_dta->b1.") WHERE store.store_id = '".$recharge_dta->created_by."'");

                }
                else
                {

                    DB::statement("UPDATE store SET store.balance = (store.balance + ".$recharge_dta->b1."), store.pending_balance = (store.pending_balance - ".$recharge_dta->b1.") WHERE store.store_id = '".$recharge_dta->created_by."'");

                }

                //$rowId = uniqid('').bin2hex(random_bytes(8));

                /*DB::connection('mysql')->table('recharge')->insert(array(
                    'recharge_id'=>$rowId,
                    'mfs_name' => 'Refund',
                    'phone_number' => '',
                    'recharge_amount' => $recharge_dta->b1,
                    'mfs_number_type'=> "",
                    'b1'=>'0',
                    'refund_recharge_row_id'=>$recharge_id,
                    'locked_by'=>"",
                    'recharge_type'=>'store_refund',
                    'processed_vendor_id'=>"",
                    'mfs_package_id'=>'',
                    'recharge_meta'=>json_encode(array(), true),
                    'recharge_status'=>'approved',
                    'created_by' => $recharge_dta->created_by,
                    'created_at' => date("Y-m-d H:i:s"),
                    'modified_at' => date("Y-m-d H:i:s")
                ));*/

                DB::statement("UPDATE recharge INNER JOIN store ON store.store_id = recharge.created_by SET recharge.store_balance = store.balance, recharge.store_loan_balance = store.loan_balance WHERE recharge.recharge_id = '".$recharge_id."'");
            }
        }


        $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, base_currency, due_euro, simcard_due_amount")->where('store_id', $recharge_dta->created_by)->first();

        $currentBalance = number_format($store_dta->balance, 2);
        $currentBalanceCurrency = strtoupper($store_dta->base_currency);

        Redis::set('user:current_balance:'.$recharge_dta->created_by, json_encode(
            array(
                'currency'=>$currentBalanceCurrency,
                'amount'=>$currentBalance,
                'due_euro'=>$store_dta->due_euro,
                'simcard_due_amount'=>$store_dta->simcard_due_amount
            )
        ), 'EX', (60 * 60 * 24 * 7));

        // Send Store a Messaging Knock.
        $vendors_dta = DB::connection('mysql')
            ->table('aauth_users')
            ->selectRaw('fcm_token')
            ->where('user_type', '=', 'store')->where('store_vendor_id', '=', $recharge_dta->created_by);

        $mfs_dta = DB::connection('mysql')->table('mfs')->selectRaw("mfs_id, mfs_name")->where('mfs_id', $recharge_dta->mfs_id)->first();

        $vendors_result = $vendors_dta->get();
        foreach($vendors_result as $value)
        {
            if(!empty($value->fcm_token))
            {
                $n = new \App\Libs\FirebaseMessaging($value->fcm_token);

                if($data['recharge_status'] == "approved")
                {
                    $n->setMessage("Recharge Request (".$mfs_dta->mfs_name.") Approved", ((strtoupper($recharge_dta->base_currency)." ".number_format(round($recharge_dta->recharge_amount), 0))."/= on ".$recharge_dta->phone_number." Recharge Request have been Approved. Tap here for more info"), config('constants.RECHARGE_REQUEST_APPROVED'))->sendMessage();
                }

                if($data['recharge_status'] == "rejected") {
                    $n->setMessage("Recharge Request (" . $mfs_dta->mfs_name . ") Rejected", ((strtoupper($recharge_dta->base_currency) . " " . number_format(round($recharge_dta->recharge_amount), 0)) . "/= on " . $recharge_dta->phone_number . " Recharge Request have been Rejected. Tap here for more info"), config('constants.RECHARGE_REQUEST_REJECTED'))->sendMessage();
                }
            }
        }


        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function save_number(Request $request)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);
        $errorMessages = array();

        $data = json_decode($request->getContent(), true);

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $validator = Validator::make(json_decode($request->getContent(), true), [
            'name' => 'required|min:3|max:200',
            'mobile_number' => 'required',
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

        $savedNumber = DB::connection('mysql')
            ->table('store_phone_numbers')
            ->where('phone_number', '=', $data['mobile_number'])
            ->where('store_id', '=', $profile_details['store_vendor_id'])
            ->first();

        if(!$savedNumber)
        {
            $_id = uniqid('').bin2hex(random_bytes(8));
            DB::connection('mysql')->table('store_phone_numbers')->insert(array(
                'row_id'=>$_id,
                'store_id'=>$profile_details['store_vendor_id'],
                'name'=>$data['name'],
                'phone_number'=>$data['mobile_number'],
                'created_at'=>date("Y-m-d H:i:s")
            ));
        }
        else
        {
            DB::connection('mysql')->table("store_phone_numbers")
                ->where(array(
                    'phone_number'=>$data['mobile_number'],
                    'store_id'=>$profile_details['store_vendor_id'],
                ))->update(array(
                    'name'=>$data['name']
                ));
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function search_number(Request $request)
    {
        /*$token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);
        $errorMessages = array();

        $data = json_decode($request->getContent(), true);

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);*/

        return response()->json(array (
            array (
                    'value' => 1,
                    'text' => 'Google Cloud Platform',
                ),
            array (
                    'value' => 2,
                    'text' => 'Amazon AWS',
                ),
            array (
                    'value' => 3,
                    'text' => 'Docker',
                ),
            array (
                    'value' => 4,
                    'text' => 'Digital Ocean',
                ),
        ), 200);
    }
}
