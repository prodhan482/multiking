<?php

namespace App\Http\Controllers\Api\simcard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);
        $params = $_POST;
        $storeIDlistForXLS = [];

        $whereQuery = array();

        $whereQuery[] = " (sc_sim_card.sold_at BETWEEN '".date("Y-m-d 00:00:00", strtotime($params["start_date"]))."' AND '".date("Y-m-d 23:59:59", strtotime($params["end_date"]))."')";

        if(in_array("StoreController::list", $profile_details['permission_lists'])){
            if(empty($params["store_id"]))
            {
                $storeListQ = DB::connection('mysql')->table('store')->selectRaw("store_id as id")->where(array(
                    'enable_simcard_access'=>"1",
                    'parent_store_id'=>$profile_details['store_vendor_id']
                ));
                $storeList = $storeListQ->get();
                $storeListQQ = array($profile_details['store_vendor_id']);
                foreach($storeList as $row)
                {
                    $storeListQQ[] = $row->id;
                    $storeIDlistForXLS[] = $row->id;
                }

                $whereQuery[] = " sc_sim_card.store_id IN ('".join("', '", $storeListQQ)."') ";
            }
            else
            {
                $whereQuery[] = ("sc_sim_card.store_id = '".($params["store_id"])."'");
                $storeIDlistForXLS[] = $params["store_id"];
            }
        }
        else
        {
            $whereQuery[] = "sc_sim_card.store_id = '".$profile_details['store_vendor_id']."'";
            $storeIDlistForXLS[] = $profile_details['store_vendor_id'];
        }

        $whereQuery[] = "sc_sim_card.sales_status = 'sold'";

        $whereQuery[] = (!empty($params["product_id"])?("sc_sim_card.product_id = '".($params["product_id"])."'"):"");

        $whereQuery = array_values(array_diff($whereQuery,array("")));

        if(!empty($params['generateXLS']))
        {
            if(empty($params['store_id'])){
                return response()->json(array(
                    'right_now'=>date("Y-m-d H:i:s"),
                    'timestamp'=>time(),
                    'success'=>true,
                    'message'=>array('Please Select a Reseller.')
                ), 406);
            }

            $info = self::generateSalesXls($params, $whereQuery, $storeIDlistForXLS);

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'xls_file_path'=>$info['url'],
                'success'=>true,
            ), 200);
        }

        DB::select(DB::raw("set session sql_mode=''"));

        $sql = "SELECT SUM(sc_sim_card.cost) AS cost, SUM(sc_sim_card.sales_price) AS sales_price, SUM( sc_sim_card.sales_price - sc_sim_card.cost ) AS profit_loss, inv_products.`name` AS product_name, inv_products.`id` AS product_id ".((in_array("StoreController::list", $profile_details['permission_lists']))?", store.`store_name` AS store_name, store.`store_id` AS the_store_id":"").", SUM( IF ( sc_sim_card. STATUS = 'approved', 1, 0 )) AS approved, SUM( IF ( sc_sim_card. STATUS = 'pending', 1, 0 )) AS pending, SUM( IF ( sc_sim_card. STATUS = 'rejected', 1, 0 )) AS rejected, SUM( IF ( sc_sim_card.sales_status = 'sold', 1, 0 )) AS sold FROM sc_sim_card LEFT JOIN inv_products ON sc_sim_card.product_id = inv_products.id ".((in_array("StoreController::list", $profile_details['permission_lists']))?" LEFT JOIN store ON store.store_id = sc_sim_card.store_id ":"").(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")." GROUP BY sc_sim_card.product_id, sc_sim_card.store_id";

        $query = DB::select(DB::raw($sql));

        $queryCount = DB::select(DB::raw("SELECT count(sc_sim_card.id) AS total, SUM(sc_sim_card.cost) AS cost, SUM(sc_sim_card.sales_price) AS revenue, SUM(sc_sim_card.sales_price - sc_sim_card.cost) AS profit FROM sc_sim_card ".(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")." LIMIT 1"));

        $data = array();
        $pos = ($params['start'] + 1);

        $pageCost= 0;
        $pageRevenue= 0;
        $pageProfit= 0;
        $totalActivated= 0;
        $totalRejected= 0;
        $totalSold= 0;

        foreach($query as $row)
        {
            $pageCost= $pageCost + $row->cost;
            $pageRevenue= $pageRevenue + $row->sales_price;
            $pageProfit= $pageProfit + $row->profit_loss;
            $totalActivated= $totalActivated + $row->approved;
            $totalRejected= $totalRejected + $row->rejected;
            $totalSold= $totalSold + $row->sold;

            $ii = array(
                $pos++,
                $row->product_name
            );

            if(in_array("StoreController::list", $profile_details['permission_lists'])) $ii[] = $row->store_name;

            //$ii[] = $row->store_name;
            $ii[] = $row->approved;
            $ii[] = $row->rejected;
            $ii[] = $row->sold;
            $ii[] = number_format($row->cost, 2);
            $ii[] = number_format($row->sales_price, 2);
            $ii[] = number_format($row->profit_loss, 2);


            $data[] = $ii;
        }

        if(!empty($data))
        {
            $data[] = array(
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
            );

            $data[] = array(
                "Total:",
                "",
                "",
                $totalActivated,
                $totalRejected,
                $totalSold,
                number_format($pageCost, 2),
                number_format($pageRevenue, 2),
                number_format($pageProfit, 2),
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

    private function generateSalesXls($params, $whereQuery, $storeIds)
    {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load(base_path("public/assets/xls_format/f7.xlsx"));

        $store_Details = DB::connection('mysql')->table('store')
            ->selectRaw("store.*, parent_store.store_name as parent_store_name")
            ->leftJoin('store as parent_store', 'parent_store.store_id', '=', 'store.parent_store_id')
            ->where('store.store_id', $params['store_id'])->first();

        $spreadsheet->getActiveSheet()->getStyle('A1:C1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF011F60');

        $spreadsheet->getActiveSheet()->getStyle('A1:C2')
            ->getFont()->getColor()->setARGB('FFFFFFFF');

        $spreadsheet->getActiveSheet()->getStyle('A2:C2')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF0A00');

        $spreadsheet->getActiveSheet()->getStyle('D4:G5')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF602726');

        $spreadsheet->getActiveSheet()->getStyle('D4:G5')
            ->getFont()->getColor()->setARGB('FFFFFFFF');

        $spreadsheet->getActiveSheet()->getStyle('A7:J18')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFA6A6A6');

        $spreadsheet->getActiveSheet()->getStyle('A17:J17')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFA6A6A6');


        $spreadsheet->getActiveSheet()->getCell('C7')->setValue(''.time());
        $spreadsheet->getActiveSheet()->getStyle('C7')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');

        $spreadsheet->getActiveSheet()->getCell('J7')->setValue(date("F d, Y"));
        $spreadsheet->getActiveSheet()->getStyle('J7:K7')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');

        $spreadsheet->getActiveSheet()->getCell('C9')->setValue($store_Details->store_code);
        $spreadsheet->getActiveSheet()->getStyle('C9')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');

        $spreadsheet->getActiveSheet()->getCell('C11')->setValue($store_Details->store_name);
        $spreadsheet->getActiveSheet()->getStyle('C11:H11')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');

        $spreadsheet->getActiveSheet()->getCell('C12')->setValue("");
        $spreadsheet->getActiveSheet()->getCell('C13')->setValue($store_Details->store_address);
        $spreadsheet->getActiveSheet()->getStyle('C13:G13')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');

        $spreadsheet->getActiveSheet()->getCell('C16')->setValue(date("F d, Y", strtotime($params['start_date']))." to ".date("F d, Y", strtotime($params['end_date'])));

        $spreadsheet->getActiveSheet()->getStyle('C16:E16')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');


        $spreadsheet->getActiveSheet()->getCell('K13')->setValue("".$store_Details->parent_store_name);
        $spreadsheet->getActiveSheet()->getStyle('K13:K13')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');

        /*$spreadsheet->getActiveSheet()->getCell('B57')->setValue(((!empty($store_Details->last_payment_received))?("Paid (".date("d/m/Y", strtotime($store_Details->last_payment_received)).")"):""));
        if(!empty($store_Details->last_payment_received_amount)) $spreadsheet->getActiveSheet()->getCell('D57')->setValue(((!empty($store_Details->last_payment_received_amount))?($store_Details->last_payment_received_amount):""));*/

        foreach (range(20,39) as $pos)
        {
            foreach (range('A','K') as $char)
            {
                $spreadsheet->getActiveSheet()->getCell($char.''.$pos)->setValue("");
            }
        }

        DB::select(DB::raw("set session sql_mode=''"));

        $whereQuery[] = " sc_sim_card.status = 'approved' ";


        //$sql = "SELECT sc_sim_card.*,  inv_products.`name` AS product_name, sc_simcard_offer.title as sc_simcard_offer_title, sc_sim_card_meta_data.meta_value as other_information FROM sc_sim_card LEFT JOIN inv_products ON sc_sim_card.product_id = inv_products.id LEFT JOIN sc_sim_card_meta_data ON sc_sim_card_meta_data.meta_key = 'other_information' AND sc_sim_card_meta_data.sim_card_id = sc_sim_card.id LEFT JOIN sc_simcard_offer ON sc_simcard_offer.id = sc_sim_card.product_offer_id ".(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")." ";

        $sql = "SELECT sc_sim_card.*,  inv_products.`name` AS product_name, sc_simcard_offer.title as sc_simcard_offer_title FROM sc_sim_card LEFT JOIN inv_products ON sc_sim_card.product_id = inv_products.id LEFT JOIN sc_simcard_offer ON sc_simcard_offer.id = sc_sim_card.product_offer_id ".(count($whereQuery)>0?'WHERE '.join(' AND ', $whereQuery):"")." ";

        $query = DB::select(DB::raw($sql));

        $query_adjusted_amount = "SELECT IFNULL(SUM(adjustment_history.adjusted_amount), 0) as adjusted_amount FROM adjustment_history WHERE adjustment_history.store_vendor_id IN ('".implode("', '", $storeIds)."') AND adjustment_history.adjustment_type = 'simcard' AND adjustment_history.created_on BETWEEN '".$_POST["start_date"]." 00:00:00' AND '".$_POST["end_date"]." 23:59:59'";

        $adjusted_amount = DB::select(DB::raw($query_adjusted_amount));


        if(count($query) > 22) $spreadsheet->getActiveSheet()->insertNewRowBefore(22, (count($query) - 22) + 5);

        $rowPos = 20;
        $sl = 1;
        $summation = 0;
        foreach($query as $row)
        {
            $summation += floatval($row->reseller_price);

            //Company	ICCID No	Mob No	Act.Dt	Offerta	Ric	Ex Ric	Total	Remarks
            $spreadsheet->getActiveSheet()->getCell('A'.$rowPos)->setValue(($sl++));
            $spreadsheet->getActiveSheet()->getCell('B'.$rowPos)->setValue($row->product_name);
            //$spreadsheet->getActiveSheet()->getCell('C'.$rowPos)->setValue("`".$row->sim_card_iccid);

            $spreadsheet->getActiveSheet()
                ->getCell('C'.$rowPos)
                ->setValueExplicit(
                    $row->sim_card_iccid,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2
                );

            $spreadsheet->getActiveSheet()
                ->getCell('D'.$rowPos)
                ->setValueExplicit(
                    $row->sim_card_mobile_number,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2
                );

            //$spreadsheet->getActiveSheet()->getCell('D'.$rowPos)->setValue("`".$row->sim_card_mobile_number);
            $spreadsheet->getActiveSheet()->getCell('E'.$rowPos)->setValue(date('Y/m/d', strtotime($row->activated_at)));
            $spreadsheet->getActiveSheet()->getCell('F'.$rowPos)->setValue($row->sc_simcard_offer_title);

            $spreadsheet->getActiveSheet()->getCell('G'.$rowPos)->setValue($row->mnp_operator_name);


            $spreadsheet->getActiveSheet()->getCell('H'.$rowPos)->setValue("".$row->reseller_price);
            $spreadsheet->getActiveSheet()->getCell('I'.$rowPos)->setValue("".$row->reseller_price);
            //$spreadsheet->getActiveSheet()->getCell('J'.$rowPos)->setValue("".$row->cost);
            $spreadsheet->getActiveSheet()->getCell('K'.$rowPos)->setValue("".$summation);

            $spreadsheet->getActiveSheet()->getStyle('C'.$rowPos)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $spreadsheet->getActiveSheet()->getStyle('D'.$rowPos)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $rowPos = $rowPos + 1;
            //if($rowPos > 51) $spreadsheet->getActiveSheet()->insertNewRowBefore(53);
            //if($rowPos > 21) $spreadsheet->getActiveSheet()->insertNewRowBefore(23, 1);
        }

        $spreadsheet->getActiveSheet()->getCell('F'.$rowPos)->setValue("ADJUST BY CASH");
        $spreadsheet->getActiveSheet()->getCell('J'.$rowPos)->setValue("".(!empty($adjusted_amount)?$adjusted_amount[0]->adjusted_amount:0));
        $spreadsheet->getActiveSheet()->getCell('K'.$rowPos)->setValue("".($summation - (!empty($adjusted_amount)?$adjusted_amount[0]->adjusted_amount:0)));

        $spreadsheet->getActiveSheet()->getStyle('A2:C2')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF0A00');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileP = "/assets/temp_xls_file/".$store_Details->store_name."-".time().".xlsx";
        $writer->save(base_path("public".$fileP));

        return array("url"=>$fileP);
    }

    public function recharge(Request $request)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);
        $params = $_POST;
        $tableData = array();

        $sc_simcard_offer = DB::connection('mysql')->table('sc_simcard_offer')->selectRaw("id, title")->where(array(
           'status'=>"enable",
            'product_id'=>(empty($params['product_id'])?"-1":$params['product_id'])
        ))->get();


        if(!empty($params))
        {
            $whereQuery = (!empty($params["store_id"])?("sc_sim_card.store_id = '".($params["store_id"])."' AND"):"");

            $simCardDetails = DB::select(DB::raw("SELECT `sc_sim_card`.*, store.`store_name` AS store_name FROM `sc_sim_card` LEFT JOIN store ON store.store_id = sc_sim_card.store_id WHERE ".$whereQuery." `sc_sim_card`.`product_id` = '".(empty($params['product_id'])?"-1":$params['product_id'])."' AND `sc_sim_card`.`sales_status` = 'sold'  AND sc_sim_card.status = 'approved' AND sc_sim_card.sold_at BETWEEN '".$params["start_date"]." 00:00:00' AND '".$params["end_date"]." 23:59:59' ORDER BY `sc_sim_card`.`sold_at`"));

            $checkSameDate = "";

            $mnpTotal = 0;
            $RicaRica = 0;
            $ResellerPrice = 0;
            $SalesPrice = 0;
            $poc = array();

            $n = 1;

            foreach($simCardDetails as $simCardDetail)
            {
                $rowData = array(
                    $n++
                );

                $SalesPrice = $SalesPrice + floatval($simCardDetail->sales_price);
                $mnpExists = "";
                if(!empty($simCardDetail->mnp_operator_name) && $simCardDetail->mnp_operator_name != "None")
                {
                    $mnpExists = "1";
                    $mnpTotal = $mnpTotal + 1;
                }
                if($checkSameDate != date("d-m-Y", strtotime($simCardDetail->sold_at)))
                {
                    $rowData[] = date("d-m-Y", strtotime($simCardDetail->sold_at));
                    $checkSameDate = date("d-m-Y", strtotime($simCardDetail->sold_at));
                }else{
                    $rowData[] = "-&nbsp;";
                }
                if(in_array("StoreController::list", $profile_details['permission_lists'])){
                    $rowData[] = $simCardDetail->store_name;
                }

                $rowData[] = $simCardDetail->sim_card_mobile_number;
                $rowData[] = $mnpExists;

                //$poc = array();
                foreach($sc_simcard_offer as $promotionDetail)
                {
                    if($simCardDetail->product_offer_id == $promotionDetail->id)
                    {
                        if(!empty($poc["_".$simCardDetail->product_offer_id]))  $poc["_".$simCardDetail->product_offer_id] = $poc["_".$simCardDetail->product_offer_id] + 1;
                        if(empty($poc["_".$simCardDetail->product_offer_id])) $poc["_".$simCardDetail->product_offer_id] = 1;
                        $rowData[] = 1;
                    }
                    else
                    {
                        $rowData[] = "&nbsp;";
                    }
                }

                if(!empty($simCardDetail->ricarica))
                {
                    $RicaRica = $RicaRica + floatval(explode("_", $simCardDetail->ricarica)[0]);
                    $rowData[] = explode("_", $simCardDetail->ricarica)[0];
                }
                else
                {
                    $rowData[] = "&nbsp;";
                }

                if(!empty($simCardDetail->reseller_price))
                {
                    $ResellerPrice = $ResellerPrice + floatval(explode("_", $simCardDetail->reseller_price)[0]);
                    $rowData[] = $ResellerPrice;
                }
                else
                {
                    $rowData[] = "&nbsp;";
                }

                $rowData[] = number_format($simCardDetail->sales_price, 2);

                $tableData[] = $rowData;
            }
        }

        $rowData = array(
            "",
            "",
        );
        if(in_array("StoreController::list", $profile_details['permission_lists'])){
            $rowData[] = "";
        }

        $rowData[] = "";
        $rowData[] = $mnpTotal;

        foreach($sc_simcard_offer as $promotionDetail)
        {
            $rowData[] = (!empty($poc["_".$promotionDetail->id])?$poc["_".$promotionDetail->id]:"0");
        }


        $rowData[] = "";
        $rowData[] = number_format($ResellerPrice, 2);
        $rowData[] = number_format($SalesPrice, 2);

        $footerData = array($rowData);

        //if(!empty($tableData)) $footerData = array($rowData);

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'simcard_offer'=>$sc_simcard_offer,
            'tableData'=>$tableData,
            'footerData'=>$footerData,
            'success'=>true,
        ), 200);
    }

    public function adjustment(Request $request)
    {
        $token = $request->header('Authorization');
        $profile_details = Redis::get('user:token:' . str_replace("Bearer ", "", $token));
        $profile_details = json_decode($profile_details, true);
        $params = $_POST;
        $tableData = array();

        if(!in_array("StoreController::list", $profile_details['permission_lists'])){
            $store_query = "adjustment_history.store_vendor_id = '".$profile_details['store_vendor_id']."' AND ";
        }
        else
        {
            $store_query = (!empty($params["store_id"])?("adjustment_history.store_vendor_id = '".($params["store_id"])."' AND "):"");
        }

        if(!empty($params['generateXLS']) && $params['generateXLS'] == "true")
        {
            if(empty($params['store_id'])){
                return response()->json(array(
                    'right_now'=>date("Y-m-d H:i:s"),
                    'timestamp'=>time(),
                    'success'=>true,
                    'message'=>array('Please Select a Reseller.')
                ), 406);
            }

            $info = self::generateAdjustmentXls($params, $store_query);

            return response()->json(array(
                'right_now'=>date("Y-m-d H:i:s"),
                'timestamp'=>time(),
                'xls_file_path'=>$info['url'],
                'success'=>true,
            ), 200);
        }

        $query = "SELECT adjustment_history.*, store.`store_name` AS store_name FROM adjustment_history LEFT JOIN store ON store.store_id = adjustment_history.store_vendor_id WHERE ".$store_query." adjustment_history.adjustment_type = 'simcard' AND adjustment_history.created_on BETWEEN '".$params["start_date"]." 00:00:00' AND '".$params["end_date"]." 23:59:59'";

        $infos = DB::select(DB::raw($query));

        $nr = 1;

        $due = 0;
        $adjustment = 0;
        $totalBalance = 0;

        foreach($infos as $row) {
            $rowData = array(
                $nr++,
                date("M jS, Y", strtotime($row->created_on)),
            );

            if(in_array("StoreController::list", $profile_details['permission_lists'])){
                $rowData[] = $row->store_name;
            }

            $rowData[] = $row->note;

            if(empty($row->adjusted_amount))
            {
                // Due Added.
                $due = $due + floatval($row->received_amount);
                $totalBalance = $totalBalance + floatval($row->received_amount);
                $rowData[] = number_format($row->received_amount, 2);
                $rowData[] = "";
            }
            else
            {
                // Due Adjusted
                $adjustment = $adjustment + floatval($row->adjusted_amount);
                $totalBalance = $totalBalance - floatval($row->adjusted_amount);
                $rowData[] = "";
                $rowData[] = number_format($row->adjusted_amount, 2);
            }

            $rowData[] = ($totalBalance > 0?number_format($totalBalance, 2):"(".number_format(($totalBalance * (-1)), 2).")");

            $tableData[] = $rowData;

        }

        $rowData = array(
            "",
            "",
        );
        if(in_array("StoreController::list", $profile_details['permission_lists'])){
            $rowData[] = "";
        }

        $rowData[] = "Total: ";

        $rowData[] = number_format($due, 2);
        $rowData[] = number_format($adjustment, 2);
        $rowData[] = "";

        $footerData = array(
            $rowData
        );

        return response()->json(array(
            'right_now'=>date("Y-m-d H:i:s"),
            'timestamp'=>time(),
            'success'=>true,
            'tableData'=>$tableData,
            'footerData'=>$footerData
        ), 200);
    }

    private function generateAdjustmentXls($params, $store_query)
    {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load(base_path("public/assets/xls_format/f7.xlsx"));

        $store_Details = DB::connection('mysql')->table('store')
            ->selectRaw("store.*, parent_store.store_name as parent_store_name")
            ->leftJoin('store as parent_store', 'parent_store.store_id', '=', 'store.parent_store_id')
            ->where('store.store_id', $params['store_id'])->first();

        $spreadsheet->getActiveSheet()->getStyle('A1:C1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF011F60');

        $spreadsheet->getActiveSheet()->getStyle('A1:C2')
            ->getFont()->getColor()->setARGB('FFFFFFFF');

        $spreadsheet->getActiveSheet()->getStyle('A2:C2')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF0A00');

        $spreadsheet->getActiveSheet()->getStyle('D4:G5')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF602726');

        $spreadsheet->getActiveSheet()->getStyle('D4:G5')
            ->getFont()->getColor()->setARGB('FFFFFFFF');

        $spreadsheet->getActiveSheet()->getStyle('A7:J18')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFA6A6A6');

        $spreadsheet->getActiveSheet()->getStyle('A17:J17')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFA6A6A6');


        $spreadsheet->getActiveSheet()->getCell('C7')->setValue(''.time());
        $spreadsheet->getActiveSheet()->getStyle('C7')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');

        $spreadsheet->getActiveSheet()->getCell('J7')->setValue(date("F d, Y"));
        $spreadsheet->getActiveSheet()->getStyle('J7:K7')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');

        $spreadsheet->getActiveSheet()->getCell('C9')->setValue($store_Details->store_code);
        $spreadsheet->getActiveSheet()->getStyle('C9')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');

        $spreadsheet->getActiveSheet()->getCell('C11')->setValue($store_Details->store_name);
        $spreadsheet->getActiveSheet()->getStyle('C11:H11')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');

        $spreadsheet->getActiveSheet()->getCell('C12')->setValue("");
        $spreadsheet->getActiveSheet()->getCell('C13')->setValue($store_Details->store_address);
        $spreadsheet->getActiveSheet()->getStyle('C13:G13')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');

        $spreadsheet->getActiveSheet()->getCell('C16')->setValue(date("F d, Y", strtotime($params['start_date']))." to ".date("F d, Y", strtotime($params['end_date'])));

        $spreadsheet->getActiveSheet()->getStyle('C16:E16')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');


        $spreadsheet->getActiveSheet()->getCell('K13')->setValue("".$store_Details->parent_store_name);
        $spreadsheet->getActiveSheet()->getStyle('K13:K13')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFFFF');

        /*$spreadsheet->getActiveSheet()->getCell('B57')->setValue(((!empty($store_Details->last_payment_received))?("Paid (".date("d/m/Y", strtotime($store_Details->last_payment_received)).")"):""));
        if(!empty($store_Details->last_payment_received_amount)) $spreadsheet->getActiveSheet()->getCell('D57')->setValue(((!empty($store_Details->last_payment_received_amount))?($store_Details->last_payment_received_amount):""));*/

        foreach (range(20,39) as $pos)
        {
            foreach (range('A','K') as $char)
            {
                $spreadsheet->getActiveSheet()->getCell($char.''.$pos)->setValue("");
            }
        }

        DB::select(DB::raw("set session sql_mode=''"));

        $sql = "SELECT adjustment_history.*, inv_products.`name` AS product_name, sc_simcard_offer.title as sc_simcard_offer_title, sc_sim_card.sim_card_iccid, sc_sim_card.sim_card_mobile_number ,sc_sim_card.mnp_operator_name ,sc_sim_card.activated_at FROM adjustment_history LEFT JOIN sc_sim_card ON sc_sim_card.id = adjustment_history.adjustment_type_id LEFT JOIN inv_products ON sc_sim_card.product_id = inv_products.id LEFT JOIN sc_simcard_offer ON sc_simcard_offer.id = sc_sim_card.product_offer_id WHERE ".$store_query." adjustment_history.adjustment_type = 'simcard' AND adjustment_history.created_on BETWEEN '".$params["start_date"]." 00:00:00' AND '".$params["end_date"]." 23:59:59'";

        $query = DB::select(DB::raw($sql));

        $iccids = array();
        $iccidsByRowID = [];
        foreach($query as $row)
        {
            $pattern = '/ICCID: (\d{15,})/';
            if (preg_match($pattern, $row->note, $matches)) {
                $iccids[] = $matches[1];
                if(empty($iccidsByRowID[$matches[1]])) $iccidsByRowID[$matches[1]] = [];
                $iccidsByRowID[$matches[1]][] = $row->row_id;
            }
        }

        $sql2 = "SELECT sc_sim_card.sim_card_iccid, sc_sim_card.mnp_operator_name, sc_sim_card.sim_card_mobile_number,
         inv_products.`name` AS product_name,
         sc_simcard_offer.title as sc_simcard_offer_title
         FROM sc_sim_card
         LEFT JOIN inv_products ON sc_sim_card.product_id = inv_products.id
         LEFT JOIN sc_simcard_offer ON sc_simcard_offer.id = sc_sim_card.product_offer_id
         WHERE sc_sim_card.sim_card_iccid IN ('".implode("', '", $iccids)."')";
        $query2 = DB::select(DB::raw($sql2));

        $infoAgainstIccid = array();
        foreach($query2 as $row){
            foreach($iccidsByRowID[$row->sim_card_iccid] as $cc){
                $infoAgainstIccid[$cc] = array(
                    'sim_card_iccid'=>$row->sim_card_iccid,
                    'sim_card_mobile_number'=>$row->sim_card_mobile_number,
                    'product_name'=>$row->product_name,
                    'sc_simcard_offer_title'=>$row->sc_simcard_offer_title,
                    'mnp_operator_name'=>$row->mnp_operator_name
                );
            }
        }


        if(count($query) > 22) $spreadsheet->getActiveSheet()->insertNewRowBefore(22, (count($query) - 22) + 3);

        $rowPos = 20;
        $sl = 1;
        $summation = 0;
        foreach($query as $k => &$row)
        {
            if(!empty($infoAgainstIccid[$row->row_id]))
            {
                if(!empty($infoAgainstIccid[$row->row_id]['sim_card_iccid']))
                {
                    $row->{"sim_card_iccid"} = $infoAgainstIccid[$row->row_id]['sim_card_iccid'];
                }

                if(!empty($infoAgainstIccid[$row->row_id]['product_name']))
                {
                    $row->{"product_name"} = $infoAgainstIccid[$row->row_id]['product_name'];
                }

                if(!empty($infoAgainstIccid[$row->row_id]['sc_simcard_offer_title']))
                {
                    $row->{"sc_simcard_offer_title"} = $infoAgainstIccid[$row->row_id]['sc_simcard_offer_title'];
                }

                if(!empty($infoAgainstIccid[$row->row_id]['mnp_operator_name']))
                {
                    $row->{"mnp_operator_name"} = $infoAgainstIccid[$row->row_id]['mnp_operator_name'];
                }

                if(!empty($infoAgainstIccid[$row->row_id]['sim_card_mobile_number']))
                {
                    $row->{"sim_card_mobile_number"} = $infoAgainstIccid[$row->row_id]['sim_card_mobile_number'];
                }
            }


            $summation += floatval($row->received_amount) - floatval($row->adjusted_amount);

            //Company	ICCID No	Mob No	Act.Dt	Offerta	Ric	Ex Ric	Total	Remarks
            $spreadsheet->getActiveSheet()->getCell('A'.$rowPos)->setValue(($sl++));
            $spreadsheet->getActiveSheet()->getCell('B'.$rowPos)->setValue($row->product_name);
            //$spreadsheet->getActiveSheet()->getCell('C'.$rowPos)->setValue("`".$row->sim_card_iccid);

            $spreadsheet->getActiveSheet()
                ->getCell('C'.$rowPos)
                ->setValueExplicit(
                    $row->sim_card_iccid,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2
                );

            $spreadsheet->getActiveSheet()
                ->getCell('D'.$rowPos)
                ->setValueExplicit(
                    $row->sim_card_mobile_number,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2
                );

            //$spreadsheet->getActiveSheet()->getCell('D'.$rowPos)->setValue("`".$row->sim_card_mobile_number);

            if(!empty($row->activated_at))
            {
                $spreadsheet->getActiveSheet()->getCell('E'.$rowPos)->setValue(date('Y/m/d', strtotime($row->activated_at)));
            } else {
                $spreadsheet->getActiveSheet()->getCell('E'.$rowPos)->setValue(date('Y/m/d', strtotime($row->created_on)));
            }

            if(!empty($row->adjusted_amount))
            {
                $spreadsheet->getActiveSheet()->getCell('F'.$rowPos)->setValue($row->note);
            } else {
                $spreadsheet->getActiveSheet()->getCell('F'.$rowPos)->setValue($row->sc_simcard_offer_title);
            }

            if(!empty($row->mnp_operator_name) && $row->mnp_operator_name != "None")
            {
                $spreadsheet->getActiveSheet()->getCell('G'.$rowPos)->setValue("1");
            } else {
                $spreadsheet->getActiveSheet()->getCell('G'.$rowPos)->setValue("");
            }


            $spreadsheet->getActiveSheet()->getCell('H'.$rowPos)->setValue("".(!empty($row->received_amount)?$row->received_amount:0));
            $spreadsheet->getActiveSheet()->getCell('I'.$rowPos)->setValue("".(!empty($row->received_amount)?$row->received_amount:0));
            $spreadsheet->getActiveSheet()->getCell('J'.$rowPos)->setValue("".(!empty($row->adjusted_amount)?$row->adjusted_amount:0));
            $spreadsheet->getActiveSheet()->getCell('K'.$rowPos)->setValue("".$summation);

            $spreadsheet->getActiveSheet()->getStyle('C'.$rowPos)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $spreadsheet->getActiveSheet()->getStyle('D'.$rowPos)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $rowPos = $rowPos + 1;
            //if($rowPos > 51) $spreadsheet->getActiveSheet()->insertNewRowBefore(53);
            //if($rowPos > 21) $spreadsheet->getActiveSheet()->insertNewRowBefore(23, 1);
        }

        /*$spreadsheet->getActiveSheet()->getCell('F'.$rowPos)->setValue("ADJUST BY CASH");
        $spreadsheet->getActiveSheet()->getCell('J'.$rowPos)->setValue("".(!empty($adjusted_amount)?$adjusted_amount[0]->adjusted_amount:0));
        $spreadsheet->getActiveSheet()->getCell('K'.$rowPos)->setValue("".($summation - (!empty($adjusted_amount)?$adjusted_amount[0]->adjusted_amount:0)));

        $spreadsheet->getActiveSheet()->getStyle('A2:C2')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF0A00');*/

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileP = "/assets/temp_xls_file/".$store_Details->store_name."-".time().".xlsx";
        $writer->save(base_path("public".$fileP));

        return array("url"=>$fileP);
    }
}
