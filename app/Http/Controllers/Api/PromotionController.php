<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    public function list(Request $request)
    {
        //$params = json_decode($request->getContent(), true);

        $params = $_POST;

        $query = DB::connection('mysql')->table('promotion')
            ->selectRaw('promotion.*, mfs.mfs_name')
            ->leftJoin('mfs', 'promotion.mfs_id', '=', 'mfs.mfs_id')
            ->orderBy("modified_at", "desc")->limit(50);
        //->where('status', 'enabled');

        /*foreach ($params as $key => $value)
        {
            if(!empty($value))
                $query->where($key, 'like', $value."%");
        }*/
        //$g = $query->toSql();
        $result = $query->get();

        $data = array();

        foreach($result as $key => $value)
        {
            $data[] = array(
                ($key + 1),
                (!empty($value->image_path)?'<img style="height:50px;" src="/storage/'.$value->image_path.'" class="img-fluid" alt="Responsive image">':''),
                $value->promotion_name,
                $value->mfs_name,
                number_format($value->b1, 2),
                $value->status,
                date("m/d/Y", strtotime($value->created_at)),
                $value->promotion_id."|".$value->status,
            );
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$data,
            'mfs_list'=>DB::connection('mysql')->table('mfs')->selectRaw('mfs_name, mfs_id')->get()
        ), 200);
    }

    public function create(Request $request)
    {
        $data = $_POST;//json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $validator = Validator::make($_POST, [//json_decode($request->getContent(), true), [
            'promotion_name' => 'required|string|min:2|max:200',
            'mfs' => 'required',
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

        $promotion_id = uniqid('').bin2hex(random_bytes(8));

        DB::connection('mysql')->table('promotion')->insert(array(
            'promotion_id'=>$promotion_id,
            'promotion_name' => $data['promotion_name'],
            'mfs_id'=>$data['mfs'],
            'status' => 'enabled',
            'created_by' => $data['user_id'],
            'created_at' => date("Y-m-d H:i:s"),
            'modified_at' => date("Y-m-d H:i:s")
        ));

        if(!empty($request->file)){
            $fileName = "promotion_logo_".time().'.'.$request->file->extension();
            $request->file->move(storage_path('app/public/promotion_logo'), $fileName);

            DB::connection('mysql')->table("promotion")
                ->where(array(
                    'promotion_id'=>$promotion_id,
                ))->update(array(
                    'image_path'=>'promotion_logo/'.$fileName
                ));
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function info(Request $request, $promotion_id)
    {
        $promotion_dta = DB::connection('mysql')->table('promotion')->selectRaw("*")->where('promotion_id', $promotion_id)->first();

        if(!$promotion_dta)
        {
            $errorMessages = array('Promotion Not Exists');

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
            'data'=>$promotion_dta,
        ), 200);
    }

    public function update(Request $request, $promotion_id)
    {
        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $promotion_dta = DB::connection('mysql')->table('promotion')->select("promotion_id")->where('promotion_id', $promotion_id)->first();

        if(!$promotion_dta)
        {
            $errorMessages = array('Promotion Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $info = array();
        if(!empty($data['promotion_name']))
            $info['promotion_name'] = $data['promotion_name'];

        if(!empty($data['status']))
            $info['status'] = $data['status'];

        if(!empty($data['promotional_amount']))
            $info['b1'] = $data['promotional_amount'];

        DB::connection('mysql')->table("promotion")
            ->where(array(
                'promotion_id'=>$promotion_id
            ))->update($info);

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function remove(Request $request, $promotion_id)
    {
        $promotion_dta = DB::connection('mysql')->table('promotion')->where('promotion_id', $promotion_id)->first();

        if(!$promotion_dta)
        {
            $errorMessages = array('Promotion Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        DB::connection('mysql')->table("promotion")->where('promotion_id', $promotion_id)->delete();

        $user_dta = DB::connection('mysql')->table('aauth_users')->select("id")->where('store_promotion_id', $promotion_id)->first();

        DB::connection('mysql')->table("aauth_users")->where('store_promotion_id', $promotion_id)->delete();

        if($user_dta)
            DB::connection('mysql')->table("aauth_perm_to_user")->where('user_id', $user_dta->id)->delete();

        if(!empty($promotion_dta->image_path))
            Storage::disk('local')->delete('public/'.$promotion_dta->image_path);

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }
}
