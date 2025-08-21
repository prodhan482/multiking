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
                    <h6 class="card-title"><?php if($userInfo->user_type !== "Reseller"): ?>Reseller<?php endif; ?> Due Statement</h6>
                    <div class="header-elements form-inline">
                        <?php if($userInfo->user_type == "super_admin" || in_array("StoreController::list", $userInfo->permission_lists)): ?>
                        <select class="form-control do-me-select2-reseller">
                            <?php if($userInfo->user_type != "super_admin" && in_array("StoreController::list", $userInfo->permission_lists)): ?>
                            <option value="<?php echo $userInfo->store_vendor_id; ?>">Self</option>
                            <?php else: ?>
                            <option value="">Select A Reseller</option>
                            <?php endif; ?>
                            <?php foreach($storeList as $row): ?>
                            <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php endif; ?>
                        <input type="text" class="form-control daterange-time-from" placeholder="Date From" v-model="tableFilter.date_from">
                        <input type="text" class="form-control daterange-time-to" placeholder="Date To" v-model="tableFilter.date_to">
                        <button class="btn btn-primary" v-on:click="loadPageData">Search</button>
                    </div>
                </div>
                <div class="card-body" :style="{'padding':'0'}">

                    <table class="table table-xs datatable-basic dataTable no-footer datatable-scroll-y">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Details</th>
                            <th>Dr. (&euro;)</th>
                            <th>Cr. (&euro;)</th>
                            <th>Balance (&euro;)</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
                    fromPicker:"",
                    toPicker:"",
                    masterTable:"",
                    scrollPosition:0,
                    waitingDialogInShow:false,
                    tableFilter:{
                        <?php if($userInfo->user_type != "super_admin"): ?>
                        date_from:"<?php echo date("Y-m-d"); ?>",
                        date_to:"<?php echo date("Y-m-d"); ?>",
                        store_id:"<?php echo $userInfo->store_vendor_id; ?>",
                        <?php else: ?>
                        date_from:"",
                        date_to:"",
                        store_id:"",
                        <?php endif; ?>
                    },
                }
            },
            mounted() {
                theInstance = this;
                $('.do-me-select2-reseller').select2();
                $('.do-me-select2-reseller').on('select2:select', function (e) {
                    theInstance.tableFilter.store_id = $('.do-me-select2-reseller').select2('data')[0].id
                });

                theInstance.fromPicker = $('.daterange-time-from').pickadate({
                    format: 'yyyy-mm-dd',
                    selectYears: true,
                    selectMonths: true,
                    onClose: function() {
                        theInstance.tableFilter.date_from = theInstance.fromPicker.pickadate('picker').get("value");
                    }
                });

                theInstance.toPicker = $('.daterange-time-to').pickadate({
                    format: 'yyyy-mm-dd',
                    selectYears: true,
                    selectMonths: true,
                    onClose: function() {
                        theInstance.tableFilter.date_to = theInstance.toPicker.pickadate('picker').get("value");
                    }
                });

                theInstance.masterTable = $('.dataTable').DataTable({
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
                        },
                        /*"processing": true,
                        "serverSide": true,*/
                        "pageLength": 500,
                        "language": {
                            "emptyTable": "No Transaction record found for The Selected Reseller. Try other reseller with date selected",
                        },
                        "ajax": {
                            "url": '<?php echo env('APP_URL', ''); ?>/api/report/reseller_due_statement',
                            "type": "POST",
                            'beforeSend': function (request) {
                                theInstance.showWaitingDialog();
                                request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                            },
                            "data": function ( d )
                            {
                                d.date_from = theInstance.tableFilter.date_from
                                d.date_to = theInstance.tableFilter.date_to
                                d.store_id = theInstance.tableFilter.store_id
                            },
                            complete: function(data)
                            {
                                theInstance.hideWaitingDialog();
                                theInstance.page_message = ''
                                console.log("Done...");
                            },
                            error: function (xhr, error, thrown)
                            {
                                console.log("Error");
                            }
                        },
                    }
                );
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
                loadPageData() {
                    theInstance.masterTable.ajax.reload();
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
