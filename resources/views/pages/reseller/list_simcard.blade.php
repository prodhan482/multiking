@include('inc.header')
@include('inc.menu')
<div class="app-content content ">
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
                    {{--<div class="card-header bg-white header-elements-inline">
                        <h6 class="card-title">Reseller List</h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a href="#" data-toggle="modal" data-target="#modal_filter" class="list-icons-item"><i class="icon-filter3"></i> Filter</a>&nbsp;&nbsp;&nbsp;
                                <a href="/reseller/create" class="list-icons-item"><i class="icon-plus-circle2"></i> Add New Reseller</a>
                            </div>
                        </div>
                    </div>--}}
                    <div class="card-body" :style="{'padding':'0'}">
                        <table class="table datatable-basic dataTable no-footer datatable-scroll-y">
                            <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Logo</th>
                                <th>Reseller Name</th>

                                <th>Current Due</th>
                                <th>Status</th>
                                <th>Created On</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div id="modal_AddBalance" class="modal fade" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Balance</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Euro Amount</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">&euro;</span>
                                                </div>
                                                <input type="text" placeholder="Put Euro Amount" class="form-control" id="euroAmount" onkeyup="theInstance.CalculateRechargeAmount()">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">.00</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-4">Note (If Any):</label>
                                        <div class="col-sm-8">
                                            <textarea id="addBalanceNote" class="form-control" rows="3" placeholder="Put if you have any note"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-link" data-dismiss="modal">Close</button>
                                    <button class="btn btn-danger" onclick="theInstance.addNewBalance()">Add</button>
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
    var theInstance= {
        storeSelectedBaseCurrency:'',
        parent_store_id:'0',
        masterTable:'',
        waitingDialogInShow:false,
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
        dTableMount:function () {
            jQuery(".removeBtt").click(function(e){
                theInstance.removeUser($(this).data("id"));
            });

            jQuery(".changePassword").click(function(e){
                theInstance.setDefaultUserId($(this).data("id"));
                $('#modal_user_password_change').modal('show');
            });

            jQuery(".changeStoreStatus").click(function(e){
                //console.log($(this).data("current_status"))
                theInstance.changeStoreStatus($(this).data("id"), ($(this).data("current_status")==="enabled"?"disabled":"enabled"));
            });

            jQuery(".addBalance").click(function(e){
                console.log($(this).data("conversion_rate"));
                console.log($(this).data("base_currency"));
                console.log($(this).data("base_add_balance_commission_rate"));
                $('#modal_AddBalance').modal('show');

                theInstance.storeSelectedBaseCurrency = $(this).data("base_currency");
                theInstance.selectedStoreId = $(this).data("id");

                $('#selectedCurrencyTitle').html($(this).data("base_currency").toUpperCase()+" Rate");
                $('#selectedCurrency').html($(this).data("base_currency").toUpperCase());
                $('#commissionPercent').val($(this).data("base_add_balance_commission_rate"));
                $('#selectedCurrencyAmount').val(theInstance.convertedCurrency[$(this).data("base_currency")].conv_amount);
                $('#euroAmount').val("1");

                //base_add_balance_commission_rate

                $('#theTotal').html($(this).data("base_currency").toUpperCase()+" "+theInstance.convertedCurrency[$(this).data("base_currency")].conv_amount);

                theInstance.CalculateRechargeAmount()
            });
        },
        setDefaultUserId(uid){
            theInstance.defaultUserId = uid;
        },
        changeStoreStatus(store_id, new_status){
            if(confirm("Are you sure?"))
            {
                jQuery.ajax({
                    type: "POST",
                    beforeSend: function(request) {
                        request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                    },
                    url: "<?php echo env('APP_URL', ''); ?>/api/store/"+store_id,
                    dataType: 'json',
                    data: JSON.stringify({"status":new_status}),
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
        addNewBalance(store_id){
            if(confirm("Are you sure?"))
            {
                $('#modal_AddBalance').modal('hide');
                theInstance.showWaitingDialog();
                jQuery.ajax({
                    type: "PUT",
                    beforeSend: function(request) {
                        request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                    },
                    url: "<?php echo env('APP_URL', ''); ?>/api/store/"+theInstance.selectedStoreId,
                    dataType: 'json',
                    data: JSON.stringify({
                        "euro_amount":$('#euroAmount').val(),
                        "commission":$('#commissionPercent').val(),
                        "selected_currency_rate":$('#selectedCurrencyAmount').val(),
                        "note":$('#addBalanceNote').val()
                    }),
                    statusCode: {
                        200: function() {
                            theInstance.hideWaitingDialog();
                            theInstance.masterTable.ajax.reload();
                        },
                        406: function() {
                            location.reload()
                        },
                        402: function() {
                            alert("You don't have sufficient balance to add balance for this reseller.")
                        },
                        401: function() {
                            location.reload()
                        }
                    }
                });
            }
        },
        tableFilter:{
            store_owner_name:'',
            store_phone_number:'',
        },
        addResellerEuroPaymentDialog:'',
        addResellerAmountReturnDialog:'',
        <?php
            $convertedCurrency = array();
            foreach ($userInfo->currency_conversions_list as $value) {
                $convertedCurrency[$value->type] = $value;
            }
        ?>
        convertedCurrency:<?php echo json_encode($convertedCurrency); ?>,
        storeList:[],
        selectedStoreId:'',
        CalculateRechargeAmount()
        {
            $('#selectedCurrencyTitle').html(theInstance.storeSelectedBaseCurrency.toUpperCase()+" Rate");
            $('#selectedCurrency').html(theInstance.storeSelectedBaseCurrency.toUpperCase());
            //$('#selectedCurrencyAmount').val(theInstance.convertedCurrency[theInstance.storeSelectedBaseCurrency].conv_amount);
            //$('#euroAmount').val("1");

            var euroAmount = $('#euroAmount').val();
            var selectedCurrencyAmount = $('#selectedCurrencyAmount').val();
            var commissionPercent = $('#commissionPercent').val();

            if(!euroAmount) euroAmount = 0;
            if(!selectedCurrencyAmount) selectedCurrencyAmount = theInstance.convertedCurrency[theInstance.storeSelectedBaseCurrency].conv_amount;

            $('#theTotal').html(theInstance.storeSelectedBaseCurrency.toUpperCase()+" "+((
                parseFloat(selectedCurrencyAmount) *
                parseFloat(euroAmount)
            ) * ((100 - commissionPercent) / 100)).toFixed(2));
            //console.log()
        }
    };

    jQuery(function(){
        theInstance.masterTable = jQuery('.dataTable').DataTable({
                scrollCollapse: true,
                "searching": true,
                "info": false,
                "paging": true,
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
                dom:
                    '<"row d-flex justify-content-between align-items-center"' +
                    '<"col-lg-2 d-flex align-items-center"<"dt-action-buttons the_page_title text-left ">>' +
                    '<"col-lg-6 d-flex align-items-center"<"dt-action-buttons text-center "B>>' +
                    '<"col-lg-4 d-flex align-items-center justify-content-lg-end flex-lg-nowrap flex-wrap p-0"<"select_reseller">f<"ml-sm-2">>' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                buttons: [
                    {
                        text: 'Add New Reseller',
                        className: 'btn btn-danger btn-add-record ml-2',
                        action: function (e, dt, button, config) {
                            window.location.href = "/reseller/create";
                        }
                    },
                    {
                        text: 'Add New Payment Received',
                        className: 'btn btn-success btn-add-record ml-2',
                        action: function (e, dt, button, config) {

                            var strDropDown = "";
                            for (var key in theInstance.storeList) {
                                strDropDown = strDropDown + '<option value="'+theInstance.storeList[key].store_id+'">'+theInstance.storeList[key].store_name+'</option>';
                            }

                            theInstance.addResellerEuroPaymentDialog = bootbox.dialog({
                                title: 'Add New Payment From Reseller.',
                                message: (
                                    '<div class="form-group"><label>Reseller</label>' +

                                    '<select class="form-control select2box2">' +
                                        '<option value="">Select A Reseller</option>'+strDropDown+
                                    '</select>' +

                                    '</div>'

                                    +'<div class="form-group"><label>Euro (&euro;) Amount </label><input class="form-control" id="theEuroAmount" type="text" placeholder="Put Received Euro Amount."></div>'
                                    +'<div class="form-group"><label>Note</label><textarea class="form-control" id="addResellerEuroPaymentDialog_note" rows="3" placeholder="Enter your note here."></textarea></div>'
                                ),
                                //centerVertical:true,
                                buttons: {
                                    cancel: {
                                        label: "Cancel",
                                        className: 'btn-danger',
                                        callback: function(){
                                            console.log('Custom cancel clicked');
                                        }
                                    },
                                    ok: {
                                        label: "Ok",
                                        className: 'btn-info',
                                        callback: function(){
                                            theInstance.showWaitingDialog();
                                            jQuery.ajax({
                                                type: "PATCH",
                                                beforeSend: function(request) {
                                                    request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                                                },
                                                url: "<?php echo env('APP_URL', ''); ?>/api/store/"+$('.select2box2').find(':selected').val(),
                                                dataType: 'json',
                                                data: JSON.stringify({
                                                    "store_id":$('.select2box2').find(':selected').val(),
                                                    "euro_amount":$('#theEuroAmount').val(),
                                                    "simcard":"1",
                                                    "note":$('#addResellerEuroPaymentDialog_note').val()
                                                }),
                                                statusCode: {
                                                    200: function() {
                                                        theInstance.hideWaitingDialog();
                                                        theInstance.masterTable.ajax.reload();
                                                    },
                                                    406: function() {
                                                        theInstance.hideWaitingDialog();
                                                        location.reload()
                                                    },
                                                    401: function() {
                                                        theInstance.hideWaitingDialog();
                                                        location.reload()
                                                    }
                                                }
                                            });
                                        }
                                    }
                                }
                            });

                            theInstance.addResellerEuroPaymentDialog.on('shown.bs.modal', function(e){
                                $('.select2box2').select2({'width':'100%'});
                            });
                        }
                    },
                ],
                "columnDefs": [
                    {
                        'targets': 3,'searchable': false, 'orderable': false, 'width':'10%',
                        'render': function (data, type, full, meta)
                        {
                            var info = $('<div/>').text(data).html();

                            if(parseFloat(info.replace(/[^0-9-]/g, "")) > 0 )
                            {
                                return '<span style="font-weight: bold" class="text-danger">'+info+'</span>';
                            }
                            else
                            {
                                return '<span style="font-weight: bold" class="text-success">'+info+'</span>';
                            }

                            return '';
                        }
                    },
                    {
                        'targets': 4,'searchable': false, 'orderable': false,
                        'render': function (data, type, full, meta)
                        {
                            var info = $('<div/>').text(data).html();

                            if(info === "disabled") return '<span class="badge badge-danger badge-pill">Disabled</span>';
                            if(info === "enabled") return '<span class="badge badge-success badge-pill">Enabled</span>';

                            return '';
                        }
                    },
                    {
                        'targets': 6,'searchable': false, 'orderable': false,
                        'render': function (data, type, full, meta)
                        {
                            var info = $('<div/>').text(data).html();

                            if(theInstance.parent_store_id == "0")
                            {
                                return '<div class="btn-group btn-group-sm">'+
                                    //'<button type="button" class="btn btn-danger removeStoreBtt" data-id="'+info.split('|')[0]+'">Remove</button>'+
                                    '<button type="button" class="btn btn-primary changeStoreStatus" data-id="'+info.split('|')[0]+'" data-current_status="'+info.split('|')[1]+'">Change Status</button>'+
                                    '<a type="button" class="btn btn-warning updateStore" href="/reseller/'+info.split('|')[0]+'/update" target="_blank">Update</a>'+
                                    '<a type="button" class="btn btn-info" href="/simcard/report/adjustment/'+info.split('|')[0]+'">View Transactions</a>'+
                                    '<a type="button" class="btn btn-success" href="/simcard/configure_reseller_promo/'+info.split('|')[0]+'">Configure Offer</a>'+
                                    //'<button type="button" class="btn btn-success addBalance" data-id="'+info.split('|')[0]+'">Add Balance</button>'+
                                    '</div>';
                            } else {
                                return '<div class="btn-group btn-group-sm">'+
                                    //'<button type="button" class="btn btn-danger removeStoreBtt" data-id="'+info.split('|')[0]+'">Remove</button>'+
                                    '<a type="button" class="btn btn-info" href="/simcard/report/adjustment/'+info.split('|')[0]+'">View Transactions</a>'+
                                    '<a type="button" class="btn btn-success" href="/simcard/configure_reseller_promo/'+info.split('|')[0]+'">Configure Offer</a>'+
                                    //'<button type="button" class="btn btn-success addBalance" data-id="'+info.split('|')[0]+'">Add Balance</button>'+
                                    '</div>';
                            }

                            return '';
                        }
                    }
                ],
                "processing": true,
                "serverSide": true,
                "pageLength": 50,
                "language": {
                    "emptyTable": "No Store Found.",
                },
                "ajax": {
                    "url": '<?php echo env('APP_URL', ''); ?>/api/store',
                    "type": "POST",
                    'beforeSend': function (request) {
                        request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                    },
                    "data": function ( d )
                    {
                        d.store_owner_name = jQuery('#store_owner_name').val();
                        d.store_phone_number = jQuery('#store_phone_number').val();
                        d.parent_store_id = theInstance.parent_store_id;
                        d.simcard_view = "1";
                    },
                    complete: function(data)
                    {
                        theInstance.page_message = ''
                        theInstance.storeList = data.responseJSON.store_list
                        //theInstance.newStore.mfsList = data.responseJSON.mfs_list
                        $(".the_page_title").html('<h6 class="card-title">Reseller List (Sim Card)</h6>');

                        <?php if($userInfo->user_type == "super_admin"): ?>
                        var strDropDown = "";
                        for (var key in  data.responseJSON.allStoreList) {
                            strDropDown = strDropDown + '<option value="'+data.responseJSON.allStoreList[key].store_id+'" '+((theInstance.parent_store_id == data.responseJSON.allStoreList[key].store_id)?"selected":"")+'>'+data.responseJSON.allStoreList[key].store_name+'</option>';
                        }

                        $(".select_reseller").html('<div style="margin-right: 30px;"><div class="form-group"><select class="form-control hdfkdjshfkj"><option value="0">Self Reseller</option>'+strDropDown+'</select></div></div>');
                        $('.hdfkdjshfkj').select2({'width':'100%', 'placeholder':'Self Reseller'});
                        $('.hdfkdjshfkj').on('select2:select', function (e) {
                            theInstance.parent_store_id = $('.hdfkdjshfkj').val();
                            theInstance.masterTable.ajax.reload();
                        });
                        <?php endif; ?>
                    },
                    error: function (xhr, error, thrown)
                    {
                        console.log("Error");
                    }
                },
            }
        );
    });
</script>
<style>
    .select-mfs2 + .select2-container{
        width: 100%;
    }
</style>
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>
@include('inc.footer', ['load_datatable_scripts' => true])
