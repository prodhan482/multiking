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
                        <h6 class="card-title">Product List</h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Inventory::create_product", $userInfo->permission_lists)): ?>
                                <a href="#" data-toggle="modal" data-target="#modal_create_new_product" class="list-icons-item"><i class="icon-plus-circle2"></i> Add New Product</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" :style="{'padding':'0'}">
                        <table class="table datatable-basic dataTable no-footer datatable-scroll-y">
                            <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Current Price</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Inventory::create_product", $userInfo->permission_lists)): ?>
                    <div id="modal_create_new_product" class="modal fade" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Create Product</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Name <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Product Name" class="form-control" v-model="newProduct.name">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Product Type <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-control mfs-type" v-model="newProduct.type">
                                                <option value="sim_card">Sim Card</option>
                                                <option value="mobile_phone_device">Mobile Device</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Price <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="number" step="any" class="form-control" placeholder="Product Price" value="" v-model="newProduct.price">
                                        </div>
                                    </div>
                                    <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
                                        <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                                        <span class="font-weight-semibold">Error!</span> <span v-html="formErrorMessage"></span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-link" data-dismiss="modal">Close</button>
                                    <button class="btn btn-primary" v-on:click="createUpdateProduct(false)">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Inventory::update_product", $userInfo->permission_lists)): ?>
                    <div id="modal_update_existing_product" class="modal fade" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Update Product</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Name <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Product Name" class="form-control" v-model="newProduct.name">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Product Type <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-control mfs-type" v-model="newProduct.type" disabled>
                                                <option value="sim_card">Sim Card</option>
                                                <option value="mobile_phone_device">Mobile Device</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Price <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="number" step="any" class="form-control" placeholder="Product Price" value="" v-model="newProduct.price">
                                        </div>
                                    </div>
                                    <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
                                        <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                                        <span class="font-weight-semibold">Error!</span> <span v-html="formErrorMessage"></span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-link" data-dismiss="modal">Close</button>
                                    <button class="btn btn-primary" v-on:click="createUpdateProduct(true)">Save</button>
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
                    defaultProductId:'',
                    formToDoUpdate:false,
                    page: 1,
                    newProduct:{
                        name:'',
                        type:'sim_card',
                        price:''
                    }
                }
            },
            mounted() {
                theInstance = this;
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
                        "columnDefs": [
                            {
                                'targets': 5,'searchable': false, 'orderable': false, 'width':'10%',
                                'render': function (data, type, full, meta)
                                {
                                    var info = $('<div/>').text(data).html();

                                    return '<div class="btn-group-sm">'+
                                        <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Inventory::update_product", $userInfo->permission_lists)): ?>
                                        '<button type="button" class="btn btn-warning updateProduct" data-id="'+info.split('|')[0]+'">Update</button>'+
                                        <?php endif; ?>
                                        '</div>';
                                }
                            }
                        ],
                        "processing": true,
                        "serverSide": true,
                        "pageLength": 500,
                        "language": {
                            "emptyTable": "No Product Found.",
                        },
                        "ajax": {
                            "url": '<?php echo env('APP_URL', ''); ?>/api/inventory/product/list',
                            "type": "POST",
                            'beforeSend': function (request) {
                                request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                            },
                            "data": function ( d )
                            {
                                d = theInstance.tableFilter
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
                    $(".updateProduct").click(function(e){
                        theInstance.setDefaultProductId($(this).data("id"));
                    });
                },
                loadPageData(){
                    this.newProduct.name = ""
                    this.newProduct.price = ""
                    this.newProduct.type = "sim_card"
                    this.masterTable.ajax.reload();
                },
                resetFilter(){
                    this.loadPageData();
                },
                setDefaultProductId(uid){
                    this.page_message = "Gathering Store Details. Please wait...."
                    this.defaultProductId = uid;
                    this.formToDoUpdate = true;
                    axios.get("<?php echo env('APP_URL', ''); ?>/api/inventory/product/info/"+uid, {
                        headers: {Authorization: '<?php echo session('AuthorizationToken'); ?>'}
                    })
                        .then(response => {
                            this.page_message = "";
                            this.newProduct.name = response.data.data.name;
                            this.newProduct.price = response.data.data.price;
                            this.newProduct.type = response.data.data.type;
                            $('#modal_update_existing_product').modal('show');
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
                createUpdateProduct(update){
                    if(update)
                    {
                        if(confirm("Are you sure?"))
                        {
                            $('#modal_update_existing_product').modal('hide');

                            this.page_message = 'Updating Product Status...';

                            axios.put("<?php echo env('APP_URL', ''); ?>/api/inventory/product/update/"+this.defaultProductId, this.newProduct,{
                                headers: {
                                    Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                }
                            })
                                .then(response => {
                                    this.page_message = 'Product Updated Successfully. Reloading Table....'
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
                                });
                        }
                    } else {
                        $('#modal_create_new_product').modal('hide');

                        let formData = new FormData();

                        Object.keys(this.newProduct).forEach(key => {
                            formData.append(key, this.newProduct[key])
                        });

                        axios.post("<?php echo env('APP_URL', ''); ?>/api/inventory/product/create", formData,
                            {
                                headers: {
                                    Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                },
                            }).then(response => {
                            this.page_message = 'Product Created Successfully. Reloading Product Table....'
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
                                    $('#modal_create_new_product').modal('show');
                                    this.page_message = ''
                                }
                            });

                        this.page_message = 'Please Wait. We Are Creating Product .....'
                    }
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
@include('inc.footer', ['load_datatable_scripts' => true])
