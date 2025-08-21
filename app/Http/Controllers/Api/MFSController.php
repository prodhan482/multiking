<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class MFSController extends Controller
{
    public function list(Request $request)
    {
        //$params = json_decode($request->getContent(), true);

        $params = $_POST;

        $query = DB::connection('mysql')->table('mfs')->orderBy("modified_at", "desc")->limit(50);
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
                (!empty($value->image_path)?'<img style="height:50px;" src="/'.$value->image_path.'" class="img-fluid" alt="Responsive image">':''),
                $value->mfs_name,
                $value->default_commission." %",
                $value->default_charge." %",
                date("m/d/Y", strtotime($value->created_at)),
                $value->mfs_id."|".$value->status,
            );
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$data
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
            'mfs_name' => 'required|string|min:2|max:200',
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

        $mfs_id = uniqid('').bin2hex(random_bytes(8));

        DB::connection('mysql')->table('mfs')->insert(array(
            'mfs_id'=>$mfs_id,
            'mfs_name' => $data['mfs_name'],
            'default_commission' => $data['default_commission'],
            'default_charge' => $data['default_charge'],
            'status' => 'enabled',
            'mfs_type' => $data['mfs_type'],
            'created_by' => $data['user_id'],
            'created_at' => date("Y-m-d H:i:s"),
            'modified_at' => date("Y-m-d H:i:s")
        ));

        if(!empty($request->file)){
            $fileName = "mfs_logo_".time().'.'.$request->file->extension();
            $request->file->move(base_path('public/assets/mfs_logo'), $fileName);
            //Storage::disk('local')->put('public/file.txt', 'Contents');

            DB::connection('mysql')->table("mfs")
                ->where(array(
                    'mfs_id'=>$mfs_id
                ))->update(array(
                    'image_path'=>'assets/mfs_logo/'.$fileName
                ));
        }

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function info(Request $request, $mfs_id)
    {
        $mfs_dta = DB::connection('mysql')->table('mfs')->selectRaw("mfs_id, mfs_name, status, created_by, created_at, modified_at, default_commission, default_charge, mfs_type")->where('mfs_id', $mfs_id)->first();

        if(!$mfs_dta)
        {
            $errorMessages = array('Mfs Not Exists');

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
            'data'=>$mfs_dta,
        ), 200);
    }

    public function update(Request $request, $mfs_id)
    {
        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $mfs_dta = DB::connection('mysql')->table('mfs')->select("mfs_id")->where('mfs_id', $mfs_id)->first();

        if(!$mfs_dta)
        {
            $errorMessages = array('Mfs Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $info = array();
        foreach($data as $key => $value){
            if(!empty($value))
                $info[$key] = $value;
        }

        DB::connection('mysql')->table("mfs")
            ->where(array(
                'mfs_id'=>$mfs_id
            ))->update($info);

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }


    public function package_list(Request $request)
    {
        //$params = json_decode($request->getContent(), true);

        $params = $_POST;

        $query = DB::connection('mysql')->table('mfs_package')
            ->selectRaw('mfs_package.*, mfs.mfs_name')
            //->orderBy("created_at", "desc")
            ->leftJoin('mfs', 'mfs.mfs_id', '=', 'mfs_package.mfs_id')
            ->where('enabled', '1');

        /*foreach ($params as $key => $value)
        {
            if(!empty($value))
                $query->where($key, 'like', $value."%");
        }*/
        //$g = $query->toSql();

        if(!empty($params['name']))
            $query->where('mfs_package.package_name', 'like', "%".$params['name']."%");

        if(!empty($params['mfs_id']))
            $query->where('mfs_package.mfs_id', '=', $params['mfs_id']);

        $query->orderByRaw('mfs_package.amount ASC');

        $resultCount = $query->get();

        //$query->offset($params['start']);
        //$query->limit($params['length']);
        $result = $query->get();

        $data = array();

        foreach($result as $key => $value)
        {
            $data[] = array(
                ($key + 1),
                $value->package_name,
                $value->mfs_name,

                (!empty(intval($value->start_slab))?(number_format($value->start_slab, 2)):"").(!empty(intval($value->end_slab))?(" - ".number_format($value->end_slab, 2)):""),
                number_format($value->amount, 2),
                number_format($value->discount, 2)."%",
                number_format($value->charge, 2)."%",
                $value->note,

                $value->row_id,
            );
        }

        $query2 = DB::connection('mysql')->table('mfs')->selectRaw('mfs.mfs_name as text, mfs.mfs_id as id')->orderBy("modified_at", "desc");

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'data'=>$data,
            'draw'=>$params['draw'],
            'mfs'=>$query2->get(),
            //'sql'=>$query->toSql(),
            'recordsFiltered'=>$resultCount->count(),
            'recordsTotal'=>$resultCount->count()
        ), 200);
    }

    public function create_package(Request $request)
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
            'package_name' => 'required|string|min:2|max:250',
            'mfs_id' => 'required',
            'note' => 'max:250',
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

        $row_id = uniqid('').bin2hex(random_bytes(8));

        $profile_details = Redis::get('user:token:'.str_replace("Bearer ","",$token));
        $profile_details = json_decode($profile_details, true);

        DB::connection('mysql')->table('mfs_package')->insert(array(
            'row_id'=>$row_id,
            'store_id' => '0000',
            'created_by' => $profile_details['user_id'],
            'created_at' => date("Y-m-d H:i:s"),
            'mfs_id' => $data['mfs_id'],
            'discount' => (empty($data['discount'])?"0":$data['discount']),
            'charge' => (empty($data['charge'])?"0":$data['charge']),
            'start_slab' => $data['start_slab'],
            'end_slab' => $data['end_slab'],
            'package_name' => $data['package_name'],
            'amount' => $data['amount'],
            'note' => $data['note']
        ));

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }

    public function package_info(Request $request, $mfs_package_id)
    {
        $mfs_dta = DB::connection('mysql')->table('mfs_package')->where('row_id', $mfs_package_id)->first();

        if(!$mfs_dta)
        {
            $errorMessages = array('Mfs Not Exists');

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
            'data'=>$mfs_dta,
        ), 200);
    }

    public function update_package(Request $request, $mfs_package_id)
    {
        $data = json_decode($request->getContent(), true);
        $errorMessages = array();

        if(empty($data))
            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
            ), 400);

        $mfs_dta = DB::connection('mysql')->table('mfs_package')->where('row_id', $mfs_package_id)->first();

        if(!$mfs_dta)
        {
            $errorMessages = array('Mfs Package Not Exists');

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'success'=>false,
                'message'=>$errorMessages
            ), 406);
        }

        $info = array();
        foreach($data as $key => $value){
            if(isset($value))
                $info[$key] = $value;
        }

        DB::connection('mysql')->table("mfs_package")
            ->where(array(
                'row_id'=>$mfs_package_id
            ))->update($info);

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
        ), 200);
    }
}
