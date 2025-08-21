<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function info(Request $request)
    {
        $token = $request->header('Authorization');

        if(empty($token))
        {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'description'=>'No Authorization Data Found'
            ), 401);
        }

        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));

        $profile_details = json_decode($profile_details, true);
        $current_balance = (($profile_details['user_type'] != "super_admin" || $profile_details['user_type'] != "vendor")?Redis::get('user:current_balance:'.$profile_details['store_vendor_id']):json_encode(
            array(
                'currency'=>'',
                'amount'=>'',
                'due_euro'=>'',
                'simcard_due_amount'=>''
            )
        ));

        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>(String) time(),
            'success'=>true,
            'current_balance'=>json_decode($current_balance),
            'data'=>json_decode($profile_details)
        ), 200);
    }

    public function update(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $validator = Validator::make(json_decode($request->getContent(), true), [
            'new_password' => 'required|string|min:2|max:50',
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
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $updateData = array();

        if(!empty($data["new_password"])){
            $updateData['pass'] = base64_encode($data["new_password"]);
            $updateData['insecure'] = $data["new_password"];
        }

        if(!empty($updateData))
            DB::connection('mysql')->table("aauth_users")
                ->where(array(
                    'id'=>$profile_details['user_id']
                ))->update($updateData);

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function updateReseller(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $errorMessages = array();
        $updateData = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $validator = Validator::make($data, [
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

        if(!empty($data["transaction_pin"])){
            $updateData['transaction_pin'] = $data["transaction_pin"];
        }

        if(empty($updateData)){
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);
        }

        if(!empty($updateData)){
            DB::connection('mysql')->table("store")
                ->where(array(
                    'store_id'=>$profile_details['store_vendor_id']
                ))->update($updateData);
        }


        /*$updateData = array();

        if(!empty($data["new_password"])){
            $updateData['pass'] = base64_encode($data["new_password"]);
            $updateData['insecure'] = $data["new_password"];
        }

        if(!empty($updateData))
            DB::connection('mysql')->table("aauth_users")
                ->where(array(
                    'id'=>$profile_details['user_id']
                ))->update($updateData);*/

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function update_fcm(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        if(!empty($data["fcm_token"]))
        {
            DB::connection('mysql')
                ->table("aauth_users")
                ->where(array('id'=>$profile_details["user_id"]))
                ->update(array('fcm_token' => $data["fcm_token"]));
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function verify_transaction_pin(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        if(empty($data["transaction_pin"]))
        {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 422);
        }

        $search_for_transaction_pin = DB::connection('mysql')->table('store')->where('store_id', $profile_details['store_vendor_id'])->where('transaction_pin', $data['transaction_pin'])->first();

        if(!$search_for_transaction_pin)
        {
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 422);
        }

        $profile_details['pin_verification_required'] = 0;

        $token = $request->header('Authorization');
        Redis::set(('user:token:'.str_replace("Bearer ","",$token)), json_encode(
            $profile_details
        ), 'EX', (60 * 60 * 24 * 7));


        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function dashboard(Request $request)
    {
        $notice = "Basic Notice";
        $highlighted_blocks = array();
        $table = array();

        $token = $request->header('Authorization');
        //$params = json_decode($request->getContent(), true);

        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);


        if($profile_details['user_type'] == "super_admin")
        {
            $highlighted_blocks[] = array('icon_class'=>'person', 'title'=>'title 1', 'value'=>'100%');
            $highlighted_blocks[] = array('icon_class'=>'person', 'title'=>'title 2', 'value'=>'100%');

            $table = array(
                "title"=>"Today Usages",
                "rows"=>array(
                    array("col t", "col v"),
                    array("col t", "col v"),
                    array("col t", "col v"),
                    array("col t", "col v"),
                    array("col t", "col v"),
                    array("col t", "col v"),
                )
                );
        }
        else if($profile_details['user_type'] == "store")
        {
            $highlighted_blocks[] = array('icon_class'=>'person', 'title'=>'title 1', 'value'=>'100%');
            $highlighted_blocks[] = array('icon_class'=>'person', 'title'=>'title 2', 'value'=>'100%');

            $table = array(
                "title"=>"Today Usages",
                "rows"=>array(
                    array("col t", "col v"),
                    array("col t", "col v"),
                    array("col t", "col v"),
                    array("col t", "col v"),
                    array("col t", "col v"),
                    array("col t", "col v"),
                )
                );
        }
        else if($profile_details['user_type'] == "vendor")
        {
            $highlighted_blocks[] = array('icon_class'=>'person', 'title'=>'title 1', 'value'=>'100%');
            $highlighted_blocks[] = array('icon_class'=>'person', 'title'=>'title 2', 'value'=>'100%');

            $table = array(
                "title"=>"Today Usages",
                "rows"=>array(
                    array("col t", "col v"),
                    array("col t", "col v"),
                    array("col t", "col v"),
                    array("col t", "col v"),
                    array("col t", "col v"),
                    array("col t", "col v"),
                )
                );
        }
        else{}



        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'dashboard_info'=>array(
                'notice' => $notice,
                'highlighted_blocks' => $highlighted_blocks,
                'table' => $table
            )
        ), 200);
    }
}
