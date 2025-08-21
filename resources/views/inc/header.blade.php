<!DOCTYPE html>
<html class="loading" lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-textdirection="ltr">
<!-- BEGIN: Head-->


<head>
    <base href="<?php echo env('APP_URL', ''); ?>"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>HelloDuniya22.Com</title>
    <link rel="apple-touch-icon" href="/assets/apple-touch-icon1.png">
    <link rel="shortcut icon" type="image/x-icon" href="/assets/favicon1.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="/assets/vendors/css/extensions/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/vendors/css/tables/datatable/datatables.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/vendors/css/tables/datatable/responsive.bootstrap.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/core/menu/menu-types/horizontal-menu.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/plugins/charts/chart-apex.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/plugins/extensions/ext-component-toastr.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/pages/app-invoice-list.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/core/menu/menu-types/horizontal-menu.min.css">
    <!-- END: Page CSS-->

    <link rel="stylesheet" type="text/css" href="/assets/vendors/css/forms/select/select2.min.css">

    <?php if(!empty($load_pick_a_date_scripts)): ?>
    <link rel="stylesheet" type="text/css" href="/assets/vendors/css/pickers/pickadate/pickadate.css">
    <?php endif; ?>

    <?php if(!empty($load_richtexteditor)): ?>
    <!--<link rel="stylesheet" href="/assets/richtexteditor/richtexteditor/rte_theme_default.css" />
    <script type="text/javascript" src="/assets/richtexteditor/richtexteditor/rte.js"></script>
    <script type="text/javascript" src='/assets/richtexteditor/richtexteditor/plugins/all_plugins.js'></script>-->

    <script type="text/javascript" src="/assets/ckeditor/ckeditor.js"></script>


    <?php endif; ?>

    <!-- BEGIN: Vendor JS-->
    <script src="/assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- production version, optimized for size and speed -->
    <?php if(!empty($load_vuejs)): ?>
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="https://unpkg.com/axios@1.0.0/dist/axios.min.js"></script>
    <?php endif; ?>

    <?php if(!empty($load_html2canvas)): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
    <?php endif; ?>

    <script src="/assets/js/waitingfor.js?t=<?php echo time(); ?>"></script>
    <style>
        body {
            font-size: 0.93rem;
        }
        .table>tbody>tr:nth-child(odd)>td, .table>tbody>tr:nth-child(odd)>th {
            background-color: #f7f7f7;
        }

        .leftMashfjdjgdflkg{
            left: calc(25% - 56px);
        }

        @media only screen and (min-width: 768px) {
            .containerwww {
                display: flex;
                align-items: center;
                justify-content: center;
                height: 10vh; /* Adjust this as needed */
            }
            .content {
                /*padding-top: 13rem !important;*/
            }
            .kjhkj90height {
                height: 90px !important;
            }
            .hhdkfkgtop90 {
                top:90px !important;
            }
            .leftMashfjdjgdflkg{
                left: 18px !important;
            }
            .content-wrapper{
                margin-top: 20px !important;
            }
            .horizontal-menu .header-navbar.navbar-brand-center .navbar-header .navbar-brand .brand-logo img
            {
                max-width: 7vw !important;
            }
        }

        @media only screen and (max-width: 767px) {
            .containerwww {
            }
            .content {
            }
            .kjhkj90height {
            }
            .hhdkfkgtop90 {
            }
        }
    </style>
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="horizontal-layout horizontal-menu  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="">

<!-- BEGIN: Header-->
