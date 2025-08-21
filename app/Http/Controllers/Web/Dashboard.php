<?php


namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class Dashboard extends Controller
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

    public function manage(Request $request)
    {
        $this->_prep($request);

        switch ($this->userInfo->user_type) {
            case "manager":
            case "super_admin":
                DB::select(DB::raw("set session sql_mode=''"));

                $reports = array(
                    'simcard_sold_today'=>array(),
                    'simcard_sold_yesterday'=>array(),
                    'simcard_sold_last_7_days'=>array(),
                    'simcard_sold_this_month'=>array(),
                    'simcard_sold_last_month'=>array(),
                    'simcard_sold_last_month_earlier'=>array(),
                    'pending_sim_activation'=>array(),
                    'pending_sim_order'=>array()
                );

                $reports['simcard_sold_today'] = DB::select(DB::raw("SELECT inv_products.`id` AS product_id, inv_products.`name` AS product_name, SUM(IF((sc_sim_card.`sales_status` = 'sold' AND sc_sim_card.status = 'approved'), 1, 0 )) AS total FROM sc_sim_card LEFT JOIN inv_products ON inv_products.id = sc_sim_card.product_id WHERE (sc_sim_card.sold_at BETWEEN '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."') GROUP BY sc_sim_card.product_id"));

                $reports['simcard_sold_yesterday'] = DB::select(DB::raw("SELECT inv_products.`id` AS product_id, inv_products.`name` AS product_name, SUM(IF((sc_sim_card.`sales_status` = 'sold' AND sc_sim_card.status = 'approved'), 1, 0 )) AS total FROM sc_sim_card LEFT JOIN inv_products ON inv_products.id = sc_sim_card.product_id WHERE (sc_sim_card.sold_at BETWEEN '".date("Y-m-d 00:00:00", strtotime('-1 day'))."' AND '".date("Y-m-d 23:59:59", strtotime('-1 day'))."') GROUP BY sc_sim_card.product_id"));

                $reports['simcard_sold_last_7_days'] = DB::select(DB::raw("SELECT inv_products.`id` AS product_id, inv_products.`name` AS product_name, SUM(IF((sc_sim_card.`sales_status` = 'sold' AND sc_sim_card.status = 'approved'), 1, 0 )) AS total FROM sc_sim_card LEFT JOIN inv_products ON inv_products.id = sc_sim_card.product_id WHERE (sc_sim_card.sold_at BETWEEN '".date("Y-m-d 00:00:00", strtotime('-7 day'))."' AND '".date("Y-m-d 23:59:59")."') GROUP BY sc_sim_card.product_id"));

                $reports['simcard_sold_this_month'] = DB::select(DB::raw("SELECT inv_products.`id` AS product_id, inv_products.`name` AS product_name, SUM(IF((sc_sim_card.`sales_status` = 'sold' AND sc_sim_card.status = 'approved'), 1, 0 )) AS total FROM sc_sim_card LEFT JOIN inv_products ON inv_products.id = sc_sim_card.product_id WHERE (sc_sim_card.sold_at BETWEEN '".date("Y-m-01 00:00:00")."' AND '".date("Y-m-d 23:59:59")."') GROUP BY sc_sim_card.product_id"));

                $reports['simcard_sold_last_month'] = DB::select(DB::raw("SELECT inv_products.`id` AS product_id, inv_products.`name` AS product_name, SUM(IF((sc_sim_card.`sales_status` = 'sold' AND sc_sim_card.status = 'approved'), 1, 0 )) AS total FROM sc_sim_card LEFT JOIN inv_products ON inv_products.id = sc_sim_card.product_id WHERE (sc_sim_card.sold_at BETWEEN '".date("Y-m-01 00:00:00", strtotime("first day of previous month"))."' AND '".date("Y-m-t 23:59:59", strtotime("first day of previous month"))."') GROUP BY sc_sim_card.product_id"));

                $reports['simcard_sold_last_month_earlier'] = DB::select(DB::raw("SELECT inv_products.`id` AS product_id, inv_products.`name` AS product_name, SUM(IF((sc_sim_card.`sales_status` = 'sold' AND sc_sim_card.status = 'approved'), 1, 0 )) AS total FROM sc_sim_card LEFT JOIN inv_products ON inv_products.id = sc_sim_card.product_id WHERE (sc_sim_card.sold_at BETWEEN '".date("Y-m-01 00:00:00", strtotime("first day of -2 months"))."' AND '".date("Y-m-t 23:59:59", strtotime("first day of -2 months"))."') GROUP BY sc_sim_card.product_id"));

                $reports['pending_sim_activation'] = DB::select(DB::raw("SELECT `sc_sim_card`.id, `sc_sim_card`.sim_card_iccid, `sc_sim_card`.sold_at, inv_products.`name` as product_name, store.store_code, store.store_name FROM `sc_sim_card` LEFT JOIN  inv_products ON inv_products.id = sc_sim_card.product_id LEFT JOIN  store ON store.store_id = sc_sim_card.store_id WHERE `sc_sim_card`.status = 'pending' AND `sc_sim_card`.sales_status = 'sold' ORDER BY `sc_sim_card`.sold_at ASC LIMIT 5"));

                $reports['pending_sim_order'] = DB::select(DB::raw("SELECT sc_orders.id, sc_orders.created_at, sc_orders.order_serial, inv_products.`name` as product_name, store.store_name, store.store_code FROM `sc_orders` LEFT JOIN  inv_products ON inv_products.id = sc_orders.product_id LEFT JOIN  store ON store.store_id = sc_orders.store_id WHERE sc_orders.status = 'pending' ORDER BY sc_orders.created_at ASC LIMIT 5"));


                if($this->userInfo->user_type == "manager")
                {
                    return view('pages.dashboard.manager', ['userInfo'=>$this->userInfo, 'reports'=>$reports, 'current_balance'=>json_decode($this->current_balance)]);
                }
                else
                {
                    return view('pages.dashboard.admin', ['userInfo'=>$this->userInfo, 'reports'=>$reports, 'current_balance'=>json_decode($this->current_balance)]);
                }

            case "vendor":
                return view('pages.dashboard.vendor', ['userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)]);
            case "store":
                if(!empty($this->userInfo->pin_verification_required))
                {
                    return view('pages.pin_verification', ['userInfo'=>$this->userInfo]);
                }

                $reports = array(
                    'last_sim_request'=>''
                );

                DB::select(DB::raw("set session sql_mode=''"));

                $reports['last_sim_request'] = DB::select(DB::raw("SELECT inv_products.`name` AS product_name, sc_sim_card.sim_card_iccid, sc_sim_card.sim_card_mobile_number, sc_sim_card.sold_at, sc_sim_card.cost, sc_sim_card.sales_price, sc_sim_card.status, sc_sim_card.sales_status FROM sc_sim_card LEFT JOIN inv_products ON inv_products.id = sc_sim_card.product_id WHERE sc_sim_card.sales_status = 'sold' AND sc_sim_card.store_id = '".$this->userInfo->store_vendor_id."' ORDER BY sc_sim_card.sold_at DESC LIMIT 10"));

                $reports['simcard_current_stock'] = DB::select(DB::raw("SELECT inv_products.`name` AS product_name, inv_products.`id` AS product_id, SUM(IF(sc_sim_card.sales_status = 'in_stock', 1, 0)) AS in_stock_status,  inv_products.`id` FROM sc_sim_card LEFT JOIN inv_products ON inv_products.id = sc_sim_card.product_id WHERE sc_sim_card.store_id = '".$this->userInfo->store_vendor_id."' GROUP BY inv_products.id"));

                $reports['simcard_last_order_info'] = DB::select(DB::raw("SELECT sc_orders.quantity, sc_orders.id, sc_orders.`status`, sc_orders.created_at, inv_products.`name` AS product_name FROM sc_orders LEFT JOIN inv_products ON inv_products.id = sc_orders.product_id WHERE sc_orders.store_id = '".$this->userInfo->store_vendor_id."' ORDER BY sc_orders.id DESC LIMIT 3"));

                $reports['simcard_product_offers'] = DB::select(DB::raw("SELECT * FROM sc_simcard_offer WHERE sc_simcard_offer.product_id IN ('".join("', '", $this->userInfo->allowed_products)."') AND status = 'enable'"));

                $reports['simcard_product_promotion'] = DB::select(DB::raw("SELECT * FROM sc_product_promotion WHERE sc_product_promotion.product_id IN ('".join("', '", $this->userInfo->allowed_products)."') ORDER BY created_by DESC"));

                if(!empty($this->userInfo->mfs_list) && !empty($this->userInfo->allowed_products))
                {
                    // Both Simcard and MFS
                    return view('pages.dashboard.reseller_sim_mfs', ['userInfo'=>$this->userInfo, 'reports'=>$reports, 'current_balance'=>json_decode($this->current_balance)]);
                }

                if(!empty($this->userInfo->mfs_list) && empty($this->userInfo->allowed_products))
                {
                    // MFS
                    return view('pages.dashboard.reseller_mfs', ['userInfo'=>$this->userInfo, 'reports'=>$reports, 'current_balance'=>json_decode($this->current_balance)]);
                }

                if(empty($this->userInfo->mfs_list) && !empty($this->userInfo->allowed_products))
                {
                    // Simcard
                    return view('pages.dashboard.reseller_simcard', ['userInfo'=>$this->userInfo, 'reports'=>$reports, 'current_balance'=>json_decode($this->current_balance)]);
                }

                return view('pages.dashboard.reseller', ['userInfo'=>$this->userInfo, 'reports'=>$reports, 'current_balance'=>json_decode($this->current_balance)]);

            default:
                return view('pages.dashboard.default', ['userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)]);
        }
    }



    public function refill(Request $request)
    {
        $this->_prep($request);

        if(!empty($this->userInfo->pin_verification_required))
        {
            return view('pages.pin_verification', ['userInfo'=>$this->userInfo]);
        }

        return view('pages.dashboard.reseller_refill', ['userInfo'=>$this->userInfo, 'current_balance'=>json_decode($this->current_balance)]);
    }
}
