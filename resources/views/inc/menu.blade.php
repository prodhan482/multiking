<?php
if($userInfo->user_type == "store"|| $userInfo->user_type == "Reseller"){

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

}
?>

<nav class="header-navbar navbar-expand-lg navbar navbar-fixed align-items-center navbar-shadow navbar-brand-center kjhkj90height" data-nav="brand-center" style="">
    <div class="navbar-header d-xl-block d-none leftMashfjdjgdflkg" style="">
        <ul class="nav navbar-nav">
            <li class="nav-item" style="margin: 0 0 0 10px;">
                <a class="navbar-brand containerwww" href="/manage">
                    <span class="brand-logo">
                            <img class="img-fluid" src="/assets/images/Logo2.png"/>
                    </span>
                </a>
            </li>
            <li class="nav-item" style="margin: 10px 20px 0 10px;">
                <?php if(!empty($userInfo->mfs_list)): ?>
                <span style="font-weight: bold; <?php if(!empty($hide_balance)): ?>display: none;<?php endif; ?>"><?php echo $current_balance->currency; ?> <?php echo $current_balance->amount; ?><?php if(!empty($current_balance->amount)): ?>/=<?php endif; ?></span><br>

                    <?php if(!empty($current_balance->due_euro)): ?>
                <span style="color: <?php echo ((floatval($current_balance->due_euro)) > 0?"red":"green") ?>;font-weight: bold; <?php if(!empty($hide_balance)): ?>display: none;<?php endif; ?>">&euro; <?php echo number_format((floatval($current_balance->due_euro) > 0?$current_balance->due_euro:($current_balance->due_euro * (-1))), 2); ?><?php if(!empty($current_balance->due_euro)): ?>/=<?php endif; ?></span><br>
                <?php endif; ?>
                    <?php if(in_array("Simcard::list", $userInfo->permission_lists)){ ?>
                    <?php if(!empty($current_balance->simcard_due_amount)): ?>
                <span style="color: <?php echo ((floatval($current_balance->simcard_due_amount)) > 0?"red":"green") ?>;font-weight: bold; <?php if(!empty($hide_balance)): ?>display: none;<?php endif; ?>"> Sim Card Due: &euro; <?php echo number_format((floatval($current_balance->simcard_due_amount) > 0?$current_balance->simcard_due_amount:($current_balance->simcard_due_amount * (-1))), 2); ?><?php if(!empty($current_balance->simcard_due_amount)): ?>/=<?php endif; ?></span><br>
                <?php endif; ?>
                <?php } ?>
                <?php else: ?>
                <?php if(!empty($current_balance->simcard_due_amount)): ?>
                <span style="color: <?php echo ((floatval($current_balance->simcard_due_amount)) > 0?"red":"green") ?>;font-weight: bold; <?php if(!empty($hide_balance)): ?>display: none;<?php endif; ?>">&euro; <?php echo number_format((floatval($current_balance->simcard_due_amount) > 0?$current_balance->simcard_due_amount:($current_balance->simcard_due_amount * (-1))), 2); ?><?php if(!empty($current_balance->simcard_due_amount)): ?>/=<?php endif; ?></span><br>
                <?php endif; ?>

                <?php endif; ?>
            </li>
            <li class="nav-item" style="margin: 0 10px 0 10px;">
                <a class="navbar-brand containerwww" href="https://asiangroup.it/transportations" target="_blank">
                    <span class="brand-logo">
                            <img class="img-fluid" src="/assets/images/logo/all bus.png"/>
                    </span>
                </a>
            </li>
            <li class="nav-item" style="margin: 0 10px 0 10px;">
                <a class="navbar-brand containerwww" href="https://asiangroup.it/train" target="_blank">
                    <span class="brand-logo">
                            <img class="img-fluid" src="/assets/images/logo/download.png"/>
                    </span>
                </a>
            </li>
            <li class="nav-item" style="margin: 0 10px 0 10px;">
                <a class="navbar-brand containerwww" href="https://webmail.sicurezzapostale.it/?_task=mail&_mbox=INBOX" target="_blank">
                    <span class="brand-logo">
                            <img class="img-fluid" src="/assets/images/logo/Logo-SicurezzaPostale.png"/>
                    </span>
                </a>
            </li>
            <li class="nav-item" style="margin: 0 10px 0 10px;">
                <a class="navbar-brand containerwww" href="https://jmnation.com/login" target="_blank">
                    <span class="brand-logo">
                            <img class="img-fluid" src="/assets/images/logo/JM NATION 2.jpg"/>
                    </span>
                </a>
            </li>
            <li class="nav-item" style="margin: 0 10px 0 10px;">
                <a class="navbar-brand containerwww" href="https://ticket.asinternational.xyz/" target="_blank">
                    <span class="brand-logo">
                            <img class="img-fluid" src="/assets/images/logo/D209.png"/>
                    </span>
                </a>
            </li>
            <li class="nav-item" style="margin: 0 10px 0 10px;">
                <a class="navbar-brand containerwww" href="https://jmnation.com/login" target="_blank">
                    <span class="brand-logo">
                            <img class="img-fluid" src="/assets/images/logo/JM NATION FLIGHT.jpg"/>
                    </span>
                </a>
            </li>
            <li class="nav-item" style="margin: 0 10px 0 10px;">
                <a class="navbar-brand containerwww" href="https://asiangroup.it/flixbus" target="_blank">
                    <span class="brand-logo">
                            <img class="img-fluid" src="/assets/images/logo/Flix Bus.jpg"/>
                    </span>
                </a>
            </li>
            <li class="nav-item" style="margin: 0 10px 0 10px;">
                <a class="navbar-brand containerwww" href="https://flight.asiangroup.us/ticket" target="_blank">
                    <span class="brand-logo">
                            <img class="img-fluid" src="/assets/images/logo/DOMESTIC FLIGHT.png"/>
                    </span>
                </a>
            </li>
        </ul>
    </div>
    <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i class="ficon" data-feather="menu"></i></a></li>
            </ul>
            <ul class="nav navbar-nav bookmark-icons d-xl-none">

                <?php if(!empty($userInfo->mfs_list)): ?>
                    <span style="font-weight: bold; <?php if(!empty($hide_balance)): ?>display: none;<?php endif; ?>"><?php echo $current_balance->currency; ?> <?php echo $current_balance->amount; ?><?php if(!empty($current_balance->amount)): ?>/=<?php endif; ?></span>

                    <?php if(!empty($current_balance->due_euro)): ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span style="color: <?php echo ((floatval($current_balance->due_euro)) > 0?"red":"green") ?>;font-weight: bold; <?php if(!empty($hide_balance)): ?>display: none;<?php endif; ?>">&euro; <?php echo number_format((floatval($current_balance->due_euro) > 0?$current_balance->due_euro:($current_balance->due_euro * (-1))), 2); ?><?php if(!empty($current_balance->due_euro)): ?>/=<?php endif; ?></span>
                    <?php endif; ?>

                    <?php if(in_array("Simcard::list", $userInfo->permission_lists)){ ?>
                        <?php if(!empty($current_balance->simcard_due_amount)): ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span style="color: <?php echo ((floatval($current_balance->simcard_due_amount)) > 0?"red":"green") ?>;font-weight: bold; <?php if(!empty($hide_balance)): ?>display: none;<?php endif; ?>"> Sim Card Due: &euro; <?php echo number_format((floatval($current_balance->simcard_due_amount) > 0?$current_balance->simcard_due_amount:($current_balance->simcard_due_amount * (-1))), 2); ?><?php if(!empty($current_balance->simcard_due_amount)): ?>/=<?php endif; ?></span>
                        <?php endif; ?>
                    <?php } ?>
                <?php else: ?>

                    <?php if(!empty($current_balance->simcard_due_amount)): ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span style="color: <?php echo ((floatval($current_balance->simcard_due_amount)) > 0?"red":"green") ?>;font-weight: bold; <?php if(!empty($hide_balance)): ?>display: none;<?php endif; ?>">&euro; <?php echo number_format((floatval($current_balance->simcard_due_amount) > 0?$current_balance->simcard_due_amount:($current_balance->simcard_due_amount * (-1))), 2); ?><?php if(!empty($current_balance->simcard_due_amount)): ?>/=<?php endif; ?></span>
                    <?php endif; ?>

                <?php endif; ?>

            </ul>
        </div>
        <ul class="nav navbar-nav align-items-center ml-auto">
            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none">
                        <span class="user-name font-weight-bolder"><?php echo ucwords($userInfo->username); ?></span>
                        <?php
                        $userInfo->user_type = ($userInfo->user_type!= "store"?$userInfo->user_type:"Reseller");
                        ?>
                        <span class="user-status"><?php echo ucwords(implode(" ", explode("_", ($userInfo->user_type)))); ?></span>
                    </div>
                    <span class="avatar">
                        <?php if(!empty($userInfo->logo)): ?>
                        <img class="round" src="<?php echo (filter_var($userInfo->logo, FILTER_VALIDATE_URL) === FALSE?('/'):('')); ?><?php echo $userInfo->logo; ?>" alt="avatar" height="40" width="40">
                        <?php else: ?>
                        <img class="round" src="https://i.pravatar.cc/150?u=<?php echo $userInfo->user_id; ?>" alt="avatar" height="40" width="40">
                        <?php endif; ?>
                        <span class="avatar-status-online"></span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                    {{--<a class="dropdown-item" href="page-profile.html"><i class="mr-50" data-feather="user"></i> Profile</a>
                    <a class="dropdown-item" href="app-email.html"><i class="mr-50" data-feather="mail"></i> Inbox</a>
                    <a class="dropdown-item" href="app-todo.html"><i class="mr-50" data-feather="check-square"></i> Task</a>
                    <a class="dropdown-item" href="app-chat.html"><i class="mr-50" data-feather="message-square"></i> Chats</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="page-account-settings.html"><i class="mr-50" data-feather="settings"></i> Settings</a>
                    <a class="dropdown-item" href="page-pricing.html"><i class="mr-50" data-feather="credit-card"></i> Pricing</a>
                    <a class="dropdown-item" href="page-faq.html"><i class="mr-50" data-feather="help-circle"></i> FAQ</a>--}}

                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#changePassword"><i class="mr-50" data-feather="lock"></i> Change Pass.</a>
                    <?php if($userInfo->user_type == "Reseller"): ?>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#changePin"><i class="mr-50" data-feather="lock"></i> Change Pin</a>
                    <?php endif; ?>
                    <a class="dropdown-item" href="/logout"><i class="mr-50" data-feather="power"></i> Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- END: Header-->


