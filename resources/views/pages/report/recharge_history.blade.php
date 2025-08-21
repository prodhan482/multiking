@include('inc.header', ['load_vuejs' => true, 'load_pick_a_date_scripts' => true, 'load_html2canvas' => true])
@include('inc.menu')
<!-- BEGIN: Content-->
<div class="app-content content " id="app">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <div class="card" style="margin-top: 20px">
                <div class="card-header bg-white header-elements-inline" style="padding-bottom: 5px">
                    <h6 class="card-title" v-html="('Recent Recharge Activities (Refreshing within '+countdownSec+' second)')"></h6>
                    {{--<div class="header-elements">
                        <div class="list-icons">
                            <a href="#" data-toggle="modal" data-target="#modal_filter" class="list-icons-item"><i class="icon-filter3"></i> Filter</a>
                        </div>
                    </div>--}}
                </div>
                <div class="card-body" :style="{'padding':'0'}">
                    <div class="row p-1">
                        <?php if($userInfo->user_type == "super_admin"): ?>
                        <div class="col-md-3 col-sm-12">
                            <div class="input-group">
                                <select class="form-control do-me-select2-reseller" v-model="tableFilter.store_id">
                                    <option value="">Select A Reseller</option>
                                    <option v-for="option in storeList" v-bind:value="option.id" v-html="('Reseller -> ') +option.name"></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="input-group">
                                <select class="form-control do-me-select2-vendor" v-model="tableFilter.vendor_id">
                                    <option value="">Select A Vendor</option>
                                    <option v-for="option in vendorList" v-bind:value="option.id" v-html="('Vendor -> ') +option.name"></option>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($userInfo->user_type !== "super_admin"): ?>
                            <div class="col-md-3 col-sm-12">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">From</span></div>
                                    <input type="text" placeholder="Click to open date picker" class="form-control daterange-time-from" v-model="tableFilter.date_from">
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-12">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">To</span></div>
                                    <input type="text" placeholder="Click to open date picker" class="form-control daterange-time-to" v-model="tableFilter.date_to">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-md-3 col-sm-12">
                            <div class="input-group">
                                <select class="form-control do-me-select2-mfs" v-model="tableFilter.mfs_id">
                                    <option value="">Select A MFS</option>
                                    <option value="">All MFS</option>
                                    <option v-for="option in mfs_list" v-bind:value="option.mfs_id" v-html="('MFS -> ') +option.mfs_name"></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">Status</span></div>
                                <select class="form-control" v-model="tableFilter.recharge_status">
                                    <option value="">Select A Status</option>
                                    <option value="">All Status</option>
                                    <option value="pending">Requested</option>
                                    <option value="approved">Approved</option>
                                    <option value="progressing">Progressing</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-inline">
                        <div class="col-md-3 col-sm-12">
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">Phone Number</span></div>
                                <input type="number" placeholder="Enter Phone Number" class="form-control" v-model="tableFilter.phone_number">
                            </div>
                        </div>
                        <?php if($userInfo->user_type == "super_admin"): ?>
                        <div class="col-md-3 col-sm-12">
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">From</span></div>
                                <input type="text" placeholder="Click to open date picker" class="form-control daterange-time-from" v-model="tableFilter.date_from">
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">To</span></div>
                                <input type="text" placeholder="Click to open date picker" class="form-control daterange-time-to" v-model="tableFilter.date_to">
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($userInfo->user_type !== "super_admin"): ?>
                            <?php if(in_array("StoreController::list", $userInfo->permission_lists)): ?>
                            <div class="col-md-3 col-sm-12">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">Reseller</span></div>
                                    <select class="form-control do-me-select2-reseller" v-model="tableFilter.store_id">
                                        <option value="">Self</option>
                                        <option v-for="option in storeList" v-bind:value="option.id" v-html="option.name"></option>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-3 col-sm-12"></div>
                        <?php endif; ?>

                        <div class="col-md-3 col-sm-12">
                            <button type="button" class="btn btn-primary" v-on:click="loadPageData">Search</button>
                            <button type="button" class="btn btn-warning" v-on:click="clearAllFilter">Clear</button>

                        </div>
                    </div>
                    <table class="table table-xs datatable-basic dataTable no-footer datatable-scroll-y">
                        <thead>
                        <?php if($userInfo->user_type == "super_admin"): ?>
                        <tr>
                            <th>Sl</th>
                            <th>Created At</th>
                            <th>Phone Number</th>
                            <th>Amount</th>{{--
                            <th>Received Amount</th>--}}
                            <th>MFS</th>
                            <th>Note</th>
                            <th>Vendor Note</th>
                            <th>Store</th>
                            <th>Vendor</th>
                            <th>Status</th>
                            <th>Last Updated On</th>
                            <th>Parent</th>
                            <th>Action</th>
                        </tr>
                        <?php endif; ?>
                        <?php if($userInfo->user_type !== "super_admin"): ?>
                        <tr>
                            <th>Sl</th>
                            <th>Created At</th>
                            <?php if($userInfo->user_type == "Reseller"): ?>
                            <th>Receipt</th>
                            <?php endif; ?>
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
                        <?php endif; ?>

                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade receiptShow" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Receipt</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div id="receiptShowPrintArea" class="modal-body"></div>
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
                    formErrorMessage:'',
                    page_message:'',
                    masterTable:{},
                    windowHeight: 0,
                    windowWidth: 0,
                    scrollPosition:0,
                    formToDoUpdate:false,
                    waitingDialogInShow:false,
                    tableFilter:{
                        date_from:"",
                        date_to:"",
                        store_id:"",
                        vendor_id:"",
                        mfs_id:"",
                        recharge_status:"",
                        phone_number:"",
                        vendorListLoaded:0,
                        storeListLoaded:0
                    },
                    mfs_list:[],
                    storeList:[],
                    vendorList:[],
                    fromPicker:"",
                    toPicker:"",
                    countdownSec:0,
                    countdownSecLimit:15,
                    createNewRecharge:{
                        note:'',
                        mobile_number:'',
                        mfs_id:'',
                        mfs_type:'',
                        recharge_amount:0.0,
                        user_id:''
                    },
                    page: 1
                }
            },
            mounted() {
                theInstance = this;

                theInstance.countdownSec = theInstance.countdownSecLimit;

                setInterval(function(){
                    theInstance.countdownSec = theInstance.countdownSecLimit
                    theInstance.loadPageData();
                }, (1000 * theInstance.countdownSecLimit));

                setInterval(function(){
                    theInstance.countdownSec = theInstance.countdownSec - 1;
                }, 1000);

                this.masterTable = $('.dataTable').DataTable({
                        scrollCollapse: true,
                        "searching": false,
                        "info": false,
                        "paging": false,
                        "ordering": false,
                        "preDrawCallback": function( settings ) {
                            theInstance.scrollPosition = $(".dataTables_scrollBody").scrollTop();
                        },
                        "drawCallback": function( settings ) {
                            //var api = this.api();
                            $(".dataTables_scrollBody").scrollTop(theInstance.scrollPosition);
                            theInstance.dTableMount();
                            theInstance.page_message = ''
                        },
                        <?php if($userInfo->user_type == "super_admin"): ?>
                        "columnDefs": [
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
                            {
                                'targets': 12,'searchable': false, 'orderable': false, 'width':'10%',
                                'render': function (data, type, row, meta)
                                {

                                    var info = $('<div/>').text(data).html();

                                    return '<div class="btn-group btn-group-sm">'+
                                        (info.split('|')[1] === "rejected"?'<button type="button" class="btn btn-success reInitBtt" data-id="'+info.split('|')[0]+'">Re-Init</button>':'')+
                                        (info.split('|')[1] === "progressing"?'<button type="button" class="btn btn-success unlockRechargeBtt" data-id="'+info.split('|')[0]+'">UnLock</button>':'')+
                                        //(info.split('|')[1] === "requested"?'<button type="button" class="btn btn-success approveBtt" data-id="'+info.split('|')[0]+'">Approve</button>':'')+
                                        ((info.split('|')[1] !== undefined && info.split('|')[1] !== "approved" && info.split('|')[1] !== "rejected")?'<button type="button" class="btn btn-danger rejectBtt" data-id="'+info.split('|')[0]+'">Reject</button>':'')+

                                        ((info.split('|')[1] === "progressing" && !theInstance.isMobile())?'<a class="btn btn-primary shareBtt" href="https://api.whatsapp.com/send?text=HelloDuniya22.com%0aNumber '+row[2]+'%0aAmount '+row[8]+'%0aType '+row[3]+'%0aRefer ID '+row[0]+'" target="_blank" data-id="'+info.split('|')[0]+'">Share</a>':'')+

                                        ((info.split('|')[1] === "progressing" && theInstance.isMobile())?'<button class="btn btn-primary shareBtt" data-title="HelloDuniya22.com" data-text="HelloDuniya22.com\nNumber '+row[2]+'\nAmount '+row[8]+'\nType '+row[3]+'\nRefer ID '+row[0]+'">Share</button>':'')+


                                        '</div>';
                                }
                            },
                        ],
                        <?php endif; ?>
                        <?php if($userInfo->user_type !== "super_admin"): ?>
                            <?php if($userInfo->user_type == "Reseller"): ?>
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
                            <?php else: ?>
                                "columnDefs": [
                                    {
                                        'targets': 8,'searchable': false, 'orderable': false, 'width':'10%',
                                        'render': function (data, type, full, meta)
                                        {
                                            var info = $('<div/>').text(data).html();

                                            if(info === "Pending" || info === "Requested") return '<span class="badge badge-warning badge-pill" style="font-size: 14px;">'+info+'</span>';
                                            if(info === "Approved") return '<span class="badge badge-success badge-pill" style="font-size: 14px;">'+info+'</span>';
                                            if(info === "Progressing") return '<span class="badge badge-info badge-pill" style="font-size: 14px;">'+info+'</span>';
                                            if(info === "Rejected") return '<span class="badge badge-danger badge-pill" style="font-size: 14px;">'+info+'</span>';

                                            return '';
                                        }
                                    }
                                ],
                                createdRow: function (row, data, index) {
                                    if (data[3] == "Balance Refill ()" || data[3] == "Refund") {
                                        $(row).addClass("table-success");
                                    }
                                    if (data[3] == "Balance Return ()") {
                                        $(row).addClass("table-warning");
                                    }
                                },
                            <?php endif; ?>
                        <?php endif; ?>
                        "processing": true,
                        "serverSide": true,
                        "pageLength": 500,
                        "language": {
                            "emptyTable": "No Adjustment History Data Found.",
                        },
                        "ajax": {
                            "url": '<?php echo env('APP_URL', ''); ?>/api/recharge/activity?report=1',
                            "type": "POST",
                            'beforeSend': function (request) {
                                //theInstance.showWaitingDialog();
                                request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                            },
                            "data": function ( d )
                            {
                                console.log(theInstance.tableFilter)
                                d.date_from = theInstance.tableFilter.date_from
                                d.date_to = theInstance.tableFilter.date_to
                                d.store_id = theInstance.tableFilter.store_id
                                d.vendor_id = theInstance.tableFilter.vendor_id
                                d.mfs_id = theInstance.tableFilter.mfs_id
                                d.recharge_status = theInstance.tableFilter.recharge_status
                                d.phone_number = theInstance.tableFilter.phone_number
                                d.vendorListLoaded = theInstance.tableFilter.vendorListLoaded
                                d.storeListLoaded = theInstance.tableFilter.storeListLoaded
                                d.query_type = "show_only_recharges"
                            },
                            complete: function(data)
                            {
                                //theInstance.hideWaitingDialog();
                                theInstance.mfs_list = data.responseJSON.mfs_list

                                if(theInstance.tableFilter.storeListLoaded === 0)
                                {
                                    theInstance.storeList = data.responseJSON.storeList
                                }

                                if(theInstance.tableFilter.vendorListLoaded === 0)
                                {
                                    theInstance.vendorList = data.responseJSON.vendorList
                                }

                                theInstance.tableFilter.vendorListLoaded = 1
                                theInstance.tableFilter.storeListLoaded = 1

                                theInstance.dTableMount()
                                //console.log(data.responseJSON);
                            },
                            error: function (xhr, error, thrown)
                            {
                                console.log("Error");
                            }
                        },
                    }
                );

                theInstance.fromPicker = $('.daterange-time-from').pickadate({
                    format: 'yyyy-mm-dd',
                    selectYears: true,
                    selectMonths: true,
                    onClose: function() {
                        //$(this)[0].dispatchEvent(new Event('input', { 'bubbles': true }))
                        //console.log(theInstance.fromPicker.pickadate('picker').get("value"));
                        theInstance.tableFilter.date_from = theInstance.fromPicker.pickadate('picker').get("value");
                    }
                });

                theInstance.toPicker = $('.daterange-time-to').pickadate({
                    format: 'yyyy-mm-dd',
                    selectYears: true,
                    selectMonths: true,
                    onClose: function() {
                        //$(this)[0].dispatchEvent(new Event('input', { 'bubbles': true }))
                        //console.log(theInstance.toPicker.pickadate('picker').get("value"));
                        theInstance.tableFilter.date_to = theInstance.toPicker.pickadate('picker').get("value");
                    }
                });

                $('.do-me-select2-reseller').select2();
                $('.do-me-select2-reseller').on('select2:select', function (e) {
                    theInstance.tableFilter.store_id = $('.do-me-select2-reseller').select2('data')[0].id
                });

                $('.do-me-select2-vendor').select2();
                $('.do-me-select2-vendor').on('select2:select', function (e) {
                    theInstance.tableFilter.vendor_id = $('.do-me-select2-vendor').select2('data')[0].id
                });

                $('.do-me-select2-mfs').select2();
                $('.do-me-select2-mfs').on('select2:select', function (e) {
                    theInstance.tableFilter.mfs_id = $('.do-me-select2-mfs').select2('data')[0].id
                });

                /*$('.daterange-time').daterangepicker({
                    //timePicker: true,
                    //timePicker24Hour: true,
                    startDate: moment().subtract(1, "days").format('YYYY-MM-DD'),
                    "singleDatePicker": true,
                    opens: 'left',
                    applyClass: 'bg-slate-600',
                    cancelClass: 'btn-default',
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });

                $('.daterange-time').on('apply.daterangepicker', function(ev, picker) {
                    //console.log(picker.startDate.format('YYYY-MM-DD'));
                    //console.log(picker.endDate.format('YYYY-MM-DD'));
                    $(this)[0].dispatchEvent(new Event('input', { 'bubbles': true }))
                    //theInstance.tableFilter.date_from = picker.startDate.format('YYYY-MM-DD')
                });*/



                /*$('.daterange-time2').daterangepicker({
                  //timePicker: true,
                  //timePicker24Hour: true,
                  startDate: moment().subtract(1, "days").format('YYYY-MM-DD'),
                  "singleDatePicker": true,
                  opens: 'left',
                  applyClass: 'bg-slate-600',
                  cancelClass: 'btn-default',
                  locale: {
                    format: 'YYYY-MM-DD'
                  }
                });

                $('.daterange-time-start2').on('apply.daterangepicker', function(ev, picker) {
                  //console.log(picker.startDate.format('YYYY-MM-DD'));
                  //console.log(picker.endDate.format('YYYY-MM-DD'));
                  $(this)[0].dispatchEvent(new Event('input', { 'bubbles': true }))
                  theInstance.tableFilter.date_to = picker.startDate.format('YYYY-MM-DD')
                });*/
            },
            methods: {
                showWaitingDialog:function ()
                {
                    if(!theInstance.waitingDialogInShow)
                    {
                        waitingDialog.show();
                        waitingDialog.animate("Loading. Please Wait.");
                        theInstance.waitingDialogInShow = true;
                    }
                },
                hideWaitingDialog:function ()
                {
                    setTimeout(function () {
                        waitingDialog.hide();
                    }, 1000);
                    theInstance.waitingDialogInShow = false;
                },
                isMobile() {
                    if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                        return true
                    } else {
                        return false
                    }
                },
                doMobileShare:function (title, text)
                {
                    let shareData = {
                        title: title,
                        text: text,
                        //url: 'https://developer.mozilla.org',
                    }
                    navigator.share(shareData)
                        .then(() =>
                            resultPara.textContent = 'MDN shared successfully'
                        )
                        .catch((e) =>
                            resultPara.textContent = 'Error: ' + e
                        )
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
                    theInstance.showWaitingDialog();
                    axios.get("<?php echo env('APP_URL', ''); ?>/api/recharge/html_receipt/"+recharge_id,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        })
                        .then(response => {
                            theInstance.hideWaitingDialog();
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
                createRecharge:function()
                {
                    if(confirm("Are you sure?"))
                    {
                        if(!this.createNewRecharge.mobile_number.match(/(^(\+01|01))[3-9]{1}(\d){8}$/))
                        {
                            alert("Invalid Bangladeshi Mobile Number.")
                            return;
                        }

                        if(parseFloat(this.createNewRecharge.recharge_amount) <= 0)
                        {
                            alert("Invalid Recharge Amount.")
                            return;
                        }

                        if(this.createNewRecharge.mfs_id === "")
                        {
                            alert("Please select a MFS")
                            return;
                        }

                        if(this.createNewRecharge.mfs_type === "")
                        {
                            alert("Please select A MFS Type")
                            return;
                        }


                        theInstance.showWaitingDialog();
                        axios.post("<?php echo env('APP_URL', ''); ?>/api/recharge/create", this.createNewRecharge,{
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            }
                        })
                            .then(response => {
                                this.loadPageData();
                            })
                            .catch(error => {
                                if (error.response) {
                                    switch (error.response.status)
                                    {
                                        case 401:
                                            this.makeForceLogout()
                                            break;
                                    }
                                }
                            })
                    }
                },
                dTableMount() {
                    $(".unlockRechargeBtt").off('click').on('click', function() {
                        theInstance.unlock($(this).data("id"));
                    });

                    $(".reInitBtt").off('click').on('click', function() {
                        theInstance.reinit($(this).data("id"));
                    });

                    $(".rejectBtt").off('click').on('click', function() {
                        theInstance.approveReject($(this).data("id"), 'rejected');
                    });

                    $(".approveBtt").off('click').on('click', function() {
                        theInstance.approveReject($(this).data("id"), 'pending');
                    });
                    $(".showRechargeReceipt").off('click').on('click', function() {
                        theInstance.showReceipt($(this).data("id"));
                    });

                    $(".shareBtt").click(function(e){
                        theInstance.doMobileShare($(this).data("title"), $(this).data("text"));
                    });
                },

                approveReject:function(recharge_id, recharge_status)
                {
                    if(confirm("Are you sure?"))
                    {
                        theInstance.showWaitingDialog();
                        axios.post("<?php echo env('APP_URL', ''); ?>/api/recharge/approve_reject/"+recharge_id, {'recharge_status':recharge_status},{
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            }
                        })
                            .then(response => {
                                this.loadPageData();
                            })
                            .catch(error => {
                                if (error.response) {
                                    switch (error.response.status)
                                    {
                                        case 401:
                                            this.makeForceLogout()
                                            break;
                                        case 403:
                                            alert(error.response.data.message)
                                            break;
                                    }
                                }
                            })
                    }
                },
                unlock:function(recharge_id)
                {
                    if(confirm("Are you sure?"))
                    {
                        theInstance.showWaitingDialog();
                        axios.post("<?php echo env('APP_URL', ''); ?>/api/recharge/unlock/"+recharge_id, this.createNewRecharge,{
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            }
                        })
                            .then(response => {
                                this.loadPageData();
                            })
                            .catch(error => {
                                if (error.response) {
                                    switch (error.response.status)
                                    {
                                        case 401:
                                            this.makeForceLogout()
                                            break;
                                        case 403:
                                            alert(error.response.data.message)
                                            break;
                                    }
                                }
                            })
                    }
                },
                reinit:function(recharge_id)
                {
                    if(confirm("Are you sure?"))
                    {
                        theInstance.showWaitingDialog();
                        axios.post("<?php echo env('APP_URL', ''); ?>/api/recharge/reinit/"+recharge_id, {},{
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            }
                        })
                            .then(response => {
                                this.loadPageData();
                            })
                            .catch(error => {
                                if (error.response) {
                                    switch (error.response.status)
                                    {
                                        case 401:
                                            this.makeForceLogout()
                                            break;
                                        case 403:
                                            alert(error.response.data.message)
                                            break;
                                    }
                                }
                            })
                    }
                },
                loadPageData()
                {
                    theInstance.hideWaitingDialog();
                    this.masterTable.ajax.reload();
                },
                clearAllFilter()
                {
                    this.tableFilter = {
                        date_from:"",
                        date_to:"",
                        store_id:"",
                        vendor_id:"",
                        mfs_id:"",
                        recharge_status:"",
                        phone_number:"",
                        vendorListLoaded:0,
                        storeListLoaded:0
                    };
                    $('.do-me-select2-reseller').val(null).trigger('change');
                    $('.do-me-select2-vendor').val(null).trigger('change');
                    $('.do-me-select2-mfs').val(null).trigger('change');

                    this.loadPageData();
                }
            }
        })
    });
</script>
<style>
    .select2-container .select2-selection--single
    {
        height: 34px;
    }
</style>
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>
@include('inc.footer', ['load_datatable_scripts' => true, 'load_pick_a_date_scripts' => true])
