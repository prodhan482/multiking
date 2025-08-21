@include('inc.header', ['load_vuejs' => true, 'load_pick_a_date_scripts' => true])
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
                <div class="card-header bg-white header-elements-inline">
                    <h6 class="card-title">Payment Receipt Upload</h6>
                    <div class="header-elements form-inline">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#uploadPaymentReceipt"><i class="icon-upload"></i> Upload New</button>
                    </div>
                </div>
                <div class="card-body" :style="{'padding':'0'}">
                    <div class="form-inline">
                        <?php if($userInfo->user_type == "super_admin" || in_array("StoreController::list", $userInfo->permission_lists)): ?>
                        <div class="col-md-3 col-sm-12">
                            <div class="input-group">
                                <select class="form-control do-me-select2-reseller" v-model="tableFilter.store_id">
                                    <option value="">Select A Reseller</option>
                                    <?php if($userInfo->user_type != "super_admin" && in_array("StoreController::list", $userInfo->permission_lists)): ?>
                                    <option value="<?php echo $userInfo->store_vendor_id; ?>">Self</option>
                                    <?php endif; ?>
                                    <option v-for="option in storeList" v-bind:value="option.id" v-html="('Reseller -> ') +option.name"></option>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>

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
                        <?php if($userInfo->user_type != "super_admin" && !in_array("StoreController::list", $userInfo->permission_lists)): ?>
                        <div class="col-md-3 col-sm-12"></div>
                        <?php endif; ?>

                        <div class="col-md-3 col-sm-12">
                            <button type="button" class="btn btn-primary" v-on:click="loadPageData">Search</button>
                            <button type="button" class="btn btn-warning" v-on:click="clearAllFilter">Clear</button>
                        </div>
                    </div>
                    <table class="table table-xs datatable-basic dataTable no-footer datatable-scroll-y">
                        <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Date</th>
                            <th>Reseller Name</th>

                            <th>Download</th>
                            <th>Amount (&euro;)</th>
                            <th>Status</th>

                            <th>Note</th>
                            <th>Admin Note</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="uploadPaymentReceipt" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Payment Receipt</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <?php if($userInfo->user_type == "super_admin" || in_array("StoreController::list", $userInfo->permission_lists)): ?>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">Reseller <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-control do-me-select2-reseller-upload_payment" v-model="uploadForm.store_id">
                                        <option value="">Select A Reseller</option>
                                        <?php if($userInfo->user_type != "super_admin" && in_array("StoreController::list", $userInfo->permission_lists)): ?>
                                        <option value="">Self</option>
                                        <?php endif; ?>
                                        <option v-for="option in storeList" v-bind:value="option.id" v-html="option.name"></option>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">Title <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Add your Title" class="form-control" v-model="uploadForm.title">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">Note</label>
                            <div class="col-sm-9">
                                <textarea id="addBalanceNote" class="form-control" rows="3" placeholder="Put if you have any note" v-model="uploadForm.note"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">Amount</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">&euro;</span>
                                    </div>
                                    <input type="number" placeholder="Put Amount" class="form-control" v-model="uploadForm.amount">
                                    <div class="input-group-append">
                                        <span class="input-group-text">.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">File <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control h-auto" ref="uploadCreateFile" accept="image/*, application/pdf" v-on:change="uploadFileSelected">
                                <small class="form-text text-muted">Only Pdf, image files are allowed. Max size 2 MB.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-link" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary" v-on:click="uploadPaymentReceipt()">Upload</button>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
