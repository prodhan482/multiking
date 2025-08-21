@include('inc.header', ['load_vuejs' => true, 'load_richtexteditor'=>true])
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
                        <h6 class="card-title">Update Sim Card Promo : <?php echo $operatorInfo->title; ?></h6>
                        <div class="header-elements">
                            <div class="list-icons  btn-group-sm">
                                <button type="button" class="btn btn-warning btn-sm" v-on:click="update()">Update</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" >
                        <div class="row" :style="{'padding':'5px'}">
                            <div class="col-md-6 col-xs-12">
                                <div class="form-group card required">
                                    <label class="control-label">Title</label>
                                    <input autocomplete="off" class="form-control" type="text" v-model="newSimCardPromo.title" name="title">
                                </div>
                                <div class="form-group card required">
                                    <label class="control-label">Product</label>
                                    <select class="form-control do-me-select2" v-model="newSimCardPromo.product_id">
                                        <option value="">Select A Product</option>
                                        <?php foreach($productList as $row): ?>
                                        <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group card required">
                                    <label class="control-label">Default Reseller Price</label>
                                    <input autocomplete="off" class="form-control" type="text" v-model="newSimCardPromo.reseller_price" name="reseller_price">
                                </div>
                            </div>
                            <div class="col-md-6 col-xs-12" style="padding-left: 35px;">
                                <div class="form-group card required">
                                    <label class="control-label">Bonus</label>
                                    <input autocomplete="off" class="form-control" type="text" v-model="newSimCardPromo.reseller_bonus" name="reseller_bonus">
                                </div>
                                <div class="form-group card required">
                                    <label class="control-label">File <?php if(!empty($operatorInfo->upload_path)){ ?>(<a target="_blank" href="<?php echo ((!empty($operatorInfo->space_uploaded) && $operatorInfo->space_uploaded == "uploaded")?config('constants.dgSpaceURL'):"/").$operatorInfo->upload_path; ?>">Click to View Existing</a>)<?php } ?></label>
                                    <input type="file" class="form-control h-auto" ref="simCardPromoFile" accept="image/png, image/gif, image/jpeg,application/pdf" v-on:change="simCardPromoCreateImageSelected">
                                    <small id="emailHelp" class="form-text text-muted">Max file upload size allowed <b><?php echo ini_get('upload_max_filesize'); ?></b></small>
                                </div>

                                <div class="form-row"><div class="form-group card required red_colored_text" id="viewErrorMessage" style="display: none;">Error Message</div></div>
                            </div>
                        </div>

                        <div class="form-group card required">
                            <label class="control-label">Description</label>
                            <textarea autocomplete="off" id="div_editor1" class="form-control summernote"><?php echo $operatorInfo->description; ?></textarea>
                        </div>

                        <table class="table dataTable no-footer convert2DataTable">
                            <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Reseller</th>
                                <th>Reseller Bonus</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <?php
                            if(!empty($operatorInfo->reseller_offer)) $product_offer = json_decode($operatorInfo->reseller_offer);
                            if(empty($operatorInfo->reseller_offer)) $product_offer = array();
                            $storeByName = array();
                            foreach($storeList as $__row) {
                                $storeByName[$__row->id] = $__row->name;
                            }
                            ?>
                            <tbody>
                            <?php
                            $p = 1;
                            $alreadySelectedResellerIds = array();
                            foreach($product_offer as $i)
                            {
                            $info = explode("|", $i);
                            $pos = $p++;
                            $alreadySelectedResellerIds [] = $info[0];
                            if(empty($storeByName[$info[0]])) continue;
                            ?>
                            <tr>
                                <td><?php echo $pos; ?></td>
                                <td><?php echo $storeByName[$info[0]]; ?></td>
                                <td><?php echo $info[1]; ?></td>
                                <td>
                                    <input type="hidden" name="reseller_offer[]" value="<?php echo $i; ?>"><a href="javascript:void(0)" data-toggle="tooltip" data-original-title="Close" data-position="<?php echo $pos; ?>" data-reseller_id="<?php echo $info[0]; ?>" data-reseller_price="<?php echo $info[1]; ?>" class="icon-pencil"><i class="fa fa-pencil-square-o text-danger"></i></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-original-title="Close" class="icon-delete" data-reseller_id="<?php echo $info[0]; ?>"><i class="fa fa-close text-danger"></i></a>

                                </td>
                            </tr>
                            <?php
                            }?>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <div class="pull-left">
                                        <button type="button" class="btn btn-success" v-on:click="addReseller()">Add Row</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <div class="float-right">
                                        <button type="button" class="btn btn-warning" v-on:click="update()">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
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
                    richTextEditor:{},
                    defaultOrderId:'',
                    formToDoUpdate:false,
                    alreadySelectedResellerIds:<?php echo json_encode($alreadySelectedResellerIds); ?>,
                    page: 1,

                    newSimCardPromo:{
                        title:"<?php echo nl2br($operatorInfo->title); ?>",
                        product_id:"<?php echo $operatorInfo->product_id; ?>",
                        reseller_bonus:"<?php echo $operatorInfo->bonus; ?>",
                        reseller_price:"<?php echo $operatorInfo->reseller_price; ?>",
                        description:"",
                        file:""
                    }
                }
            },
            mounted() {
                theInstance = this;

                $('.do-me-select2').select2({'width':'100%'});
                $('.do-me-select2').on('select2:select', function (e) {
                    theInstance.newSimCardPromo.product_id = $('.do-me-select2').select2('data')[0].id
                });

                $(document).ready(function() {
                    // On Expire use this
                    // https://github.com/facebook/lexical

                    /*theInstance.richTextEditor = new RichTextEditor(document.getElementById("div_editor1"));
                    theInstance.richTextEditor.attachEvent("change", function () {
                        theInstance.newSimCardPromo.description = theInstance.richTextEditor.getHTMLCode();
                    });*/

                    CKEDITOR.replace('div_editor1');

                    //theInstance.richTextEditor = CKEDITOR.document.getById( 'div_editor1' );
                    /*theInstance.richTextEditor.setHtml(
                        'Hello world!\n\n' +
                        'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                    );*/
                });

                theInstance.masterTable = $('.convert2DataTable').DataTable({
                    "searchable": false,
                    "orderable": false,
                    "paging":   false,
                    "ordering": false,
                    "info":     false,
                    "bPaginate": false,
                    "searching": false,
                    "columnDefs": [ {
                        "searchable": false,
                        "orderable": false,
                        "targets": 0
                    } ],
                    /*"drawCallback": function( settings ) {
                        //var api = this.api();
                        console.log("11")
                        $(".dataTables_scrollBody").scrollTop(theInstance.scrollPosition);
                    },*/
                    "order": [[ 1, 'asc' ]]
                });

                theInstance.masterTable.on( 'order.dt search.dt', function () {
                    theInstance.masterTable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                        cell.innerHTML = i+1;
                    } );
                } ).draw();

                $('.convert2DataTable tbody').on( 'click', 'a.icon-delete', function () {
                    if(confirm("Are you sure?"))
                    {
                        var reseller_id = $(this).data('reseller_id')+"";
                        theInstance.alreadySelectedResellerIds = theInstance.alreadySelectedResellerIds.filter(e => e !== reseller_id);
                        theInstance.masterTable
                            .row( $(this).parents('tr') )
                            .remove()
                            .draw();
                    }
                } );

                $('.convert2DataTable tbody').on( 'click', 'a.icon-pencil',function(e){
                    theInstance.updateReseller($(this).data("position"), $(this).data("reseller_id"), $(this).data("reseller_price"))
                });

            },
            methods: {
                addReseller()
                {
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_PRIMARY,
                        title: "Add Row",
                        message: '<form method="post" enctype="multipart/form-data" id="addResellerRow"><div class="form-group card required"> <label class="control-label">Reseller</label><select class="form-control" id="__reseller_id" name="__reseller_id"><?php

                                $storeByName = array();

                                foreach($storeList as $__row)
                                {
                                $storeByName[$__row->id] = $__row->name;
                                ?><option value="<?php echo $__row->id; ?>"><?php echo str_replace("'", "", $__row->name); ?></option><?php
                                }
                                ?></select> </div>  <div class="form-group card required"> <label class="control-label">Reseller Bonus</label> <input autocomplete="off" class="form-control" type="number" step="any" id="__reseller_price" name="__reseller_price"/> </div>' +
                            '</form>',
                        closable: false,
                        onshown:function(dialog)
                        {
                            $('#__reseller_id').select2()
                            $('#__reseller_id').select2({dropdownParent:$('#'+dialog.options.id)})
                            //$("#editOfferForm select[name=product_id]").val(product_id);
                            //console.log("HI")
                        },
                        draggable: false,
                        //data:{offer_id:id, offer_name:name, product_id:product_id},
                        buttons: [{
                            label: "Cancel",
                            action: function(dialog) {
                                dialog.close();
                            }
                        }, {
                            label: "Add",
                            cssClass:"btn-info",
                            action: function(dialog)
                            {
                                var other_data = $('#addResellerRow').serializeArray();
                                var info = {};
                                $.each(other_data,function(key,input){
                                    //fd.append(input.name,input.value);
                                    info[input.name] = input.value
                                });

                                <?php
                                    echo 'var storeByName = '.json_encode($storeByName);
                                    ?>

                                if(info.__reseller_id.length < 1) {
                                    alert("Please select a Reseller.");
                                    return false;
                                }

                                if(theInstance.alreadySelectedResellerIds.includes(info.__reseller_id))
                                {
                                    alert("Selected reseller already have this mnp Operator.");
                                    return false;
                                }

                                if(info.__reseller_price.length < 1) {
                                    alert("Please put a reseller bonus.");
                                    return false;
                                }

                                theInstance.alreadySelectedResellerIds.push(info.__reseller_id)

                                //$(this).data("position"), $(this).data("reseller_id"), $(this).data("reseller_price")

                                theInstance.masterTable.row.add( [
                                    (theInstance.alreadySelectedResellerIds.length),
                                    storeByName[info.__reseller_id],
                                    info.__reseller_price,
                                    '<input type="hidden" name="reseller_offer[]" value="'+(info.__reseller_id+'|'+info.__reseller_price)+'"><a href="javascript:void(0)" data-toggle="tooltip" data-original-title="Close" class="icon-pencil" data-position="'+theInstance.alreadySelectedResellerIds.length+'" data-reseller_id="'+info.__reseller_id+'" data-reseller_price="'+info.__reseller_price+'"><i class="fa fa-pencil-square-o text-danger"></i></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-original-title="Close" class="icon-delete" data-reseller_id="' + (info.__reseller_id) + '"><i class="fa fa-close text-danger"></i></a>'
                                ] ).draw( false );

                                dialog.close();
                            }
                        }]
                    });
                },
                updateReseller(position, reseller_id, reseller_price)
                {
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_PRIMARY,
                        title: "Update Row",
                        message: '<form method="post" enctype="multipart/form-data" id="addResellerRow"><div class="form-group card required"> <label class="control-label">Reseller Bonus</label> <input autocomplete="off" class="form-control" type="number" step="any" id="__reseller_price" name="__reseller_price" value="'+reseller_price+'"/></div>' +
                            '</form>',
                        closable: false,
                        onshown:function(dialog)
                        {
                            //$("#addResellerRow select[name=__reseller_id]").val(reseller_id);
                            $("#addResellerRow input[name=__reseller_price]").val(reseller_price);

                            //$('#__reseller_id').select2({dropdownParent:$('#'+dialog.options.id)})

                            //console.log("HI")
                        },
                        draggable: false,
                        data:{reseller_id:reseller_id, reseller_price:reseller_price, position:position},
                        buttons: [{
                            label: "Cancel",
                            action: function(dialog) {
                                dialog.close();
                            }
                        }, {
                            label: "Update",
                            cssClass:"btn-info",
                            action: function(dialog)
                            {
                                var other_data = $('#addResellerRow').serializeArray();
                                var info = {};
                                $.each(other_data,function(key,input){
                                    //fd.append(input.name,input.value);
                                    info[input.name] = input.value
                                });

                                <?php
                                    echo 'var storeByName = '.json_encode($storeByName);
                                    ?>


                                if(info.__reseller_price.length < 1) {
                                    alert("Please put a reseller bonus.");
                                    return false;
                                }

                                theInstance.masterTable.row((parseInt(position) - 1)).data([
                                    position,
                                    storeByName[reseller_id],
                                    info.__reseller_price,
                                    '<input type="hidden" name="reseller_offer[]" value="'+(reseller_id+'|'+info.__reseller_price)+'"><a href="javascript:void(0)" data-toggle="tooltip" data-original-title="Close" class="icon-pencil" data-position="'+theInstance.alreadySelectedResellerIds.length+'" data-reseller_id="'+reseller_id+'" data-reseller_price="'+info.__reseller_price+'"><i class="fa fa-pencil-square-o text-danger"></i></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-original-title="Close" class="icon-delete"><i class="fa fa-close text-danger"></i></a>'
                                ] ).draw();

                                dialog.close();
                            }
                        }]
                    });
                },
                update(){
                    var reseller_offer = []

                    $('input[name^="reseller_offer[]"]').each(function( index ) {
                        reseller_offer.push($(this).val())
                    });

                    let formData = new FormData();
                    formData.append("reseller_offer", JSON.stringify(reseller_offer))

                    //theInstance.newSimCardPromo.description = theInstance.richTextEditor.getHTMLCode();

                    theInstance.newSimCardPromo.description = CKEDITOR.instances.div_editor1.getData();

                    Object.keys(this.newSimCardPromo).forEach(key => {
                        formData.append(key, theInstance.newSimCardPromo[key])
                    });

                    axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/promo/update/<?php echo $operatorInfo->id; ?>", formData,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        }).then(response => {
                        alert("SIM Promo Saved Successfully.")
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
                                        this.formErrorMessage = error.response.data.message.join(",")
                                        break;
                                }
                                $('#modal_create_new_order').modal('show');
                                this.page_message = ''
                            }
                        });
                },
                simCardPromoCreateImageSelected()
                {
                    theInstance.newSimCardPromo.file = this.$refs.simCardPromoFile.files[0];
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
<link href="https://cdn.jsdelivr.net/gh/GedMarc/bootstrap4-dialog/dist/css/bootstrap-dialog.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/gh/GedMarc/bootstrap4-dialog/dist/js/bootstrap-dialog.js"></script>
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css.map" rel="stylesheet" type="text/css" />

@include('inc.footer', ['load_datatable_scripts' => true])
