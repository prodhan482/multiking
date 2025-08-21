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

        </div>
    </div>
</div>
<!-- END: Content-->
@include('inc.footer', ['load_dashboard_scripts' => false])
