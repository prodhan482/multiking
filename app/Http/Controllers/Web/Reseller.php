<?php

namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class Reseller extends Controller
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

        return view('pages.reseller.list', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function list_simcard(Request $request)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        return view('pages.reseller.list_simcard', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function create(Request $request)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $productListQ = DB::connection('mysql')->table('inv_products')
            ->where(array(
                'enabled'=>1,
                'type'=>"sim_card"
            ));

        if($this->userInfo->user_type == "store")
        {
            $productListQ->whereIn("id", ($this->userInfo->allowed_products));
        }

        $productList = $productListQ->get();

        return view('pages.reseller.add', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),

            'product_list'=>$productList,

            'store_list'=>DB::connection('mysql')->table('store')->selectRaw('store_name, store_id')->get(),
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance),
            'mfs_list'=>DB::connection('mysql')->table('mfs')->selectRaw('mfs_name, mfs_id')->get()
        ));
    }

    public function update(Request $request, $reseller_id)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $productListQ = DB::connection('mysql')->table('inv_products')
            ->where(array(
                'enabled'=>1,
                'type'=>"sim_card"
            ));

        if($this->userInfo->user_type == "store")
        {
            $productListQ->whereIn("id", ($this->userInfo->allowed_products));
        }

        $productList = $productListQ->get();

        return view('pages.reseller.edit', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'reseller_id'=>$reseller_id,

            'product_list'=>$productList,

            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance),
            'mfs_list'=>DB::connection('mysql')->table('mfs')->selectRaw('mfs_name, mfs_id')->get()
        ));
    }

    public function currency_conversion(Request $request)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $store_dta = DB::connection('mysql')->table('store')->selectRaw("default_conv_rate_json")->first();

        return view('pages.reseller.conversion_rate', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance),
            'hideTable'=>(!$store_dta)
        ));
    }
}
