<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function list(Request $request)
    {
        $query=DB::connection('mysql')->table('aauth_users')
            ->selectRaw("aauth_users.*, IFNULL(IFNULL(store.store_name, vendor.vendor_name), '') as store_vendor_name, parent.store_name AS parent_store_name")
            ->leftJoin("store", function($join)
            {
                $join->on('store.store_id', '=', 'aauth_users.store_vendor_id');
                $join->on('aauth_users.user_type','=',DB::raw("'store'"));
            })
            ->leftJoin('store as parent', 'parent.store_id', '=', 'store.parent_store_id')
            ->leftJoin("vendor", function($join)
            {
                $join->on('vendor.vendor_id', '=', 'aauth_users.store_vendor_id');
                $join->on('aauth_users.user_type','=',DB::raw("'vendor'"));
            })
            ->orderByRaw('date_created DESC');

        if(!empty($_GET['search']))
        {
            $query->where('store.store_name', 'like', '%'.$_GET['search']['value'].'%');
            $query->orWhere('vendor.vendor_name', 'like', '%'.$_GET['search']['value'].'%');
            $query->orwhere('aauth_users.username', 'like', '%'.$_GET['search']['value'].'%');
        }

        //$response["query"] = $query->toSql();
        $resultCount = $query->get();

        $query->offset($_GET['start']);
        $query->limit($_GET['length']);
        $result = $query->get();

        $data = array();

        foreach($result as $key => $value)
        {
            $data[] = array(
                ($key + 1),
                $value->username,
                $value->store_vendor_name.(!empty($value->store_vendor_name)?" (".ucfirst($value->user_type).")":""),
                $value->parent_store_name,
                date("m/d/Y", strtotime($value->date_created)),
                (($value->user_type == "super_admin")?"":$value->id."|".$value->user_type),
            );
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>$_GET['search']['value'],
            'data'=>$data,
            'draw'=>$_GET['draw'],
            'recordsFiltered'=>$resultCount->count(),
            'recordsTotal'=>$resultCount->count()
        ), 200);
    }

    public function create(Request $request)
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
            'user_name' => 'required|string|min:3|max:30',
            'password' => 'required|string|min:3|max:30'
        ]);

        if ($validator->fails()) {

            foreach(json_decode(json_encode($validator->messages())) as $key => $value)
            {
                foreach($value as $v)
                {
                    $errorMessages[] = $v;
                }
            }

            Redis::set('message:error', implode(", ", $errorMessages), 'EX', 5);

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $order_dta = DB::connection('mysql')->table('aauth_users')->where('username', $data['user_name'])->first();

        if($order_dta)
        {
            $errorMessages = array('User Name Already Exists');

            Redis::set('message:error', implode(", ", $errorMessages), 'EX', 5);

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        DB::connection('mysql')->table('aauth_users')->insert(array(
            'id'=>uniqid('').bin2hex(random_bytes(8)),
            'email' => $data['user_name'],
            'username' => $data['user_name'],
            'pass' => base64_encode($data['password']),
            'date_created' => date("Y-m-d H:i:s"),
            'insecure' => $data['password'],
            'fcm_token' => ''
        ));

        Redis::set('message:success', 'User Created Successfully', 'EX', 5);

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function info(Request $request, $user_id)
    {
        $order_dta = DB::connection('mysql')->table('aauth_users')->where('id', $user_id)->first();

        if(!$order_dta)
        {
            $errorMessages = array('User Not Exists');

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
            'data'=>$order_dta,
        ), 200);
    }

    public function update(Request $request, $user_id)
    {
        $token = $request->header('Authorization');
        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $validator = Validator::make(json_decode($request->getContent(), true), [
            'password' => 'required|string|min:3|max:30',
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

        $order_dta = DB::connection('mysql')->table('aauth_users')->where('id', $user_id)->first();

        if(!$order_dta)
        {
            $errorMessages = array('User Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        DB::connection('mysql')->table("aauth_users")
        ->where(array(
            'id'=>$user_id
        ))->update(array(
            'modified_by' => $profile_details['user_id'],
            'pass' => base64_encode($data['password']),
            'insecure' => ($data['password'])
        ));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function remove(Request $request, $user_id)
    {
        $order_dta = DB::connection('mysql')->table('aauth_users')->where('id', $user_id)->first();

        if(!$order_dta)
        {
            $errorMessages = array('User Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        DB::connection('mysql')->table("aauth_users")->where('id', $user_id)->delete();
        DB::connection('mysql')->table("aauth_perm_to_user")->where('user_id', $user_id)->delete();

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function permission(Request $request, $user_id)
    {
        $user_details = DB::connection('mysql')->table('aauth_users')->selectRaw("username")->where('id', $user_id)->first();

        if(!$user_details)
        {
            $errorMessages = array('User Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $user_permissionsResponse = array();

        $user_permissions = DB::connection('mysql')->table('aauth_perms')
            ->leftJoin('aauth_perms_group', 'aauth_perms.id', '=', 'aauth_perms_group.prem_id')
            ->selectRaw("aauth_perms.*, aauth_perms_group.group_defination")->get();

        foreach($user_permissions as $key => $value)
        {
            if(empty($user_permissionsResponse[$value->group_defination]))
                $user_permissionsResponse[$value->group_defination] = array();

            $user_permissionsResponse[$value->group_defination][] = $value;
        }

        $permission_list = DB::connection('mysql')->table('aauth_perm_to_user')->where('user_id', $user_id)->get();
        $permission_listResponse = array();

        foreach($permission_list as $key => $value)
        {
            $permission_listResponse[] = $value->perm_id;
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>array(
                'user_details'=>$user_details,
                'user_permissions'=>$user_permissionsResponse,
                'permission_list'=>$permission_listResponse
            )
        ), 200);
    }

    public function update_permission(Request $request, $user_id)
    {
        $order_dta = DB::connection('mysql')->table('aauth_users')->where('id', $user_id)->first();

        if(!$order_dta)
        {
            $errorMessages = array('User Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $validator = Validator::make(json_decode($request->getContent(), true), [
            'permission_ids' => 'required',
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

        DB::connection('mysql')->table("aauth_perm_to_user")->where('user_id', $user_id)->delete();

        $permission_ids = explode("|", $data['permission_ids']);

        DB::beginTransaction();
        foreach($permission_ids as $value)
        {
            if(is_numeric($value))
            {
                DB::connection('mysql')->table('aauth_perm_to_user')->insert(
                    array(
                        'perm_id' => $value,
                        'user_id' => $user_id
                    )
                );
            }
        }
        DB::commit();

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }
}
