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
                            <div class="card-header flex-column align-items-start pb-0">
                                <div class="avatar bg-light-warning p-50 m-0">
                                    <div class="avatar-content">
                                        <i data-feather="package" class="font-medium-5"></i>
                                    </div>
                                </div>
                                <h2 class="font-weight-bolder mt-1" style="color: <?php echo ((floatval($current_balance->simcard_due_amount)) > 0?"red":"green") ?>">&euro; <?php echo number_format((floatval($current_balance->simcard_due_amount) > 0?$current_balance->simcard_due_amount:($current_balance->simcard_due_amount * (-1))), 2); ?><?php if(!empty($current_balance->simcard_due_amount)): ?>/=<?php endif; ?></h2>
                                <p class="card-text"><?php if(floatval($current_balance->simcard_due_amount) >= 0.00): ?>Due Euro<?php else: ?>Advance Euro<?php endif; ?></p>
                            </div>
                            <div id="order-chart"></div>
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
            },
            methods: {
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