<!-- END: Content-->
<script>
    var theInstance = {};
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
                        date_from:moment().subtract(7, "days").format('YYYY-MM-DD'),
                        date_to:moment().format('YYYY-MM-DD'),
                        <?php if($userInfo->user_type != "super_admin"): ?>
                        store_id:"<?php echo $userInfo->store_vendor_id; ?>",
                        <?php else: ?>
                        store_id:"",
                        <?php endif; ?>
                        vendorListLoaded:0,
                        storeListLoaded:0
                    },
                    uploadForm:{
                        title:'',
                        note:'',
                        file:'',
                        amount:'',
                        store_id:""
                    },
                    mfs_list:[],
                    storeList:[],
                    fromPicker:"",
                    toPicker:"",
                    approveDialogBox:{},
                    page: 1
                }
            },
            mounted() {
                this.windowHeight = window.innerHeight
                this.windowWidth = window.innerWidth
                window.addEventListener('resize', () => {
                    this.windowHeight = window.innerHeight
                    this.windowWidth = window.innerWidth
                })
                theInstance = this;
                this.masterTable = $('.dataTable').DataTable({
                        scrollX: true,
                        scrollY: (this.windowHeight - 260)+'px',
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
                                'targets': 0,'searchable': false, 'orderable': false, 'width':'5%',
                            },
                            {
                                'targets': 1,'searchable': false, 'orderable': false, 'width':'20%',
                            },
                            {
                                'targets': 2,'searchable': false, 'orderable': false, <?php if($userInfo->user_type == "Reseller" && !in_array("StoreController::list", $userInfo->permission_lists)): ?>"visible": false<?php endif; ?>
                            },
                            {
                                'targets': 3,'searchable': false, 'orderable': false,
                                'render': function (data, type, full, meta)
                                {
                                    var info = $('<div/>').text(data).html();

                                    return '<div class="btn-group-sm">'+
                                        '<a href="'+info+'" class="btn btn-warning updateMfs" target="_blank">View</a>'+
                                        '</div>';
                                }
                            },
                            {
                                'targets': 4,'searchable': false, 'orderable': false,
                                'render': function (data, type, full, meta)
                                {
                                    var info = $('<div/>').text(data).html();

                                    return '(&euro;) '+info;
                                }
                            },
                            {
                                'targets': 6,'searchable': false, 'orderable': false,
                            },
                            {
                                'targets': 5,'searchable': false, 'orderable': false,
                                'render': function (data, type, full, meta)
                                {
                                    var info = $('<div/>').text(data).html();
                                    switch(info) {
                                        case "Pending":
                                            return '<span class="badge badge-light-primary">'+info+'</span>';
                                        case "Approve":
                                            return '<span class="badge badge-light-success">'+info+'</span>';
                                        case "Reject":
                                            return '<span class="badge badge-light-danger">'+info+'</span>';
                                        default:
                                            return '';
                                    }
                                }
                            },
                            {
                                'targets': 7,'searchable': false, 'orderable': false,
                            },
                            {
                                'targets': 8,'searchable': false, 'orderable': false,
                                'render': function (data, type, full, meta)
                                {
                                    var info = $('<div/>').text(data).html();
                                    var i = info.split("||");

                                    if(i.length > 0 && i[1] == "Pending")
                                    {
                                        return '<div class="btn-group-sm">'+
                                            '<button data-id="'+i[0]+'" data-euro_amount="'+i[2]+'" class="btn btn-success approveRequest">Approve</button>'+
                                            '<button data-id="'+i[0]+'" data-euro_amount="" class="btn btn-danger rejectRequest">Reject</button>'+
                                            '</div>';
                                    }
                                    else
                                    {
                                        return '';
                                    }
                                }
                            }
                        ],
                        "processing": true,
                        "serverSide": true,
                        "pageLength": 500,
                        "language": {
                            "emptyTable": "No Data Found. Change Date and try again.",
                        },
                        "ajax": {
                            "url": '<?php echo env('APP_URL', ''); ?>/api/report/payment_doc_upload_statement',
                            "type": "POST",
                            'beforeSend': function (request) {
                                theInstance.showWaitingDialog();
                                request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                            },
                            "data": function ( d )
                            {
                                d.store_id = theInstance.tableFilter.store_id
                                d.date_from = theInstance.tableFilter.date_from
                                d.date_to = theInstance.tableFilter.date_to

                                d.storeListLoaded = theInstance.tableFilter.storeListLoaded
                            },
                            complete: function(data)
                            {
                                theInstance.hideWaitingDialog();
                                if(theInstance.tableFilter.storeListLoaded === 0)
                                {
                                    theInstance.storeList = data.responseJSON.storeList
                                }
                                theInstance.tableFilter.storeListLoaded = 1
                                //theInstance.mfs_list = data.responseJSON.mfs_list
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

                $('.do-me-select2-reseller-upload_payment').select2({ width: '100%' });
                $('.do-me-select2-reseller-upload_payment').on('select2:select', function (e) {
                    theInstance.uploadForm.store_id = $('.do-me-select2-reseller-upload_payment').select2('data')[0].id
                });
            },
            methods: {
                showWaitingDialog:function ()
                {
                    if(!theInstance.waitingDialogInShow)
                    {
                        waitingDialog.show();
                        waitingDialog.animate("Loading. Please Wait.");
                        waitingDialog.setTimeout(50);
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
                dTableMount:function () {
                    $(".approveRequest").click(function(e){
                        theInstance.approveDialogBox = bootbox.dialog({
                            title: 'Please recheck before approve',
                            message: (
                                '<input type="hidden" id="theId" value="'+$(this).data("id")+'"><div class="form-group"><label>Euro (&euro;) Amount </label><input class="form-control" id="theEuroAmount" type="text" placeholder="Put Received Euro Amount." value="'+$(this).data("euro_amount")+'"></div>'
                                +'<div class="form-group"><label>Approve Note</label><textarea class="form-control" id="approveDialogBox_note" rows="3" placeholder="Enter your approve note here."></textarea></div>'
                            ),
                            //centerVertical:true,
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
                                        theInstance.approveRejectRequest("Approve", $("#theId").val(), $('#approveDialogBox_note').val(), $('#theEuroAmount').val());
                                    }
                                }
                            }
                        });
                    });

                    $(".rejectRequest").click(function(e){
                        var cause = prompt("Put your reject cause.", "")
                        if (cause != null && cause.length > 2) {
                            theInstance.approveRejectRequest("Reject", $(this).data("id"), cause, 0);
                        }
                        else
                        {
                            alert("Please try again with a reject cause.")
                        }
                    });

                },
                loadPageData(){
                    $('#modal_filter').modal('hide');
                    this.masterTable.ajax.reload();
                },
                approveRejectRequest(status, id, cause, amount)
                {
                    theInstance.showWaitingDialog();
                    let formData = new FormData();
                    formData.append("status", status);
                    formData.append("admin_note", cause);
                    if(parseFloat(amount) > 0) formData.append("amount", amount);
                    axios.post('<?php echo env('APP_URL', ''); ?>/api/report/payment_doc_upload_statement?do_update_row='+id, formData,
                    {
                        headers: {
                            Authorization: '<?php echo session('AuthorizationToken'); ?>'
                        },
                    }).then(response => {
                        theInstance.loadPageData();
                    }).catch(error => {
                        if (error.response) {
                            switch (error.response.status)
                            {
                                case 401:
                                    this.makeForceLogout()
                                    break;
                                case 406:
                                    console.log(error.response)
                                    theInstance.formErrorMessage = error.response.data.message.join(",");
                                    $('html, body').animate({ scrollTop: 0 }, 500);
                                    break;
                            }
                            this.page_message = ''
                        }
                        theInstance.hideWaitingDialog();
                    });
                },
                clearAllFilter()
                {
                    theInstance.tableFilter  ={
                        date_from:"",
                        date_to:"",
                        store_id:"",
                        vendorListLoaded:0,
                        storeListLoaded:0
                    };
                    $('.do-me-select2-reseller').val(null).trigger('change');

                    this.loadPageData();
                },
                uploadFileSelected()
                {
                    this.uploadForm.file = this.$refs.uploadCreateFile.files[0];
                },
                uploadPaymentReceipt()
                {
                    let formData = new FormData();
                    theInstance.showWaitingDialog();

                    if(this.uploadForm.title.length == 0) return alert("You have to enter a title");
                    if(this.$refs.uploadCreateFile.files.length == 0) return alert("Please select a file.");

                    Object.keys(this.uploadForm).forEach(key => {
                        formData.append(key, this.uploadForm[key])
                    });
                    $('#uploadPaymentReceipt').modal('hide');

                    axios.post('<?php echo env('APP_URL', ''); ?>/api/report/payment_doc_upload_statement?allow_file_upload=1', formData,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        }).then(response => {
                            if(response.data.message.length > 0)
                            {
                                theInstance.hideWaitingDialog();
                                alert(response.data.message);
                            }
                            else
                            {
                                alert('Record have been created Successfully');
                                theInstance.loadPageData();
                            }
                        })
                        .catch(error => {
                            if (error.response) {
                                switch (error.response.status)
                                {
                                    case 401:
                                        this.makeForceLogout()
                                        break;
                                    case 413:
                                        alert("You are trying a very large file. Please make it less then 2 MB.")
                                        break;
                                    case 406:
                                        console.log(error.response)
                                        theInstance.formErrorMessage = error.response.data.message.join(",");
                                        $('html, body').animate({ scrollTop: 0 }, 500);
                                        break;
                                }
                                this.page_message = ''
                            }
                            theInstance.hideWaitingDialog();
                        });
                },
                makeForceLogout()
                {
                    if(confirm("Your Session have been Expired. You have to re-login to continue. Press ok to logout")){
                        this.userLogout()
                    }
                },
                async userLogout() {
                    try {
                        let response = await this.$auth.logout()
                        console.log(response.data)
                    } catch (err) {
                        console.log(err)
                    }
                    window.location.href = "login"
                },
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