<!-- BEGIN: Main Menu-->
<div class="horizontal-menu-wrapper hhdkfkgtop90" style="">
    <div class="header-navbar navbar-expand-sm navbar navbar-horizontal floating-nav navbar-light navbar-shadow menu-border" role="navigation" data-menu="menu-wrapper" data-menu-type="floating-nav">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto">
                    <a class="navbar-brand" href="/manage">
                        <img width="50" src="/assets/images/Logo2.png"/>
                        <h2 class="brand-text mb-0" style="color: #E3266E">King Multi Service</h2>
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <!-- Horizontal menu content-->
        <div class="navbar-container main-menu-content" data-menu="menu-container">
            <!-- include ../../../includes/mixins-->
            <ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">
                <li class="dropdown nav-item
                    @if(Route::current()->getName() == 'dashboard')
                    active
                    @endif
                    ">
                    <a class="d-flex nav-link align-items-center" href="/manage"><i data-feather="sliders"></i><span data-i18n="Dashboards">Dashboards</span>
                    </a>
                </li>

                <?php if($userInfo->user_type == "Reseller" && !empty($userInfo->mfs_list)): ?>
                <li class="dropdown nav-item">
                    <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-toggle="dropdown"><i data-feather="smartphone"></i><span data-i18n="MFS">New Request</span></a>
                    <ul class="dropdown-menu">
                        <?php foreach($userInfo->mfs_list as $mfs): ?>
                        <li data-menu="">
                            <a class="dropdown-item d-flex align-items-center" href="/refill?mfs=<?php echo $mfs->mfs_id; ?>&type=<?php echo $mfs->mfs_type; ?>" data-toggle="dropdown" data-i18n="Place New Request">
                                <i data-feather="smartphone"></i><span data-i18n="View All MFS"><?php echo $mfs->mfs_name; ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if(!empty($userInfo) && $userInfo->user_type == "Reseller" && !empty($userInfo->mfs_list)): ?>
                <li class="dropdown nav-item
                    @if(Route::current()->getName() == 'refill')
                    active
                    @endif
                    ">
                    <a class="d-flex nav-link  align-items-center" href="/refill"><i data-feather="bar-chart"></i><span data-i18n="Refill">Refill</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                (in_array("StoreController::list", $userInfo->permission_lists))  ): ?>
                <li class="dropdown nav-item
                            @if(Route::current()->getName() == 'reseller_list_simcard' || Route::current()->getName() == 'reseller_list' || Route::current()->getName() == 'add_new_reseller' || Route::current()->getName() == 'update_existing_reseller' || Route::current()->getName() == 'currency_conversion' || Route::current()->getName() == 'simcard_configure_reseller_promo')
                            active
                            @endif
                          " data-menu="dropdown">
                    <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-toggle="dropdown"><i data-feather="home"></i><span data-i18n="Reseller">Reseller</span></a>
                    <ul class="dropdown-menu">
                        <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("StoreController::list", $userInfo->permission_lists)): ?>
                            <li data-menu="">
                                <a class="dropdown-item d-flex align-items-center" href="/reseller" data-toggle="dropdown" data-i18n="Reseller">
                                    <i data-feather="home"></i><span data-i18n="Reseller">View All Reseller</span>
                                </a>
                            </li>
                            <?php if((in_array("Simcard::list", $userInfo->permission_lists)) ): ?>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="/reseller_simcard" data-toggle="dropdown" data-i18n="Reseller">
                                    <i data-feather="home"></i><span data-i18n="Reseller">View All Reseller (Sim Card)</span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="/currency_conversion" data-toggle="dropdown" data-i18n="Reseller Conversion">
                                    <i data-feather="dollar-sign"></i><span data-i18n="Reseller">Update Currency Conversion</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                (in_array("VendorController::list", $userInfo->permission_lists))  ): ?>
                <li class="dropdown nav-item
                    @if(Route::current()->getName() == 'vendor_list')
                        active
                    @endif
                    " data-menu="dropdown">
                    <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-toggle="dropdown"><i data-feather="sun"></i><span data-i18n="Vendor">Vendor</span></a>
                    <ul class="dropdown-menu">
                        <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("StoreController::list", $userInfo->permission_lists)): ?>
                        <li data-menu="">
                            <a class="dropdown-item d-flex align-items-center" href="/vendor" data-toggle="dropdown" data-i18n="Vendor">
                                <i data-feather="sun"></i><span data-i18n="Vendor">View All Vendor</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>


                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                (
                 in_array("MFSController::list", $userInfo->permission_lists) ||
                 in_array("MFSController::package_list", $userInfo->permission_lists)
                )): ?>
                <li class="dropdown nav-item
                    @if(Route::current()->getName() == 'mfs_list' || Route::current()->getName() == 'mfs_package_list')
                        active
                    @endif
                    " data-menu="dropdown">
                    <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-toggle="dropdown"><i data-feather="smartphone"></i><span data-i18n="MFS">MFS</span></a>
                    <ul class="dropdown-menu">
                        <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("MFSController::list", $userInfo->permission_lists)): ?>
                        <li data-menu="">
                            <a class="dropdown-item d-flex align-items-center" href="/mfs" data-toggle="dropdown" data-i18n="View All MFS">
                                <i data-feather="smartphone"></i><span data-i18n="View All MFS">View All MFS</span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("MFSController::package_list", $userInfo->permission_lists)): ?>
                        <li data-menu="">
                            <a class="dropdown-item d-flex align-items-center" href="/mfs_package" data-toggle="dropdown" data-i18n="MFS Package">
                                <i data-feather="file-text"></i><span data-i18n="MFS Package">MFS Package</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                (
                    in_array("RechargeController::list", $userInfo->permission_lists) ||
                 in_array("ReportController::mfs_summery", $userInfo->permission_lists) ||
                 in_array("ReportController::reseller_balance_recharge", $userInfo->permission_lists) ||
                 in_array("ReportController::reseller_due_adjust", $userInfo->permission_lists) ||
                 in_array("ReportController::reseller_due_statement", $userInfo->permission_lists)
                ) ): ?>
                <li class="dropdown nav-item
                    @if(Route::current()->getName() == 'report_recharge_history' || Route::current()->getName() == 'report_mfs_summery' || Route::current()->getName() == 'reseller_balance_recharge' || Route::current()->getName() == 'reseller_due_adjust' || Route::current()->getName() == 'reseller_due_statement')
                       active
                    @endif
                    " data-menu="dropdown">
                    <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-toggle="dropdown"><i data-feather="file-text"></i><span data-i18n="Report">Report</span></a>
                    <ul class="dropdown-menu">
                        <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("RechargeController::list", $userInfo->permission_lists)): ?>
                        <li data-menu="">
                            <a class="dropdown-item d-flex align-items-center" href="/report/recharge" data-toggle="dropdown" data-i18n="View All Recharge History">
                                <i data-feather="smartphone"></i><span data-i18n="View All Recharge History">History</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("ReportController::reseller_balance_recharge", $userInfo->permission_lists)): ?>
                        <li data-menu="">
                            <a class="dropdown-item d-flex align-items-center" href="/report/reseller_balance_recharge" data-toggle="dropdown" data-i18n="MFS Summery">
                                 <i data-feather="smartphone"></i><span data-i18n="MFS Summery">Add Balance</span>
                            </a>
                        </li>
                        <?php endif; ?>

                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("ReportController::reseller_due_adjust", $userInfo->permission_lists)): ?>
                            <li data-menu="">
                                <a class="dropdown-item d-flex align-items-center" href="/report/reseller_due_adjust" data-toggle="dropdown" data-i18n="MFS Summery">
                                    <i data-feather="smartphone"></i><span data-i18n="MFS Summery">Payment</span>
                                </a>
                            </li>
                            <li data-menu="">
                                <a class="dropdown-item d-flex align-items-center" href="/report/reseller_return_payment" data-toggle="dropdown" data-i18n="MFS Summery">
                                    <i data-feather="smartphone"></i><span data-i18n="MFS Summery">Balance Return</span>
                                </a>
                            </li>
                            <?php endif; ?>

                        <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("ReportController::reseller_due_statement", $userInfo->permission_lists)): ?>
                            <li data-menu="">
                                <a class="dropdown-item d-flex align-items-center" href="/report/reseller_due_statement" data-toggle="dropdown" data-i18n="MFS Summery">
                                    <i data-feather="smartphone"></i><span data-i18n="MFS Summery">Statement</span>
                                </a>
                            </li>
                        <?php endif; ?>

                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("ReportController::mfs_summery", $userInfo->permission_lists)): ?>
                            <li data-menu="">
                                <a class="dropdown-item d-flex align-items-center" href="/report/mfs_summery" data-toggle="dropdown" data-i18n="MFS Summery">
                                    <i data-feather="smartphone"></i><span data-i18n="MFS Summery">Daily Summery</span>
                                </a>
                            </li>
                            <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                (
                    in_array("Simcard::list", $userInfo->permission_lists) || in_array("Simcard::view_orders", $userInfo->permission_lists)
                ) ): ?>
                <li class="dropdown nav-item
                    @if(Route::current()->getName() == 'simcard_order_list' || Route::current()->getName() == 'simcard_approve_order' || Route::current()->getName() == 'simcard_list' || Route::current()->getName() == 'simcard_mnp_operators' || Route::current()->getName() == 'simcard_update_mnp_operator' || Route::current()->getName() == 'simcard_promo' || Route::current()->getName() == 'simcard_update_promo' || Route::current()->getName() == 'simcard_list' || Route::current()->getName() == 'simcard_list_by_order' || Route::current()->getName() == 'simcard_sale' || Route::current()->getName() == 'simcard_info' || Route::current()->getName() == 'simcard_update' || Route::current()->getName() == 'simcard_report_sales' || Route::current()->getName() == 'simcard_report_recharge' || Route::current()->getName() == 'simcard_report_adjustment' || Route::current()->getName() == 'simcard_report_adjustment_by_id')

                      active
