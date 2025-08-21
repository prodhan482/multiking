<?php

namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class Report extends Controller
{
    private $userInfo = array();

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

    public function recharge_history(Request $request)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        return view('pages.report.recharge_history', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function mfs_summery(Request $request)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $storeList = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc")
            ->where(array(
                'parent_store_id'=>(($this->userInfo->user_type == "super_admin")?'by_admin':$this->userInfo->store_vendor_id)
            ))
            ->get();

        return view('pages.report.recharge_by_mfs', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'storeList'=>$storeList,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function reseller_balance_recharge(Request $request)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $store_dta = DB::connection('mysql')->table('store')->selectRaw("default_conv_rate_json")->first();

        return view('pages.report.reseller_balance_recharge_history', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance),
            'hideTable'=>(!$store_dta)
        ));
    }

    public function reseller_due_adjust(Request $request, $store_id="")
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $store_dta = DB::connection('mysql')->table('store')->selectRaw("default_conv_rate_json")->first();

        return view('pages.report.reseller_due_adjust_history', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'store_id'=>$store_id,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance),
            'hideTable'=>(!$store_dta)
        ));
    }



    public function reseller_due_statement(Request $request)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $storeList = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc")
            ->where(array(
                'parent_store_id'=>(($this->userInfo->user_type == "super_admin")?'by_admin':$this->userInfo->store_vendor_id)
            ))->get();

        return view('pages.report.reseller_due_statement', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'storeList'=>$storeList,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }



    public function reseller_return_payment(Request $request)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        return view('pages.report.reseller_return_payment', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function report_payment_receipt_upload(Request $request)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        return view('pages.report.report_payment_receipt_upload', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }
}
