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
                        <h6 class="card-title">Mfs List</h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a href="#" data-toggle="modal" data-target="#modal_create_new_mfs" class="list-icons-item"><i class="icon-plus-circle2"></i> Add New MFS</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" :style="{'padding':'0'}">
                        <table class="table datatable-basic dataTable no-footer datatable-scroll-y">
                            <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Logo</th>
                                <th>MFS Name</th>
                                <th>Default Commission (%)</th>
                                <th>Default Charge (%)</th>
                                <th>Created On</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div id="modal_create_new_mfs" class="modal fade" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Create MFS</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">MFS Title</label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Mfs Title" class="form-control" v-model="newMfs.mfs_name">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">MFS Type</label>
                                        <div class="col-sm-8">
                                            <select class="form-control mfs-type" v-model="newMfs.mfs_type">
                                                <option value="mobile_recharge">Mobile Recharge</option>
                                                <option value="financial_transaction">Financial Transaction</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Commission <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="In Percentage" value="2.5" v-model="newMfs.default_commission">
                                                <span class="input-group-append">
                        <span class="input-group-text">%</span>
                      </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Charge <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="In Percentage" value="2.5" v-model="newMfs.default_charge">
                                                <span class="input-group-append">
                        <span class="input-group-text">%</span>
                      </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">MFS Logo</label>
                                        <div class="col-sm-8">
                                            <input type="file" class="form-control h-auto" ref="mfsCreateFile" accept="image/*" v-on:change="createImageSelected">
                                        </div>
                                    </div>
                                    <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
                                        <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                                        <span class="font-weight-semibold">Error!</span> <span v-html="formErrorMessage"></span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-link" data-dismiss="modal">Close</button>
                                    <button class="btn btn-primary" v-on:click="createUpdateMfs(false)">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="modal_update_existing_mfs" class="modal fade" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="icon-user-plus mr-2"></i> <span v-html='("Update MFS "+newMfs.mfs_name)'></span></h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">MFS Title</label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Mfs Title" class="form-control" v-model="newMfs.mfs_name">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">MFS Type</label>
                                        <div class="col-sm-8">
                                            <select class="form-control mfs-type" v-model="newMfs.mfs_type">
                                                <option value="mobile_recharge">Mobile Recharge</option>
                                                <option value="financial_transaction">Financial Transaction</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Commission <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="In Percentage" value="2.5" v-model="newMfs.default_commission">
                                                <span class="input-group-append">
                        <span class="input-group-text">%</span>
                      </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Charge <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="In Percentage" value="2.5" v-model="newMfs.default_charge">
                                                <span class="input-group-append">
                        <span class="input-group-text">%</span>
                      </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
                                        <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                                        <span class="font-weight-semibold">Error!</span> <span v-html="formErrorMessage"></span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-link" data-dismiss="modal">Close</button>
                                    <button class="btn btn-primary" v-on:click="createUpdateMfs(true)">Save</button>
                                </div>
                            </div>
                        </div>
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
                    defaultMfsId:'',
                    formToDoUpdate:false,
                    page: 1,
                    newMfs:{
                        mfs_name:'',
                        mfs_type:'mobile_recharge',
                        default_commission:'',
                        default_charge:'',
                        file:'',
                        user_id:''
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
                                'targets': 6,'searchable': false, 'orderable': false, 'width':'10%',
                                'render': function (data, type, full, meta)
                                {
                                    var info = $('<div/>').text(data).html();

                                    return '<div class="btn-group-sm">'+
                                        '<button type="button" class="btn btn-warning updateMfs" data-id="'+info.split('|')[0]+'">Update</button>'+
                                        '</div>';
                                }
                            }
                        ],
                        "processing": true,
                        "serverSide": true,
                        "pageLength": 500,
                        "language": {
                            "emptyTable": "No Mfs Found.",
                        },
                        "ajax": {
                            "url": '<?php echo env('APP_URL', ''); ?>/api/mfs',
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
                    $(".updateMfs").click(function(e){
                        theInstance.setDefaultMfsId($(this).data("id"));
                    });
                },
                loadPageData(){
                    this.masterTable.ajax.reload();
                },
                createImageSelected()
                {
                    this.newMfs.file = this.$refs.mfsCreateFile.files[0];
                },
                resetFilter(){
                    this.loadPageData();
                },
                setDefaultMfsId(uid){
                    this.page_message = "Gathering STore Details. Please wait...."
                    this.defaultMfsId = uid;
                    this.formToDoUpdate = true;
                    axios.get("<?php echo env('APP_URL', ''); ?>/api/mfs/"+uid, {
                        headers: {Authorization: '<?php echo session('AuthorizationToken'); ?>'}
                    })
                        .then(response => {
                            this.page_message = "";
                            this.newMfs.mfs_name = response.data.data.mfs_name;
                            this.newMfs.mfs_type = response.data.data.mfs_type;
                            this.newMfs.default_commission = response.data.data.default_commission;
                            this.newMfs.default_charge = response.data.data.default_charge;
                            $('#modal_update_existing_mfs').modal('show');
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
                createUpdateMfs(update){
                    if(update)
                    {
                        if(confirm("Are you sure?"))
                        {
                            $('#modal_update_existing_mfs').modal('hide');

                            this.page_message = 'Updating Mfs Status...';

                            axios.patch("<?php echo env('APP_URL', ''); ?>/api/mfs/"+this.defaultMfsId, this.newMfs,{
                                headers: {
                                    Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                }
                            })
                                .then(response => {
                                    this.page_message = 'Mfs Updated Successfully. Reloading Table....'
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
                        $('#modal_create_new_mfs').modal('hide');

                        let formData = new FormData();

                        Object.keys(this.newMfs).forEach(key => {
                            formData.append(key, this.newMfs[key])
                        });

                        axios.post("<?php echo env('APP_URL', ''); ?>/api/mfs_c", formData,
                            {
                                headers: {
                                    Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                },
                            }).then(response => {
                            this.page_message = 'Mfs Created Successfully. Reloading Mfs Table....'
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
                                    $('#modal_create_new_mfs').modal('show');
                                    this.page_message = ''
                                }
                            });

                        this.page_message = 'Please Wait. We Are Creating Mfs .....'
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
