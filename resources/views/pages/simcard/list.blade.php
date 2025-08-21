@include('inc.header', ['load_vuejs' => true, 'load_pick_a_date_scripts' => true])
@include('inc.menu')
<div class="app-content content " id="app">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row"></div>
        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="alert-body">{{ $message }}</div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="alert-body">{{ $message }}</div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="content-body">
            <section class="app-user-list">
                <!-- list section start -->
                <div class="card">
                    <div class="card-header bg-white header-elements-inline" style="padding-bottom: 0px">
                        <h6 class="card-title"><?php echo ($status == "in_stock"?"Stocked":"Sold"); ?> Sim Cards</h6>
                    </div>
                    <div class="card-body" :style="{'padding':'0'}">
                        <div class="row p-1">

                            <?php if($status == "sold"): ?>
                            <div class="col-md-3 col-sm-12">
                                <input type="text" class="form-control addADateTimePickerHere hasDatepicker daterange-time-from" placeholder="<?php echo ($status == "in_stock"?"Stocked":"Sold"); ?> From (Date Picker)" v-model="tableFilter.start_date">
                            </div>

                            <div class="col-md-3 col-sm-12">
                                <input type="text" class="form-control addADateTimePickerHere hasDatepicker daterange-time-to" placeholder="<?php echo ($status == "in_stock"?"Stocked":"Sold"); ?> To (Date Picker)" v-model="tableFilter.end_date">
                            </div>
                            <?php endif; ?>

                            <div class="col-md-3 col-sm-12">
                                <input type="text" class="form-control" placeholder="Sim card ICCID" v-model="tableFilter.sim_card_iccid">
                            </div>

                            <div class="col-md-3 col-sm-12">
                                <input type="text" class="form-control" placeholder="Sim Card/MNP Mobile Number" v-model="tableFilter.sim_card_mobile_number">
                            </div>

                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                            (in_array("StoreController::list", $userInfo->permission_lists))  ): ?>
                            <div class="col-md-3 col-sm-12">
                                <select class="form-control do-me-select2-store" v-model="tableFilter.store_id">
                                    <option value="">Select A Reseller</option>
                                    <?php foreach($storeList as $row): ?>
                                    <option value="<?php echo $row->id; ?>">Reseller -> <?php echo $row->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>

                                <div class="col-md-3 col-sm-12">
                                    <select class="form-control order-type" v-model="tableFilter.product_id">
                                        <option value="">Select A Product</option>
                                        <?php foreach($productList as $row): ?>
                                        <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            <?php if($status == "sold"): ?>
                            <div class="col-md-3 col-sm-12">
                                <select class="form-control" v-model="tableFilter.status">
                                    <option v-for="(key, value) in orderStatus" v-bind:value="value" v-html="('Status => '+key)"></option>
                                </select>
                            </div>
                            <?php endif; ?>

                            <div class="col-md-3 col-sm-12">
                                <button type="button" class="btn btn-primary" v-on:click="loadPageData">Search</button>
                                <button type="button" class="btn btn-warning" v-on:click="resetFilter">Clear</button>
                            </div>
                        </div>
                        <table class="table datatable-basic dataTable no-footer datatable-scroll-y">
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th>Product Name</th>
                                    <th>SIM ICCID</th>
                                    <th>SIM Mobile NUMBER</th>
                                    <?php if($status !== "in_stock"): ?>
                                    <th>MNP ICCID</th>
                                    <th>MNP Mobile NUMBER</th>
                                    <?php endif; ?>
                                    <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                                    (in_array("StoreController::list", $userInfo->permission_lists))  ): ?>
                                        <th>Reseller</th>
                                    <?php else: ?>
                                        <th>Cost</th>
                                        <th>Sales Price</th>
                                        <th>Profit/(Loss)</th>
                                    <?php endif; ?>
                                    <?php if($status !== "in_stock"): ?>
                                    <th>Sold On</th>
                                    <th>Offer</th>
                                    <?php endif; ?>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- list section end -->
            </section>
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
                    enteredUserName:'',
                    enteredUserPassword:'',
                    masterTable:{},
                    windowHeight: 0,
                    windowWidth: 0,
                    scrollPosition:0,
                    fromPicker:"",
                    toPicker:"",
                    defaultMfsId:'',
                    formToDoUpdate:false,
                    orderStatus:{'':'All','approved':'Approved','pending':'Pending','rejected':'Rejected'},
                    page: 1,
                    tableFilter:{
                        store_id:"<?php echo $order_store_id; ?>",
                        status:"",
                        sales_status:"<?php echo $status; ?>",
                        order_id:"<?php echo $order_id; ?>",
                        product_id:"<?php echo (!empty($_GET["product"])?$_GET["product"]:"") ?>",
                        start_date:"",
                        end_date:"",
                        sim_card_iccid:"",
                        sim_card_mobile_number:""
                    },
                }
            },
            mounted() {
                theInstance = this;



                $('.do-me-select2-store').select2();
                $('.do-me-select2-store').on('select2:select', function (e) {
                    theInstance.tableFilter.store_id = $('.do-me-select2-store').select2('data')[0].id
                });


                this.masterTable = $('.dataTable').DataTable({
                        scrollCollapse: true,
                        "searching": false,
                        "info": false,
                        "paging": true,
                        "bLengthChange": false,
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
                                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && (in_array("StoreController::list", $userInfo->permission_lists))  ): ?>
                                    <?php if($status !== "in_stock"): ?>
                                        'targets': 10,
                                    <?php else: ?>
                                        'targets': 6,
                                    <?php endif; ?>
                                <?php else: ?>
                                        <?php if($status !== "in_stock"): ?>
                                        'targets': 12,
                                        <?php else: ?>
                                        'targets': 8,
                                        <?php endif; ?>
                                <?php endif; ?>
                                'searchable': false, 'orderable': false,
                                'render': function (data, type, full, meta)
                                {
                                    var info = $('<div/>').text(data).html();
                                    var permissions = info.split('||');

                                    return '<div class="btn-group-sm" style="white-space: nowrap">'+
                                        (permissions.includes("sale_button")?'<a href="/simcard/sale/'+info.split('||')[0]+'" class="btn btn-info">Sale</a>&nbsp;&nbsp;':"")+
                                        (permissions.includes("upload_button")?'<a href="/simcard/info/'+info.split('||')[0]+'?upload=1" class="btn btn-primary" data-id="'+info.split('||')[0]+'">Upload</a>&nbsp;&nbsp;':"")+
                                        (permissions.includes("view_button")?'<a href="/simcard/info/'+info.split('||')[0]+'" class="btn btn-info" data-id="'+info.split('||')[0]+'">View</a>&nbsp;&nbsp;':"")+
                                        (permissions.includes("activate_button")?'<a href="/simcard/info/'+info.split('||')[0]+'?activate=1" class="btn btn-warning" data-id="'+info.split('||')[0]+'">Activate</a>&nbsp;&nbsp;':"")+
                                        (permissions.includes("reject_button")?'<button type="button" class="btn btn-danger rejectBtn" data-id="'+info.split('||')[0]+'" data-title="'+info.split('||')[1]+'">Reject</button>':"")+
                                        (permissions.includes("unlock_button")?'<button type="button" class="btn btn-primary unlockBtn" data-id="'+info.split('||')[0]+'" data-title="'+info.split('||')[1]+'">Un-Lock</button>':"")+
                                        (permissions.includes("lock_button")?'<button type="button" class="btn btn-danger lockBtn" data-id="'+info.split('||')[0]+'" data-title="'+info.split('||')[1]+'">Lock</button>':"")+
                                        '</div>';
                                }
                            }
                        ],
                        "processing": true,
                        "serverSide": true,
                        "pageLength": 50,
                        "language": {
                            "emptyTable": "No Sim Card Found.",
                        },
                        "ajax": {
                            "url": '<?php echo env('APP_URL', ''); ?>/api/simcard/list',
                            "type": "POST",
                            'beforeSend': function (request) {
                                request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                            },
                            "data": function ( d )
                            {
                                d.store_id = theInstance.tableFilter.store_id
                                d.order_id = theInstance.tableFilter.order_id
                                d.status = theInstance.tableFilter.status
                                d.sales_status = theInstance.tableFilter.sales_status
                                d.product_id = theInstance.tableFilter.product_id
                                d.start_date = theInstance.tableFilter.start_date
                                d.end_date = theInstance.tableFilter.end_date
                                d.sim_card_iccid = theInstance.tableFilter.sim_card_iccid
                                d.sim_card_mobile_number = theInstance.tableFilter.sim_card_mobile_number
                            },
                            complete: function(data)
                            {
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
                dTableMount()
                {
                    $(".rejectBtn").click(function(e){
                        theInstance.reject($(this).data("id"), $(this).data("title"));
                    });

                    $(".unlockBtn").click(function(e){
                        theInstance.unlock($(this).data("id"), $(this).data("title"));
                    });

                    $(".lockBtn").click(function(e){
                        theInstance.lock($(this).data("id"), $(this).data("title"));
                    });

                    theInstance.fromPicker = $('.daterange-time-from').pickadate({
                        format: 'yyyy-mm-dd',
                        selectYears: true,
                        selectMonths: true,
                        onClose: function() {
                            theInstance.tableFilter.start_date = theInstance.fromPicker.pickadate('picker').get("value");
                        }
                    });

                    theInstance.toPicker = $('.daterange-time-to').pickadate({
                        format: 'yyyy-mm-dd',
                        selectYears: true,
                        selectMonths: true,
                        onClose: function() {
                            theInstance.tableFilter.end_date = theInstance.toPicker.pickadate('picker').get("value");
                        }
                    });
                },
                unlock(id)
                {
                    if(confirm("Are you sure about un-locking this?"))
                    {
                        axios.get("<?php echo env('APP_URL', ''); ?>/api/simcard/change_lock_status/"+id,
                            {
                                headers: {
                                    Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                },
                            })
                            .then(response => {
                                theInstance.loadPageData()
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
                    }
                },
                lock(id)
                {
                    if(confirm("Are you sure about locking this?"))
                    {
                        axios.get("<?php echo env('APP_URL', ''); ?>/api/simcard/change_lock_status/"+id,
                            {
                                headers: {
                                    Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                },
                            })
                            .then(response => {
                                theInstance.loadPageData()
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
                    }
                },
                reject(id, title) {
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_DANGER,
                        title: "Reject ("+title+")",
                        message: '<div class="form-group"><label class="control-label">Please add the cause of rejection</label><textarea id="full_cause" name="full_cause" class="form-control" tabindex="9"></textarea></div>',
                        closable: false,
                        draggable: false,
                        data:{id:id},
                        buttons: [{
                            label: "Cancel",
                            action: function(dialog) {
                                dialog.close();
                            }
                        }, {
                            label: "Reject",
                            cssClass:"btn-danger",
                            action: function(dialog)
                            {
                                let formData = new FormData();
                                formData.append("full_cause", $("#full_cause").val())

                                axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/reject/"+id, formData,
                                    {
                                        headers: {
                                            Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                        },
                                    }).then(response => {
                                    dialog.close();
                                    alert("Sim Card Rejected Successfully.")
                                    location.reload();
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
                                                    alert(error.response.data.message.join(","))
                                                    break;
                                            }
                                            this.page_message = ''
                                        }
                                    });
                            }
                        }]
                    });
                },
                loadPageData(){
                    this.masterTable.ajax.reload();
                },
                resetFilter(){
                    theInstance.tableFilter.store_id = ""
                    theInstance.tableFilter.status = ""
                    theInstance.tableFilter.product_id = ""
                    theInstance.tableFilter.start_date = ""
                    theInstance.tableFilter.end_date = ""
                    theInstance.tableFilter.sim_card_iccid = ""
                    theInstance.tableFilter.sim_card_mobile_number = ""
                    this.loadPageData();
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
                },
            }
        })
    });
</script>
<link href="https://cdn.jsdelivr.net/gh/GedMarc/bootstrap4-dialog/dist/css/bootstrap-dialog.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/gh/GedMarc/bootstrap4-dialog/dist/js/bootstrap-dialog.js"></script>
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>

@include('inc.footer', ['load_datatable_scripts' => true, 'load_pick_a_date_scripts' => true])
