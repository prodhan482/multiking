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
                    <div class="card-header bg-white header-elements-inline" >
                        <h6 class="card-title">Sim Cards</h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::create", $userInfo->permission_lists)): ?>
                                <a href="/simcard/add" class="list-icons-item"><i class="icon-plus-circle2"></i> Add New Sim Card</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" :style="{'padding':'0'}">
                        <table class="table datatable-basic dataTable no-footer datatable-scroll-y">
                            <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>ICCID</th>
                                <th>Number</th>
                                <th>Product</th>
                                <th>Lot</th>
                                <th>Reseller</th>
                                <th>Current Status</th>
                                <th>Created At</th>
                                <th>Ordered At</th>
                                <th>Approved at</th>
                                <th>Sold at</th>
                                <th>Activated at</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::appoint_sim_card", $userInfo->permission_lists)){ ?>
                        <div class="bulk_options" style="padding: 15px;">
                            <form class="form-inline" method="post">
                                <div class="form-group">
                                    <input checked id="7789" type="radio" name="bulk_update" value="bulk_change_status">
                                    <label for="7789">Move Stocked Sim Card to Order &nbsp;&nbsp;</label>
                                    <div class="input-group">
                                        <select class="form-control" id="bulk_update_selected_order">
                                            <?php
                                            /*foreach($pendingOrders as $row)
                                            {
                                            if($row->apointed < $row->quantity) {
                                            ?>
                                            <option value="<?php echo $row->id; ?>">Order # <?php echo $row->order_serial; ?>
                                                (<?php echo($row->apointed . "/" . $row->quantity); ?>)
                                            </option>
                                            <?php
                                            }
                                            }*/
                                            ?>
                                                <option v-for="(key, value) in orderInfo" v-if="(parseInt(key.apointed) < parseInt(key.quantity))" v-bind:value="key.id" v-html="('Order # '+key.order_serial+'('+key.apointed+'/'+key.quantity+')')"></option>
                                        </select>
                                        <button type="button" class="btn btn-primary" v-on:click="doBulkUpdate()">Move</button>
                                    </div>

                                </div>
                                &nbsp;&nbsp;&nbsp;
                                <div class="form-group">
                                    <label>MNP Number&nbsp;&nbsp;<input id="mnp_number_value" type="text" class="form-control input-sm"></label><button type="button" class="btn btn-primary btn-sm doSearch">search</button><button type="button" class="btn btn-info btn-sm resetFilter">reset</button>
                                </div>
                            </form>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <!-- list section end -->
            </section>
        </div>
    </div>
