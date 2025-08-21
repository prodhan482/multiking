@include('inc.header')
@include('inc.menu')
<!-- BEGIN: Content-->

<?php

$result = DB::connection('mysql')->table('store')->selectRaw("notice_meta")->first();

$notice_meta = array(
    'hotline_number'=>'1234',
    'site_notice'=>'',
);

$notice_meta = json_decode(json_encode($notice_meta));

if(!empty($result) && !empty($result->notice_meta) && !empty(json_decode($result->notice_meta)))
{
    $notice_meta = json_decode($result->notice_meta);
}

?>
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="one-tab" data-toggle="tab" href="#one" role="tab" aria-controls="One" aria-selected="true"><h3>Transaction</h3></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="two-tab" data-toggle="tab" href="#two" role="tab" aria-controls="Two" aria-selected="false"><h3>Sim Card</h3>
                        <?php if(count($reports['pending_sim_activation']) > 0): ?>
                        <span class="badge badge-up badge-pill badge-danger" style="top: 0; right: 0; "><?php echo count($reports['pending_sim_activation']); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="one" role="tabpanel" aria-labelledby="one-tab">

                    <!-- Dashboard Analytics Start -->
                    <section id="dashboard-analytics">
                        <div class="row match-height">
                            <!-- Avg Sessions Chart Card starts -->
                            <div class="col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between pb-1">
                                        <h4 class="card-title">All Due Details</h4>
                                    </div>
                                    <div class="card-body" style="height: 300px; overflow-y: auto">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Reseller</th>
                                                <th>Code</th>
                                                <th>Due Euro</th>
                                                <th>Balance</th>
                                            </tr>
                                            </thead>

                                            <?php

                                            $query = DB::connection('mysql')->table('store')->where("status", "=", "enabled")->where("parent_store_id", 'by_admin')->orderBy("due_euro", "desc")->limit(200);
                                            $resultPPP = $query->get();

                                            ?>

                                            <tbody>
                                            <?php
                                            foreach($resultPPP as $position => $row)
                                            {
                                            ?>
                                            <tr>
                                                <td><?php echo $position+1; ?></td>
                                                <td><?php echo $row->store_name; ?></td>
                                                <td><?php echo $row->store_code; ?></td>
                                                <td style="font-weight:bold; color: <?php echo (floatval($row->due_euro) > 0?"red":"green"); ?>">€ <?php echo number_format($row->due_euro, 2); ?></td>
                                                <td><?php echo number_format($row->balance, 2); ?></td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Avg Sessions Chart Card ends -->

                            <!-- Support Tracker Chart Card starts -->
                            <div class="col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between pb-1">
                                        <h4 class="card-title">Daily Summery</h4>
                                    </div>
                                    <div class="card-body" style="height: 300px; overflow-y: auto">
                                        <table role="table" aria-busy="false" aria-colcount="5" class="table b-table">
                                            <thead role="rowgroup" class="">
                                            <tr role="row" class="">
                                                <th role="columnheader" scope="col" aria-colindex="1" class="">&nbsp;</th>
                                                <th role="columnheader" scope="col" aria-colindex="2" class="">Reseller</th>
                                                <th role="columnheader" scope="col" aria-colindex="2" class="">Amount</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $trans = DB::select(DB::raw("SELECT IFNULL(SUM(`recharge`.recharge_amount), 0) as recharge_amount, `recharge`.base_currency,`recharge`.mfs_name  FROM `recharge` WHERE `recharge`.`recharge_type` = 'mfs_recharge' AND `recharge`.`recharge_status` = 'approved'  AND recharge.`modified_at` BETWEEN '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."' GROUP BY recharge.mfs_name, recharge.base_currency"));


                                            $pos = 1;
                                            $total_recharge_amount = 0;
                                            $table_data = array();
                                            foreach($trans as $row)
                                            {
                                                $table_data[] = array(
                                                    ($pos++),
                                                    $row->mfs_name,
                                                    (strtoupper(implode("", explode("_ ", $row->base_currency)))." ".number_format($row->recharge_amount, 2)),
                                                );

                                                $total_recharge_amount = $total_recharge_amount + $row->recharge_amount;
                                            }

                                            if($total_recharge_amount > 0)
                                            {
                                                $table_data[] = array(
                                                    '','', ''
                                                );
                                                $table_data[] = array(
                                                    '','Total:', number_format($total_recharge_amount, 2)
                                                );
                                            }
                                            foreach($table_data as $row)
                                            {
                                            ?>

                                            <tr>
                                                <td><?php echo $row[0]; ?></td>
                                                <td><?php echo $row[1]; ?></td>
                                                <td><?php echo $row[2]; ?></td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Support Tracker Chart Card ends -->
                        </div>

                        <div class="row match-height">
                            <!-- Avg Sessions Chart Card starts -->

                            <div class="col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between pb-1">
                                        <h4 class="card-title">Last Payment Received</h4>
                                    </div>
                                    <div class="card-body" style="height: 300px; overflow-y: auto">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Reseller</th>
                                                <th>Code</th>
                                                <th>Last Paid Euro</th>
                                                <th>Date</th>
                                            </tr>
                                            </thead>

                                            <?php

                                            $query = DB::connection('mysql')->table('store')
                                                //->where("parent_store_id", 'by_admin')
                                                ->whereRaw("last_payment_received_amount > 0")->orderBy("last_payment_received", "desc")->limit(200);
                                            $resultPPP = $query->get();

                                            ?>

                                            <tbody>
                                            <?php
                                            foreach($resultPPP as $position => $row)
                                            {
                                            ?>
                                            <tr>
                                                <td><?php echo $position+1; ?></td>
                                                <td><a style="color: black;" target="_blank" href="/report/reseller_due_adjust/<?php echo $row->store_id; ?>"><?php echo $row->store_name; ?></a></td>
                                                <td><?php echo $row->store_code; ?></td>

                                                <td style="font-weight:bold; color: green"><?php echo (!empty($row->last_payment_received_amount)?("€ ".number_format($row->last_payment_received_amount, 2)):""); ?></td>

                                                <td><?php echo (!empty($row->last_payment_received)?(date("m/d/Y", strtotime($row->last_payment_received))):""); ?></td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Avg Sessions Chart Card ends -->

                            <!-- Support Tracker Chart Card starts -->
                            <div class="col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between pb-1">
                                        <h4 class="card-title">Update Site Info</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="hotlineNumber">Hotline Number</label>
                                            <input type="text" class="form-control" id="hotlineNumber" placeholder="01XXXXXXXXX" value="<?php echo $notice_meta->hotline_number; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="dashboardNotice">Dashboard Notice</label>
                                            <textarea class="form-control" id="dashboardNotice" rows="3" placeholder="Put yoru notice here."><?php echo $notice_meta->site_notice; ?></textarea>
                                        </div>
                                        <button class="btn btn-primary float-right" type="button" onclick="saveSiteInfo()">Update</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Support Tracker Chart Card ends -->
                        </div>

                        <?php /* ?>
                <div class="card" style="display: none">
                    <div class="row">
                        <div class="col-10"><h4 style="padding-top: 10px; padding-left: 20px;">Reseller Dues</h4></div>
                        <div class="col-2"><button style="margin-top: 10px;" data-toggle="modal" data-target="#NoticeBoardModal" type="button" class="btn btn-sm btn-warning removeMfs">Update Notice</button></div>
                    </div>
                </div>
                <?php */ ?>
                    </section>
                    <!-- Dashboard Analytics end -->

                </div>
                <div class="tab-pane fade p-1" id="two" role="tabpanel" aria-labelledby="two-tab">
                    <section>
                        <div class="row match-height">
                            <div class="col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between pb-1">
                                        <h4 class="card-title">Reseller Due Balance</h4>
                                    </div>
                                    <div class="card-body" style="height: 300px; overflow-y: auto">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Reseller</th>
                                                <th>Code</th>
                                                <th>Due Euro</th>
                                            </tr>
                                            </thead>

                                            <?php

                                            $query = DB::connection('mysql')->table('store')->where("status", "=", "enabled")->where("enable_simcard_access", "=", "1")->where("parent_store_id", 'by_admin')->orderBy("simcard_due_amount", "desc")->limit(200);
                                            $resultPPP = $query->get();

                                            ?>

                                            <tbody>
                                            <?php
                                            foreach($resultPPP as $position => $row)
                                            {
                                            ?>
                                            <tr>
                                                <td><?php echo $position+1; ?></td>
                                                <td><?php echo $row->store_name; ?></td>
                                                <td><?php echo $row->store_code; ?></td>
                                                <td style="font-weight:bold; color: <?php echo (floatval($row->simcard_due_amount) > 0?"red":"green"); ?>">€ <?php echo number_format($row->simcard_due_amount, 2); ?></td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between pb-1">
                                        <h4 class="card-title">Pending Sim Activation</h4>
                                    </div>
                                    <div class="card-body" style="height: 300px; overflow-y: auto">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <th style="width: 50px;">#</th>
                                                <th>Id</th>
                                                <th>Product Name</th>
                                                <th>Reseller Name</th>
                                                <th>Created On</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $nr = 1;
                                            foreach($reports['pending_sim_activation'] as $row)
                                            {
                                            ?>
                                            <tr>
                                                <td><?php echo $nr++; ?></td>
                                                <td><a href="/simcard/info/<?php echo $row->id; ?>"><?php echo $row->sim_card_iccid; ?></a></td>
                                                <td><?php echo $row->product_name; ?></td>
                                                <td><?php echo $row->store_name." (".$row->store_code.")"; ?></td>
                                                <td><?php echo $row->sold_at; ?></td>
                                            </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row match-height">
                            <div class="col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between pb-1">
                                        <h4 class="card-title">Pending Sim Order</h4>
                                    </div>
                                    <div class="card-body" style="height: 300px; overflow-y: auto">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <th style="width: 50px;">#</th>
                                                <th>Id</th>
                                                <th>Product Name</th>
                                                <th>Reseller Name</th>
                                                <th>Created On</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            foreach($reports['pending_sim_order'] as $row)
                                            {
                                            ?>
                                            <tr>
                                                <td><?php echo $nr++; ?></td>
                                                <td><a href="/simcard/orders"># <?php echo $row->order_serial; ?></a></td>
                                                <td><?php echo $row->product_name; ?></td>
                                                <td><?php echo $row->store_name." (".$row->store_code.")"; ?></td>
                                                <td><?php echo $row->created_at; ?></td>
                                            </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between" style="padding-bottom: 10px;">
                                        <h4 class="card-title">Sim Sale & Activation</h4>
                                    </div>
                                    <div class="card-body" style="height: 300px; overflow-y: auto">
                                        <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="one-tab1" data-toggle="tab" href="#one1" role="tab" aria-controls="One" aria-selected="true">Sold Today</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="two-tab2" data-toggle="tab" href="#two2" role="tab" aria-controls="Two" aria-selected="false">Sold Yesterday</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="three-tab3" data-toggle="tab" href="#three3" role="tab" aria-controls="Three" aria-selected="false">Sold Last 7 Days</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="three-four4" data-toggle="tab" href="#four4" role="tab" aria-controls="Four" aria-selected="false">Sold On <?php echo date("F");?></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="three-four5" data-toggle="tab" href="#four5" role="tab" aria-controls="Four" aria-selected="false">Sold On <?php echo date("F", strtotime("first day of previous month"));?></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="three-four6" data-toggle="tab" href="#four6" role="tab" aria-controls="Four" aria-selected="false">Sold On <?php echo date("F", strtotime("first day of -2 months"));?></a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active" id="one1" role="tabpanel" aria-labelledby="one-tab1">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 50px;">#</th>
                                                        <th>Name</th>
                                                        <th style="width: 50px;">Qty.</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $nr = 1;
                                                    $total= 0;

                                                    foreach($reports['simcard_sold_today'] as $row)
                                                    {

                                                    $total= $total + $row->total;

                                                    ?>
                                                    <tr>
                                                        <td><?php echo $nr++; ?></td>
                                                        <td><?php echo $row->product_name; ?></td>
                                                        <td><?php echo $row->total; ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                    <tr><th colspan="3"></th></tr>
                                                    <tr><th colspan="2">Total: </th><th><?php echo $total; ?></th></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="two2" role="tabpanel" aria-labelledby="two-tab2">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 50px;">#</th>
                                                        <th>Name</th>
                                                        <th style="width: 50px;">Qty.</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $nr = 1;
                                                    $total= 0;

                                                    foreach($reports['simcard_sold_yesterday'] as $row)
                                                    {

                                                    $total= $total + $row->total;

                                                    ?>
                                                    <tr>
                                                        <td><?php echo $nr++; ?></td>
                                                        <td><?php echo $row->product_name; ?></td>
                                                        <td><?php echo $row->total; ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                    <tr><th colspan="3"></th></tr>
                                                    <tr><th colspan="2">Total: </th><th><?php echo $total; ?></th></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="three3" role="tabpanel" aria-labelledby="three-tab3">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 50px;">#</th>
                                                        <th>Name</th>
                                                        <th style="width: 50px;">Qty.</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $nr = 1;
                                                    $total= 0;

                                                    foreach($reports['simcard_sold_last_7_days'] as $row)
                                                    {

                                                    $total= $total + $row->total;

                                                    ?>
                                                    <tr>
                                                        <td><?php echo $nr++; ?></td>
                                                        <td><?php echo $row->product_name; ?></td>
                                                        <td><?php echo $row->total; ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                    <tr><th colspan="3"></th></tr>
                                                    <tr><th colspan="2">Total: </th><th><?php echo $total; ?></th></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="four4" role="tabpanel" aria-labelledby="three-tab4">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 50px;">#</th>
                                                        <th>Name</th>
                                                        <th style="width: 50px;">Qty.</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $nr = 1;
                                                    $total= 0;

                                                    foreach($reports['simcard_sold_this_month'] as $row)
                                                    {

                                                    $total= $total + $row->total;

                                                    ?>
                                                    <tr>
                                                        <td><?php echo $nr++; ?></td>
                                                        <td><?php echo $row->product_name; ?></td>
                                                        <td><?php echo $row->total; ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                    <tr><th colspan="3"></th></tr>
                                                    <tr><th colspan="2">Total: </th><th><?php echo $total; ?></th></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="four5" role="tabpanel" aria-labelledby="three-tab5">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 50px;">#</th>
                                                        <th>Name</th>
                                                        <th style="width: 50px;">Qty.</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $nr = 1;
                                                    $total= 0;

                                                    foreach($reports['simcard_sold_last_month'] as $row)
                                                    {

                                                    $total= $total + $row->total;

                                                    ?>
                                                    <tr>
                                                        <td><?php echo $nr++; ?></td>
                                                        <td><?php echo $row->product_name; ?></td>
                                                        <td><?php echo $row->total; ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                    <tr><th colspan="3"></th></tr>
                                                    <tr><th colspan="2">Total: </th><th><?php echo $total; ?></th></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="four6" role="tabpanel" aria-labelledby="three-tab6">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 50px;">#</th>
                                                        <th>Name</th>
                                                        <th style="width: 50px;">Qty.</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $nr = 1;
                                                    $total= 0;

                                                    foreach($reports['simcard_sold_last_month_earlier'] as $row)
                                                    {

                                                    $total= $total + $row->total;

                                                    ?>
                                                    <tr>
                                                        <td><?php echo $nr++; ?></td>
                                                        <td><?php echo $row->product_name; ?></td>
                                                        <td><?php echo $row->total; ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                    <tr><th colspan="3"></th></tr>
                                                    <tr><th colspan="2">Total: </th><th><?php echo $total; ?></th></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="modal fade" id="NoticeBoardModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Update Site Info</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="hotlineNumber">Hotline Number</label>
                                <input type="text" class="form-control" id="hotlineNumber" placeholder="01XXXXXXXXX" value="<?php echo $notice_meta->hotline_number; ?>">
                            </div>
                            <div class="form-group">
                                <label for="dashboardNotice">Dashboard Notice</label>
                                <textarea class="form-control" id="dashboardNotice" rows="3" placeholder="Put yoru notice here."><?php echo $notice_meta->site_notice; ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" onclick="saveSiteInfo()" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    function saveSiteInfo()
    {
        if($("#hotlineNumber").val().length > 1 && $("#dashboardNotice").val().length > 1)
        {
            jQuery.ajax({
                type: "POST",
                beforeSend: function(request) {
                    request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                },
                url: "<?php echo env('APP_URL', ''); ?>/api/stores/save_store_currency",
                dataType: 'json',
                //notice_meta
                data: JSON.stringify({
                    "notice_meta": JSON.stringify({
                        "hotline_number":$('#hotlineNumber').val(),
                        "site_notice":$('#dashboardNotice').val()
                    })
                }),
                statusCode: {
                    200: function() {
                        location.reload()
                    },
                    406: function() {
                        location.reload()
                    },
                    401: function() {
                        location.reload()
                    }
                }
            });
        }
        else
        {
            alert("Invalid Data. Fill hotlineNUmber and Dashboard Notice..")
        }
    }
</script>
<!-- END: Content-->
@include('inc.footer', ['load_dashboard_scripts' => false])
