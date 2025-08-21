<?php

namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class Users extends Controller
{
    private $userInfo = array();
    private $current_balance = array();

    public function __construct(){}

    private function _prep(Request $request)
    {
        $token = $request->session()->get('AuthorizationToken', '');
        $userInfo = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $this->userInfo = json_decode($userInfo);

        $this->current_balance = (($this->userInfo->user_type != "super_admin")?Redis::get('user:current_balance:'.$this->userInfo->store_vendor_id):json_encode(
            array(
                'currency'=>'',
                'amount'=>'',
                'due_euro'=>''
            )
        ));
    }


    public function list(Request $request)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        return view('pages.users.list', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function permission_management(Request $request, $user_id)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $selected_user_dta = DB::connection('mysql')->table('aauth_users')->where('id', $user_id)->first();

        return view('pages.users.permission', array(
            'q_user_id'=>$user_id,
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'selected_user_dta'=>$selected_user_dta,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }
}
