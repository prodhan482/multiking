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
                    <h6 class="card-title"><?php if($userInfo->user_type !== "Reseller"): ?>Reseller<?php endif; ?> Payment Received</h6>
                    <div class="header-elements form-inline">
                    </div>
                </div>
                <div class="card-body" :style="{'padding':'0'}">
                    <div class="form-inline">
                        <?php if($userInfo->user_type == "super_admin" || in_array("StoreController::list", $userInfo->permission_lists)): ?>
                        <div class="col-md-3 col-sm-12">
                            <div class="input-group">
                                {{--<div class="input-group-prepend"><span class="input-group-text">Reseller</span></div>--}}
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
                            <th>Parent</th>
                            <th>Euro</th>
                            <th>Balance</th>
                            <th>Note</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
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
                        <?php if($userInfo->user_type != "super_admin"): ?>
                        store_id:"<?php echo $userInfo->store_vendor_id; ?>",
                        <?php else: ?>
                        store_id:"<?php echo $store_id; ?>",
                        <?php endif; ?>
                        vendorListLoaded:0,
                        storeListLoaded:0
                    },
                    mfs_list:[],
                    storeList:[],
                    fromPicker:"",
                    toPicker:"",
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
                        scrollY: (this.windowHeight - 390)+'px',
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
                            //theInstance.dTableMount();
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
                                'targets': 2,'searchable': false, 'orderable': false, <?php if($userInfo->user_type == "Reseller"): ?>"visible": false<?php endif; ?>
                            },
                            {
                                'targets': 3,'searchable': false, 'orderable': false, <?php if($userInfo->user_type == "Reseller"): ?>"visible": false<?php endif; ?>
                            },
                            {
                                'targets': 4,'searchable': false, 'orderable': false,
                            },
                            {
                                'targets': 5,'searchable': false, 'orderable': false,
                            },
                            {
                                'targets': 6,'searchable': false, 'orderable': false,
                            }
                        ],
                        "processing": true,
                        "serverSide": true,
                        "pageLength": 500,
                        "language": {
                            "emptyTable": "No Adjustment History Data Found.",
                        },
                        "ajax": {
                            "url": '<?php echo env('APP_URL', ''); ?>/api/report/adjustment_history/store',
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
                                d.trans_type = "received_payment"

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
                loadPageData(){
                    $('#modal_filter').modal('hide');
                    this.masterTable.ajax.reload();
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