@endif
                    " data-menu="dropdown">

                    <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-toggle="dropdown"><i data-feather="bar-chart"></i><span data-i18n="Report">Sim Card</span></a>
                    <ul class="dropdown-menu">
                        <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::list", $userInfo->permission_lists)): ?>
                        <li data-menu="">
                            <a class="dropdown-item d-flex align-items-center" href="/simcard/all" data-toggle="dropdown" >
                                <i data-feather="align-justify"></i><span >View All</span>
                            </a>
                        </li>
                        <?php endif; ?>

                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::view_orders", $userInfo->permission_lists)): ?>
                            <li data-menu="">
                                <a class="dropdown-item d-flex align-items-center" href="/simcard/orders" data-toggle="dropdown" >
                                    <i data-feather="align-justify"></i><span >Orders</span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::view_stock", $userInfo->permission_lists)): ?>
                            <li data-menu="">
                                <a class="dropdown-item d-flex align-items-center" href="/simcard/list/in_stock" data-toggle="dropdown" >
                                    <i data-feather="layers"></i><span >In Stock (SIM Card)</span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::view_sold", $userInfo->permission_lists)): ?>
                            <li data-menu="">
                                <a class="dropdown-item d-flex align-items-center" href="/simcard/list/sold" data-toggle="dropdown" >
                                    <i data-feather="shopping-bag"></i><span >Sold (SIM Card)</span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::promo", $userInfo->permission_lists)): ?>
                            <li data-menu="">
                                <a class="dropdown-item d-flex align-items-center" href="/simcard/promo" data-toggle="dropdown" >
                                    <i data-feather="award"></i><span >Promotional Offers</span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::list_banner", $userInfo->permission_lists)): ?>
                            <li data-menu="">
                                <a class="dropdown-item d-flex align-items-center" href="/simcard/banners" data-toggle="dropdown" >
                                    <i data-feather="layout"></i><span>Banners</span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::view_mnp_operator_list", $userInfo->permission_lists)): ?>
                            <li data-menu="">
                                <a class="dropdown-item d-flex align-items-center" href="/simcard/mnp_operators" data-toggle="dropdown" >
                                    <i data-feather="align-justify"></i><span>MNP Operators</span>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("SimCardReport::sales_report", $userInfo->permission_lists)): ?>
                            <li data-menu="">
                                <a class="dropdown-item d-flex align-items-center" href="/simcard/report/sales" data-toggle="dropdown" >
                                    <i data-feather="book-open"></i><span>Sales Report</span>
                                </a>
                            </li>
                            <?php endif; ?>


                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("SimCardReport::recharge_report", $userInfo->permission_lists)): ?>
                            <li data-menu="">
                                <a class="dropdown-item d-flex align-items-center" href="/simcard/report/recharge" data-toggle="dropdown" >
                                    <i data-feather="book-open"></i><span >Recharge Report</span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("SimCardReport::adjustment_report", $userInfo->permission_lists)): ?>
                            <li data-menu="">
                                <a class="dropdown-item d-flex align-items-center" href="/simcard/report/adjustment" data-toggle="dropdown" >
                                    <i data-feather="book-open"></i><span >Make Bill Copy</span>
                                </a>
                            </li>
                            <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                (
                in_array("Inventory::view_product", $userInfo->permission_lists)
                ) ): ?>
                <li class="dropdown nav-item
                    @if(Route::current()->getName() == 'inventory_product_list')
                    active
