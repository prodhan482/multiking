<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    public function list(Request $request)
    {
        //$params = json_decode($request->getContent(), true);

        $params = $_POST;

        $query = DB::connection('mysql')->table('vendor')->orderBy("modified_at", "desc");
            //->where('status', 'enabled');

        if(!empty($params['search']) && !empty($params['search']['value']))
        {
            $query->orWhere('vendor_name', 'like', $params['search']['value']."%");
            $query->orWhere('d2', 'like', $params['search']['value']."%");
        }

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
                (!empty($value->image_path)?'<img style="height:50px;" src="/public/'.$value->image_path.'" class="img-fluid" alt="Responsive image">':''),
                $value->vendor_name,
                "&#2547; ".number_format($value->b1, 2),
                $value->status,
                date("m/d/Y", strtotime($value->created_at)),
                $value->vendor_id."|".$value->status,
            );
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$data,
            'mfs_list'=>DB::connection('mysql')->table('mfs')->selectRaw('mfs_name, mfs_id')->get()
        ), 200);
    }

    public function create(Request $request)
    {
        $data = $_POST;//json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
            'vendor_name' => 'required|string|min:2|max:200',
            'manager_user_name' => 'string|min:3|max:30',
            'manager_user_password' => 'string|min:3|max:30',
            'transaction_pin' => 'required|min:3|max:10',
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


        $vendor_id = uniqid('').bin2hex(random_bytes(8));

        DB::connection('mysql')->table('vendor')->insert(array(
            'vendor_id'=>$vendor_id,
            'vendor_name' => $data['vendor_name'],
            'status' => 'enabled',
            'allowed_mfs'=>json_encode(explode(",", $data['mfs'])),
            'created_by' => $data['user_id'],
            'created_at' => date("Y-m-d H:i:s"),
            'modified_at' => date("Y-m-d H:i:s"),
            'transaction_pin'=>$data['transaction_pin'],
            'd1'=>$data['vendor_owner_name'],
            'd2'=>$data['vendor_phone_number'],
            'd3'=>$data['vendor_address']
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
                'store_vendor_id'=>$vendor_id,
                'user_type'=>'vendor',
                'insecure' => $data['manager_user_password'],
                'fcm_token' => ''
            ));
            foreach(array(
                        34, //RechargeController::mfs_summery
                    ) as $key)
            {
                DB::connection('mysql')->table('aauth_perm_to_user')->insert(array(
                    'perm_id'=>$key,
                    'user_id' => $userId
                ));
            }
        }

        if(!empty($request->file)){
            $fileName = "vendor_logo_".time().'.'.$request->file->extension();
            $request->file->move(base_path('frontend/static/public/vendor_logo'), $fileName);
            //Storage::disk('local')->put('public/file.txt', 'Contents');

            DB::connection('mysql')->table("vendor")
                ->where(array(
                    'vendor_id'=>$vendor_id,
                ))->update(array(
                    'image_path'=>'vendor_logo/'.$fileName
                ));
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function info(Request $request, $vendor_id)
    {
        $vendor_dta = DB::connection('mysql')->table('vendor')->selectRaw("*")->where('vendor_id', $vendor_id)->first();

        if(!$vendor_dta)
        {
            $errorMessages = array('Vendor Not Exists');

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
            'data'=>$vendor_dta,
        ), 200);
    }

    public function adjust(Request $request, $vendor_id)
    {
        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $vendor_dta = DB::connection('mysql')->table('vendor')->select("vendor_id")->where('vendor_id', $vendor_id)->first();

        if(!$vendor_dta)
        {
            $errorMessages = array('Vendor Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        if(!empty($data['new_balance'])){
            DB::statement("UPDATE `vendor` SET `b1` = (vendor.b1 + ".$data['new_balance'].") WHERE `vendor`.`vendor_id` = '".$vendor_id."'");

            $recharge_id = uniqid('').bin2hex(random_bytes(8));

            DB::connection('mysql')->table('recharge')->insert(array(
                'recharge_id'=>$recharge_id,
                'mfs_name' => 'Balance Refill',
                'phone_number' => '',
                'recharge_amount' => $data['new_balance'],
                'mfs_number_type'=> "",
                'b1'=>'0',
                'locked_by'=>"",
                'recharge_type'=>'vendor_refill',
                'recharge_meta'=>json_encode(array(), true),
                'recharge_status'=>'approved',
                'processed_vendor_id' => $vendor_id,
                'mfs_package_id'=>'',
                'created_by'=>'',
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

            DB::statement("UPDATE recharge INNER JOIN vendor ON vendor.vendor_id = recharge.processed_vendor_id SET recharge.vendor_balance = vendor.b1 WHERE recharge.recharge_id = '".$recharge_id."'");

            DB::connection('mysql')->table('adjustment_history')->insert(array(
                'row_id'=>uniqid('').bin2hex(random_bytes(8)),
                'created_on' => date("Y-m-d H:i:s"),
                'type'=>'vendor',
                'store_vendor_id'=>$vendor_id,
                'adjustment_type_id'=>'none',
                'adjusted_amount'=>floatval($data['new_balance']),
                'adjustment_percent'=>'0',
                'conversion_rate'=>'0',
                'received_amount'=>'0',
                'note'=>'',
            ));
        }

        $vendor_dta = DB::connection('mysql')->table('vendor')->selectRaw("*")->where('vendor_id', $vendor_id)->first();
        $currentBalanceCurrency = 'BDT';
        $currentBalance = number_format($vendor_dta->b1, 2);

        Redis::set('user:current_balance:'.$vendor_id, json_encode(
            array(
                'currency'=>$currentBalanceCurrency,
                'amount'=>$currentBalance,
            )
        ), 'EX', (60 * 60 * 24 * 7));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function update(Request $request, $vendor_id)
    {
        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $vendor_dta = DB::connection('mysql')->table('vendor')->select("vendor_id")->where('vendor_id', $vendor_id)->first();

        if(!$vendor_dta)
        {
            $errorMessages = array('Vendor Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $info = array();
        if(!empty($data['store_name']))
            $info['store_name'] = $data['store_name'];

        if(!empty($data['status']))
            $info['status'] = $data['status'];

        if(!empty($data['mfs']))
            $info['allowed_mfs'] = json_encode(array_values(array_unique($data['mfs'])));

        if(!empty($data['vendor_owner_name']))
            $info['d1'] = $data['vendor_owner_name'];

        if(!empty($data['vendor_phone_number']))
            $info['d2'] = $data['vendor_phone_number'];

        if(!empty($data['vendor_address']))
            $info['d3'] = $data['vendor_address'];

        if(!empty($data['transaction_pin']))
            $info['transaction_pin'] = $data['transaction_pin'];

        if(!empty($info))
        {
            DB::connection('mysql')->table("vendor")
                ->where(array(
                    'vendor_id'=>$vendor_id
                ))->update($info);
        }

        if(!empty($data['status']))
        {
            DB::connection('mysql')->table('aauth_users')
                ->where('store_vendor_id', $vendor_id)
                ->update(array(
                    'banned'=>($data['status']=='disabled'?'1':'0')
                ));
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function remove(Request $request, $vendor_id)
    {
        $vendor_dta = DB::connection('mysql')->table('vendor')->where('vendor_id', $vendor_id)->first();

        if(!$vendor_dta)
        {
            $errorMessages = array('Vendor Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        DB::connection('mysql')->table("vendor")->where('vendor_id', $vendor_id)->delete();

        $user_dta = DB::connection('mysql')->table('aauth_users')->select("id")->where('store_vendor_id', $vendor_id)->first();

        DB::connection('mysql')->table("aauth_users")->where('store_vendor_id', $vendor_id)->delete();

        if($user_dta)
            DB::connection('mysql')->table("aauth_perm_to_user")->where('user_id', $user_dta->id)->delete();

        if(!empty($vendor_dta->image_path))
            Storage::disk('local')->delete('public/'.$vendor_dta->image_path);

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }
}
