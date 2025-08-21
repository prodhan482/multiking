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
                <div class="card">
                    <div class="card-header pb-0">
                        <h4 class="card-title">Sim Card Information</h4>
                        <div class="header-elements">
                            <div class="list-icons  btn-group-sm">
                                {{--<button type="button" class="btn btn-primary btn-sm" v-on:click="save()">Print</button>--}}



                                <?php if(in_array("Simcard::activate", $userInfo->permission_lists) && ($sim_card_info->status == "pending" || $sim_card_info->status == "rejected")  && $sim_card_info->locked == "1"){ ?>
                                <button type="button" class="btn btn-success" v-on:click="activateThis('<?php echo $sim_card_info->id; ?>')">Activate</button>
                                <?php } ?>

                                <?php if(in_array("Simcard::update", $userInfo->permission_lists)){ ?>
                                <a data-toggle="modal" data-target="#updateSimCard" class="btn btn-warning btn-sm">Update</a>
                                <?php } ?>

                                <?php if(in_array("Simcard::reject", $userInfo->permission_lists) && ($sim_card_info->status == "pending") && $sim_card_info->locked == "1"){ ?>
                                <button type="button" class="btn btn-danger btn-sm" v-on:click="rejectThis('<?php echo $sim_card_info->id; ?>', '<?php echo ($sim_card_info->product_name." [ICCID: ".$sim_card_info->sim_card_iccid." , Mobile Number: ".$sim_card_info->sim_card_mobile_number." ]"); ?>')">Reject</button>
                                <?php } ?>

                                <?php if(in_array("Simcard::lock", $userInfo->permission_lists) && $sim_card_info->status != "approved"){ ?>
                                <button type="button" class="btn btn-primary btn-sm" v-on:click="lockUnlockThis('<?php echo $sim_card_info->id; ?>', '<?php echo ($sim_card_info->product_name." [ICCID: ".$sim_card_info->sim_card_iccid." , Mobile Number: ".$sim_card_info->sim_card_mobile_number." ]"); ?>', '<?php if($sim_card_info->locked == "1"){?>Unlock<?php }else{?>Lock<?php }?>')"><?php if($sim_card_info->locked == "1"){?><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Unlock<?php }else{?><span class="glyphicon glyphicon-lock" aria-hidden="true"></span> Lock<?php }?></button>
                                <?php } ?>




                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 order-md-0 order-1">
                            <div class="card-body">
                                <div><b>Product :</b> <?php echo $sim_card_info->product_name; ?></div>
                                <div><b>Sim Card ICCID :</b> <?php echo $sim_card_info->sim_card_iccid; ?></div>
                                <div><b>Sim Card Mobile Number :</b> <?php echo $sim_card_info->sim_card_mobile_number; ?></div>
                            </div>
                        </div>
                        <div class="col-md-3 order-md-1 order-0">
                            <?php if(true): ?>
                            <div class="text-left">
                                <div><b>Reseller :</b> <?php echo $sim_card_info->store_name; ?></div>
                                <div><b>Stock at :</b> <?php echo date("F jS, Y h:i A", strtotime($sim_card_info->created_at)); ?></div>
                                <?php if($sim_card_info->sales_status == "sold"){ ?>
                                <div><b>Sold on :</b> <?php echo date("F jS, Y h:i A", strtotime($sim_card_info->sold_at)); ?></div>
                                <?php } ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3 order-md-1 order-0">
                            <?php if(true && $sim_card_info->status == "approved"): ?>
                            <div class="text-left">
                                <div><b>Activation ID :</b> <?php echo $sim_card_info->activation_id; ?></div>
                                <div><b>Activated By :</b> <?php echo $sim_card_info->activator; ?></div>
                                <div><b>Activated At :</b> <?php echo date("F jS, Y h:i A", strtotime($sim_card_info->activated_at)); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if(!empty($simcard_rejection_history) && count($simcard_rejection_history) > 0): ?>
                        <div class="col-md-3 order-md-1 order-0">
                            <div class="text-left">
                                <h6>Rejection History</h6>
                                <ul>
                                <?php
                                foreach ($simcard_rejection_history as $row) {
                                    ?>
                                    <li>Rejected at <?php echo date("F jS, Y h:i A", strtotime($row->created_at)); ?> due to "<?php echo $row->cause; ?>"</li>
                                    <?php
                                }
                                ?>
                                </ul>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Sales Details</h4>
                            </div>
                            <div class="card-body">
                                <h6>Offer</h6>
                                <?php echo (!empty($sim_card_info->sc_simcard_offer_title)?$sim_card_info->sc_simcard_offer_title:"Other Offer"); ?><br><br>
                                <h6>Client Nome- Cognome</h6>
                                <?php echo $sim_card_info->sur_name; ?><br><br>
                                <h6>Date of Birth</h6>
                                <?php echo $sim_card_info->date_of_birth; ?><br><br>
                                <h6>Country</h6>
                                <?php echo $sim_card_info->country_name; ?><br><br>
                                <h6>Codicifiscale</h6>
                                <?php echo $sim_card_info->codicifiscale; ?><br><br>
                                <h6>Sales Price</h6>
                                <?php echo $sim_card_info->sales_price; ?><br><br>
                                <h6>Reseller Price</h6>
                                <?php echo $sim_card_info->reseller_price; ?><br><br>{{--
                                <h6>Notes</h6>
                                <?php echo $sim_card_info->other_information; ?><br><br>--}}
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">MNP Details</h4>
                            </div>
                            <div class="card-body">
                                <h6>Operator Name</h6>
                                <?php echo (!empty($sim_card_info->sc_mnp_operator_list_title)?$sim_card_info->sc_mnp_operator_list_title:$sim_card_info->mnp_operator_name); ?><br><br>
                                <h6>19 Digit ICCID Number</h6>
                                <?php echo $sim_card_info->mnp_iccid_number; ?><br><br>
                                <h6>Mobile Number</h6>
                                <?php echo $sim_card_info->mnp_iccid_mobile_number; ?><br><br>
                                <h6>Notes</h6>
                                <?php echo $sim_card_info->mnp_notes; ?><br><br>
                            </div>
                        </div>
                    </div>

                    <?php
                    $adminFiles = array();
                    $generalFiles = array();
                    ?>

                    <?php if($sim_card_info->status != "approved" || (($userInfo->user_type == "super_admin" || $userInfo->user_type == "manager") && $sim_card_info->status == "approved")){ ?>

                    <?php
                    foreach ($sc_sim_card_files as $row) {
                        if (str_contains($row->file_path, '/admin/')) {
                            $adminFiles[] = array('id'=>$row->row_id, 'file'=>(($row->space_uploaded=="uploaded"?config('constants.dgSpaceURL'):"/").$row->file_path));
                        } else {
                            $generalFiles[] = array('id'=>$row->row_id, 'file'=>(($row->space_uploaded=="uploaded"?config('constants.dgSpaceURL'):"/").$row->file_path));
                        }
                    }
                    ?>


                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Uploaded Documents</h4>
                            </div>
                            <div class="card-body">
                                <?php if($userInfo->user_type == "super_admin" || $userInfo->user_type == "manager"){ ?>
                                <div class="row">
                                    <?php
                                    //$map = Storage::disk('public-folder')->allFiles('assets/sim_card/'.$sim_card_info->id.'/admin');
                                    $map = $adminFiles;

                                    if(!empty($map)) {
                                    //$map = array_reverse($map);
                                    foreach ($map as $row) {
                                    ?>
                                        <div class="col-3">
                                            <?php if (strtolower(pathinfo($row['file'], PATHINFO_EXTENSION)) == "jpeg" || strtolower(pathinfo($row['file'], PATHINFO_EXTENSION)) == "jpg" || strtolower(pathinfo($row['file'], PATHINFO_EXTENSION)) == "png") { ?>
                                            <a target="_blank"
                                               href="<?php echo $row['file']; ?>">
                                                <img src="<?php echo $row['file']; ?>"
                                                     class="img-fluid">
                                            </a>
                                            <?php } else { ?>
                                            <a target="_blank"
                                               href="<?php echo $row['file']; ?>"><i class="fa fa-file fa-5x"></i></a>
                                            <?php } ?>

                                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::remove_simcard_files", $userInfo->permission_lists)){ ?>

                                            <button v-on:click="removeUploadedFile('<?php echo $row['file']; ?>', '<?php echo $row['id']; ?>')" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-remove-circle"></span> Remove</button>

                                            <?php } ?>
                                        </div>
                                    <?php
                                    }
                                    }
                                    ?>
                                </div>
                                <hr>
                                <?php } ?>
                                <div class="row">
                                    <?php
                                    //$map = Storage::disk('public-folder')->files('assets/sim_card/'.$sim_card_info->id.'/general');
                                    $map = $generalFiles;

                                    if(!empty($map)) {
                                    //$map = array_reverse($map);
                                    foreach ($map as $row) {
                                    ?>
                                    <div class="col-3">
                                        <?php if (strtolower(pathinfo($row['file'], PATHINFO_EXTENSION)) == "jpeg" || strtolower(pathinfo($row['file'], PATHINFO_EXTENSION)) == "jpg" || strtolower(pathinfo($row['file'], PATHINFO_EXTENSION)) == "png") { ?>
                                        <a target="_blank"
                                           href="<?php echo $row['file']; ?>">
                                            <img src="<?php echo $row['file']; ?>"
                                                 class="img-fluid">
                                        </a>
                                        <?php } else { ?>
                                        <a target="_blank"
                                           href="<?php echo $row['file']; ?>"><i class="fa fa-file fa-5x"></i></a>
                                        <?php } ?>

                                        <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::remove_simcard_files", $userInfo->permission_lists)){ ?>

                                        <button v-on:click="removeUploadedFile('<?php echo $row['file']; ?>', '<?php echo $row['id']; ?>')" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-remove-circle"></span> Remove</button>

                                        <?php } ?>
                                    </div>
                                    <?php
                                    }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <?php if($sim_card_info->status != "approved" || ($userInfo->user_type == "super_admin" && $sim_card_info->status == "approved")){ ?>
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Uploaded Documents</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        Upload Instruction:<br>
                                        1. Max size per file is <?php echo ini_get('upload_max_filesize'); ?>B.<br>
                                        2. Please upload PNG, JPEG, PDF file only.<br>
                                    </div>
                                    <div class="col-md-6"><br>
                                        <input id="choose10" name="upl_files10[]" ref="uploadCreateFile11" type="file" accept="application/pdf, image/jpeg, image/png" multiple v-on:change="uploadFileSelected()"/><br>
                                        <br>
                                    </div>
                                </div>
                                <div class="float-right">
                                    <button type="button" class="btn btn-primary" v-on:click="doUpload">Upload</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">&nbsp;</h4>
                        <div class="header-elements">
                            <div class="list-icons  btn-group-sm">
                                {{--<button type="button" class="btn btn-primary btn-sm" v-on:click="save()">Print</button>--}}
                                <?php if(in_array("Simcard::activate", $userInfo->permission_lists) && ($sim_card_info->status == "pending" || $sim_card_info->status == "rejected")  && $sim_card_info->locked == "1"){ ?>
                                    <button type="button" class="btn btn-success" v-on:click="activateThis('<?php echo $sim_card_info->id; ?>')">Activate</button>
                                <?php } ?>

                                <?php if(in_array("Simcard::update", $userInfo->permission_lists)){ ?>
                                    <a data-toggle="modal" data-target="#updateSimCard" class="btn btn-warning btn-sm">Update</a>
                                <?php } ?>

                                <?php if(in_array("Simcard::reject", $userInfo->permission_lists) && ($sim_card_info->status == "pending") && $sim_card_info->locked == "1"){ ?>
                                    <button type="button" class="btn btn-danger btn-sm" v-on:click="rejectThis('<?php echo $sim_card_info->id; ?>', '<?php echo ($sim_card_info->product_name." [ICCID: ".$sim_card_info->sim_card_iccid." , Mobile Number: ".$sim_card_info->sim_card_mobile_number." ]"); ?>')">Reject</button>
                                <?php } ?>

                                <?php if(in_array("Simcard::lock", $userInfo->permission_lists) && $sim_card_info->status != "approved"){ ?>
                                    <button type="button" class="btn btn-primary btn-sm" v-on:click="lockUnlockThis('<?php echo $sim_card_info->id; ?>', '<?php echo ($sim_card_info->product_name." [ICCID: ".$sim_card_info->sim_card_iccid." , Mobile Number: ".$sim_card_info->sim_card_mobile_number." ]"); ?>', '<?php if($sim_card_info->locked == "1"){?>Unlock<?php }else{?>Lock<?php }?>')"><?php if($sim_card_info->locked == "1"){?><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Unlock<?php }else{?><span class="glyphicon glyphicon-lock" aria-hidden="true"></span> Lock<?php }?></button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <?php if(in_array("Simcard::update", $userInfo->permission_lists)){ ?>
            <div class="modal" id="updateSimCard" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Update Sim Card</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Sim Card ICCID</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="Sim Card ICCID" v-model="updateSimCardInfo.sim_card_iccid">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Sim Card Mobile Number</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="Sim Card Mobile Number" v-model="updateSimCardInfo.sim_card_mobile_number">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Client Nome- Cognome</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="Client Nome- Cognome" v-model="updateSimCardInfo.sur_name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Codicifiscale</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="Codicifiscale" v-model="updateSimCardInfo.codicifiscale">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Country</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="Codicifiscale" v-model="updateSimCardInfo.country_name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Date of Birth</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="Codicifiscale" v-model="updateSimCardInfo.date_of_birth">
                                </div>
                            </div>

                            <br><h5>MNP- Number Portability</h5><br>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">19 Digit ICCID Number</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="19 Digit ICCID Number" v-model="updateSimCardInfo.mnp_iccid_number">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Mobile Number</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="Mobile Number" v-model="updateSimCardInfo.mnp_iccid_mobile_number">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Notes</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" v-model="updateSimCardInfo.mnp_notes"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" v-on:click="updateSimCard">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

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
                    waitingDialogInShow:false,
                    orderStatus:{'':'All','approved':'Approved','pending':'Pending','rejected':'Rejected'},
                    page: 1,
                    updateSimCardInfo:{
                        sim_card_iccid:"<?php echo $sim_card_info->sim_card_iccid; ?>",
                        sim_card_mobile_number:"<?php echo $sim_card_info->sim_card_mobile_number; ?>",
                        codicifiscale:"<?php echo $sim_card_info->codicifiscale; ?>",
                        sur_name:"<?php echo $sim_card_info->sur_name; ?>",
                        mnp_iccid_number:"<?php echo $sim_card_info->mnp_iccid_number; ?>",
                        mnp_iccid_mobile_number:"<?php echo $sim_card_info->mnp_iccid_mobile_number; ?>",
                        mnp_notes:"<?php echo $sim_card_info->mnp_notes; ?>",

                        country_name:"<?php echo $sim_card_info->country_name; ?>",
                        date_of_birth:"<?php echo $sim_card_info->date_of_birth; ?>",
                    },
                    tableFilter:{
                        store_id:"",
                        status:"",
                        product_id:"",
                        start_date:"",
                        end_date:"",
                        sim_card_iccid:"",
                        sim_card_mobile_number:""
                    },
                    uploaded_files:[]
                }
            },
            mounted() {
                theInstance = this;
            },
            methods: {
                doUpload()
                {
                    let formData = new FormData();

                    var pos = 0;

                    Object.keys(theInstance.uploaded_files).forEach(key => {
                        formData.append("file["+pos+"]", theInstance.uploaded_files[key]);
                        pos = pos + 1;
                    });

                    theInstance.showWaitingDialog();
                    axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/upload_file/<?php echo $sim_card_id; ?>", formData,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        }).then(response => {
                        //alert("Sim card have been updated successfully.")
                        location.reload()
                    })
                        .catch(error => {
                            theInstance.hideWaitingDialog();
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
                                $('#modal_create_new_order').modal('show');
                                this.page_message = ''
                            }
                        });
                },
                <?php if(in_array("Simcard::update", $userInfo->permission_lists)){ ?>
                updateSimCard(){
                    if(confirm("Are You Sure?"))
                    {
                        theInstance.showWaitingDialog();
                        axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/update/<?php echo $sim_card_id; ?>", theInstance.updateSimCardInfo,
                            {
                                headers: {
                                    Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                },
                            })
                            .then(response => {
                                location.reload();
                            })
                            .catch(error => {
                                theInstance.hideWaitingDialog();
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
                <?php } ?>
                uploadFileSelected()
                {
                    theInstance.uploaded_files = []
                    Object.keys(theInstance.$refs).forEach(key => {
                        Object.keys(theInstance.$refs[key].files).forEach(fkeys => {
                            theInstance.uploaded_files.push(theInstance.$refs[key].files[fkeys])
                        })
                    });
                    //this.simCardInfo.file[pos] = this.$refs.uploadCreateFile.files;
                },
                activateThis(id)
                {
                    if(confirm("Are You Sure"))
                    {
                        if(theInstance.uploaded_files.length == 0) return alert("You have to select a activation file.")

                        let formData = new FormData();
                        formData.append("test", "1")

                        var pos = 0;

                        Object.keys(theInstance.uploaded_files).forEach(key => {
                            formData.append("file["+pos+"]", theInstance.uploaded_files[key]);
                            pos = pos + 1;
                        });

                        theInstance.showWaitingDialog();
                        axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/activate/"+id, formData,
                            {
                                headers: {
                                    Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                },
                            }).then(response => {
                            //alert("Sim Card Activated Successfully.")
                            window.location.href = "/simcard/list/sold";
                        })
                            .catch(error => {
                                theInstance.hideWaitingDialog();
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
                },
                rejectThis(id, title)
                {
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

                                theInstance.showWaitingDialog();
                                axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/reject/"+id, formData,
                                    {
                                        headers: {
                                            Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                        },
                                    }).then(response => {
                                    dialog.close();
                                    //alert("Sim Card Rejected Successfully.")
                                    location.reload();
                                })
                                    .catch(error => {
                                        theInstance.hideWaitingDialog();
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
                lockUnlockThis(id)
                {
                    if(confirm("Are You Sure?"))
                    {
                        theInstance.showWaitingDialog();
                        axios.get("<?php echo env('APP_URL', ''); ?>/api/simcard/change_lock_status/"+id,
                            {
                                headers: {
                                    Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                },
                            })
                            .then(response => {
                                theInstance.hideWaitingDialog();
                                location.reload();
                            })
                            .catch(error => {
                                theInstance.hideWaitingDialog();
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
                                theInstance.hideWaitingDialog();
                            });
                    }
                },
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
                dTableMount()
                {
                    $(".updateMfs").click(function(e){
                        theInstance.setDefaultMfsId($(this).data("id"));
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
                loadPageData(){
                    this.masterTable.ajax.reload();
                },
                removeUploadedFile(file_url, file_id)
                {
                    if(confirm("Are You Sure?"))
                    {
                        theInstance.showWaitingDialog();
                        axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/remove_file/<?php echo $sim_card_info->id; ?>", {file_url:file_url, file_id:file_id},{
                            headers: {Authorization: '<?php echo session('AuthorizationToken'); ?>'}
                        })
                            .then(response => {
                                location.reload();
                            })
                            .catch(error => {
                                theInstance.hideWaitingDialog();
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
<style>
    .content-body {
        font-size: 1.1rem;
    }
</style>

<link href="https://cdn.jsdelivr.net/gh/GedMarc/bootstrap4-dialog/dist/css/bootstrap-dialog.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/gh/GedMarc/bootstrap4-dialog/dist/js/bootstrap-dialog.js"></script>
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css.map" rel="stylesheet" type="text/css" />
@include('inc.footer', [])
