<?php

namespace App\Http\Controllers\Api\simcard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use function base_path;
use function response;

class PromoController extends Controller
{
    public function list(Request $request)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $params = $_POST;

        $whereQuery = array();

        if(!empty($_POST["product_id"])) $whereQuery[] = ("sc_simcard_offer.product_id = '".addslashes($_POST["product_id"])."'");

        /*;

        if($profile_details['user_type'] == "super_admin") {
            if(!empty($_POST["store_id"])) $whereQuery[] = ("sc_orders.store_id = '" . addslashes($_POST["store_id"]) . "'");
        }
        else
        {
            $whereQuery[] = "sc_orders.store_id = ".$profile_details['store_vendor_id'];
        }*/

        $whereQuery = array_values(array_diff($whereQuery,array("")));

        if($profile_details['user_type'] == "store")
        {
            $query = DB::select(DB::raw("SELECT

    IFNULL(sc_simcard_offer_reseller.title, sc_simcard_offer.title) as title,
    IFNULL(sc_simcard_offer_reseller.description, sc_simcard_offer.description) as description,
    IFNULL(sc_simcard_offer_reseller.bonus, sc_simcard_offer.bonus) as bonus,
    IFNULL(sc_simcard_offer_reseller.reseller_price, sc_simcard_offer.reseller_price) as reseller_price,
    IFNULL(sc_simcard_offer_reseller.status, sc_simcard_offer.status) as status,
    sc_simcard_offer.id,
    inv_products.`name` AS product_name

       FROM sc_simcard_offer LEFT JOIN inv_products ON inv_products.id = sc_simcard_offer.product_id LEFT JOIN sc_simcard_offer_reseller ON sc_simcard_offer_reseller.sc_simcard_offer_id = sc_simcard_offer.id AND sc_simcard_offer_reseller.store_id = '".$profile_details['store_vendor_id']."' ".(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")." ORDER BY FIELD(sc_simcard_offer.status, 'enable') DESC, sc_simcard_offer.created_at DESC LIMIT ".$params['start'].", ".$params['length']));
        }
        else
        {
            $query = DB::select(DB::raw("SELECT sc_simcard_offer.*, inv_products.`name` AS product_name FROM sc_simcard_offer LEFT JOIN inv_products ON inv_products.id = sc_simcard_offer.product_id ".(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")." ORDER BY FIELD(sc_simcard_offer.status, 'enable') DESC, sc_simcard_offer.created_at DESC LIMIT ".$params['start'].", ".$params['length']));
        }

        $queryCount = DB::select(DB::raw("SELECT count(sc_simcard_offer.id) as total FROM sc_simcard_offer ".(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")));

        $data = array();
        $pos = ($params['start'] + 1);

        foreach($query as $row)
        {
            $data[] = array(
                $pos++,
                $row->title,
                $row->description,
                $row->product_name,
                $row->bonus,
                $row->reseller_price,
                ($row->status),
                ($row->id."||".($row->status == "enable"?"disable":"enable")),
            );
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$data,
            'draw'=>$params['draw'],
            'recordsFiltered'=>$queryCount[0]->total,
            'recordsTotal'=>$queryCount[0]->total
        ), 200);
    }

    public function create(Request $request)
    {
        $token = $request->header('Authorization');
        $data = $_POST;//json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
            'title' => 'required|string',
            'product_id' => 'required|string',
            'reseller_bonus' => 'required|string',
            //'description' => 'required|string',
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

        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $sc_simcard_offer = uniqid('').bin2hex(random_bytes(8));

        DB::connection('mysql')->table('sc_simcard_offer')->insert(array(
            'id'=>$sc_simcard_offer,
            'title' => $data["title"],
            'product_id' => $data["product_id"],
            'status' => "enable",
            'bonus' => $data["reseller_bonus"],
            'reseller_price' => $data["reseller_price"],
            'reseller_offer' => json_encode(array()),
            'description' =>  $data["description"],
            'upload_path'=>"",
            'created_by'=>$profile_details["user_id"],
            'created_at'=>date("Y-m-d H:i:s")
        ));

        if(!empty($request->file)){
            $fileName = "simcard_offer_".time().'.'.$request->file->extension();
            //$request->file->move(storage_path('app/public/store_logo'), $fileName);
            $request->file->move(base_path('public/assets/simcard_offer'), $fileName);
            //Storage::disk('local')->put('public/file.txt', 'Contents');

            DB::connection('mysql')->table("sc_simcard_offer")
                ->where(array(
                    'id'=>$sc_simcard_offer
                ))->update(array(
                    'upload_path'=>'assets/simcard_offer/'.$fileName
                ));

            self::sendUploadRequestToDigOcenSpace((base_path('public/assets/simcard_offer')."/".$fileName), ("assets/simcard_offer/".$fileName), "sc_simcard_offer", array("id"), array($sc_simcard_offer));
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function change_status(Request $request, $promo_id)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);
        $data = json_decode($request->getContent(), true);

        $sc_simcard_offer = DB::connection('mysql')->table('sc_simcard_offer')->where('id', $promo_id)->first();

        if(!$sc_simcard_offer)
        {
            $errorMessages = array('order Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        if($profile_details['user_type'] == "store") {
            $sc_simcard_offer_reseller_exists = DB::connection('mysql')->table('sc_simcard_offer_reseller')->select("sc_simcard_offer_id")->where('sc_simcard_offer_id', $promo_id)->where('store_id', $profile_details['store_vendor_id'])->first();

            if(empty($sc_simcard_offer_reseller_exists))
            {
                $dataArray = array(
                    'sc_simcard_offer_id' => $promo_id,
                    'store_id' => $profile_details['store_vendor_id'],
                    'title' => $sc_simcard_offer->title,
                    "status"=>$data['status'],
                    'bonus' => $sc_simcard_offer->bonus,
                    'reseller_price' => $sc_simcard_offer->reseller_price,
                    'reseller_offer' => $sc_simcard_offer->reseller_offer,
                    'description' =>  $sc_simcard_offer->description
                );
                $dataArray["created_by"] = $profile_details["user_id"];
                $dataArray["created_at"] = date("Y-m-d H:i:s");
                $dataArray["upload_path"] = "";
                $dataArray['row_id'] = uniqid('').bin2hex(random_bytes(8));
                DB::connection('mysql')->table('sc_simcard_offer_reseller')->insert($dataArray);
            }
            else
            {
                DB::connection('mysql')->table('sc_simcard_offer_reseller')->where(array('sc_simcard_offer_id'=>$promo_id, "store_id"=>$profile_details['store_vendor_id']))->update(array(
                    "status"=>$data['status']
                ));
            }
        } else {
            DB::connection('mysql')->table("sc_simcard_offer")
                ->where(array(
                    'id'=>$promo_id
                ))->update(array(
                    "status"=>$data['status']
                ));

            DB::connection('mysql')->table("sc_simcard_offer_reseller")
                ->where(array(
                    'sc_simcard_offer_id'=>$promo_id
                ))->update(array(
                    "status"=>$data['status']
                ));
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function update(Request $request, $promo_id)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);
        $data = $_POST;

        $mnp_operators_dta = DB::connection('mysql')->table('sc_simcard_offer')->select("id")->where('id', $promo_id)->first();

        if(!$mnp_operators_dta)
        {
            $errorMessages = array('order Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
            'title' => 'required|string',
            'product_id' => 'required|string',
            'reseller_bonus' => 'required|string',
            'reseller_price' => 'required|string',
            'description' => 'required|string',
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

        if($profile_details['user_type'] == "store") {
            $sc_simcard_offer_reseller_exists = DB::connection('mysql')->table('sc_simcard_offer_reseller')->select("sc_simcard_offer_id")->where('sc_simcard_offer_id', $promo_id)->where('store_id', $profile_details['store_vendor_id'])->first();

            $dataArray = array(
                'sc_simcard_offer_id' => $promo_id,
                'store_id' => $profile_details['store_vendor_id'],
                'title' => $data["title"],
                'bonus' => $data["reseller_bonus"],
                'reseller_price' => $data["reseller_price"],
                'reseller_offer' => $data["reseller_offer"],
                'description' =>  $data["description"]
            );

            if(empty($sc_simcard_offer_reseller_exists))
            {
                $dataArray["created_by"] = $profile_details["user_id"];
                $dataArray["created_at"] = date("Y-m-d H:i:s");
                $dataArray["status"] = "enable";
                $dataArray["upload_path"] = "";
                $dataArray['row_id'] = uniqid('').bin2hex(random_bytes(8));
                DB::connection('mysql')->table('sc_simcard_offer_reseller')->insert($dataArray);
            }
            else
            {
                DB::connection('mysql')->table('sc_simcard_offer_reseller')->where(array('sc_simcard_offer_id'=>$promo_id, "store_id"=>$profile_details['store_vendor_id']))->update($dataArray);
            }
        }
        else
        {
            DB::connection('mysql')->table("sc_simcard_offer")
                ->where(array(
                    'id'=>$promo_id
                ))->update(array(
                    'title' => $data["title"],
                    'product_id' => $data["product_id"],
                    'bonus' => $data["reseller_bonus"],
                    'reseller_price' => $data["reseller_price"],
                    'reseller_offer' => $data["reseller_offer"],
                    'description' =>  $data["description"]
                ));
        }

        if(!empty($request->file)){
            $fileName = "simcard_offer_".($profile_details['user_type'] == "store"?"reseller_":"").time().'.'.$request->file->extension();
            //$request->file->move(storage_path('app/public/store_logo'), $fileName);
            $request->file->move(base_path('public/assets/simcard_offer'), $fileName);
            //Storage::disk('local')->put('public/file.txt', 'Contents');

            if($profile_details['user_type'] == "store") {
                DB::connection('mysql')->table("sc_simcard_offer_reseller")
                    ->where(array(
                        'sc_simcard_offer_id' => $promo_id,
                        'store_id' => $profile_details['store_vendor_id']
                    ))->update(array(
                        'upload_path'=>'assets/simcard_offer/'.$fileName
                    ));

                self::sendUploadRequestToDigOcenSpace((base_path('public/assets/simcard_offer')."/".$fileName), ("assets/simcard_offer/".$fileName), "sc_simcard_offer_reseller", array("sc_simcard_offer_id", "store_id"), array($promo_id, $profile_details['store_vendor_id']));
            }
            else
            {
                DB::connection('mysql')->table("sc_simcard_offer")
                    ->where(array(
                        'id'=>$promo_id
                    ))->update(array(
                        'upload_path'=>'assets/simcard_offer/'.$fileName
                    ));

                self::sendUploadRequestToDigOcenSpace((base_path('public/assets/simcard_offer')."/".$fileName), ("assets/simcard_offer/".$fileName), "sc_simcard_offer", array("id"), array($promo_id));
            }
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function config_reseller_bonus(Request $request, $reseller_id)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        $data = json_decode($request->getContent(), true);

        $storeDetails = DB::connection('mysql')
            ->table('store')
            ->where("store_id", "=" ,$reseller_id)->first();

        if($storeDetails->parent_store_id == "by_admin")
        {
            if(!empty($data['offerDetails']))
            {
                foreach($data['offerDetails'] as $promo_id => $reseller_bonus)
                {
                    // Gather Promo Data
                    $sc_simcard_offer = DB::connection('mysql')
                        ->table('sc_simcard_offer')
                        ->where('id', $promo_id)
                        ->first();
                    $reseller_offer = json_decode($sc_simcard_offer->reseller_offer);
                    $new_reseller_offer = array();
                    $ri = false;
                    foreach($reseller_offer as $r)
                    {
                        $ii = explode("|", $r);
                        if($ii[0] == $reseller_id)
                        {
                            $ri = true;
                            $new_reseller_offer[] = $reseller_id."|".$reseller_bonus;
                        }
                        else
                        {
                            $new_reseller_offer[] = $r;
                        }
                    }
                    if(!$ri) $new_reseller_offer[] = $reseller_id."|".$reseller_bonus;
                    DB::connection('mysql')
                        ->table("sc_simcard_offer")
                        ->where(array('id'=>$promo_id))
                        ->update(array('reseller_offer' => json_encode($new_reseller_offer)));
                }
            }
        }
        else
        {
            if(!empty($data['offerDetails']))
            {
                foreach($data['offerDetails'] as $promo_id => $reseller_bonus)
                {
                    // Gather Promo Data
                    $sc_simcard_offer = DB::connection('mysql')
                        ->table('sc_simcard_offer_reseller')
                        ->where(array('sc_simcard_offer_id'=>$promo_id,
                            "store_id"=>$storeDetails->parent_store_id))
                        ->first();

                    if(empty($sc_simcard_offer))
                    {
                        // No Promotion exists, Lets check if main table have promotion.
                        $sc_simcard_offer = DB::connection('mysql')
                            ->table('sc_simcard_offer')
                            ->where('id', $promo_id)
                            ->first();

                        $dataArray = array(
                            'sc_simcard_offer_id' => $promo_id,
                            'store_id' => $storeDetails->parent_store_id,
                            'title' => $sc_simcard_offer->title,
                            'bonus' => $sc_simcard_offer->bonus,
                            'reseller_price' => $sc_simcard_offer->reseller_price,
                            'reseller_offer' => $sc_simcard_offer->reseller_offer,
                            'description' =>  $sc_simcard_offer->description
                        );

                        $dataArray["created_by"] = $profile_details["user_id"];
                        $dataArray["created_at"] = date("Y-m-d H:i:s");
                        $dataArray["status"] = "enable";
                        $dataArray["upload_path"] = "";
                        $dataArray['row_id'] = uniqid('').bin2hex(random_bytes(8));

                        DB::connection('mysql')
                            ->table('sc_simcard_offer_reseller')
                            ->insert($dataArray);

                        $sc_simcard_offer = DB::connection('mysql')
                            ->table('sc_simcard_offer_reseller')
                            ->where(array('sc_simcard_offer_id'=>$promo_id,
                                "store_id"=>$storeDetails->parent_store_id))
                            ->first();
                    }

                    $reseller_offer = json_decode($sc_simcard_offer->reseller_offer);
                    $new_reseller_offer = array();
                    $ri = false;
                    foreach($reseller_offer as $r)
                    {
                        $ii = explode("|", $r);
                        if($ii[0] == $reseller_id)
                        {
                            $ri = true;
                            $new_reseller_offer[] = $reseller_id."|".$reseller_bonus;
                        }
                        else
                        {
                            $new_reseller_offer[] = $r;
                        }
                    }
                    if(!$ri) $new_reseller_offer[] = $reseller_id."|".$reseller_bonus;
                    DB::connection('mysql')
                        ->table("sc_simcard_offer_reseller")
                        ->where(array('sc_simcard_offer_id'=>$promo_id,
                            "store_id"=>$storeDetails->parent_store_id))
                        ->update(array('reseller_offer' => json_encode($new_reseller_offer)));
                }
            }
        }

        /*


            DB::connection('mysql')->table("sc_simcard_offer")
                ->where(array(
                    'id'=>$promo_id
                ))->update(array(
                    'reseller_offer' => $data["reseller_offer"],
                ));
         * */

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>($data['offerDetails']),
            'success'=>true,
        ), 200);
    }

    private function sendUploadRequestToDigOcenSpace($local, $remote, $table, $column=array(), $val=array())
    {
        DB::connection('mysql')->table('pending_dig_ocn_spc')->insert(array(
            'id'=>uniqid('').bin2hex(random_bytes(8)),
            'upload_absolute_path' => $local,
            'remote_file_name' => $remote,
            'table_name' => $table,
            'column_name' => json_encode($column),
            'table_primary_key_val' => json_encode($val)
        ));
    }
}