</div>
<script>
    var theInstance = {};
    $(function(){
        var app = new Vue({
            el: '#app',
            data() {
                return {
                    formErrorMessage:'',
                    page_message:'',
                    enteredUserName:'',
                    enteredUserPassword:'',
                    lastTimeLoaded:new Date().getTime(),
                    masterTable:{},
                    windowHeight: 0,
                    windowWidth: 0,
                    scrollPosition:0,
                    defaultMfsId:'',
                    formToDoUpdate:false,
                    orderStatus:{'':'All','approved':'Approved','pending':'Pending','rejected':'Rejected'},
                    page: 1,
                    tableFilter:{
                        selected_reseller:"",
                        selected_mnp_number:"",
                        selected_lot:"",
                        selected_product:"",
                        sales_status:"",
                        status:"",
                        search:""
                    },
                    orderInfo:[]
                }
            },
            mounted() {
                theInstance = this;
                this.masterTable = $('.dataTable').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "responsive": false,
                        "pageLength": 50,
                        "dom": '<"toolbar">frtip',
                        scrollY: ($(window).height() - ($(window).height() * 0.35)),
                        scrollX: true,
                        scrollCollapse: true,
                        paging: true,
                        "order": [[1, "desc"]],
                        "searching": false,
                        "oLanguage": {
                            "sSearch": "ICCID / Number"
                        },
                        initComplete: function () {
                            var input = $('.dataTables_filter input').unbind(),
                                self = this.api(),
                                $searchButton = $('<button>')
                                    .text('search')
                                    .click(function () {
                                        self.search(input.val()).draw();
                                    }),
                                $clearButton = $('<button>')
                                    .text('clear')
                                    .click(function () {
                                        input.val('');
                                        $searchButton.click();
                                    })
                            $('.dataTables_filter').append($searchButton, $clearButton);
                            theInstance.dTableMount();
                        },
                        "ajax": {
                            "url": '<?php echo env('APP_URL', ''); ?>/api/simcard/all?response=json&time=' + Math.random(),
                            "type": "POST",
                            'beforeSend': function (request) {
                                request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                            },
                            "data": function (d) {
                                d.reseller_id = theInstance.tableFilter.selected_reseller
                                d.selected_mnp_number = theInstance.tableFilter.selected_mnp_number
                                d.product_id = theInstance.tableFilter.selected_product
                                d.sales_status = theInstance.tableFilter.sales_status
                                d.lot_id = theInstance.tableFilter.selected_lot
                                d.status = theInstance.tableFilter.status
                                d.search = theInstance.tableFilter.search
                            },
                            complete: function (data) {
                                /*$('input[type="checkbox"]:checked').each(function () {
                                    this.checked = false;
                                });*/
                                theInstance.lastTimeLoaded = new Date().getTime()
                                theInstance.orderInfo = data.responseJSON.pendingOrders
                            },
                            error: function (xhr, error, thrown) {
                                console.log(xhr);
                            }
                        },
                        "infoCallback": function (settings, start, end, max, total, pre) {
                            return "Showing " + start + " to " + end + " of " + total + " Sim Cards";
                        },
                        columns: [
                            {"name": "id"}, {"name": "sim_card_iccid"}, {"name": "sim_card_mobile_number"}, {"name": "product_name"}, {"name": "lot_name"}, {"name": "reseller"}, {"name": "current_status"}, {"name": "created_at"}, {"name": "ordered_at"}, {"name": "approved_at"}, {"name": "sold_at"}, {"name": "activated_at"}, {"name": "action"}
                        ],
                        'select': {
                            'style': 'multi'
                        },
                        "columnDefs": [
                            {
                                'targets': 0,
                                'searchable': false,
                                'orderable': false,
                                'className': 'dt-body-center',
                                'render': function (data, type, full, meta) {
                                    return '<input type="checkbox" class="dt-checkboxes" name="id[]" value="' + $('<div/>').text(data).html() + '">';
                                },
                                'checkboxes': {
                                    'selectRow': true
                                }
                            },
                            {
                                'targets': 5,
                                'searchable': false,
                                'orderable': false
                            },
                            {
                                'targets': 3,
                                'searchable': false,
                                'orderable': false
                            },
                            {
                                'targets': 12,
                                'searchable': false,
                                'orderable': false,
                                'render': function (data, type, full, meta) {

                                    var action = $('<div/>').text(data).html().split("|");
                                    var button = "";

                                    if (action[0] == "MV_STOCK") {
                                        button = '<button type="button" class="btn btn-warning btn-sm moveToBtt" data-type="stock" data-id="' + action[2] + '">Do Stock</button>'
                                    }
                                    if (action[0] == "MV_TRASH") {
                                        button = '<button type="button" class="btn btn-danger btn-sm moveToBtt" data-type="trash" data-id="' + action[2] + '">Remove</button>'
                                    }
                                    if (action[0] == "MV_ARCHIVE") {
                                        button = '<button type="button" class="btn btn-danger btn-sm moveToBtt" data-type="archived" data-id="' + action[2] + '">Archive</button>'
                                    }

                                    <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::info", $userInfo->permission_lists)){ ?>
                                    if (action[1] == "VIEW") {
                                        button = button + '&nbsp;&nbsp;<a class="btn btn-info btn-sm" title="view" target="_blank" href="<?php echo env('APP_URL', '') . "/simcard/info/";?>' + action[2] + '">View</a>'
                                    }
                                    <?php } ?>
                                        return button;
                                }
                            }
                        ],
                        "drawCallback": function (settings) {
                            theInstance.dTableMount();
                        },
                    }
                );

                //var DropDown = "<div class='col-2'><div class='dropdown'> <button class='btn btn-primary dropdown-toggle' type='button' id='dropdownMenu1' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'> Options <span class='caret'></span> </button> <ul class='dropdown-menu' aria-labelledby='dropdownMenu1'> <li><a href='javascript:void(0);' onclick='showAs(\"all\")'><span class='glyphicon glyphicon-font' aria-hidden='true'></span> Show All Sim Cards</a></li> <li><a href='javascript:void(0);' onclick='showAs(\"stocked_only\")'><span class='glyphicon glyphicon-home' aria-hidden='true'></span> Show Stocked Sim Cards Only</a></li> <li><a href='javascript:void(0);' onclick='showAs(\"sold_only\")'><span class='glyphicon glyphicon-shopping-cart' aria-hidden='true'></span> Show Sold Sim Cards Only</a></li> <li><a href='javascript:void(0);' onclick='showAs(\"rejected_only\")'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> Show Rejected Sim Cards Only</a></li> <li><a href='javascript:void(0);' onclick='showAs(\"approved_only\")'><span class='glyphicon glyphicon-ok-circle' aria-hidden='true'></span> Show Approved Sim Cards Only</a></li><li role='separator' class='divider'></li> <li><a href='javascript:void(0);' onclick='showAs(\"archived_only\")'><span class='glyphicon glyphicon-briefcase' aria-hidden='true'></span> Show Archived Sim Cards Only</a></li> ";

                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::change_status", $userInfo->permission_lists)){ ?>
                    //DropDown = DropDown + "<li role='separator' class='divider'></li> <li><a href='javascript:void(0);' onclick='moveTo(\"archived\")'><span class='glyphicon glyphicon-briefcase' aria-hidden='true'></span> Move selected Sim Card as Archived</a></li> <li><a href='javascript:void(0);' onclick='moveTo(\"stock\")'><span class='glyphicon glyphicon-home' aria-hidden='true'></span> Move selected Sim Card as Stocked</a></li>";

                //DropDown = DropDown + "<li><a href='javascript:void(0);' onclick='moveTo(\"reseller_own_stock\")'><span class='glyphicon glyphicon glyphicon-share' aria-hidden='true'></span> Move selected Sim Card as Reseller Own Stocked</a></li>"
                <?php } ?>

                    //DropDown = DropDown + " </ul></div></div>";

                var DropDown2 = '<div class="col-2">&nbsp;&nbsp;&nbsp;<div class="btn-group"> <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Options</button>'+
                    '<div class="dropdown-menu">'+
                        "<a class='dropdown-item showAsBtt' href='javascript:void(0);' data-status='all'><span class='glyphicon glyphicon-font' aria-hidden='true'></span> Show All Sim Cards</a>"+
                        "<a class='dropdown-item showAsBtt' href='javascript:void(0);' data-status='stocked_only'><span class='glyphicon glyphicon-home' aria-hidden='true'></span> Show Stocked Sim Cards Only</a>"+
                        "<a class='dropdown-item showAsBtt' href='javascript:void(0);' data-status='sold_only'><span class='glyphicon glyphicon-shopping-cart' aria-hidden='true'></span> Show Sold Sim Cards Only</a>"+
                        "<a class='dropdown-item showAsBtt' href='javascript:void(0);' data-status='rejected_only'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> Show Rejected Sim Cards Only</a>"+
                        "<a class='dropdown-item showAsBtt' href='javascript:void(0);' data-status='approved_only'><span class='glyphicon glyphicon-ok-circle' aria-hidden='true'></span> Show Approved Sim Cards Only</a>"+
                        '<div class="dropdown-divider"></div>'+
                        "<a class='dropdown-item showAsBtt' href='javascript:void(0);' data-status='archived_only'><span class='glyphicon glyphicon-briefcase' aria-hidden='true'></span> Show Archived Sim Cards Only</a>";

                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::change_status", $userInfo->permission_lists)){ ?>

                    DropDown2 = DropDown2 + "<div class='dropdown-divider'></div>"

                    DropDown2 = DropDown2 + "<a class='dropdown-item moveToBtt' href='javascript:void(0);' data-type='archived'><span class='glyphicon glyphicon-briefcase' aria-hidden='true'></span> Move selected Sim Card as Archived</a>"+
                        "<a class='dropdown-item moveToBtt' href='javascript:void(0);'  data-type='stock'><span class='glyphicon glyphicon-home' aria-hidden='true'></span> Move selected Sim Card as Stocked</a>";

                    DropDown2 = DropDown2 + "<a href='javascript:void(0);' class='dropdown-item moveToBtt' data-type='reseller_own_stock'><span class='glyphicon glyphicon glyphicon-share' aria-hidden='true'></span> Move selected Sim Card as Reseller Own Stocked</a>"
                <?php } ?>

                    <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && in_array("Simcard::remove_sim", $userInfo->permission_lists)
                    ){ ?>

                DropDown2 = DropDown2 + "<a href='javascript:void(0);' class='dropdown-item removeBtt' data-type='remove_sim_card'><span class='glyphicon glyphicon glyphicon-share' aria-hidden='true'></span> Remove Sim Card</a>"
                <?php } ?>

                    DropDown2 = DropDown2 + '</div>'+
                '</div></div>'


                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) && (in_array("StoreController::list", $userInfo->permission_lists))){ ?>
                var resellerList = "<div class='col-2'><div class='form-group' style='margin-left: 27px;'><select id='reseller_id' name='reseller_id' class='form-control set-as-select2' ><option value=''>All Reseller</option><?php

                    if($userInfo->user_type == "store"|| $userInfo->user_type == "Reseller") echo "<option value='".$userInfo->store_vendor_id."'>Self (".ucwords($userInfo->username).")</option>";

                    foreach($storeList as $row){
                    ?><option value='<?php echo $row->id; ?>'>Reseller: <?php echo $row->name; ?></option><?php
                    }

                    ?></select></div></div>";
                <?php } ?>

                var productList="<div class='col-2'><div class='form-group' style='margin-left: 27px;'><select class='form-control set-as-select2' id='product_id'><option value=''>All Product</option><?php
                    foreach($productList as $row){
                    ?><option value='<?php echo $row->id; ?>'>Product: <?php echo $row->name; ?></option><?php
                    }
                    ?></select></div></div>";

                var lotList="<div class='col-2'><div class='form-group' style='margin-left: 27px;'><select class='form-control set-as-select2' id='lot_id'><option value=''>All Lot</option><?php
                    foreach($simCardLot as $row){
                    ?><option value='<?php echo $row->id; ?>'>Lot: <?php echo $row->name; ?></option><?php
                    }
                    ?></select></div></div>";

               var searchBY = '<div class="col-4"><div class="input-group"><input  id="search_text" type="text" class="form-control input1" placeholder="ICCID / Number" /><button class="btn btn-danger waves-effect resetFilter" type="button">Clear</button><button class="btn btn-primary waves-effect doSearch" type="button">Search</button></div></div>';


                <?php if(!empty($userInfo) && $userInfo->user_type == "super_admin"){ ?>
                $("div.toolbar").html('<div class="row">'+DropDown2+productList+lotList+resellerList+searchBY+'</div>');
                <?php }else{ ?>
                    <?php if(in_array("StoreController::list", $userInfo->permission_lists)){ ?>
                        $("div.toolbar").html('<div class="row">'+DropDown2+productList+resellerList+searchBY+'</div>');
                    <?php }else{ ?>
                        $("div.toolbar").html('<div class="row">'+DropDown2+productList+searchBY+'</div>');
                    <?php } ?>
                <?php } ?>


            },
            methods: {
                dTableMount()
                {
                    $(".resetFilter").click(function(e){
                        $("#reseller_id").val("").trigger('change');;
                        $("#mnp_number_value").val("");
                        $("#product_id").val("");
                        $("#lot_id").val("");
                        $("#search_text").val("");
                        theInstance.tableFilter.selected_reseller = "";
                        theInstance.tableFilter.selected_mnp_number = "";
                        theInstance.tableFilter.selected_product = "";
                        theInstance.tableFilter.sales_status = "";
                        theInstance.tableFilter.selected_lot = "";
                        theInstance.tableFilter.status = "";
                        theInstance.tableFilter.search = "";

                        theInstance.loadPageData();
                    });
                    $(".doSearch").click(function(e){
                        theInstance.tableFilter.selected_reseller = $("#reseller_id").val();
                        theInstance.tableFilter.selected_mnp_number = $("#mnp_number_value").val();
                        theInstance.tableFilter.selected_product = $("#product_id").val();
                        theInstance.tableFilter.sales_status = "";
                        theInstance.tableFilter.selected_lot = $("#lot_id").val();
                        theInstance.tableFilter.search = $("#search_text").val();
                        theInstance.loadPageData();
                    });
                    $(".showAsBtt").click(function(e){
                        theInstance.showAs($(this).data("status"));
                    });
                    $(".moveToBtt").click(function(e){
                        theInstance.moveTo($(this).data("type"), $(this).data("id"));
                    });
                    $(".removeBtt").click(function(e){
                        theInstance.remove($(this).data("type"), $(this).data("id"));
                    });

                    $(".input1").on('keyup', function (e) {
                        if (e.key === 'Enter' || e.keyCode === 13) {
                            theInstance.tableFilter.search = $("#search_text").val();
                            theInstance.loadPageData();
                        }
                    });

                    $('#reseller_id').select2();
                    $('#reseller_id').on('select2:select', function (e) {
                        theInstance.tableFilter.selected_reseller = $('#reseller_id').select2('data')[0].id
                    });
                },
                showAs(condition)
                {
                    switch (condition) {
                        case "all":
                            theInstance.tableFilter.selected_reseller = "";
                            theInstance.tableFilter.selected_mnp_number = "";
                            theInstance.tableFilter.selected_product = "";
                            theInstance.tableFilter.sales_status = "";
                            theInstance.tableFilter.selected_lot = "";
                            theInstance.tableFilter.status = "";
                            break;
                        case "stocked_only":
                            theInstance.tableFilter.sales_status = "in_stock";
                            theInstance.tableFilter.status = "";
                            break;
                        case "sold_only":
                            theInstance.tableFilter.sales_status = "sold";
                            theInstance.tableFilter.status = "";
                            break;
                        case "rejected_only":
                            theInstance.tableFilter.sales_status = "sold";
                            theInstance.tableFilter.status = "rejected";
                            break;
                        case "approved_only":
                            theInstance.tableFilter.sales_status = "sold";
                            theInstance.tableFilter.status = "approved";
                            break;
                        case "archived_only":
                            theInstance.tableFilter.sales_status = "";
                            theInstance.tableFilter.status = "archived";
                            break;
                    }

                    theInstance.loadPageData();
                },
                moveTo(type, id)
                {
                    var ids = []

                    if(typeof id !== 'undefined')
                    {
                        ids.push(id)
                    }
                    else
                    {
                        $('input[type="checkbox"]:checked').each(function () {
                            ids.push(this.value);
                        });
                    }

                    if (ids.length != 0)
                    {
                        var conf = confirm("Are you sure about this ?")
                        if (conf == true)
                        {
                            let formData = new FormData();

                            formData.append("ids", ids.join("|"))
                            formData.append("status", type)

                            axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/change_status", formData,
                                {
                                    headers: {
                                        Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                    },
                                }).then(response => {
                                theInstance.loadPageData();
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
                    }
                    else
                    {
                        alert('No Sim Card have been selected.');
                    }
                },
                remove()
                {
                    var conf = confirm("Are you sure about this ? You will not able to recover the sim cards.")
                    if (conf == true)
                    {
                        var ids = []

                        $('input[type="checkbox"]:checked').each(function () {
                            ids.push(this.value);
                        });

                        if (ids.length != 0)
                        {
                            axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/remove_sim_card", {"id":ids.join("|")},
                                {
                                    headers: {
                                        Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                    },
                                }).then(response => {
                                $('input[type="checkbox"]:checked').each(function () {
                                    this.checked = false;
                                });
                                theInstance.loadPageData();
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
                        } else {
                            alert("No Sim Card Have been Selected")
                        }
                    }
                },
                doBulkUpdate()
                {
                    if($("#bulk_update_selected_order").val() == null)
                    {
                        return alert("No Order Selected.")
                    }

                    var ids = []

                    $('input[type="checkbox"]:checked').each(function () {
                        ids.push(this.value);
                    });

                    if (ids.length != 0)
                    {
                        var conf = confirm("Are you sure about this ?")
                        if (conf == true)
                        {
                            let formData = new FormData();

                            formData.append("ids", ids.join("|"))
                            formData.append("order_id", $("#bulk_update_selected_order").val())

                            axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/order/appoint_sim_card", formData,
                                {
                                    headers: {
                                        Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                    },
                                }).then(response => {
                                //location.reload();
                                theInstance.loadPageData();
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
                    }
                    else
                    {
                        alert('No Sim Card have been selected.');
                    }
                },
                loadPageData(){
                    if((new Date().getTime() - theInstance.lastTimeLoaded) > 1500){
                        theInstance.lastTimeLoaded = new Date().getTime()
                        this.masterTable.ajax.reload();
                    }
                },
                resetFilter(){
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
@include('inc.footer', ['load_datatable_scripts' => true])
<style>
    .select2-container .select2-selection--single
    {
        height: 34px;
    }
</style>
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>
