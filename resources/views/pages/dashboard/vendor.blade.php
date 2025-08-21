@include('inc.header', ['load_vuejs' => true, 'load_pick_a_date_scripts' => true])
@include('inc.menu', ['hide_balance' => true])
<!-- BEGIN: Content-->
<div class="app-content content " id="app">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- Dashboard Analytics Start -->
            <section id="dashboard-analytics">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <div class="card-title" style="font-weight: bold; font-size: 15px;" v-html="('Recent Recharge Activities (Refreshing within '+countdownSec+' second)')"></div>
                                <div class="header-elements">
                                    <div class="list-icons">

                                    </div>
                                </div>
                            </div>
                            <div class="card-body" :style="{'padding':'0'}">
                                <div class="form-inline">
                                    <div class="col-md-3 col-sm-12">
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">Phone</span></div>
                                            <input type="number" placeholder="Enter Phone Number" class="form-control" v-model="tableFilter.phone_number">
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <div class="from-group">
                                            <select class="form-control do-me-select2-mfs" v-model="tableFilter.mfs_id">
                                                <option value="">Select A MFS</option>
                                                <option v-for="option in mfs_list" v-bind:value="option.mfs_id" v-html="option.mfs_name"></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-sm-12">
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">From</span></div>
                                            <input type="text" placeholder="Click to open date picker" class="form-control daterange-time-from" v-model="tableFilter.date_from">
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-sm-12">
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">To</span></div>
                                            <input type="text" placeholder="Click to open date picker" class="form-control daterange-time-to" v-model="tableFilter.date_to">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-12">
                                        <button type="button" id="llkjgldkjfglkdf" class="btn btn-primary">Search</button>
                                        <button type="button" id="dkfgjhfdkjhgkjdfg" class="btn btn-warning" v-on:click="clearAllFilter">Clear</button>
                                    </div>
                                </div>
                                <table class="table table-xs datatable-border dataTable no-footer datatable-scroll-y table-border-solid" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>MFS</th>
                                        <th>Phone Number</th>

                                        <th>Amount</th>
                                        <th>Action</th>

                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Last Updated On</th>
                                        <th>Reseller</th>
                                        <th>Parent</th>

                                        <th>Note</th>
                                    </tr>
                                    </thead>
                                    <tbody>
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
<script>
    $(function(){
        var app = new Vue({
            el: '#app',
            data() {
                return {
                    formErrorMessage:'',
                    page_message:'',
                    masterTable:{},
                    waitingDialogInShow:false,
                    logo:'',
                    storeName:'',
                    storeOwnerName:'',
                    storePhoneNumber:'',
                    storeAddress:'',
                    windowHeight: 0,
                    windowWidth: 0,
                    scrollPosition:0,
                    formToDoUpdate:false,
                    current_balance:0,
                    approveRejectDB:{},
                    mfs_list:[],
                    tableFilter:{
                        date_from:"",
                        date_to:"",
                        phone_number:"",
                        mfs_id:""
                    },
                    fromPicker:"",
                    toPicker:"",
                    page: 1,
                    countdownSec:0,
                    countdownSecLimit:15
                }
            },
            mounted() {
                theInstance = this;
                theInstance.countdownSec = theInstance.countdownSecLimit;

                setTimeout(function () {
                    theInstance.fixTheTable();
                }, 500);

                setInterval(function(){
                    theInstance.countdownSec = theInstance.countdownSecLimit
                    theInstance.loadPageData();
                }, (1000 * theInstance.countdownSecLimit));

                setInterval(function(){
                    theInstance.countdownSec = theInstance.countdownSec - 1;
                }, 1000);

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

                $("#llkjgldkjfglkdf").click(function(e){
                    theInstance.loadPageData();
                });

                $("#dkfgjhfdkjhgkjdfg").click(function(e){
                    theInstance.clearAllFilter();
                });

                $('.do-me-select2-mfs').select2({'width':'100%'});
                $('.do-me-select2-mfs').on('select2:select', function (e) {
                    theInstance.tableFilter.mfs_id = $('.do-me-select2-mfs').select2('data')[0].id
                });
            },
            methods: {
                showWaitingDialog:function ()
                {
                    /*if(!theInstance.waitingDialogInShow)
                    {
                        waitingDialog.show();
                        waitingDialog.animate("Loading. Please Wait.");
                        theInstance.waitingDialogInShow = true;
                    }*/
                },
                hideWaitingDialog:function ()
                {
                    /*
                    setTimeout(function () {
                        waitingDialog.hide();
                    }, 1000);
                    theInstance.waitingDialogInShow = false;
                    */
                },
                isMobile() {
                    if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                        return true
                    } else {
                        return false
                    }
                },
                fixTheTable()
                {
                    this.masterTable = $('.dataTable').DataTable({
                            scrollX: true,
                            scrollY: (this.windowHeight - 300)+'px',//(this.windowHeight - 500)+'px',
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
                            "columnDefs": [
                                {
                                    'targets': 5,'searchable': false, 'orderable': false, 'width':'10%',
                                    'render': function (data, type, full, meta)
                                    {
                                        var info = $('<div/>').text(data).html();

                                        if(info === "Pending" || info === "Requested") return '<span class="badge badge-warning badge-pill" style="font-size: 14px;">'+info+'</span>';
                                        if(info === "Approved") return '<span class="badge badge-success badge-pill" style="font-size: 14px;">'+info+'</span>';
                                        if(info === "Progressing") return '<span class="badge badge-info badge-pill" style="font-size: 14px;">'+info+'</span>';
                                        if(info === "Rejected") return '<span class="badge badge-danger badge-pill" style="font-size: 14px;">Canceled</span>';

                                        return '';
                                    }
                                },
                                {
                                    'targets': 4,'searchable': false, 'orderable': false, 'width':'10%',
                                    'render': function (data, type, row, meta)
                                    {
                                        var info = $('<div/>').text(data).html();

                                        return '<div class="btn-group-sm">'+
                                            ((info.split('|')[1] === "pending" || info.split('|')[1] === "requested")?'<button type="button" class="btn btn-warning lockRechargeBtt" data-id="'+info.split('|')[0]+'">Lock</button>':'')+
                                            (info.split('|')[1] === "progressing"?'<button type="button" class="btn btn-success approveBtt" data-id="'+info.split('|')[0]+'">Approve</button>':'')+
                                            ((info.split('|')[1] === "progressing" && !theInstance.isMobile())?'<a class="btn btn-primary shareBtt" href="https://api.whatsapp.com/send?text=HelloDuniya22.com%0aNumber '+row[2]+'%0aAmount '+row[3]+'%0aType '+row[1]+'%0aRefer ID '+row[0]+'%0aReseller: '+row[8]+'%0aParent: '+row[9]+'" target="_blank" data-id="'+info.split('|')[0]+'">Share</a>':'')+

                                            ((info.split('|')[1] === "progressing" && theInstance.isMobile())?'<button class="btn btn-primary shareBtt" data-title="HelloDuniya22.com" data-text="HelloDuniya22.com\nNumber '+row[2]+'\nAmount '+row[3]+'\nType '+row[1]+'\nRefer ID '+row[0]+'\nReseller: '+row[8]+'\nParent: '+row[9]+'">Share</button>':'')+

                                            (info.split('|')[1] === "progressing"?'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-danger rejectBtt" data-id="'+info.split('|')[0]+'">Cancel</button>':'')+

                                            '</div>';

                                    }
                                },
                                {
                                    'targets': 10,'searchable': false, 'orderable': false, 'width':'10%',
                                    'render': function (data, type, row)
                                    {
                                        var info = $('<div/>').text(data).html();
                                        return info+'&nbsp;&nbsp;'+(info.length > 0?('<a href="javascript:void(0)" class="updateNoteBtt" aria-hidden="true" data-note="'+info.replace(/['"]+/g, '')+'" data-id="'+row[4].split('|')[0]+'">Update</a>'):"");
                                    }
                                },
                            ],
                            createdRow: function (row, data, index) {
                                if (data[1] == "Balance Refill") {
                                    $(row).addClass("table-success");
                                }
                            },
                            "processing": true,
                            "serverSide": true,
                            "pageLength": 500,
                            "language": {
                                "emptyTable": "No Adjustment History Data Found.",
                            },
                            "ajax": {
                                "url": '<?php echo env('APP_URL', ''); ?>/api/recharge/activity',
                                "type": "POST",
                                'beforeSend': function (request) {
                                    theInstance.showWaitingDialog();
                                    request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                                },
                                "data": function ( d )
                                {
                                    d.date_from = theInstance.tableFilter.date_from
                                    d.date_to = theInstance.tableFilter.date_to
                                    d.phone_number = theInstance.tableFilter.phone_number
                                    d.mfs_id = theInstance.tableFilter.mfs_id
                                },
                                complete: function(data)
                                {
                                    theInstance.hideWaitingDialog();
                                    theInstance.mfs_list = data.responseJSON.mfs_list
                                    theInstance.current_balance = data.responseJSON.current_balance
                                    //console.log(data.responseJSON);
                                },
                                error: function (xhr, error, thrown)
                                {
                                    console.log("Error");
                                }
                            },
                        }
                    );
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
                lock:function(recharge_id)
                {
                    /*var transaction_pin = prompt("Please enter your Transaction Pin", "");
                    if(transaction_pin && transaction_pin.length > 1)
                    {
                        //'transaction_pin':transaction_pin
                    }*/
                    theInstance.showWaitingDialog();
                    axios.post("<?php echo env('APP_URL', ''); ?>/api/recharge/lock/"+recharge_id, {},{
                        headers: {
                            Authorization: '<?php echo session('AuthorizationToken'); ?>'
                        }
                    })
                        .then(response => {
                            //location.reload();
                            theInstance.loadPageData();
                        })
                        .catch(error => {
                            if (error.response) {
                                switch (error.response.status)
                                {
                                    case 401:
                                        this.makeForceLogout()
                                        break;
                                    case 403:
                                    case 406:
                                        alert(error.response.data.message.join(","))
                                        break;
                                }
                            }
                        })
                },
                approveReject:function(recharge_id, recharge_status)
                {
                    //var note = prompt("Please enter your note (if any)", "");

                    theInstance.approveRejectDB = bootbox.dialog({
                        title: 'Are you Sure?',
                        show: false,
                        message: (
                            //'<div class="form-group"><label>Transaction Pin</label><input class="form-control" id="transaction_pin_val" type="password" placeholder="Put you Transaction Pin here."></div>'
                            '<div class="form-group"><label>Note</label><textarea class="form-control" id="note_val" rows="3" placeholder="Enter your note here."></textarea></div>'
                        ),
                        buttons: {
                            cancel: {
                                label: "Cancel",
                                className: 'btn-danger',
                                callback: function(){
                                    console.log('Custom cancel clicked');
                                }
                            },
                            ok: {
                                label: (recharge_status == "approved"?"Approve":"Reject"),
                                className: 'btn-info',
                                callback: function(){

                                    if($("#note_val").val().length < 2)
                                    {
                                        alert("Please enter a Valid Note.")
                                        return;
                                    }
                                    theInstance.showWaitingDialog();

                                    axios.post("<?php echo env('APP_URL', ''); ?>/api/recharge/approve_reject/"+recharge_id, {
                                        'recharge_status':recharge_status,
                                        'note':$("#note_val").val(),
                                        //'transaction_pin':$("#transaction_pin_val").val()
                                    },{
                                        headers: {
                                            Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                        }
                                    })
                                        .then(response => {
                                            theInstance.loadPageData();
                                        })
                                        .catch(error => {
                                            if (error.response) {
                                                switch (error.response.status)
                                                {
                                                    case 401:
                                                        this.makeForceLogout()
                                                        break;
                                                    case 403:
                                                    case 406:
                                                        alert(error.response.data.message);
                                                        theInstance.approveRejectDB.modal('show');
                                                        break;
                                                }
                                            }
                                        })
                                }
                            }
                        }
                    });

                    theInstance.approveRejectDB.modal('show');
                },
                updateNote:function(recharge_id, note)
                {
                    theInstance.approveRejectDB = bootbox.dialog({
                        title: 'Update Note',
                        show: false,
                        message: (
                            '<div class="form-group"><label>Note</label><textarea class="form-control" id="note_val" rows="3" placeholder="Enter your note here.">'+note+'</textarea></div>'
                        ),
                        buttons: {
                            cancel: {
                                label: "Cancel",
                                className: 'btn-danger',
                                callback: function(){
                                    console.log('Custom cancel clicked');
                                }
                            },
                            ok: {
                                label: "Ok",
                                className: 'btn-info',
                                callback: function(){
                                    theInstance.showWaitingDialog();
                                    axios.post("<?php echo env('APP_URL', ''); ?>/api/recharge/update_note/"+recharge_id, {
                                        'note':$("#note_val").val(),
                                    },{
                                        headers: {
                                            Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                        }
                                    })
                                        .then(response => {
                                            theInstance.loadPageData();
                                        })
                                        .catch(error => {
                                            if (error.response) {
                                                switch (error.response.status)
                                                {
                                                    case 401:
                                                        this.makeForceLogout()
                                                        break;
                                                    case 403:
                                                    case 406:
                                                        alert(error.response.data.message);
                                                        theInstance.approveRejectDB.modal('show');
                                                        break;
                                                }
                                            }
                                        })
                                }
                            }
                        }
                    });

                    theInstance.approveRejectDB.modal('show');
                },
                dTableMount() {
                    $(".lockRechargeBtt").click(function(e){
                        theInstance.lock($(this).data("id"));
                    });

                    $(".approveBtt").click(function(e){
                        theInstance.approveReject($(this).data("id"), 'approved');
                    });

                    $(".updateNoteBtt").click(function(e){
                        theInstance.updateNote($(this).data("id"), $(this).data("note"));
                    });

                    $(".rejectBtt").click(function(e){
                        theInstance.approveReject($(this).data("id"), 'rejected');
                    });

                    $(".shareBtt").click(function(e){
                        theInstance.doMobileShare($(this).data("title"), $(this).data("text"));
                    });
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
                loadPageData()
                {
                    //location.reload();
                    theInstance.masterTable.ajax.reload();
                },
                clearAllFilter()
                {
                    theInstance.tableFilter = {
                        date_from:"",
                        date_to:"",
                        phone_number:"",
                        mfs_id:""
                    };

                    theInstance.loadPageData();
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