@endif
                    " data-menu="dropdown">
                    <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-toggle="dropdown"><i data-feather="codesandbox"></i><span data-i18n="Report">Inventory</span></a>
                    <ul class="dropdown-menu">
                        <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Inventory::view_product", $userInfo->permission_lists)): ?>
                        <li data-menu="">
                            <a class="dropdown-item d-flex align-items-center" href="/inventory/product_list" data-toggle="dropdown" data-i18n="View All Products">
                                <i data-feather="gift"></i><span data-i18n="View All Products">Products</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>


                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                (in_array("UserController::list", $userInfo->permission_lists))  ): ?>
                <li class="dropdown
                    @if(Route::current()->getName() == 'user_list' || Route::current()->getName() == 'user_permission')
                    active
                    @endif
                    " data-menu="dropdown">
                    <a class="nav-link d-flex align-items-center" href="/users"><i data-feather="users"></i><span data-i18n="Users">Users</span></a>
                </li>
                <?php endif; ?>

                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                (in_array("RechargeController::upload_payment_doc", $userInfo->permission_lists) ||
                in_array("RechargeController::upload_payment_doc", $userInfo->permission_lists))): ?>
                <li class="dropdown
                    @if(Route::current()->getName() == 'report_payment_receipt_upload')
                    active
                    @endif
                    " data-menu="dropdown">
                    <a class="nav-link d-flex align-items-center" href="/report/report_payment_receipt_upload"><i data-feather="upload-cloud"></i><span data-i18n="Users">Pay. Receipt Upload</span></a>
                </li>
                <?php endif; ?>

                <!--<li class="dropdown nav-item">
                    <a class="d-flex nav-link align-items-center" href="<?php /*echo ($userInfo->user_type == "vendor"?"/apk/helloDuniya22-v1-1.0.0-260822_2227-vendor-release.apk":"/apk/helloDuniya22-v1-1.0.0-260822_2227-live-release.apk"); */?>"><i data-feather="smartphone"></i><span data-i18n="Dashboards">Download APK</span></a>
                </li>-->
            </ul>
            <?php if(($userInfo->user_type == "store"|| $userInfo->user_type == "Reseller") && !empty($notice_meta->hotline_number)): ?>
            <div class="d-sm-none d-md-block" style="width: 200px; position: absolute; top:20px; right: 38px; height: 63px; text-align: center; vertical-align: center; font-weight: bold;"><?php echo $notice_meta->hotline_number; ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- END: Main Menu-->
