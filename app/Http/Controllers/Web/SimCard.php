<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class SimCard extends Controller
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

    public function orders(Request $request)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $storeListQ = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc");

        if($this->userInfo->user_type == "super_admin" || $this->userInfo->user_type == "manager")
        {
            /*$storeListQ->where(array(
                'parent_store_id'=>(($this->userInfo->user_type == "super_admin")?'by_admin':$this->userInfo->store_vendor_id)
            ));*/
            $storeListQ->where(array(
                'enable_simcard_access'=>"1",
            ));
        }
        else
        {
            $storeListQ->where(array(
                'enable_simcard_access'=>"1",
                'parent_store_id'=>$this->userInfo->store_vendor_id
            ));
        }

        $storeList = $storeListQ->get();

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

        return view('pages.simcard.order.list', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'storeList'=>$storeList,
            'productList'=>$productList,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function approve_order(Request $request, $order_id)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        return view('pages.simcard.order.approve_order', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),

            'order_appointed_sim_card'=>DB::connection('mysql')->table('sc_sim_card')->selectRaw("id, sim_card_iccid, sim_card_mobile_number, cost")->where('sc_sim_card.order_id', $order_id)->get(),

            'orderInfo'=>DB::connection('mysql')->table('sc_orders')->selectRaw("sc_orders.*, inv_products.name, inv_products.price, store.store_name")
                ->leftJoin('inv_products', 'inv_products.id', '=', 'sc_orders.product_id')->leftJoin('store', 'store.store_id', '=', 'sc_orders.store_id')->where('sc_orders.id', $order_id)->first(),

            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }


    public function list(Request $request, $status = "in_stock", $order_id = "")
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $storeListQ = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc");

        if($this->userInfo->user_type == "super_admin" || $this->userInfo->user_type == "manager")
        {
            /*$storeListQ->where(array(
                'parent_store_id'=>(($this->userInfo->user_type == "super_admin")?'by_admin':$this->userInfo->store_vendor_id)
            ));*/
            $storeListQ->where(array(
                'enable_simcard_access'=>"1",
            ));
        }
        else
        {
            $storeListQ->where(array(
                'enable_simcard_access'=>"1",
                'parent_store_id'=>$this->userInfo->store_vendor_id
            ));
        }

        $storeList = $storeListQ->get();

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

        $order_store_id = "";

        if(!empty($order_id))
        {
            $sc_orders = DB::connection('mysql')->table('sc_orders')->select("store_id")->where('id', $order_id)->first();

            if($sc_orders)
            {
                $order_store_id = $sc_orders->store_id;
            }
        }

        return view('pages.simcard.list', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'storeList'=>$storeList,
            'productList'=>$productList,
            'status'=>$status,
            'order_id'=>$order_id,
            'order_store_id'=>$order_store_id,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }




    public function all(Request $request)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $storeListQ = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc");

        if($this->userInfo->user_type == "super_admin")
        {
            /*$storeListQ->where(array(
                'parent_store_id'=>(($this->userInfo->user_type == "super_admin")?'by_admin':$this->userInfo->store_vendor_id)
            ));*/
            $storeListQ->where(array(
                'enable_simcard_access'=>"1",
            ));
        }
        else
        {
            $storeListQ->where(array(
                'enable_simcard_access'=>"1",
                'parent_store_id'=>$this->userInfo->store_vendor_id
            ));
        }

        $storeList = $storeListQ->get();


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

        $simCardLot = DB::connection('mysql')->table('sc_sim_card_lot')->get();

        return view('pages.simcard.all', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'storeList'=>$storeList,
            'productList'=>$productList,
            'simCardLot'=>$simCardLot,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function add(Request $request)
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

        return view('pages.simcard.add_sim_card', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'productList'=>$productList,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function sale(Request $request, $sim_card_id)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $sim_card_info = DB::connection('mysql')->table('sc_sim_card')->selectRaw("sc_sim_card.*, inv_products.name as product_name")
            ->where(array(
                'sc_sim_card.sales_status'=>"in_stock",
                'sc_sim_card.id'=>$sim_card_id,
            ))
            ->leftJoin('inv_products', 'inv_products.id', '=', 'sc_sim_card.product_id')
            ->first();

        if(!$sim_card_info)
        {
            return redirect()->route('simcard_list', ['status' => 'in_stock']);
        }



        $svi = $this->userInfo->store_vendor_id;

        if($this->userInfo->store_vendor_id != $sim_card_info->store_id)
        {
            // I am selling other reseller SIM Card. Lets Search that reseller's parent reseller id.
            $parentReseller = DB::connection('mysql')->table('store')->selectRaw("parent_store_id")->where(array(
                'store_id'=>$sim_card_info->store_id
            ))->first();
            if(!empty($parentReseller)) $svi = $parentReseller->parent_store_id;
        }
        else
        {
            // I am Selling my Sim Card. Let's Search what's my parent reseller offering.
            $svi = $this->userInfo->parent_store_id;
        }

        $SimCardOffer = DB::connection('mysql')->table('sc_simcard_offer')
            ->selectRaw("
                IFNULL(sc_simcard_offer_reseller.title, sc_simcard_offer.title) as title,
                IFNULL(sc_simcard_offer_reseller.description, sc_simcard_offer.description) as description,
                IFNULL(sc_simcard_offer_reseller.bonus, sc_simcard_offer.bonus) as bonus,
                IFNULL(sc_simcard_offer_reseller.reseller_price, sc_simcard_offer.reseller_price) as reseller_price,
                IFNULL(sc_simcard_offer_reseller.status, sc_simcard_offer.status) as status,
                IFNULL(sc_simcard_offer_reseller.upload_path, sc_simcard_offer.upload_path) as upload_path,
                IFNULL(sc_simcard_offer_reseller.space_uploaded, sc_simcard_offer.space_uploaded) as space_uploaded,
                sc_simcard_offer.id,
                sc_simcard_offer_reseller.reseller_offer as sc_simcard_resellers_reseller_offer,
                sc_simcard_offer.reseller_offer as sc_simcard_reseller_offer
                ")
            ->leftJoin('sc_simcard_offer_reseller', function($join) use ($svi)
            {
                $join->on('sc_simcard_offer_reseller.sc_simcard_offer_id', '=', 'sc_simcard_offer.id');
                $join->on('sc_simcard_offer_reseller.store_id','=',DB::raw("'".$svi."'"));
            })
            ->where('sc_simcard_offer.status', '=', 'enable')
            ->where('sc_simcard_offer.product_id', DB::raw("'".$sim_card_info->product_id."'"))->orderBy("sc_simcard_offer.created_at", "DESC")->get();




        $MnpOperators = DB::connection('mysql')->table('sc_mnp_operator_list')
            ->where(array(
                'status'=>'enable'
            ))
            ->where('product_id', 'like', '%"' .$sim_card_info->product_id. '"%')
            ->orderBy("created_at", "DESC")
            ->get();

        return view('pages.simcard.sale_edit_simcard', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'sim_card_id'=>$sim_card_id,
            'sim_card_info' => $sim_card_info,
            'SimCardOffer'=>$SimCardOffer,
            'MnpOperators'=>$MnpOperators,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function update(Request $request, $sim_card_id)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $sim_card_info = DB::connection('mysql')->table('sc_sim_card')->selectRaw("sc_sim_card.*, inv_products.name as product_name")
            ->where(array(
                'sc_sim_card.id'=>$sim_card_id,
            ))
            ->leftJoin('inv_products', 'inv_products.id', '=', 'sc_sim_card.product_id')
            ->first();

        if(!$sim_card_info)
        {
            return redirect()->route('simcard_list', ['status' => 'in_stock']);
        }

        $SimCardOffer = DB::connection('mysql')->table('sc_simcard_offer')
            ->where(array(
                'status'=>'enable'
            ))
            ->orderBy("created_at", "DESC")
            ->get();

        $MnpOperators = DB::connection('mysql')->table('sc_mnp_operator_list')
            ->where(array(
                'status'=>'enable'
            ))
            ->orderBy("created_at", "DESC")
            ->get();

        return view('pages.simcard.sale_edit_simcard', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'sim_card_id'=>$sim_card_id,
            'sim_card_info' => $sim_card_info,
            'SimCardOffer'=>$SimCardOffer,
            'MnpOperators'=>$MnpOperators,
            'doUpdate'=>true,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function info(Request $request, $sim_card_id)
    {
        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        /*$pp = Storage::disk('public-folder')->allFiles('assets/sim_card/625cdcb65957f3a311a810446b2e1/admin');
        print_r($pp);
        die();*/

        $sim_card_info = DB::connection('mysql')->table('sc_sim_card')->selectRaw("sc_sim_card.*, inv_products.name as product_name, aauth_users.username as activator, store.store_name as store_name, sc_simcard_offer.title as sc_simcard_offer_title, sc_mnp_operator_list.title as sc_mnp_operator_list_title")
            ->where(array(
                'sc_sim_card.id'=>$sim_card_id,
            ))
            ->leftJoin('inv_products', 'inv_products.id', '=', 'sc_sim_card.product_id')
            ->leftJoin('aauth_users', 'aauth_users.id', '=', 'sc_sim_card.activated_by')
            ->leftJoin('store', 'store.store_id', '=', 'sc_sim_card.store_id')
            ->leftJoin('sc_simcard_offer', 'sc_simcard_offer.id', '=', 'sc_sim_card.product_offer_id')
            ->leftJoin('sc_mnp_operator_list', 'sc_mnp_operator_list.id', '=', 'sc_sim_card.mnp_operator_name')
            ->first();

        if(!$sim_card_info)
        {
            return redirect()->route('simcard_list', ['status' => 'in_stock']);
        }

        if($sim_card_info->locked == 1 && $sim_card_info->locked_by != $this->userInfo->user_id)
        {
            Redis::set('message:error', 'SIM card is locked for updating', 'EX', 5);
            return redirect()->route('simcard_list', ['status' => 'sold']);
        }

        $simcard_rejection_history = DB::connection('mysql')->table('sc_sim_card_history')
            ->where(array(
                'sim_card_id'=>$sim_card_id
            ))
            ->orderBy("created_at", "DESC")
            ->get();

        $sc_sim_card_files = DB::connection('mysql')->table('sc_sim_card_files')
            ->where(array(
                'sc_sim_card_id'=>$sim_card_id
            ))
            ->orderBy("created_at", "DESC")
            ->get();

        return view('pages.simcard.simcard_info', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'sim_card_id'=>$sim_card_id,
            'sim_card_info' => $sim_card_info,
            'simcard_rejection_history'=>$simcard_rejection_history,
            'sc_sim_card_files'=>$sc_sim_card_files,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function mnp_operators(Request $request)
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

        return view('pages.simcard.mnp_operators', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'productList'=>$productList,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function update_mnp_operator(Request $request, $mnp_operator_id)
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

        $storeList = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc")
            ->where(array(
                'enable_simcard_access'=>"1",
                'parent_store_id'=>(($this->userInfo->user_type == "super_admin")?'by_admin':$this->userInfo->store_vendor_id)
            ))
            ->get();

        return view('pages.simcard.update_mnp_operator', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),

            'productList'=>$productList,
            'storeList'=>$storeList,
            'operatorInfo'=>DB::connection('mysql')->table('sc_mnp_operator_list')->where('sc_mnp_operator_list.id', $mnp_operator_id)->first(),

            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function configure_reseller_promo(Request $request, $reseller_id)
    {
        $this->_prep($request);

        if (!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if (!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $storeDetails = DB::connection('mysql')->table('store')->where("store_id", "=" ,$reseller_id)->first();

        if($storeDetails->parent_store_id != "by_admin")
        {
            $sc_simcard_offer = [];
            $existingOffersIds = [];

            $sc_simcard_offer1 = DB::connection('mysql')->table('sc_simcard_offer_reseller')
                ->selectRaw('sc_simcard_offer_reseller.*,  sc_simcard_offer_reseller.sc_simcard_offer_id as id, inv_products.name as inv_products_name, sc_simcard_offer.product_id')
                ->leftJoin('sc_simcard_offer', function($join)
                {
                    $join->on('sc_simcard_offer.id', '=', 'sc_simcard_offer_reseller.sc_simcard_offer_id');
                })
                ->leftJoin('inv_products', function($join)
                {
                    $join->on('inv_products.id', '=', 'sc_simcard_offer.product_id');
                })
                ->whereIn('sc_simcard_offer.product_id', json_decode($storeDetails->allowed_products, true))
                ->where('sc_simcard_offer_reseller.store_id', $storeDetails->parent_store_id)
                ->get();

           // print_r(json_decode($storeDetails->allowed_products, true));
            //print_r($storeDetails->parent_store_id);


            foreach ($sc_simcard_offer1 as $x) {
                $sc_simcard_offer[] = $x;
                $existingOffersIds[] = $x->sc_simcard_offer_id;
            }

            $sc_simcard_offer2 = DB::connection('mysql')->table('sc_simcard_offer')
                ->selectRaw('sc_simcard_offer.*, inv_products.name as inv_products_name')
                ->leftJoin('inv_products', function($join)
                {
                    $join->on('inv_products.id', '=', 'sc_simcard_offer.product_id');
                })
                ->whereRaw("sc_simcard_offer.product_id IN ('".implode("', '", json_decode($storeDetails->allowed_products, true))."') ".(!empty($existingOffersIds)?"AND sc_simcard_offer.id NOT IN ('".implode("', '", $existingOffersIds)."')":""))
                //->whereIn('sc_simcard_offer.product_id', json_decode($storeDetails->allowed_products, true))
                //->whereNotIn('sc_simcard_offer.id', $existingOffersIds)
                ->get();

            foreach ($sc_simcard_offer2 as $x) {
                $sc_simcard_offer[] = $x;
            }
        }
        else
        {
            $sc_simcard_offer = DB::connection('mysql')->table('sc_simcard_offer')
                ->selectRaw('sc_simcard_offer.*, inv_products.name as inv_products_name')
                ->leftJoin('inv_products', function($join)
                {
                    $join->on('inv_products.id', '=', 'sc_simcard_offer.product_id');
                })
                ->whereIn('sc_simcard_offer.product_id', json_decode($storeDetails->allowed_products, true))->get();
        }


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

        return view('pages.simcard.promo_reseller', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'storeDetails'=>$storeDetails,
            'sc_simcard_offer'=>$sc_simcard_offer,
            'userInfo'=>$this->userInfo,
            'productList'=>$productList,
            'current_balance'=>json_decode($this->current_balance)
        ));

    }

    public function promo(Request $request)
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

        return view('pages.simcard.promo', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'productList'=>$productList,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function update_promo(Request $request, $promo_id)
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

        $storeList = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc")
            ->where(array(
                'enable_simcard_access'=>"1",
                'parent_store_id'=>(($this->userInfo->user_type == "super_admin")?'by_admin':$this->userInfo->store_vendor_id)
            ))
            ->get();

        $sc_simcard_offer = DB::connection('mysql')->table('sc_simcard_offer')->where('sc_simcard_offer.id', $promo_id)->first();

        if($this->userInfo->user_type == "store")
        {
            $svi = $this->userInfo->store_vendor_id;

            /*$sc_simcard_offer = DB::connection('mysql')->table('sc_simcard_offer')
                ->selectRaw("sc_simcard_offer_reseller.*, sc_simcard_offer.product_id, sc_simcard_offer.id")
                ->leftJoin('sc_simcard_offer_reseller', function($join) use ($promo_id, $svi)
                {
                    $join->on('sc_simcard_offer_reseller.sc_simcard_offer_id', '=', DB::raw("'".$promo_id."'"));
                    $join->on('sc_simcard_offer_reseller.store_id','=',DB::raw("'".$svi."'"));
                })
                ->where('sc_simcard_offer.id', DB::raw("'".$promo_id."'"))->first();*/

            $sc_simcard_offer = DB::connection('mysql')->table('sc_simcard_offer')
                ->selectRaw("
                IFNULL(sc_simcard_offer_reseller.title, sc_simcard_offer.title) as title,
                IFNULL(sc_simcard_offer_reseller.description, sc_simcard_offer.description) as description,
                IFNULL(sc_simcard_offer_reseller.bonus, sc_simcard_offer.bonus) as bonus,
                IFNULL(sc_simcard_offer_reseller.reseller_price, sc_simcard_offer.reseller_price) as reseller_price,
                IFNULL(sc_simcard_offer_reseller.status, sc_simcard_offer.status) as status,
                sc_simcard_offer.id,
                sc_simcard_offer.product_id,
                sc_simcard_offer_reseller.reseller_offer
                ")
                ->leftJoin('sc_simcard_offer_reseller', function($join) use ($svi)
                {
                    $join->on('sc_simcard_offer_reseller.sc_simcard_offer_id', '=', 'sc_simcard_offer.id');
                    $join->on('sc_simcard_offer_reseller.store_id','=',DB::raw("'".$svi."'"));
                })
                ->where('sc_simcard_offer.status', '=', 'enable')
                ->where('sc_simcard_offer.id', DB::raw("'".$promo_id."'"))->first();

        }

        return view('pages.simcard.update_promo', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),

            'productList'=>$productList,
            'storeList'=>$storeList,
            'operatorInfo'=>$sc_simcard_offer,

            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function banners(Request $request)
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

        return view('pages.simcard.banner.list', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'productList'=>$productList,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function update_banners(Request $request, $banners_id)
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

        return view('pages.simcard.update_promo', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'productList'=>$productList,
            'operatorInfo'=>DB::connection('mysql')->table('sc_product_promotion')->where('sc_product_promotion.id', $banners_id)->first(),
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function banner_details(Request $request, $banners_id){

        $this->_prep($request);

        if(!empty(Redis::get('message:success'))) $request->session()->flash('success', Redis::get('message:success'));
        if(!empty(Redis::get('message:error'))) $request->session()->flash('error', Redis::get('message:error'));

        $sc_product_promotion = DB::connection('mysql')->table('sc_product_promotion')->where('id', $banners_id)->first();

        return view('pages.simcard.banner.details', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'product_promotion'=>$sc_product_promotion,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function report_sales(Request $request)
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

        $storeListQ = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc");

        if($this->userInfo->user_type == "super_admin")
        {
            /*$storeListQ->where(array(
                'parent_store_id'=>(($this->userInfo->user_type == "super_admin")?'by_admin':$this->userInfo->store_vendor_id)
            ));*/
            $storeListQ->where(array(
                'enable_simcard_access'=>"1",
            ));
        }
        else
        {
            $storeListQ->where(array(
                'enable_simcard_access'=>"1",
                'parent_store_id'=>$this->userInfo->store_vendor_id
            ));
        }

        $storeList = $storeListQ->get();

        return view('pages.simcard.report.sales', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'productList'=>$productList,
            'storeList'=>$storeList,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function report_recharge(Request $request)
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

        $storeListQ = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc");

        if($this->userInfo->user_type == "super_admin")
        {
            /*$storeListQ->where(array(
                'parent_store_id'=>(($this->userInfo->user_type == "super_admin")?'by_admin':$this->userInfo->store_vendor_id)
            ));*/
            $storeListQ->where(array(
                'enable_simcard_access'=>"1",
            ));
        }
        else
        {
            $storeListQ->where(array(
                'enable_simcard_access'=>"1",
                'parent_store_id'=>$this->userInfo->store_vendor_id
            ));
        }

        $storeList = $storeListQ->get();

        return view('pages.simcard.report.recharge', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'productList'=>$productList,
            'storeList'=>$storeList,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }

    public function report_adjustment(Request $request, $reseller_id="")
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

        $storeListQ = DB::connection('mysql')->table('store')->selectRaw("store_id as id, CONCAT(store_name, ' [' ,store_code, ']') AS name")->orderBy("modified_at", "desc");

        if($this->userInfo->user_type == "super_admin")
        {
            /*$storeListQ->where(array(
                'parent_store_id'=>(($this->userInfo->user_type == "super_admin")?'by_admin':$this->userInfo->store_vendor_id)
            ));*/
            $storeListQ->where(array(
                'enable_simcard_access'=>"1",
            ));
        }
        else
        {
            $storeListQ->where(array(
                'enable_simcard_access'=>"1",
                'parent_store_id'=>$this->userInfo->store_vendor_id
            ));
        }

        $storeList = $storeListQ->get();

        if(empty($reseller_id)) $reseller_id = $this->userInfo->store_vendor_id;

        return view('pages.simcard.report.adjustment', array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'productList'=>$productList,
            'storeList'=>$storeList,
            'reseller_id'=>$reseller_id,
            'userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)
        ));
    }
}
