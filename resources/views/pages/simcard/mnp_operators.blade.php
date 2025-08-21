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
                        <h6 class="card-title">MNP Operators</h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::create_mnp_operator", $userInfo->permission_lists)): ?>
                                <a href="#" data-toggle="modal" data-target="#modal_create_new_product" class="list-icons-item"><i class="icon-plus-circle2"></i> Add New MNP Operator</a>
                                <?php endif; ?>
                            </div>
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
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::create_mnp_operator", $userInfo->permission_lists)): ?>
                    <div id="modal_create_new_product" class="modal fade" tabindex="-1">
                        <div class="modal-dialog model-lg">
                            <div class="modal-content model-lg">
                                <div class="modal-header">
                                    <h5 class="modal-title">Create MNP Operator</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Title <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="MNP Operator Name" class="form-control" v-model="newMnpOperator.title">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Product <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-control do-me-select2" multiple="multiple">
                                                <?php foreach($productList as $row): ?>
                                                <option selected value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Bonus <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="number" step="any" class="form-control" placeholder="MNP Operator Bonus" value="" v-model="newMnpOperator.reseller_bonus">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Description</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" v-model="newMnpOperator.description"></textarea>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
                                        <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                                        <span class="font-weight-semibold">Error!</span> <span v-html="formErrorMessage"></span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-link" data-dismiss="modal">Close</button>
                                    <button class="btn btn-primary" v-on:click="createMnpOperator()">Save</button>
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
                    newMnpOperator:{
                        title:"",
                        product_id:[],
                        reseller_bonus:"",
                        description:""
                    }
                }
            },
            mounted() {
                theInstance = this;

                $('#modal_create_new_product').on('shown.bs.modal', function (e) {

                    $('.do-me-select2').select2({'width':'100%', placeholder: "Select a Product",
                        allowClear: false
                    });
                    $('.do-me-select2').on('select2:select', function (e) {
                        for (const position in $('.do-me-select2').select2('data')) {
                            var info = {}

                            info.id = $('.do-me-select2').select2('data')[position].id
                            info.text = $('.do-me-select2').select2('data')[position].text

                            theInstance.newMnpOperator.product_id.push(info)
                        }
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
                        "columnDefs": [
                            {
                                'targets': 5,'searchable': false, 'orderable': false, 'width':'10%',
                                'render': function (data, type, full, meta)
                                {
                                    var info = $('<div/>').text(data).html();

                                    if(info == "disable") return "<span class='badge rounded-pill bg-danger'>Disabled</span>";
                                    if(info == "enable") return "<span class='badge rounded-pill bg-success'>Enabled</span>";

                                    return '';
                                }
                            },
                            {
                                'targets': 6,'searchable': false, 'orderable': false,
                                'render': function (data, type, full, meta)
                                {
                                    var info = $('<div/>').text(data).html();

                                    return '<div class="btn-group-sm">'+
                                        <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::create_mnp_operator", $userInfo->permission_lists)): ?>
                                            '<button type="button" class="btn btn-'+(info.split('||')[1] == "enable"?"success":"danger")+' changeStatus" data-id="'+info.split('||')[0]+'" data-status="'+info.split('||')[1]+'">Change Status</button>&nbsp;&nbsp;'+
                                            '<a href="/simcard/update_mnp_operator/'+info.split('||')[0]+'" class="btn btn-warning updateProduct" data-id="'+info.split('||')[0]+'">Update</a>'+
                                        <?php endif; ?>
                                            '</div>';
                                }
                            }
                        ],
                        "processing": true,
                        "serverSide": true,
                        "pageLength": 500,
                        "language": {
                            "emptyTable": "No MNP Operator Found.",
                        },
                        "ajax": {
                            "url": '<?php echo env('APP_URL', ''); ?>/api/simcard/mnp_operators/list',
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
                    $(".changeStatus").click(function(e){
                        if(confirm("Are you Sure?")) theInstance.changeStatus($(this).data("id"), $(this).data("status"));
                    });

                    /*$(".updateProduct").click(function(e){
                        theInstance.setDefaultProductId($(this).data("id"));
                    });
                    */
                },
                loadPageData(){
                    this.masterTable.ajax.reload();
                },
                resetFilter(){
                    this.loadPageData();
                },
                changeStatus(id, status){
                    axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/mnp_operators/change_status/"+id, {status:status},{
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
                createMnpOperator(){
                    $('#modal_create_new_product').modal('hide');

                    let formData = new FormData();

                    Object.keys(this.newMnpOperator).forEach(key => {
                        if(key == "product_id")
                        {
                            formData.append(key, JSON.stringify(this.newMnpOperator[key]))
                        }
                        else
                        {
                            formData.append(key, this.newMnpOperator[key])
                        }
                    });

                    axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/mnp_operators/create", formData,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        }).then(response => {
                        this.page_message = 'MNP Operator Created Successfully. Reloading MNP Operator Table....'
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
                                $('#modal_create_new_product').modal('show');
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
