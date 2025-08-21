<?php


namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Authentication extends Controller
{
    public function login(Request $request)
    {
        if (empty($request->getContent())) {
            return view('login');
        }
        else
        {
            $data = $_POST;

            $validator = Validator::make($_POST, [
                'login-email' => 'required|string',
                'login-password' => 'required|string',
                //'g-recaptcha-response' => 'required|string',
            ]);

            if ($validator->fails()) {
                return back()->with('error','Your Provided Username and Password is Wrong.');
            }

            /*$recaptcha = new \ReCaptcha\ReCaptcha("6Lcef_EeAAAAAJwGIJfoKHp0jU6LouRFB0g7UcEw");
            $resp = $recaptcha->setExpectedHostname('helloduniya22.com')
                ->verify($data["g-recaptcha-response"], $_SERVER['REMOTE_ADDR']);

            if (!$resp->isSuccess()) {
                return back()->with('error','In Valid reCaptcha');
            }*/

            $aauth_users = DB::connection('mysql')->table('aauth_users')
                ->where('username', $data["login-email"])
                ->where('pass', base64_encode($data["login-password"]))
                ->where('banned', '0')
                ->first();

            if(!$aauth_users)
            {
                return back()->with('error','Your Provided Username and Password is Wrong.');
            }

            if($aauth_users->user_type != "super_admin")
            {
                $user_permissionsResponse = array();
                $user_permissions = DB::connection('mysql')->table('aauth_perm_to_user')
                    ->leftJoin('aauth_perms', 'aauth_perms.id', '=', 'aauth_perm_to_user.perm_id')
                    ->where('aauth_perm_to_user.user_id', "=", $aauth_users->id)
                    ->selectRaw("aauth_perms.name")->get();

                foreach($user_permissions as $key => $value)
                {
                    $user_permissionsResponse[] = $value->name;
                }
            }
            else
            {
                $user_permissionsResponse = array();
                $permission_list = DB::connection('mysql')->table('aauth_perms')->get();
                foreach($permission_list as $key => $value)
                {
                    $user_permissionsResponse[] = $value->name;
                }
            }

            $allowed_mfs_ids = array();
            $allowed_products = array();
            $logo = "https://via.placeholder.com/100X200";
            $storeName = '';
            $storeOwnerName = '';
            $storePhoneNumber = '';
            $storeBaseCurrency = '';
            $storeAddress = '';
            $currentBalance = '';
            $currentBalanceCurrency = '';
            $simcard_due_amount = "";
            $currencyConversionRates = array();
            $due_euro = 0;
            $parent_store_id = '';
            $pin_verification_required = 0;

            if($aauth_users->user_type == "vendor")
            {
                $vendor_dta = DB::connection('mysql')->table('vendor')->selectRaw("*")->where('vendor_id', $aauth_users->store_vendor_id)->first();
                if($vendor_dta)
                {
                    $allowed_mfs_ids = json_decode($vendor_dta->allowed_mfs, true);
                }

                if($vendor_dta && !empty($vendor_dta->image_path))
                {
                    $logo = 'public/'.$vendor_dta->image_path;
                }

                $storeName = $vendor_dta->vendor_name;
                $storeOwnerName = $vendor_dta->d1;
                $storePhoneNumber = $vendor_dta->d2;
                $storeAddress = $vendor_dta->d3;

                $currentBalanceCurrency = 'BDT';
                $currentBalance = number_format($vendor_dta->b1, 2);
            }

            if($aauth_users->user_type == "store")
            {
                $store_dta = DB::connection('mysql')->table('store')->selectRaw("image_path, store_name, store_owner_name, store_address, store_phone_number, balance, base_currency, store_conv_rate_json, default_conv_rate_json, commission_percent, due_euro, parent_store_id, simcard_due_amount, allowed_products")->where('store_id', $aauth_users->store_vendor_id)->first();
                if($store_dta && !empty($store_dta->image_path))
                {
                    $logo = $store_dta->image_path;
                }



                $storeName = $store_dta->store_name;
                $storeOwnerName = $store_dta->store_owner_name;
                $storePhoneNumber = $store_dta->store_phone_number;
                $storeBaseCurrency = $store_dta->base_currency;
                $storeAddress = $store_dta->store_address;
                $parent_store_id = $store_dta->parent_store_id;

                $pin_verification_required = 1;

                $commission_percent = json_decode($store_dta->commission_percent);
                foreach($commission_percent as $row)
                {
                    $allowed_mfs_ids[] = $row->id;
                }

                $allowed_products = json_decode($store_dta->allowed_products);

                $currentBalance = number_format($store_dta->balance, 2);
                $currentBalanceCurrency = strtoupper($store_dta->base_currency);
                $due_euro = ($store_dta->due_euro);
                $simcard_due_amount = $store_dta->simcard_due_amount;

                if($store_dta->store_conv_rate_json == "[]") $store_dta->store_conv_rate_json = array();

                if(!empty($store_dta->store_conv_rate_json)) $currencyConversionRates = json_decode($store_dta->store_conv_rate_json);
                if(empty($store_dta->store_conv_rate_json)) $currencyConversionRates = json_decode($store_dta->default_conv_rate_json);
            }

            if($aauth_users->user_type != "store")
            {
                $store_dta = DB::connection('mysql')->table('store')->selectRaw("default_conv_rate_json")->first();
                if($store_dta)
                {
                    $currencyConversionRates = json_decode($store_dta->default_conv_rate_json);
                }
                else
                {
                    $currencyConversionRates = array();
                }
            }

            $expireTime = (60 * 60 * 24);
            $redisToken = Str::random(100);

            Redis::set('user:token:'.$redisToken, json_encode(
                array(
                    'user_id'=>$aauth_users->id,
                    'store_vendor_id'=>$aauth_users->store_vendor_id,
                    'store_vendor_admin'=>$aauth_users->store_vendor_admin,
                    'user_type'=>$aauth_users->user_type,
                    'username'=>$aauth_users->username,
                    'permission_lists'=>$user_permissionsResponse,
                    'allowed_mfs_ids'=>$allowed_mfs_ids,
                    'allowed_products'=>$allowed_products,
                    'mfs_list'=>DB::connection('mysql')->table('mfs')->limit(50)->whereIn('mfs_id', $allowed_mfs_ids)->get(),
                    'storeName'=>$storeName,
                    'storeOwnerName'=>$storeOwnerName,
                    'storePhoneNumber'=>$storePhoneNumber,
                    'storeBaseCurrency'=>$storeBaseCurrency,
                    'storeAddress'=>$storeAddress,
                    'logo'=>$logo,
                    'currency_conversions_list'=>$currencyConversionRates,
                    'parent_store_id'=>$parent_store_id,
                    'pin_verification_required'=>$pin_verification_required
                )
            ), 'EX', $expireTime);

            Redis::set('user:current_balance:'.$aauth_users->store_vendor_id, json_encode(
                array(
                    'currency'=>$currentBalanceCurrency,
                    'amount'=>$currentBalance,
                    'due_euro'=>$due_euro,
                    'simcard_due_amount'=>$simcard_due_amount
                )
            ), 'EX', $expireTime);

            $request->session()->put('AuthorizationToken', $redisToken);
            return redirect('/manage');
        }
    }

    public function logout(Request $request)
    {
        Redis::del('user:token:'.str_replace("Bearer ","",session('AuthorizationToken')));
        Session::flush();

        return redirect('/login');
    }
}
