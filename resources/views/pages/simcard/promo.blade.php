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
                    <div class="row" style="padding: 1.5rem 1.5rem;">
                        <div class="col-sm-4 col-xs-12">
                            <h6 class="card-title">Sim Card Promotions</h6>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                            <select class="form-control order-type" v-model="tableFilter.product_id" @change="loadPageData">
                                <option value="">Select A Product To Search</option>
                                <?php foreach($productList as $row): ?>
                                <option value="<?php echo $row->id; ?>">Product: <?php echo $row->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::create_promo", $userInfo->permission_lists)): ?>
                            <a href="#" data-toggle="modal" data-target="#modal_create_new_simcard_offer" class="btn btn-primary float-right"><i class="icon-plus-circle2"></i> Add New Sim Card Promotion</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body" :style="{'padding':'0'}">
                        <table class="table datatable-basic dataTable no-footer datatable-scroll-y">
                            <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Product Name</th>
                                <th>Bonus</th>
                                <th>Default Reseller Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::create_promo", $userInfo->permission_lists)): ?>
                    <div id="modal_create_new_simcard_offer" class="modal fade" tabindex="-1">
                        <div class="modal-dialog model-lg">
                            <div class="modal-content model-lg">
                                <div class="modal-header">
                                    <h5 class="modal-title">Create Sim Card Promotion</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Title <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Sim Card Promotion Name" class="form-control" v-model="newSimCardPromo.title">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Product <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-control do-me-select2" v-model="newSimCardPromo.product_id">
                                                <option value="">Select A Product</option>
                                                <?php foreach($productList as $row): ?>
                                                <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Bonus <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="number" step="any" class="form-control" placeholder="Sim Card Promotion Bonus" value="" v-model="newSimCardPromo.reseller_bonus">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Reseller Price <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="number" step="any" class="form-control" placeholder="Sim Card Promotion Bonus" value="" v-model="newSimCardPromo.reseller_price">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Description</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" v-model="newSimCardPromo.description"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">File</label>
                                        <div class="col-sm-8">
                                            <input type="file" class="form-control h-auto" ref="simCardPromoFile" accept="image/png, image/gif, image/jpeg,application/pdf" v-on:change="simCardPromoCreateImageSelected">
                                        </div>
                                    </div>
                                    <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
                                        <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                                        <span class="font-weight-semibold">Error!</span> <span v-html="formErrorMessage"></span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-link" data-dismiss="modal">Close</button>
                                    <button class="btn btn-primary" v-on:click="createSimCardPromo()">Save</button>
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
                    newSimCardPromo:{
                        title:"",
                        product_id:"",
                        reseller_bonus:"",
                        reseller_price:"",
                        description:"",
                        file:""
                    },
                    tableFilter:{
                        product_id:""
                    }
                }
            },
            mounted() {
                theInstance = this;

                $('.do-me-select2').select2({'width':'100%'});
                $('.do-me-select2').on('select2:select', function (e) {
                    theInstance.newSimCardPromo.product_id = $('.do-me-select2').select2('data')[0].id
                });

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
                        "columnDefs": [
                            {
                                'targets': 6,'searchable': false, 'orderable': false, 'width':'10%',
                                'render': function (data, type, full, meta)
                                {
                                    var info = $('<div/>').text(data).html();

                                    if(info == "disable") return "<span class='badge rounded-pill bg-danger'>Disabled</span>";
                                    if(info == "enable") return "<span class='badge rounded-pill bg-success'>Enabled</span>";

                                    return '';
                                }
                            },
                            {
                                'targets': 7,'searchable': false, 'orderable': false,
                                'render': function (data, type, full, meta)
                                {
                                    var info = $('<div/>').text(data).html();

                                    return '<div class="btn-group-sm">'+
                                        '<button type="button" class="btn btn-'+(info.split('||')[1] == "enable"?"success":"danger")+' changeStatus" data-id="'+info.split('||')[0]+'" data-status="'+info.split('||')[1]+'">Change Status</button>&nbsp;&nbsp;'+
                                        '<a href="/simcard/update_promo/'+info.split('||')[0]+'" class="btn btn-warning updateProduct" data-id="'+info.split('||')[0]+'">Update</a>'+
                                        '</div>';
                                }
                            }
                        ],
                        "processing": true,
                        "serverSide": true,
                        "pageLength": 500,
                        "language": {
                            "emptyTable": "No Sim Card Promotion Found.",
                        },
                        "ajax": {
                            "url": '<?php echo env('APP_URL', ''); ?>/api/simcard/promo/list',
                            "type": "POST",
                            'beforeSend': function (request) {
                                request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                            },
                            "data": function ( d )
                            {
                                d.product_id = theInstance.tableFilter.product_id
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
                loadPageData() {
                    theInstance.masterTable.ajax.reload();
                },
                dTableMount()
                {
                    $(".changeStatus").click(function(e){
                        if(confirm("Are you Sure?")) theInstance.changeStatus($(this).data("id"), $(this).data("status"));
                    });

                    /*
                    $(".updateProduct").click(function(e){
                        theInstance.setDefaultProductId($(this).data("id"));
                    });
                    */
                },
                simCardPromoCreateImageSelected()
                {
                    theInstance.newSimCardPromo.file = this.$refs.simCardPromoFile.files[0];
                },
                loadPageData(){
                    this.masterTable.ajax.reload();
                },
                resetFilter(){
                    this.loadPageData();
                },
                changeStatus(id, status){
                    axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/promo/change_status/"+id, {status:status},{
                        headers: {Authorization: '<?php echo session('AuthorizationToken'); ?>'}
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
                                }
                            }
                        });
                },
                createSimCardPromo(){
                    $('#modal_create_new_simcard_offer').modal('hide');

                    let formData = new FormData();

                    Object.keys(this.newSimCardPromo).forEach(key => {
                        formData.append(key, this.newSimCardPromo[key])
                    });

                    axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/promo/create", formData,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        }).then(response => {
                        this.page_message = 'Sim Card Promotion Created Successfully. Reloading Sim Card Promotion Table....'
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
                                        alert(error.response.data.message.join(","))
                                        break;
                                }
                                $('#modal_create_new_simcard_offer').modal('show');
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
    .select-mfs2 + .select2-container{
        width: 100%;
    }
</style>
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>
@include('inc.footer', ['load_datatable_scripts' => true])
