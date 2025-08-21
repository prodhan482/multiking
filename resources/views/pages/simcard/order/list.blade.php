@include('inc.header', ['load_vuejs' => true])
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
                    <div class="card-header bg-white header-elements-inline">
                        <h6 class="card-title">Order List</h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::create_order", $userInfo->permission_lists)): ?>
                                <a href="#" data-toggle="modal" data-target="#modal_create_new_order" class="list-icons-item"><i class="icon-plus-circle2"></i> Add New Order</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" :style="{'padding':'0'}">
                        <div class="row p-1">
                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                            (in_array("StoreController::list", $userInfo->permission_lists))  ): ?>
                            <div class="col-md-4 col-sm-12">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">Reseller</span></div>
                                    <select class="form-control do-me-select2-mfs" v-model="tableFilter.store_id">
                                        <option value="">Select A Reseller</option>
                                        <?php foreach($storeList as $row): ?>
                                        <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                                <?php endif; ?>

                            <div class="col-md-4 col-sm-12">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">Status</span></div>
                                    <select class="form-control do-me-select2-mfs" v-model="tableFilter.status">
                                        <option v-for="(key, value) in orderStatus" v-bind:value="value" v-html="key"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <button type="button" class="btn btn-primary" v-on:click="loadPageData">Search</button>
                                <button type="button" class="btn btn-warning" v-on:click="resetFilter">Clear</button>
                            </div>
                        </div>

                        <table class="table dataTable no-footer">
                            <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Request for</th>
                                <th>Placed At</th>

                                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                                    (in_array("StoreController::list", $userInfo->permission_lists))  ): ?>
                                <th>Reseller</th>
                                    <?php endif; ?>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::create_order", $userInfo->permission_lists)): ?>
                    <div id="modal_create_new_order" class="modal fade" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Create Order</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                                    (in_array("StoreController::list", $userInfo->permission_lists))  ): ?>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Reseller</label>
                                        <div class="col-sm-8">
                                            <select class="form-control order-type store_id" v-model="newOrder.store_id">
                                                <?php if($userInfo->user_type == "super_admin"){ ?>
                                                <option value="">Select A Reseller</option>
                                                <?php } ?>
                                                <?php if($userInfo->user_type == "Reseller"){ ?>
                                                    <option value="<?php echo $userInfo->store_vendor_id; ?>">Self (<?php echo $userInfo->storeName; ?>)</option>
                                                <?php } ?>

                                                <?php foreach($storeList as $row): ?>
                                                <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Product</label>
                                        <div class="col-sm-8">
                                            <select class="form-control order-type" v-model="newOrder.product_id">
                                                <option value="">Select A Product</option>
                                                <?php foreach($productList as $row): ?>
                                                <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Quantity</label>
                                        <div class="col-sm-8">
                                            <input type="number" placeholder="Order Quantity" class="form-control" v-model="newOrder.quantity">
                                        </div>
                                    </div>
                                    <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
                                        <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                                        <span class="font-weight-semibold">Error!</span> <span v-html="formErrorMessage"></span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-link" data-dismiss="modal">Close</button>
                                    <button class="btn btn-primary" v-on:click="createOrder()">Place Order</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

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
                    defaultOrderId:'',
                    formToDoUpdate:false,
                    page: 1,
                    orderStatus:{'':'All','approved':'Approved','pending':'Pending','rejected':'Rejected'},
                    tableFilter:{
                        store_id:"",
                        status:"",
                    },
                    newOrder:{
                        <?php if($userInfo->user_type != "super_admin" && !in_array("StoreController::list", $userInfo->permission_lists)): ?>
                        store_id:'<?php echo $userInfo->store_vendor_id; ?>',
                        <?php else: ?>
                        <?php if($userInfo->user_type == "Reseller"){ ?>
                        store_id:'<?php echo $userInfo->store_vendor_id; ?>',
                        <?php } else { ?>
                        store_id:'<?php echo $userInfo->store_vendor_id; ?>',
                        <?php } ?>
                        <?php endif; ?>
                        product_id:'',
                        quantity:'',
                    }
                }
            },
            mounted() {
                theInstance = this;

                $('#modal_create_new_order').on('shown.bs.modal', function (e) {
                    $('.store_id').select2({'width':'100%'});
                    $('.store_id').on('select2:select', function (e) {
                        theInstance.newOrder.store_id = $('.store_id').select2('data')[0].id
                    });
                })

                this.masterTable = $('.dataTable').DataTable({
                        scrollCollapse: true,
                        "searching": false,
                        "info": false,
                        "paging": true,
                        "lengthChange": false,
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
                        "createdRow": function( row, data, dataIndex){
                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && (in_array("StoreController::list", $userInfo->permission_lists))  ): ?>
                            $(row).addClass(data[4]);
                            <?php else: ?>
                            $(row).addClass(data[3]);
                            <?php endif; ?>
                        },
                        "columnDefs": [
                            {
                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                            (in_array("StoreController::list", $userInfo->permission_lists))  ): ?>
                            'targets': 5,
                            <?php else: ?>
                            'targets': 4,
                            <?php endif; ?>

                                'searchable': false, 'orderable': false,
                                'render': function (data, type, full, meta)
                                {
                                    var info = $('<div/>').text(data).html();
                                    var permissions = info.split('||');

                                    return '<div class="btn-group-sm">'+
                                        (permissions.includes("remove_button")?'<button type="button" class="btn btn-danger removeOrder" data-id="'+info.split('|')[0]+'">Remove</button>':"")+
                                        (permissions.includes("approve_button")?'<a href="/simcard/orders/approve/'+info.split('|')[0]+'" class="btn btn-success">Approve</a>':"")+
                                        (permissions.includes("reject_button")?'<button type="button" class="btn btn-danger rejectOrder" data-id="'+info.split('|')[0]+'">Reject</button>':"")+
                                        (permissions.includes("view_stocked")?'<a href="/simcard/list/in_stock/'+info.split('|')[0]+'" class="btn btn-primary">View Stocked</a>':"")+
                                        (permissions.includes("view_sold")?'<a href="/simcard/list/sold/'+info.split('|')[0]+'" class="btn btn-success">View Sold</a>':"")+
                                        '</div>';
                                }
                            }
                        ],
                        "processing": true,
                        "serverSide": true,
                        "pageLength": 50,
                        "language": {
                            "emptyTable": "No Order Found.",
                        },
                        "ajax": {
                            "url": '<?php echo env('APP_URL', ''); ?>/api/simcard/order/list',
                            "type": "POST",
                            'beforeSend': function (request) {
                                request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                            },
                            "data": function ( d )
                            {
                                d.store_id = theInstance.tableFilter.store_id
                                d.status = theInstance.tableFilter.status
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
                    $(".removeOrder").click(function(e){
                        if(confirm("Are you Sure?")) theInstance.removeOrder($(this).data("id"));
                    });
                    $(".rejectOrder").click(function(e){
                        if(confirm("Are you Sure?")) theInstance.rejectOrder($(this).data("id"));
                    });
                },
                loadPageData(){
                    theInstance.newOrder.store_id = ""
                    theInstance.newOrder.product_id = ""
                    theInstance.newOrder.quantity = ""
                    this.masterTable.ajax.reload();
                },
                resetFilter(){
                    theInstance.tableFilter.store_id = ""
                    theInstance.tableFilter.status = ""
                    this.loadPageData();
                },
                removeOrder(order_id)
                {
                    axios.get("<?php echo env('APP_URL', ''); ?>/api/simcard/order/remove/"+order_id, {
                        headers: {Authorization: '<?php echo session('AuthorizationToken'); ?>'}
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
                                }
                            }
                        });
                },
                rejectOrder(order_id)
                {
                    axios.get("<?php echo env('APP_URL', ''); ?>/api/simcard/order/reject/"+order_id, {
                        headers: {Authorization: '<?php echo session('AuthorizationToken'); ?>'}
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
                                }
                            }
                        });
                },
                createOrder(){
                    $('#modal_create_new_order').modal('hide');

                    let formData = new FormData();

                    Object.keys(this.newOrder).forEach(key => {
                        formData.append(key, this.newOrder[key])
                    });

                    axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/order/create", formData,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        }).then(response => {
                        this.page_message = 'Order Created Successfully. Reloading Order Table....'
                        this.loadPageData();
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
                                        this.formErrorMessage = error.response.data.message.join(",")
                                        break;
                                }
                                $('#modal_create_new_order').modal('show');
                                this.page_message = ''
                            }
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
                },
            }
        })
    });
</script>
<style>
    .Pending > td {
        background-color: #ffffff !important;
        border-bottom: black solid 1px;
    }
    .Approved > td {
        background-color: rgba(75, 255, 0, 0.13) !important;
    }
    .Rejected > td {
        background-color: rgba(255, 9, 0, 0.13) !important;
    }
</style>
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>
@include('inc.footer', ['load_datatable_scripts' => true])
