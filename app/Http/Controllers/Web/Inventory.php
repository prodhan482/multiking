<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class Inventory  extends Controller
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

    public function product_list(Request $request)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        return view('pages.inventory.product_list', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }
}
