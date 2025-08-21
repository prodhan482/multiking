@include('inc.header')
@include('inc.menu')
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>

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
                    <div class="card-datatable table-responsive pt-0">
                        <table class="table table-xs dataTable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>User Name</th>
                                <th>Reseller/Vendor Name</th>
                                <th>Parent</th>
                                <th>Created On</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- Modal to add new user starts-->
                    <div class="modal modal-slide-in new-user-modal fade" id="modals-slide-in">
                        <div class="modal-dialog">
                            <form class="add-new-user modal-content pt-0">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
                                <div class="modal-header mb-1">
                                    <h5 class="modal-title" id="exampleModalLabel">New User</h5>
                                </div>
                                <div class="modal-body flex-grow-1">
                                    <div class="form-group">
                                        <label class="form-label" for="basic-icon-default-fullname">Full Name</label>
                                        <input type="text" class="form-control dt-full-name" id="basic-icon-default-fullname" placeholder="John Doe" name="user-fullname" aria-label="John Doe" aria-describedby="basic-icon-default-fullname2" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="basic-icon-default-uname">Username</label>
                                        <input type="text" id="basic-icon-default-uname" class="form-control dt-uname" placeholder="Web Developer" aria-label="jdoe1" aria-describedby="basic-icon-default-uname2" name="user-name" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="basic-icon-default-email">Email</label>
                                        <input type="text" id="basic-icon-default-email" class="form-control dt-email" placeholder="john.doe@example.com" aria-label="john.doe@example.com" aria-describedby="basic-icon-default-email2" name="user-email" />
                                        <small class="form-text text-muted"> You can use letters, numbers & periods </small>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="user-role">User Role</label>
                                        <select id="user-role" class="form-control">
                                            <option value="subscriber">Subscriber</option>
                                            <option value="editor">Editor</option>
                                            <option value="maintainer">Maintainer</option>
                                            <option value="author">Author</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="form-label" for="user-plan">Select Plan</label>
                                        <select id="user-plan" class="form-control">
                                            <option value="basic">Basic</option>
                                            <option value="enterprise">Enterprise</option>
                                            <option value="company">Company</option>
                                            <option value="team">Team</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary mr-1 data-submit">Submit</button>
                                    <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Modal to add new user Ends-->
                </div>
                <!-- list section end -->
                <div id="modal_iconified" class="modal fade" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fa fa-user-plus"></i> Create User</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                @if ($message = Session::get('error'))
                                    <div class="alert alert-warning alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                                        <span class="font-weight-semibold">Warning!</span> {{$message}}
                                    </div>
                                @endif
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">User Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" placeholder="Type User Name" class="form-control" id="enteredUserName">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">User Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" placeholder="Type User Password" class="form-control" id="enteredUserPassword">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" data-dismiss="modal">Close</button>
                                <button type="button" onclick="theInstance.createUser()" class="btn btn-danger">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="modal_user_password_change" class="modal fade" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update Password</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                @if ($message = Session::get('error'))
                                    <div class="alert alert-warning alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                                        <span class="font-weight-semibold">Warning!</span> {{$message}}
                                    </div>
                                @endif
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">New Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" placeholder="Type New Password" class="form-control" id="enteredExistingUserChangePassword">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" data-dismiss="modal">Close</button>
                                <button class="btn btn-danger" onclick="theInstance.resetPassword()">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>

    var theInstance = {
        defaultUserId:'',
        dTableMount:function () {
            $(".removeBtt").click(function(e){
                theInstance.removeUser($(this).data("id"));
            });

            $(".changePassword").click(function(e){
                theInstance.setDefaultUserId($(this).data("id"));
                $('#modal_user_password_change').modal('show');
            });
        },
        setDefaultUserId(uid){
            theInstance.defaultUserId = uid;
        },
        resetPassword(){
            if($("#enteredExistingUserChangePassword").val().length > 1)
            {
                $('#modal_user_password_change').modal('hide');
                jQuery.ajax({
                    type: "PATCH",
                    beforeSend: function(request) {
                        request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                    },
                    url: "<?php echo env('APP_URL', ''); ?>/api/user/"+theInstance.defaultUserId,
                    dataType: 'json',
                    data: JSON.stringify({
                        password: $("#enteredExistingUserChangePassword").val()
                    }),
                    statusCode: {
                        200: function() {
                            location.reload()
                        },
                        406: function() {
                            location.reload()
                        },
                        401: function() {
                            location.reload()
                        }
                    }
                });
            }
        },
        createUser(){
            jQuery('#modal_iconified').modal('hide');

            jQuery.ajax({
                type: "POST",
                beforeSend: function(request) {
                    request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                },
                url: "<?php echo env('APP_URL', ''); ?>/api/user",
                dataType: 'json',
                data: JSON.stringify({
                    user_name: $("#enteredUserName").val(),
                    password: $("#enteredUserPassword").val()
                }),
                statusCode: {
                    200: function() {
                        location.reload()
                    },
                    406: function() {
                        location.reload()
                    },
                    401: function() {
                        location.reload()
                    }
                }
            });
        },
    };

    jQuery(function(){
        jQuery('.dataTable').DataTable({
                //scrollX: true,
                //scrollY: (this.windowHeight - 260)+'px',
                scrollCollapse: true,
                //"searching": false,
                //"info": false,
                //"paging": false,
                "ordering": false,
                "preDrawCallback": function(settings)
                {
                    theInstance.scrollPosition = jQuery(".dataTables_scrollBody").scrollTop();
                },
                "drawCallback": function(settings)
                {
                    //var api = this.api();
                    $(".dataTables_scrollBody").scrollTop(theInstance.scrollPosition);
                    //theInstance.dTableMount();
                    theInstance.page_message = ''
                },
                "columnDefs": [
                    {
                        'targets': 5,'searchable': false, 'orderable': false, 'width':'10%',
                        'render': function (data, type, full, meta)
                        {
                            var info = $('<div/>').text(data).html();

                            var dta = info.split('|');

                            return '<div class="btn-group btn-group-sm">'+
                                ((dta[0].length>0 && dta[1] && (dta[1] !=='store' && dta[1] !=='vendor'))?'<button type="button" class="btn btn-danger removeBtt" data-id="'+dta[0]+'">Remove</button>':'')+
                                (dta[0].length>0?'<button type="button" class="btn btn-primary changePassword" data-id="'+dta[0]+'">Change Password</button>':'')+
                                ((dta[0].length>0 && dta[1] && (dta[1] !=='store' && dta[1] !=='vendor'))?'<a class="btn btn-warning changePermission" href="/user/'+dta[0]+'/user_permission">Change Permission</a>':'')+
                                '</div>';

                        }
                    }
                ],
                dom:
                    '<"row d-flex justify-content-between align-items-center m-1"' +
                    '<"col-lg-6 d-flex align-items-center"l<"dt-action-buttons text-xl-right text-lg-left text-lg-right text-left "B>>' +
                    '<"col-lg-6 d-flex align-items-center justify-content-lg-end flex-lg-nowrap flex-wrap pr-lg-1 p-0"f<"invoice_status ml-sm-2">>' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                buttons: [
                    {
                        text: 'Add Manager User',
                        className: 'btn btn-danger btn-add-record ml-2',
                        action: function (e, dt, button, config) {
                            $('#modal_iconified').modal('show')
                        }
                    }
                ],
                "processing": true,
                "serverSide": true,
                "pageLength": 100,
                "language": {
                    "emptyTable": "No Adjustment History Data Found.",
                },
                "ajax": {
                    "url": '<?php echo env('APP_URL', ''); ?>/api/user',
                    "type": "GET",
                    'beforeSend': function (request) {
                        request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                    },
                    "data": function ( d )
                    {
                        //d = theInstance.tableFilter
                    },
                    complete: function(data)
                    {
                        theInstance.dTableMount();
                    },
                    error: function (xhr, error, thrown)
                    {
                        console.log("Error");
                    }
                },
            }
        );
    })
</script>
@include('inc.footer', ['load_datatable_scripts' => true])
