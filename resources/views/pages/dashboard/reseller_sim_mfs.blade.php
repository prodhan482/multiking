@include('inc.header', ['load_vuejs' => true, 'load_html2canvas' => true])
@include('inc.menu', ['hide_balance' => true])
<!-- BEGIN: Content-->
<?php
$result = DB::connection('mysql')->table('store')->selectRaw("notice_meta")->first();

$notice_meta = array(
    'hotline_number'=>'',
    'site_notice'=>'',
);

$notice_meta = json_decode(json_encode($notice_meta));

if(!empty($result))
{
    $notice_meta = json_decode($result->notice_meta);
}
?>
<div class="app-content content " id="app">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- Dashboard Analytics Start -->
            <section id="dashboard-analytics">

                <?php if(!empty($notice_meta->site_notice)): ?>
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    <div class="alert-body"><?php echo nl2br($notice_meta->site_notice); ?></div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <?php endif; ?>

                <div class="row match-height">
                    <!-- Greetings Card starts -->
                    <div class="col-lg-2 col-md-12 col-sm-12">
                        <div class="card card-congratulations">
                            <div class="card-body text-center">
                                <img src="/assets/images/elements/decore-left.png" class="congratulations-img-left" alt="card-img-left" />
                                <img src="/assets/images/elements/decore-right.png" class="congratulations-img-right" alt="card-img-right" />
                                <div class="avatar avatar-xl bg-primary shadow">
                                    <div class="avatar-content">
                                        <?php if(!empty($userInfo->logo)): ?>
                                        <img class="round" src="<?php echo $userInfo->logo; ?>" alt="avatar" height="50" width="50">
                                        <?php else: ?>
                                        <i data-feather="award" class="font-large-1"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h1 class="mb-1 text-white">Hello <?php echo $userInfo->storeOwnerName; ?>,</h1>
                                    <p class="card-text m-auto w-75">
                                        <?php echo $userInfo->storeName; ?><br>
                                        Address: <?php echo $userInfo->storeAddress; ?><br>
                                        Phone: <?php echo $userInfo->storePhoneNumber; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h3>Transaction</h3>

                                <h6>Current Balance: <?php echo $current_balance->currency; ?> <?php echo $current_balance->amount; ?><?php if(!empty($current_balance->amount)): ?>/=<?php endif; ?></h6>

                                <h6><?php if(floatval($current_balance->due_euro) >= 0.00): ?>Due Euro<?php else: ?>Advance Euro<?php endif; ?>: <span style="color: <?php echo ((floatval($current_balance->due_euro)) > 0?"red":"green") ?>">&euro; <?php echo number_format((floatval($current_balance->due_euro) > 0?$current_balance->due_euro:($current_balance->due_euro * (-1))), 2); ?><?php if(!empty($current_balance->due_euro)): ?>/=<?php endif; ?></span></h6>

                                <br><br>
                                <h3>Sim Card</h3>
                                <h6><?php if(floatval($current_balance->simcard_due_amount) >= 0.00): ?>Due Euro<?php else: ?>Advance Euro<?php endif; ?>: <span style="color: <?php echo ((floatval($current_balance->simcard_due_amount)) > 0?"red":"green") ?>">&euro; <?php echo number_format((floatval($current_balance->simcard_due_amount) > 0?$current_balance->simcard_due_amount:($current_balance->simcard_due_amount * (-1))), 2); ?><?php if(!empty($current_balance->simcard_due_amount)): ?>/=<?php endif; ?></span></h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 col-sm-6 col-12">
                        <div class="container-fluid">
                            <div class="row flex-row flex-nowrap" style="overflow-x: auto;">
                                <?php
                                foreach($reports['simcard_product_promotion'] as $row)
                                {
                                ?>
                                    <div class="col-lg-4 col-sm-12">
                                        <a target="_blank" href="<?php echo '/simcard/banner_i/'.$row->id; ?>">
                                            <img src="<?php echo '/'.$row->file_name; ?>" class="mb-25 img-fluid rounded">
                                        </a>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

               <div class="row match-height">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row match-height">
                                        <div :class="[(isMobile()?'col-sm-6':'col')]" style="text-align: center"
                                             v-for="option in mfs_list_t1">
                                            <a :href="('/refill?mfs='+option.mfs_id+'&type='+option.mfs_type)">
                                                <img :src="'/'+option.image_path" class="img-fluid" :alt="option.mfs_name" style="max-height: 100px;">
                                            </a>
                                            <br v-if="isMobile()"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row match-height">
                                        <div :class="[(isMobile()?'col-sm-6':'col')]" style="text-align: center"
                                             v-for="option in mfs_list_t2">
                                            <a :href="('/refill?mfs='+option.mfs_id+'&type='+option.mfs_type)">
                                                <img :src="'/'+option.image_path" class="img-fluid" :alt="option.mfs_name" style="max-height: 100px;">
                                            </a>
                                            <br v-if="isMobile()"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body" style="padding: 0">
                                    <table class="table datatable-basic dataTable no-footer datatable-scroll-y">
                                        <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Created At</th>
                                            <th>Receipt</th>
                                            <th>Phone Number</th>
                                            <th>Amount</th>{{--
                                        <th>Received Amount</th>--}}
                                            <th>Balance</th>
                                            <th>MFS</th>
                                            <th>Note</th>
                                            <th>Vendor Note</th>
                                            <th>Status</th>
                                            <th>Last Updated On</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                       <div class="col-12">
                           <div class="card">
                               <div class="card-header"><h4>Last Sim Request</h4></div>
                               <div class="card-body">
                                   <table class="table datatable-basic no-footer datatable-scroll-y">
                                       <thead>
                                       <tr>
                                           <th>Product Name</th>
                                           <th>SIM ICCID</th>
                                           <th>Sales Time</th>
                                           <th>SIM Mobile NUMBER</th>
                                           <th>Status</th>
                                           {{--<th>Cost</th>
                                           <th>Sales Price</th>
                                           <th>Profit/(Loss)</th>--}}
                                       </tr>
                                       </thead>
                                       <tbody>
                                        <?php
                                        foreach($reports['last_sim_request'] as $row)
                                        {
                                            ?>
                                            <tr style="background-color: <?php

                                if($row->status == "pending"){
                                    echo "rgba(255, 9, 0, 0)";
                                }

                                if($row->status == "approved"){
                                    echo "rgba(75, 255, 0, 0.13)";
                                }

                                if($row->status == "rejected"){
                                    echo "rgba(255, 9, 0, 0.13)";
                                }

                                ?>;">
                                                <th scope="row"><?php echo $row->product_name ?></th>
                                                <td><?php echo $row->sim_card_iccid ?></td>
                                                <td><?php echo date("F jS, Y", strtotime($row->sold_at)) ?></td>
                                                <td><?php echo $row->sim_card_mobile_number ?></td>
                                                <td>
                                                    <?php

                                                    if($row->sales_status == "in_stock")
                                                    {
                                                        if($row->status == "pending")
                                                        {
                                                            echo "In Stock";
                                                        }
                                                    }
                                                    else
                                                    {
                                                        if($row->status == "pending")
                                                        {
                                                            echo "Approval pending";
                                                        }
                                                        else
                                                        {
                                                            echo ucwords($row->status);
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                {{--<td><?php echo $row->cost ?></td>
                                                <td><?php echo $row->sales_price ?></td>
                                                <td><?php echo ($row->sales_price > $row->cost)?number_format($row->sales_price - $row->cost, 2, ".", ","):("(".number_format($row->cost - $row->sales_price, 2, ".", ",").")"); ?></td>--}}
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                       </tbody>
                                   </table>
                               </div>
                           </div>
                       </div>
                       <div class="col-6">
                           <div class="card">
                               <div class="card-header"><h4>Sim Card Current Stock</h4></div>
                               <div class="card-body">
                                   <table class="table datatable-basic no-footer datatable-scroll-y">
                                       <thead>
                                       <tr>
                                           <th>Product Name</th>
                                           <th>In Stock</th>
                                       </tr>
                                       </thead>
                                       <tbody>
                                       <?php
                                       foreach($reports['simcard_current_stock'] as $row)
                                       {
                                       ?>
                                       <tr>
                                           <th scope="row"><a href="/simcard/list/in_stock?product=<?php echo $row->product_id ?>" class="navigateAsReset" data-product_id="<?php echo $row->id ?>" data-stock_status="in_stock"><?php echo $row->product_name ?></a></th>
                                           <td><?php echo $row->in_stock_status ?></td>
                                       </tr>
                                       <?php
                                       }
                                       ?>
                                       </tbody>
                                   </table>
                               </div>
                           </div>
                       </div>
                       <div class="col-6">
                           <div class="card">
                               <div class="card-header"><h4>Sim Card Last Order Info</h4></div>
                               <div class="card-body">
                                   <table class="table datatable-basic no-footer datatable-scroll-y">
                                       <thead>
                                       <tr>
                                           <th>Request for</th>
                                           <th>Placed at</th>
                                           <th>Status</th>
                                           <th></th>
                                       </tr>
                                       </thead>
                                       <tbody>
                                       <?php
                                       foreach($reports['simcard_last_order_info'] as $row)
                                       {
                                       $order_title = $row->product_name." ".$row->quantity." Pcs @ ".date("F jS, Y", strtotime($row->created_at));

                                           ?>
                                           <tr style="background-color: <?php

                                if($row->status == "pending"){
                                    echo "rgba(255, 9, 0, 0)";
                                }

                                if($row->status == "approved"){
                                    echo "rgba(75, 255, 0, 0.13)";
                                }

                                if($row->status == "rejected"){
                                    echo "rgba(255, 9, 0, 0.13)";
                                }

                                ?>;">
                                               <th scope="row">

                                                   <?php if($row->status == "approved"){ ?>
                                                   <a href="/simcard/list/in_stock/<?php echo $row->id; ?>"><?php echo $order_title; ?></a>
                                                   <?php }else{ ?>
                                                        <?php echo $order_title; ?>
                                                   <?php } ?>
                                               </th>
                                               <td><?php echo date("F jS, Y", strtotime($row->created_at)); ?></td>
                                               <td><?php echo ucfirst($row->status); ?></td>
                                               <td>
                                                   <?php if($row->status == "approved"){ ?>
                                                   <a class="btn btn-xs btn-primary btn-sm" href="/simcard/list/in_stock/<?php echo $row->id; ?>"><span class="glyphicon glyphicon-eye-open"></span> View</a>
                                                   <?php } ?>
                                               </td>
                                           </tr>
                                           <?php
                                       }
                                       ?>
                                       </tbody>
                                   </table>
                               </div>
                           </div>
                       </div>
                       <div class="col-6">
                           <div class="card">
                               <div class="card-header"><h4>Sim Card Product Offers</h4></div>
                               <div class="card-body">
                                   <table class="table datatable-basic no-footer datatable-scroll-y">
                                       <thead>
                                           <tr>
                                               <th>Sr.</th>
                                               <th>Title</th>
                                               <th>Download</th>
                                           </tr>
                                       </thead>
                                       <tbody>
                                       <?php
                                       $i = 1;
                                       foreach($reports['simcard_product_offers'] as $row)
                                       {
                                           ?>
                                           <tr>
                                               <td><?php echo $i; ?></td>
                                               <td><?php echo $row->title; ?></td>
                                               <td style="width: 160px;">
                                                   <a class="btn btn-success btn-sm" href="<?php echo ((!empty($row->space_uploaded) && $row->space_uploaded == "uploaded")?config('constants.dgSpaceURL'):"/").$row->upload_path; ?>" target="_blank"> <span class="glyphicon glyphicon-save" aria-hidden="true"></span> View</a>
                                               </td>
                                           </tr>
                                           <?php
                                           $i = $i + 1;
                                       }
                                       ?>
                                       </tbody>
                                   </table>
                               </div>
                           </div>
                       </div>
                    </div>
            </section>
            <!-- Dashboard Analytics end -->

        </div>
    </div>

    <div class="modal fade receiptShow" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Receipt</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div id="receiptShowPrintArea" class="modal-body">
                    {{--<h4 style="text-align: center">Reseller Name</h4>
                    <p style="text-align: center">
                        showrav017@gmail.com<br>
                        8-2-16, 11-A
                    </p>
                    <p style="font-size: 18px;text-align: center">
                        MFS Logo<br><br>
                        Grameen Phone<br>
                        <span style="font-weight: bold;">01758930809</span>
                    </p>
                    <h3 style="text-align: center">EURO 1.12</h3><br>
                    <table class="table">
                        <tr><td>Currency</td><td>BDT</td></tr>
                        <tr><td>Delivered Amount</td><td>200 TK</td></tr>
                        <tr><td>Receiver</td><td>01758930809</td></tr>
                        <tr><td>Created At</td><td>2021-11-12 00:00 AM</td></tr>
                        <tr><td>Trans. #</td><td>0000012</td></tr>
                    </table>--}}
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" v-on:click="printDiv">Print</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->
<script>
    $(function(){

        var app = new Vue({
            el: '#app',
            data() {
                return {
                    masterTable:{},
                    mfs_list:[],
                    mfs_list_t1:[],
                    mfs_list_t2:[],
                    mfs_list_by_id:{},
                    mfs_package_list:{},
                    mfs_package_list_id:{},
                    currentPackageList:[],
                    tableFilter:{
                        limit:10
                    },
                    request:{
                        selected_mfs:"",
                        selected_mfs_name:"",
                        selected_mfs_package:"",
                        mfs_type:"",
                        send_money:"",
                        receive_money:"",
                        charge:0,
                        commission:0,
                        total:"",
                        mobile_number:"",
                        note:""
                    },
                    requestDialogBox:{}
                }
            },
            mounted() {
                theInstance = this;
                setTimeout(function () {
                    theInstance.doTable();
                }, 500);
            },
            methods: {
                dTableMount() {
                    $(".showRechargeReceipt").click(function(e){
                        theInstance.showReceipt($(this).data("id"));
                    });
                },
                printDiv() {
                    html2canvas($('#receiptShowPrintArea'), {
                        onrendered: function (canvas) {
                            var dataUrl2 = canvas.toDataURL("image/jpg");

                            var windowContent = '<!DOCTYPE html>';
                            windowContent += '<html>'
                            windowContent += '<head><title>Print canvas</title></head><style>html, body { height: auto; } @media print { body, page { margin: 0; box-shadow: #000000; page-break-after: avoid; page-break-before: avoid; size: landscape; } body{ height: auto; } }</style>';
                            windowContent += '<body style="padding:0: margin:0">'
                            windowContent += '<img src="' + dataUrl2 + '">';
                            windowContent += '</body>';
                            windowContent += '</html>';
                            var printWin = window.open('','','width=800,height=800');
                            printWin.document.open();
                            printWin.document.write(windowContent);
                            printWin.document.close();
                            printWin.focus();

                            setTimeout(function(){ printWin.print();printWin.close(); }, 500);
                        }
                    });
                },
                showReceipt(recharge_id)
                {
                    axios.get("<?php echo env('APP_URL', ''); ?>/api/recharge/html_receipt/"+recharge_id,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        })
                        .then(response => {
                            $("#receiptShowPrintArea").html(response.data.html);
                        })
                        .catch(error => {
                            if (error.response) {
                                switch (error.response.status)
                                {
                                    case 401:
                                        this.makeForceLogout()
                                        break;
                                    case 406:
                                        console.log(error.response)
                                        break;
                                }
                            }
                        });

                    //
                    $(".receiptShow").modal('show');
                },
                doTable()
                {
                    this.masterTable = $('.dataTable').DataTable({
                            scrollX: true,
                            scrollY: (this.windowHeight - 550)+'px',//(this.windowHeight - 500)+'px',
                            scrollCollapse: true,
                            "searching": false,
                            "info": false,
                            "paging": false,
                            "ordering": false,
                            "preDrawCallback": function(settings)
                            {
                                theInstance.scrollPosition = $(".dataTables_scrollBody").scrollTop();
                            },
                            "drawCallback": function(settings)
                            {
                                //var api = this.api();
                                $(".dataTables_scrollBody").scrollTop(theInstance.scrollPosition);
                                theInstance.dTableMount();
                                theInstance.page_message = ''
                            },
                            "columnDefs": [
                                {
                                    'targets': 2,'searchable': false, 'orderable': false, 'width':'10%',
                                    'render': function (data, type, row)
                                    {
                                        var info = $('<div/>').text(data).html();
                                        return (info.length > 1 ?'<button class="btn btn-sm btn-success showRechargeReceipt" data-id="'+info+'">Show</button>':'');
                                    }
                                },
                                {
                                    'targets': 9,'searchable': false, 'orderable': false, 'width':'10%',
                                    'render': function (data, type, full, meta)
                                    {
                                        var info = $('<div/>').text(data).html();

                                        if(info === "Pending" || info === "Requested") return '<span class="badge badge-warning badge-pill" style="font-size: 14px;">'+info+'</span>';
                                        if(info === "Approved") return '<span class="badge badge-success badge-pill" style="font-size: 14px;">'+info+'</span>';
                                        if(info === "Progressing") return '<span class="badge badge-info badge-pill" style="font-size: 14px;">'+info+'</span>';
                                        if(info === "Rejected") return '<span class="badge badge-danger badge-pill" style="font-size: 14px;">'+info+'</span>';

                                        return '';
                                    }
                                },
                            ],
                            createdRow: function (row, data, index) {
                                if (data[8] == "Balance Refill" || data[8] == "Refund") {
                                    $(row).addClass("table-success");
                                }
                            },
                            "processing": true,
                            "serverSide": true,
                            "pageLength": 5,
                            "language": {
                                "emptyTable": "No Adjustment History Data Found.",
                            },
                            "ajax": {
                                "url": '<?php echo env('APP_URL', ''); ?>/api/recharge/activity',
                                "type": "POST",
                                'beforeSend': function (request) {
                                    request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                                },
                                "data": function ( d )
                                {
                                    d.limit = theInstance.tableFilter.limit
                                },
                                complete: function(data)
                                {
                                    theInstance.mfs_list = data.responseJSON.mfs_list

                                    for(var m in data.responseJSON.mfs_list)
                                    {
                                        theInstance.mfs_package_list[data.responseJSON.mfs_list[m].mfs_id] = []
                                        theInstance.mfs_list_by_id[data.responseJSON.mfs_list[m].mfs_id] = data.responseJSON.mfs_list[m]

                                        if(data.responseJSON.mfs_list[m].mfs_type == "financial_transaction")
                                        {
                                            theInstance.mfs_list_t1.push(data.responseJSON.mfs_list[m])
                                        }
                                        else
                                        {
                                            theInstance.mfs_list_t2.push(data.responseJSON.mfs_list[m])
                                        }
                                    }

                                    for(var m in data.responseJSON.mfs_package_list)
                                    {
                                        var id = data.responseJSON.mfs_package_list[m].mfs_id;
                                        if (typeof theInstance.mfs_package_list[id] === 'undefined') theInstance.mfs_package_list[id] = []
                                        if (typeof theInstance.mfs_package_list[id] !== 'undefined') theInstance.mfs_package_list[id].push(data.responseJSON.mfs_package_list[m]);

                                        theInstance.mfs_package_list_id[data.responseJSON.mfs_package_list[m].row_id] = data.responseJSON.mfs_package_list[m];
                                    }

                                    for(var m in data.responseJSON.store_mfs_slab)
                                    {
                                        if(parseFloat(data.responseJSON.store_mfs_slab[m].charge) > 0){
                                            theInstance.mfs_list_by_id[data.responseJSON.store_mfs_slab[m].id].default_charge = data.responseJSON.store_mfs_slab[m].charge
                                        }
                                        if(parseFloat(data.responseJSON.store_mfs_slab[m].commission) > 0) {
                                            theInstance.mfs_list_by_id[data.responseJSON.store_mfs_slab[m].id].default_commission = data.responseJSON.store_mfs_slab[m].commission
                                        }
                                    }
                                },
                                error: function (xhr, error, thrown)
                                {
                                    console.log("Error");
                                }
                            },
                        }
                    );
                },
                async makeForceLogout() {
                    if (confirm("Your Session have been Expired. You have to re-login to continue. Press ok to logout")) {
                        try {
                            let response = await this.$auth.logout()
                            console.log(response.data)
                        } catch (err) {
                            console.log(err)
                        }
                        window.location.href = "login"
                    }
                },
                loadPageData(){
                    this.masterTable.ajax.reload();
                },
                isMobile() {
                    if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                        return true
                    } else {
                        return false
                    }
                },
            }
        })
    });
</script>
@include('inc.footer', ['load_dashboard_scripts' => true, 'load_datatable_scripts' => true])
