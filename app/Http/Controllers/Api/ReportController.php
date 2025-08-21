<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function adjustment_history(Request $request, $type)
    {
        $params = $_POST;
        $data = array();
        $storeList = array();
        $vendorList = array();

        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        if($type=="store")
        {
            $query = DB::connection('mysql')->table('adjustment_history')
                ->selectRaw("adjustment_history.*, store.store_name AS name, store.base_currency, parent.store_name AS parent_store_name")
                ->leftJoin('store', function($join)
                {
                    $join->on('store.store_id', '=', 'adjustment_history.store_vendor_id');
                    $join->on('adjustment_history.type','=',DB::raw("'store'"));
                })
                ->leftJoin('store as parent', 'parent.store_id', '=', 'store.parent_store_id')
                ->orderBy("created_on", "desc");

            $query->where("adjustment_history.type", "=", "store");
            $query->where("adjustment_history.adjustment_type", "=", "mfs");

            if(!empty($params['store_id']))
            {
                $query->where("adjustment_history.store_vendor_id", "=", $params['store_id']);
            }
            else
            {
                if(($profile_details['user_type'] !== "super_admin"))
                {
                    $query->where("adjustment_history.store_vendor_id", "=", $profile_details['store_vendor_id']);
                }
            }

            if(empty($params['storeListLoaded']))
            {
                $storeListQ = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc");

                if(($profile_details['user_type'] !== "super_admin"))
                {
                    $storeListQ->where(array(
                        //'parent_store_id'=>(($profile_details['user_type'] == "super_admin")?'by_admin':$profile_details['store_vendor_id'])
                        'parent_store_id'=>$profile_details['store_vendor_id']
                    ));
                }

                $storeList = $storeListQ->get();
            }

            if(!empty($params['trans_type']) && $params['trans_type'] == "add_balance")
            {
                $query->where("adjustment_history.received_amount", "=", "0");
            }

            if(!empty($params['trans_type']) && $params['trans_type'] == "received_payment")
            {
                $query->where("adjustment_history.adjusted_amount", "=", "0");
            }

            if(!empty($params['date_from']) && !empty($params['date_to']))
                $query->whereBetween("adjustment_history.created_on", array(
                    date("Y-m-d 00:00:00", strtotime($params['date_from'])),
                    date("Y-m-d 23:59:59", strtotime($params['date_to']))
                ));


            $query->limit(200);
        }
        else
        {
            $query = DB::connection('mysql')->table('adjustment_history')
                ->selectRaw("adjustment_history.*, vendor.vendor_name AS name, parent.store_name AS parent_store_name")
                ->leftJoin('vendor', function($join)
                {
                    $join->on('vendor.vendor_id', '=', 'adjustment_history.store_vendor_id');
                    $join->on('adjustment_history.type','=',DB::raw("'vendor'"));
                })
                ->leftJoin('store as parent', 'parent.store_id', '=', 'store.parent_store_id')
                ->orderBy("created_on", "desc");

            $query->where("adjustment_history.type", "=", "vendor");
            $query->where("adjustment_history.adjustment_type", "=", "mfs");

            if(!empty($params['vendor_id']))
            {
                $query->where("adjustment_history.store_vendor_id", "=", $params['vendor_id']);
            }
            if(empty($params['vendorListLoaded']))
            {
                $vendorList = DB::connection('mysql')->table('vendor')->selectRaw("vendor_id as id, vendor_name AS name")->orderBy("modified_at", "desc")->get();
            }
        }

        $result = $query->get();

        $data = array();
        $total = 0;

        foreach($result as $key => $value)
        {
            if(!empty($params['trans_type']) && $params['trans_type'] == "received_payment")
            {
                $data[] = array(
                    ($key + 1),
                    date("Y-m-d H:i:s", strtotime($value->created_on)),
                    $value->name,
                    $value->parent_store_name,
                    "&euro; ".$value->euro_amount,
                    "&euro; ".$value->new_balance_euro,
                    $value->note
                );

                $total = $total + floatval($value->euro_amount);
            }
            else
            {
                $data[] = array(
                    ($key + 1),
                    date("Y-m-d H:i:s", strtotime($value->created_on)),
                    $value->name,
                    $value->parent_store_name,
                    "&euro; ".$value->euro_amount,
                    "&euro; ".$value->new_balance_euro,
                    $value->commission." %",
                    $value->conversion_rate,
                    strtoupper($value->base_currency)." ".number_format($value->adjusted_amount, 2),
                    strtoupper($value->base_currency)." ".$value->new_balance,
                    $value->note
                );
            }
        }

        if(!empty($data))
        {
            if(!empty($params['trans_type']) && $params['trans_type'] == "received_payment")
            {
                $data[] = array('','','','','','','');
                $data[] = array(
                    '','Total:','','',number_format($total, 2),'',''
                );
            }
            else
            {
                //$data[] = array('','','', '','','', '','','', '');
            }
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$data,
            'storeList'=>$storeList,
            'vendorList'=>$vendorList
        ), 200);
    }

    public function reseller_due_statement(Request $request)
    {
        $params = $_POST;
        $table_data = array();

        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
            'date_from' => 'required',
            'date_to' => 'required',
            'store_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>true,
                'data'=>$table_data
            ), 200);
        }

        $add_balance = 0;
        $receive_payment = 0;

        $ires = DB::select(DB::raw("SELECT IFNULL(SUM(adjustment_history.euro_amount), 0) AS total FROM adjustment_history WHERE store_vendor_id = '".$params['store_id']."' AND adjustment_history.type = 'store' AND adjustment_history.adjustment_type = 'mfs' AND  adjusted_amount > 0"));
        if(!empty($ires)) $add_balance = floatval($ires[0]->total);

        $ires = DB::select(DB::raw("SELECT IFNULL(SUM(adjustment_history.euro_amount), 0) AS total FROM adjustment_history WHERE store_vendor_id = '".$params['store_id']."' AND adjustment_history.type = 'store' AND adjustment_history.adjustment_type = 'mfs' AND  received_amount > 0"));
        if(!empty($ires)) $receive_payment = floatval($ires[0]->total);

        $cdb = ($add_balance - $receive_payment);

        $table_data[] = array(
            'Total Receive',
            '',
            '',
            '',
            "&euro; ".number_format($receive_payment, 2),
        );

        $table_data[] = array(
            'Total Expenditure',
            '',
            '',
            '',
            "&euro; ".number_format($add_balance, 2),
        );

        if($cdb == 0) $table_data[] = array('Current Due Balance', '', '', '', ("<span class='font-weight-bold'>&euro; ".number_format($cdb, 2)."</span>"));

        if($cdb != 0) $table_data[] = array('Current Due Balance', '', '', '', ($cdb >= 0?("<span class='font-weight-bold text-danger'>&euro; ".number_format($cdb, 2)."</span>"):("<span class='font-weight-bold text-success'>&euro; ".number_format(($cdb * (-1)), 2)."</span>")));


        $table_data[] = array('', '', '', '', '');


        $add_balance = 0;
        $receive_payment = 0;

        $ires = DB::select(DB::raw("SELECT IFNULL(SUM(adjustment_history.euro_amount), 0) AS total FROM adjustment_history WHERE store_vendor_id = '".$params['store_id']."' AND adjustment_history.type = 'store' AND adjustment_history.adjustment_type = 'mfs' AND  adjusted_amount > 0 AND created_on < '".date("Y-m-d 00:00:00", strtotime($params['date_from']))."'"));
        if(!empty($ires)) $add_balance = floatval($ires[0]->total);

        $ires = DB::select(DB::raw("SELECT IFNULL(SUM(adjustment_history.euro_amount), 0) AS total FROM adjustment_history WHERE store_vendor_id = '".$params['store_id']."' AND adjustment_history.type = 'store' AND adjustment_history.adjustment_type = 'mfs' AND  received_amount > 0 AND created_on < '".date("Y-m-d 00:00:00", strtotime($params['date_from']))."'"));
        if(!empty($ires)) $receive_payment = floatval($ires[0]->total);

        $opb = ($add_balance - $receive_payment);

        if($opb == 0) $table_data[] = array('Previous Due Balance', '', '', '', ("<span class='font-weight-bold'>&euro; ".number_format($opb, 2)."</span>"));

        if($opb != 0) $table_data[] = array('Previous Due Balance', '', '', '', ($opb >= 0?("<span class='font-weight-bold text-danger'>&euro; ".number_format($opb, 2)."</span>"):("<span class='font-weight-bold text-success'>&euro; ".number_format(($opb * (-1)), 2)."</span>")));


        $trans = DB::select(DB::raw("SELECT * FROM adjustment_history WHERE store_vendor_id = '".$params['store_id']."' AND adjustment_history.type = 'store' AND adjustment_history.adjustment_type = 'mfs' AND created_on BETWEEN '".date("Y-m-d 00:00:00", strtotime($params['date_from']))."' AND '".date("Y-m-d 23:59:59", strtotime($params['date_to']))."' ORDER BY created_on asc"));

        $balance = $opb;
        $total_dr = 0;
        $total_cr = 0;

        foreach($trans as $row)
        {

            if(floatval($row->adjusted_amount)>0){
                $balance = $balance + $row->euro_amount;
                $total_dr = $total_dr + $row->euro_amount;
            }
            if(floatval($row->received_amount)>0){
                $balance = $balance - $row->euro_amount;
                $total_cr = $total_cr + $row->euro_amount;
            }

            $table_data[] = array(
                date("F d, Y", strtotime($row->created_on)),
                $row->note,
                (floatval($row->adjusted_amount)>0?"&euro; ".number_format($row->euro_amount, 2):""),
                (floatval($row->received_amount)>0?"&euro; ".number_format($row->euro_amount, 2):""),
                "&euro; ".number_format($balance, 2),
            );
        }

        if(count($trans) > 0)
        {
            $table_data[] = array(
                '',
                '',
                '',
                '',
                ''
            );
            $table_data[] = array(
                '',
                '',
                "&euro; ".number_format($total_dr, 2),
                "&euro; ".number_format($total_cr, 2),
                ''
            );
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$table_data
        ), 200);
    }

    public function recharge_by_mfs(Request $request)
    {
        $params = $_POST;
        $table_data = array();

        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
            'date_from' => 'required',
            'date_to' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>true,
                'data'=>$table_data
            ), 200);
        }

        if(($profile_details['user_type'] !== "super_admin"))
        {
            if(($profile_details['user_type'] !== "super_admin"))
            {
                if(empty($params['store_id'])) $params['store_id'] = $profile_details['store_vendor_id'];
            }
        }

        $trans = DB::select(DB::raw("SELECT IFNULL(SUM(`recharge`.recharge_amount), 0) as recharge_amount, `recharge`.base_currency,`recharge`.mfs_name
        ".((empty($params['store_id']) && !empty($params['type']) && ($params['type'] != 'vendor'))?", CONCAT(`store`.`store_name`, ' [' ,`store`.`store_code`, ']') as Reseller ":"")."
        FROM `recharge`

         ".((empty($params['store_id']) && !empty($params['type']) && ($params['type'] != 'vendor'))?"left join `store` on `store`.`store_id` = `recharge`.`created_by`":"")."

         WHERE `recharge`.`recharge_type` = 'mfs_recharge' AND `recharge`.`recharge_status` = 'approved' ".

            ((!empty($params['store_id']) && !empty($params['type']) && ($params['type'] != 'vendor'))?("AND `recharge`.`created_by` = '".$params['store_id']."'"):"").
            ((!empty($params['store_id']) && !empty($params['type']) && ($params['type'] == 'vendor'))?("AND `recharge`.`processed_vendor_id` = '".$params['store_id']."'"):"").

            " AND recharge.`modified_at` BETWEEN '".date("Y-m-d 00:00:00", strtotime($params['date_from']))."' AND '".date("Y-m-d 23:59:59", strtotime($params['date_to']))."' GROUP BY recharge.mfs_name, ".((empty($params['store_id']) && !empty($params['type']) && ($params['type'] != 'vendor'))?"`recharge`.created_by,":"")." recharge.base_currency ORDER BY recharge_amount DESC, recharge.mfs_name ASC"));

        $mItems = [];
        foreach($trans as $row)
        {
            if(empty($mItems[$row->mfs_name])){
                $mItems[$row->mfs_name] = [];
            }

            $mItems[$row->mfs_name][] = $row;
        }

        $pos = 1;
        $total_recharge_amount = 0;
        foreach($mItems as $field => $items)
        {
            $recharge_amount = 0 ;

            foreach($items as $row)
            {
                $table_data[] = array(
                    ($pos++),
                    $row->mfs_name,
                    $row->Reseller ?? "",
                    (strtoupper(implode("", explode("_ ", $row->base_currency)))." ".number_format($row->recharge_amount, 2)),
                );

                $total_recharge_amount = $total_recharge_amount + $row->recharge_amount;
                $recharge_amount = $recharge_amount + $row->recharge_amount;
            }

            $table_data[] = array(
                '','',"<b>".'Total ('.$field.'):'."</b>", "<b>".number_format($recharge_amount, 2)."</b>"
            );
        }

        if($total_recharge_amount > 0)
        {
            $table_data[] = array(
                '','', '', ''
            );
            $table_data[] = array(
                '','',"<b>".'Grand Total:'."</b>", "<b>".number_format($total_recharge_amount, 2)."</b>"
            );
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$table_data
        ), 200);
    }

    public function reseller_return_payment(Request $request)
    {
        $params = $_POST;
        $table_data = array();

        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
            'date_from' => 'required',
            'date_to' => 'required'
        ]);

        $storeList = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc")->where(array(
            'parent_store_id'=>(($profile_details['user_type'] == "super_admin")?'by_admin':$profile_details['store_vendor_id'])
        ))->get();

        if ($validator->fails()) {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'storeList'=>$storeList,
                'data'=>$table_data
            ), 200);
        }

        $trans = DB::select(DB::raw("SELECT `recharge`.recharge_amount, `recharge`.base_currency, recharge.`created_at`, recharge.`note`  FROM `recharge` WHERE `recharge`.`recharge_type` = 'store_return' ".(!empty($params['store_id'])?("AND `recharge`.`created_by` = '".$params['store_id']."'"):"")." AND recharge.`created_at` BETWEEN '".date("Y-m-d 00:00:00", strtotime($params['date_from']))."' AND '".date("Y-m-d 23:59:59", strtotime($params['date_to']))."' ORDER BY recharge.`created_at` DESC"));

        $pos = 1;
        foreach($trans as $row)
        {
            $table_data[] = array(
                ($pos++),
                date("F d, Y h:i a", strtotime($row->created_at)),
                (strtoupper(implode("", explode("_ ", $row->base_currency)))." ".number_format($row->recharge_amount, 2)),
                $row->note
            );
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$table_data,
            'storeList'=>$storeList,
        ), 200);
    }

    public function payment_doc_upload_statement(Request $request)
    {
        date_default_timezone_set('Europe/Rome');
        $token = $request->header('Authorization');
        $params = $_POST;
        $table_data = array();

        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        if(!empty($_GET) && !empty($_GET['allow_file_upload']))
        {
            // Do New Entry.

            if(empty($request->file)){
                return response()->json(array(
                    'right_now'=>date("Y-m-d H:i:s"),
                    'timestamp'=>time(),
                    'success'=>false,
                    'message'=>'File upload is mandatory.'
                ), 200);
            }

            $validator = Validator::make($request->all(), [
                'file' => 'max:2000',
            ]);

            if ($validator->fails()) {
                return response()->json(array(
                    'right_now'=>date("Y-m-d H:i:s"),
                    'timestamp'=>time(),
                    'success'=>false,
                    'message'=>'File size cannot be more then 2 MB.'
                ), 200);
            }

            $validationParams = [
                'title' => 'required',
                //'note' => '',
            ];

            if(in_array($profile_details['user_type'], array('manager','super_admin')))
            {
                $validationParams = [
                    'title' => 'required',
                    'store_id' => 'required'
                ];
            }

            $validator = Validator::make($_POST, $validationParams);
            /*
            [
                //json_decode($request->getContent(), true), [
                'title' => 'required',
                //'note' => '',
            ]
             * */

            if ($validator->fails()) {
                return response()->json(array(
                    'right_now'=>date("Y-m-d H:i:s"),
                    'timestamp'=>time(),
                    'success'=>false,
                    'message'=>'Title is mandatory.'
                ), 200);
            }

            $row_id = uniqid('').bin2hex(random_bytes(8));

            if(empty($profile_details['store_vendor_id'])) $profile_details['store_vendor_id'] = "by_admin";

            DB::connection('mysql')->table('payment_receipt_upload')->insert(array(
                'row_id'=>$row_id,
                'created_by'=>(!empty($params['store_id'])?$params['store_id']:$profile_details['store_vendor_id']),
                'created_at'=>date("Y-m-d H:i:s"),
                'modified_at'=>date("Y-m-d H:i:s"),
                'status'=>'Pending',
                'file_path'=>'',
                'admin_note'=>'',
                'note'=>$params['note'],
                'amount'=>$params['amount'],
                'parent_store_id'=>(!empty($params['store_id'])?$profile_details['store_vendor_id']:$profile_details['parent_store_id']),
                'serial_number'=>1
            ));


            DB::statement("UPDATE `payment_receipt_upload`, (SELECT COUNT(*) as total FROM `payment_receipt_upload`) AS `payment_receipt_upload_count` SET `payment_receipt_upload`.`serial_number` = `payment_receipt_upload_count`.total WHERE `payment_receipt_upload`.`row_id` = '".$row_id."'");

            if(!empty($request->file)){
                $fileName = "receipt_".time().'.'.$request->file->extension();
                //$request->file->move(storage_path('app/public/store_logo'), $fileName);
                $request->file->move(base_path('public/assets/payment_receipts'), $fileName);

                DB::connection('mysql')->table("payment_receipt_upload")
                    ->where(array(
                        'row_id'=>$row_id
                    ))->update(array(
                        'file_path'=>'assets/payment_receipts/'.$fileName
                    ));

                self::sendUploadRequestToDigOcenSpace((base_path('public/assets/payment_receipts')."/".$fileName), ("assets/payment_receipts/".$fileName), "payment_receipt_upload", array("row_id"), array($row_id));
            }

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>true,
                'message'=>''
            ), 200);
        }

        if(!empty($_GET) && !empty($_GET['do_update_row']))
        {
            $update = array(
                'modified_at'=>date("Y-m-d H:i:s"),
                'status'=>$params['status'],
                'admin_note'=>(!empty($params['admin_note'])?$params['admin_note']:" ")
            );

            if(!empty($params['amount']))
            {
                $update['amount'] = $params['amount'];
            }

            DB::connection('mysql')->table("payment_receipt_upload")
                ->where(array(
                    'row_id'=>$_GET['do_update_row']
                ))->update($update);

            if(!empty($params['amount']) && $params['status'] == "Approve")
            {
                //Need to adjust euro.
                $payment_receipt_dta = DB::connection('mysql')->table('payment_receipt_upload')->where('row_id', $_GET['do_update_row'])->first();

                $store_id = $payment_receipt_dta->created_by;

                $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, commission_percent, conversion_rate, loan_slab, loan_balance, base_currency, due_euro")->where('store_id', $store_id)->first();

                if($store_dta)
                {
                    DB::statement("UPDATE `store` SET `due_euro` = (store.due_euro - ".floatval($params['amount']).") WHERE `store`.`store_id` = '".$payment_receipt_dta->created_by."'");
                    $currentBalance = number_format($store_dta->balance, 2);
                    $currentBalanceCurrency = strtoupper($store_dta->base_currency);

                    $store_dta = DB::connection('mysql')->table('store')->selectRaw("store_id, balance, commission_percent, conversion_rate, loan_slab, loan_balance, base_currency, due_euro, simcard_due_amount")->where('store_id', $store_id)->first();

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
                        'received_amount'=>$params['amount'],
                        'note'=> (!empty($params['admin_note'])?$params['admin_note']:" "),
                        'euro_amount'=>$params['amount'],
                        'new_balance'=>'0',
                        'conversion_rate'=>0,
                        'commission'=>0,
                        'new_balance_euro'=>$store_dta->due_euro,
                    ));
                }
            }

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>true,
                'message'=>''
            ), 200);
        }

        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
            'date_from' => 'required',
            'date_to' => 'required'
        ]);

        $storeList = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc")->where(array(
            'parent_store_id'=>(($profile_details['user_type'] == "super_admin")?'by_admin':$profile_details['store_vendor_id'])
        ))->get();

        if ($validator->fails()) {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'storeList'=>$storeList,
                'data'=>$table_data
            ), 200);
        }

        $trans = DB::select(DB::raw("SELECT `payment_receipt_upload`.*, CONCAT(`store`.`store_name`, ' [' ,`store`.`store_code`, ']') as Reseller  FROM `payment_receipt_upload` left join `store` on `store`.`store_id` = `payment_receipt_upload`.`created_by` WHERE payment_receipt_upload.`created_at` BETWEEN '".date("Y-m-d 00:00:00", strtotime($params['date_from']))."' AND '".date("Y-m-d 23:59:59", strtotime($params['date_to']))."' ".(!empty($params['store_id'])?("AND `payment_receipt_upload`.`created_by` = '".$params['store_id']."'"):($profile_details['user_type'] == "super_admin"?"AND `payment_receipt_upload`.`parent_store_id` = 'by_admin' ":"AND `payment_receipt_upload`.`created_by` = '".$profile_details['store_vendor_id']."' "))." ORDER BY payment_receipt_upload.`created_at` DESC"));

        if(empty($profile_details['store_vendor_id'])) $profile_details['store_vendor_id'] = "by_admin";

        $pos = 1;
        foreach($trans as $row)
        {
            $table_data[] = array(
                $row->serial_number,
                date("F d, Y h:i a", strtotime($row->created_at)),
                (($profile_details['store_vendor_id'] == $row->created_by)?"":$row->Reseller),
                ($row->space_uploaded =="uploaded"?(config('constants.dgSpaceURL')."".$row->file_path):("/".$row->file_path)),
                number_format($row->amount, 2),
                $row->status,
                $row->note,
                $row->admin_note,
                (($profile_details['store_vendor_id'] == $row->parent_store_id)?($row->row_id."||".$row->status."||".$row->amount):""),
            );
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$table_data,
            'storeList'=>$storeList,
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
