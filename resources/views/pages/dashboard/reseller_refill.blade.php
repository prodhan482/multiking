@include('inc.header', ['load_vuejs' => true])
@include('inc.menu')

<?php

    $currency_conversions_list = $userInfo->currency_conversions_list;
    $euro_conv = 120;
    foreach($currency_conversions_list as $row)
    {
        if($row->type == $userInfo->storeBaseCurrency)
        {
            $euro_conv = $row->conv_amount;
        }
    }
    $result = DB::connection('mysql')->table('store')->selectRaw("conversion_rate")->where(array(
                    'store_id'=>$userInfo->store_vendor_id
                ))->first();

    if(!empty($result))
    {
        $euro_conv = floatval($result->conversion_rate);
    }
?>

<!-- BEGIN: Content-->
<div class="app-content content " id="app">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- Dashboard Analytics Start -->
            <section id="dashboard-analytics">
                <div class="row match-height">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row match-height">
                                    <div :class="[(isMobile()?'col-sm-6':'col')]" style="text-align: center"
                                         v-for="option in mfs_list_t1">
                                        <a href="javascript:void(0)" v-on:click="selectTheOption(option.mfs_id, option.mfs_name, option.image_path, option.mfs_type)">
                                            <img :src="'/'+option.image_path" class="img-fluid" :alt="option.mfs_name" style="max-height: 100px;">
                                        </a>
                                        <br v-if="isMobile()"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row match-height">
                                    <div :class="[(isMobile()?'col-sm-6':'col')]" style="text-align: center"
                                         v-for="option in mfs_list_t2">
                                        <a href="javascript:void(0)" v-on:click="selectTheOption(option.mfs_id, option.mfs_name, option.image_path, option.mfs_type)">
                                            <img :src="'/'+option.image_path" class="img-fluid" :alt="option.mfs_name" style="max-height: 100px;">
                                        </a>
                                        <br v-if="isMobile()"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="dsjflkjsdlkfjlksdf" :class="[((mfs_package_list[request.selected_mfs] && mfs_package_list[request.selected_mfs].length > 0)?'col-lg-4':'col-lg-6'), 'col-12']" v-if="mfs_list_by_id[request.selected_mfs]">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="basicInput1">Mobile Number</label>
                                    <input type="number" step="any" class="form-control" id="basicInput1" placeholder="01XXXXXXXXX" v-model="request.mobile_number">
                                    {{--<select class="form-control theMobileNumberdd" v-model="request.mobile_number" saved_numbers>
                                        <option v-for="number in saved_numbers" :value="number.phone_number" v-html='(number.name+" ["+number.phone_number+"]")'></option>
                                    </select>--}}
                                </div>
                                <div class="form-group">
                                    <label>Type</label>
                                    <select class="form-control mfs-type" v-model="request.mfs_type">
                                        <option value="" selected>Select A Type</option>
                                        <option value="personal" v-if="(mfs_list_by_id[request.selected_mfs].mfs_type !== 'mobile_recharge')">Personal</option>
                                        <option value="agent" v-if="(mfs_list_by_id[request.selected_mfs].mfs_type !== 'mobile_recharge')">Agent</option>
                                        <option value="prepaid" v-if="(mfs_list_by_id[request.selected_mfs].mfs_type === 'mobile_recharge')">Prepaid</option>
                                        <option value="postpaid" v-if="(mfs_list_by_id[request.selected_mfs].mfs_type === 'mobile_recharge')">Postpaid</option>
                                    </select>
                                </div>

                                <div class="form-group" v-if="!isMobile()">
                                    <label for="basicInput3">Send Amount</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend" v-if="(request.sending_currency == 'EURO')">
                                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-html="(request.send_money_type == 'with_charge'?'With Charge':'Without Charge')"></button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="javascript:void(0);" v-on:click="changeSendMoneyType('with_charge')">With Charge</a>
                                                <a class="dropdown-item" href="javascript:void(0);" v-on:click="changeSendMoneyType('without_charge')">Without Charge</a>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" id="basicInput3" placeholder="In Amount" v-model="request.send_money" v-on:keyup="comChargClacu">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-html="request.sending_currency"></button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="javascript:void(0);" v-on:click="changeSelectedCurrency('EURO')">Euro</a>
                                                <a class="dropdown-item" href="javascript:void(0);" v-on:click="changeSelectedCurrency('<?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?>')"><?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>






                                <div class="form-group" v-if="isMobile()">
                                    <label for="basicInput3">Send Amount</label>
                                    <div class="input-group">
                                        <input type="number" step="any" class="form-control" id="basicInput3" placeholder="In Amount" v-model="request.send_money" v-on:keyup="comChargClacu">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-html="request.sending_currency"></button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="javascript:void(0);" v-on:click="changeSelectedCurrency('EURO')">Euro</a>
                                                <a class="dropdown-item" href="javascript:void(0);" v-on:click="changeSelectedCurrency('<?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?>')"><?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" v-if="(request.sending_currency == 'EURO' && isMobile())">
                                    <label>Charge Type</label>
                                    <select class="form-control" v-model="request.send_money_type">
                                        <option value="with_charge" selected>With Charge</option>
                                        <option value="without_charge">Without Charge</option>
                                    </select>
                                </div>





                                <div class="form-group" v-if="(request.sending_currency == 'EURO')">
                                    <label for="basicInput80">Conversion Rate (1 &euro; = ? <?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?>)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="basicInput80" placeholder="In Amount" v-model="request.conv_rate" v-on:keyup="comChargClacu">
                                        <span class="input-group-append">
                                          <span class="input-group-text"><?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?></span>
                                            <button class="btn btn-outline-warning waves-effect" type="button" v-on:click="updateConversionRate">Set</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" v-if="(request.sending_currency == 'EURO')">
                                    <label for="basicInput9">Service Charge</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="basicInput9" placeholder="In Percent" v-model="request.reseller_servie_charge" v-on:keyup="comChargClacu">
                                        <span class="input-group-append">
                                            <span class="input-group-text">Euro (&euro;)</span>
                                            <button class="btn btn-outline-warning waves-effect" type="button" data-toggle="modal" data-target=".setServiceChargeSlab">Set</button>
                                        </span>
                                    </div>
                                </div>


                                <div class="form-group" v-if="(request.sending_currency != 'EURO')">
                                    <label for="basicInput4">Received Amount</label>
                                    <div class="input-group">
                                        <input type="number" step="any" class="form-control" placeholder="In Amount" v-model="request.receive_money" v-on:keyup="comChargClacu2">
                                        <span class="input-group-append">
                                          <span class="input-group-text"><?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?></span>
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div :class="['col-lg-4', 'col-12']" v-if="mfs_list_by_id[request.selected_mfs] && mfs_package_list[request.selected_mfs] && mfs_package_list[request.selected_mfs].length > 0">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between pb-0">
                                <h4 class="card-title">Package List</h4>
                            </div>
                            <div class="card-body" style="overflow-y: auto; max-height: 317px;">
                                <br>
                                <div class="row">

                                    <div style="cursor: pointer;" class="col-6 col-md-4 mb-1" v-for="option in mfs_package_list[request.selected_mfs]" v-on:click="setThePackage(option.row_id)">
                                        <div style="background-color: #0E459E; color: white; text-align: center; padding: 10px;">
                                            <div style="font-weight: bold;" v-html="option.package_name"></div>
                                            <div v-html="('Amount: '+toNumNoDecimal(option.amount))" style="background: white; color: #046ea6; font-weight: bold;"></div>
                                            <div style="background: white; color: black; font-weight: bold;">
                                                <span>Cost: </span>
                                                <span v-html="toNumNoDecimal(option.amount)" style="color: red">Cost: </span>
                                                <span><?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--<div class="mb-1" v-for="option in mfs_package_list[request.selected_mfs]">
                                    <div v-on:click="setThePackage(option.row_id)">
                                        <span v-html="option.package_name"></span><br>
                                        <span>Amount</span>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary waves-effect" v-html="option.package_name" v-on:click="setThePackage(option.row_id)"></button>
                                </div>--}}
                            </div>
                        </div>
                    </div>

                    <div :class="[((mfs_package_list[request.selected_mfs] && mfs_package_list[request.selected_mfs].length > 0)?'col-lg-4':'col-lg-6'), 'col-12']" v-if="mfs_list_by_id[request.selected_mfs]">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between pb-0">
                                <h4 class="card-title">Summery</h4>
                            </div>
                            <div class="card-body">
                                <br>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="pr-1" style="padding: 5px;">Gateway:</td>
                                            <td style="padding: 5px; color: orange;"><img :src="request.selected_mfs_logo_path" alt="bKash" class="img-fluid" style="max-height: 20px;">
                                                <span class="font-weight-bold" v-html="(request.selected_mfs_name +' ('+request.mfs_type.toUpperCase()+')')"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pr-1" style="padding: 5px;">Mobile Number:</td>
                                            <td style="padding: 5px;" v-html="request.mobile_number">01XXXXXXXXX</td>
                                        </tr>
                                        <tr v-if="(request.sending_currency != 'EURO')">
                                            <td class="pr-1" style="padding: 5px;">Send Amount:</td>
                                            <td style="padding: 5px;" v-html='(request.sending_currency+" "+request.send_money)'>&nbsp;</td>
                                        </tr>
                                        <tr v-if="(request.sending_currency == 'EURO')">
                                            <td class="pr-1" style="padding: 5px;">Send Amount:</td>
                                            <td style="padding: 5px;" v-html='(request.sending_currency+" "+(request.send_money - (request.reseller_servie_charge * (request.send_money_type == "with_charge"?+1:-1) )))'>&nbsp;</td>
                                        </tr>
                                        <tr v-if="(request.sending_currency == 'EURO')">
                                            <td class="pr-1" style="padding: 5px;">Send Amount (<?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?>):</td>
                                            <td style="padding: 5px;" v-html='("<?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?>"+" "+toNum(request.visualSendMoney)+"/=")'>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 5px;" class="pr-1">Charge:</td>
                                            <td  style="padding: 5px;" v-html='("<?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?> "+toNum(request.visualCharge)+"/=")'>&nbsp;</td>
                                        </tr>
                                        <tr v-if="(request.sending_currency != 'EURO')">
                                            <td style="padding: 5px;" class="pr-1">Commission:</td>
                                            <td style="padding: 5px;" v-html='("<?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?> "+toNum(request.send_money * request.commission)+"/=")'>&nbsp;</td>
                                        </tr>
                                        <tr v-if="(request.sending_currency != 'EURO')">
                                            <td style="padding: 5px;" class="pr-1">Received Amount:</td>
                                            <td style="padding: 5px;" ><span class="font-weight-bold" v-html='("<?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?> "+toNum(request.receive_money)+"/=")'>&nbsp;</span></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <br><br>
                                <button type="reset" class="btn btn-primary mr-1 waves-effect waves-float waves-light" v-on:click="sendARequest">Confirm</button>

                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body" style="padding: 0">
                                <table class="table datatable-basic dataTable no-footer datatable-scroll-y">
                                    <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Created At</th>
                                        <th>Receipt</th>
                                        <th>Phone Number</th>
                                        <th>Amount</th>
                                        <th>Balance</th>
                                        <th>MFS</th>
                                        <th>Note</th>
                                        <th>Vendor Note</th>
                                        <th>Status</th>
                                        <th>Last Updated On</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Dashboard Analytics end -->
        </div>
    </div>



    <div class="modal fade setServiceChargeSlab" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" v-if="!isMobile()">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Euro Service Charge</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">From (&euro;)</th>
                            <th scope="col">To (&euro;)</th>
                            <th scope="col">Charge (&euro;)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in euroServiceChargeList">
                            <td>
                                <input type="number" step="any" class="form-control" placeholder="Enter From (&euro;) Slab" v-model="item.from">
                            </td>
                            <td>
                                <input type="number" step="any" class="form-control" placeholder="Enter To (&euro;) Slab" v-model="item.to">
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" placeholder="Enter Service Charge (&euro;) Amount" v-model="item.charge">
                                    <div class="input-group-append">
                                        <button :class="('btn btn-danger ')+index" type="button" v-on:click="removeOldRowServiceTable(index)">X</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-link mr-auto" v-on:click="addNewRowServiceTable()">Add New Row</button>
                    <button class="btn btn-link" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" v-on:click="saveServiceTable()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade setServiceChargeSlab" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" v-if="isMobile()">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Euro Service Charge</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="card" v-for="(item, index) in euroServiceChargeList">
                        <div class="card-body">
                            <div class="input-group mb-1">
                                <span class="input-group-text">From (&euro;)</span>
                                <input type="number" step="any" class="form-control" placeholder="Enter From (&euro;) Slab" v-model="item.from">
                            </div>
                            <div class="input-group mb-1">
                                <span class="input-group-text">To (&euro;)</span>
                                <input type="number" step="any" class="form-control" placeholder="Enter To (&euro;) Slab" v-model="item.to">
                            </div>
                            <div class="input-group mb-1">
                                <span class="input-group-text">Charge (&euro;)</span>
                                <input type="number" step="any" class="form-control" placeholder="Enter Service Charge (&euro;) Amount" v-model="item.charge">
                            </div>
                            <button :class="('btn btn-danger ')+index" type="button" v-on:click="removeOldRowServiceTable(index)">Delete This</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-link mr-auto" v-on:click="addNewRowServiceTable()">Add New Row</button>
                    <button class="btn btn-link" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" v-on:click="saveServiceTable()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade receiptShow" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Receipt</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div id="receiptShowPrintArea" class="modal-body"></div>
                <div class="modal-footer">
                    <button class="btn btn-primary" v-on:click="printDiv">Print</button>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
    $euroServiceChargeList = array(
        array('from'=>'0', 'to'=>'50', 'charge'=>'3'),
        array('from'=>'50', 'to'=>'100', 'charge'=>'4'),
        array('from'=>'100', 'to'=>'150', 'charge'=>'5'),
        array('from'=>'150', 'to'=>'200', 'charge'=>'6'),
        array('from'=>'200', 'to'=>'250', 'charge'=>'7'),
    );
?>

<script>
    $(function(){
        var app = new Vue({
            el: '#app',
            data() {
                return {
                    masterTable:{},
                    euroServiceChargeList:<?php echo json_encode($euroServiceChargeList); ?>,
                    euroServiceChargeListT1:<?php echo json_encode($euroServiceChargeList); ?>,
                    euroServiceChargeListT2:<?php echo json_encode($euroServiceChargeList); ?>,
                    saved_numbers:[],
                    doSaveDone:false,
                    waitingDialogInShow:false,
                    mfs_list:[],
                    mfs_list_t1:[],
                    mfs_list_t2:[],
                    mfs_list_by_id:{},
                    mfs_package_list:{},
                    mfs_package_list_id:{},
                    currentPackageList:[],
                    tableFilter:{
                        limit:10
                    },
                    request:{
                        selected_mfs:"",
                        selected_mfs_name:"",
                        selected_mfs_name_type:"",
                        selected_mfs_logo_path:"",
                        selected_mfs_package:"",
                        mfs_type:"",
                        send_money:"",
                        reseller_servie_charge:"0",
                        sending_currency:"<?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?>",
                        conv_rate:"<?php echo $euro_conv; ?>",
                        receive_money:"",
                        send_money_type:"with_charge",
                        visualCharge:0,
                        visualSendMoney:0,
                        charge:0,
                        commission:0,
                        total:"",
                        mobile_number:"",
                        note:""
                    },
                    requestDialogBox:{}
                }
            },
            mounted() {
                theInstance = this;
                setTimeout(function () {
                    theInstance.doTable();
                }, 500);
            },
            methods: {
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
                addNewRowServiceTable()
                {
                    this.euroServiceChargeList.push({"from":"","to":"","charge":""})
                },
                removeOldRowServiceTable(index)
                {
                    if(this.euroServiceChargeList.length > 1)
                    {
                        this.euroServiceChargeList.splice(index,1)
                    }
                },
                saveServiceTable()
                {
                    let formData = new FormData();
                    if(theInstance.request.selected_mfs_name_type == "mobile_recharge")
                    {
                        formData.append('service_charge_slabs_t2', JSON.stringify(this.euroServiceChargeList))
                    }
                    else
                    {
                        formData.append('service_charge_slabs', JSON.stringify(this.euroServiceChargeList));
                    }

                    theInstance.showWaitingDialog();
                    axios.post("<?php echo env('APP_URL', ''); ?>/api/store/<?php echo $userInfo->store_vendor_id; ?>", formData,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        })
                        .then(response => {
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
                                        break;
                                }
                            }
                        });

                },
                dTableMount() {
                    $(".showRechargeReceipt").click(function(e){
                        theInstance.showReceipt($(this).data("id"));
                    });
                },
                printDiv() {
                    html2canvas($('#receiptShowPrintArea'), {
                        onrendered: function (canvas) {
                            var dataUrl2 = canvas.toDataURL("image/jpg");

                            var windowContent = '<!DOCTYPE html>';
                            windowContent += '<html>'
                            windowContent += '<head><title>Print canvas</title></head><style>html, body { height: auto; } @media print { body, page { margin: 0; box-shadow: #000000; page-break-after: avoid; page-break-before: avoid; size: landscape; } body{ height: auto; } }</style>';
                            windowContent += '<body style="padding:0: margin:0">'
                            windowContent += '<img src="' + dataUrl2 + '">';
                            windowContent += '</body>';
                            windowContent += '</html>';
                            var printWin = window.open('','','width=800,height=800');
                            printWin.document.open();
                            printWin.document.write(windowContent);
                            printWin.document.close();
                            printWin.focus();

                            setTimeout(function(){ printWin.print();printWin.close(); }, 500);
                        }
                    });
                },
                showReceipt(recharge_id)
                {
                    theInstance.showWaitingDialog();
                    axios.get("<?php echo env('APP_URL', ''); ?>/api/recharge/html_receipt/"+recharge_id,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        })
                        .then(response => {
                            theInstance.hideWaitingDialog();
                            $("#receiptShowPrintArea").html(response.data.html);
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
                                        break;
                                }
                            }
                        });

                    //
                    $(".receiptShow").modal('show');
                },
                doTable()
                {
                    this.masterTable = $('.dataTable').DataTable({
                            scrollX: true,
                            scrollY: (this.windowHeight - 550)+'px',//(this.windowHeight - 500)+'px',
                            scrollCollapse: true,
                            "searching": false,
                            "info": false,
                            "paging": false,
                            "ordering": false,
                            "preDrawCallback": function(settings)
                            {
                                theInstance.scrollPosition = $(".dataTables_scrollBody").scrollTop();
                            },
                            "drawCallback": function(settings)
                            {
                                //var api = this.api();
                                $(".dataTables_scrollBody").scrollTop(theInstance.scrollPosition);
                                theInstance.dTableMount();
                                theInstance.page_message = ''
                            },
                            "columnDefs": [
                                {
                                    'targets': 2,'searchable': false, 'orderable': false, 'width':'10%',
                                    'render': function (data, type, row)
                                    {
                                        var info = $('<div/>').text(data).html();
                                        return (info.length > 1 ?'<button class="btn btn-sm btn-success showRechargeReceipt" data-id="'+info+'">Show</button>':'');
                                    }
                                },
                                {
                                    'targets': 9,'searchable': false, 'orderable': false, 'width':'10%',
                                    'render': function (data, type, full, meta)
                                    {
                                        var info = $('<div/>').text(data).html();

                                        if(info === "Pending" || info === "Requested") return '<span class="badge badge-warning badge-pill" style="font-size: 14px;">'+info+'</span>';
                                        if(info === "Approved") return '<span class="badge badge-success badge-pill" style="font-size: 14px;">'+info+'</span>';
                                        if(info === "Progressing") return '<span class="badge badge-info badge-pill" style="font-size: 14px;">'+info+'</span>';
                                        if(info === "Rejected") return '<span class="badge badge-danger badge-pill" style="font-size: 14px;">'+info+'</span>';

                                        return '';
                                    }
                                },
                            ],
                            createdRow: function (row, data, index) {
                                if (data[8] == "Balance Refill" || data[8] == "Refund") {
                                    $(row).addClass("table-success");
                                }
                            },
                            "processing": true,
                            "serverSide": true,
                            "pageLength": 20,
                            "language": {
                                "emptyTable": "No Adjustment History Data Found.",
                            },
                            "ajax": {
                                "url": '<?php echo env('APP_URL', ''); ?>/api/recharge/activity',
                                "type": "POST",
                                'beforeSend': function (request) {
                                    theInstance.showWaitingDialog();
                                    request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                                },
                                "data": function ( d )
                                {
                                    d.limit = theInstance.tableFilter.limit
                                },
                                complete: function(data)
                                {
                                    theInstance.hideWaitingDialog();
                                    theInstance.mfs_list = data.responseJSON.mfs_list
                                    //theInstance.euroServiceChargeList = data.responseJSON.euroServiceChargeList
                                    theInstance.euroServiceChargeListT1 = data.responseJSON.euroServiceChargeList_1;
                                    theInstance.euroServiceChargeListT2 = data.responseJSON.euroServiceChargeList_2;
                                    theInstance.saved_numbers = data.responseJSON.saved_numbers;

                                    for(var m in data.responseJSON.mfs_list)
                                    {
                                        theInstance.mfs_package_list[data.responseJSON.mfs_list[m].mfs_id] = []
                                        theInstance.mfs_list_by_id[data.responseJSON.mfs_list[m].mfs_id] = data.responseJSON.mfs_list[m]

                                        if(data.responseJSON.mfs_list[m].mfs_type == "financial_transaction")
                                        {
                                            theInstance.mfs_list_t1.push(data.responseJSON.mfs_list[m])
                                        }
                                        else
                                        {
                                            theInstance.mfs_list_t2.push(data.responseJSON.mfs_list[m])
                                        }
                                    }

                                    for(var m in data.responseJSON.mfs_package_list)
                                    {
                                        var id = data.responseJSON.mfs_package_list[m].mfs_id;
                                        if (typeof theInstance.mfs_package_list[id] === 'undefined') theInstance.mfs_package_list[id] = []
                                        if (typeof theInstance.mfs_package_list[id] !== 'undefined') theInstance.mfs_package_list[id].push(data.responseJSON.mfs_package_list[m]);

                                        theInstance.mfs_package_list_id[data.responseJSON.mfs_package_list[m].row_id] = data.responseJSON.mfs_package_list[m];
                                    }

                                    for(var m in data.responseJSON.store_mfs_slab)
                                    {
                                        if(parseFloat(data.responseJSON.store_mfs_slab[m].charge) > 0){
                                            theInstance.mfs_list_by_id[data.responseJSON.store_mfs_slab[m].id].default_charge = data.responseJSON.store_mfs_slab[m].charge
                                        }
                                        if(parseFloat(data.responseJSON.store_mfs_slab[m].commission) > 0) {
                                            theInstance.mfs_list_by_id[data.responseJSON.store_mfs_slab[m].id].default_commission = data.responseJSON.store_mfs_slab[m].commission
                                        }
                                    }

                                    <?php if(!empty($_GET) && !empty($_GET['mfs'])): ?>

                                    if(theInstance.isMobile())
                                    {
                                        setTimeout(function(){
                                            $('html, body').animate({
                                                scrollTop: $("#dsjflkjsdlkfjlksdf").offset().top - 90
                                            }, 500);
                                        }, 500);
                                    }
                                    theInstance.selectTheOption("<?php echo $_GET['mfs']; ?>", theInstance.mfs_list_by_id["<?php echo $_GET['mfs']; ?>"].mfs_name, theInstance.mfs_list_by_id["<?php echo $_GET['mfs']; ?>"].image_path, "<?php echo $_GET['type']; ?>")
                                    <?php endif; ?>
                                    setTimeout(function (){
                                        $('.theMobileNumberdd').select2({'width':'100%', 'placeholder':'01XXXXXXXXX', tags: true});
                                        $('.theMobileNumberdd').on('select2:select',function(e)
                                        {
                                            var data = e.params.data;
                                            theInstance.request.mobile_number = data.id;
                                            console.log(data)
                                        });
                                    }, 500);
                                },
                                error: function (xhr, error, thrown)
                                {
                                    console.log("Error");
                                }
                            },
                        }
                    );
                },
                async makeForceLogout() {
                    if (confirm("Your Session have been Expired. You have to re-login to continue. Press ok to logout")) {
                        try {
                            let response = await this.$auth.logout()
                            console.log(response.data)
                        } catch (err) {
                            console.log(err)
                        }
                        window.location.href = "login"
                    }
                },
                comChargClacu() {

                    var i = 1;

                    if(theInstance.request.sending_currency != "<?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?>")
                    {
                        theInstance.calculateServiceCharge();

                        i = parseFloat(theInstance.request.conv_rate);
                        theInstance.request.charge = (theInstance.request.reseller_servie_charge > 0?parseFloat(theInstance.request.reseller_servie_charge/theInstance.request.send_money):0);
                        theInstance.request.commission = 0;

                        //theInstance.request.visualSendMoney = (i * (parseFloat(theInstance.request.send_money) - ((theInstance.request.reseller_servie_charge * i) * ((theInstance.request.send_money_type == "with_charge")?+1:-1)) ));

                        if(theInstance.request.send_money_type == "without_charge")
                        {
                            theInstance.request.visualSendMoney = ( i * (parseFloat(theInstance.request.send_money)))
                        }
                        else
                        {
                            theInstance.request.visualSendMoney = Math.round(( i * (parseFloat(theInstance.request.send_money) - (parseFloat(theInstance.request.reseller_servie_charge) * ((theInstance.request.send_money_type == "with_charge")?+1:-1) ) )))
                        }
                    }
                    else
                    {
                        theInstance.request.charge = parseFloat(theInstance.mfs_list_by_id[theInstance.request.selected_mfs].default_charge);
                        if(theInstance.request.charge > 0) theInstance.request.charge = theInstance.request.charge / 100;

                        theInstance.request.commission = parseFloat(theInstance.mfs_list_by_id[theInstance.request.selected_mfs].default_commission);
                        if(theInstance.request.commission > 0) theInstance.request.commission = theInstance.request.commission / 100;

                        theInstance.request.visualSendMoney = (i * parseFloat(theInstance.request.send_money));
                    }

                    theInstance.request.visualCharge = Math.round(parseFloat((i * parseFloat(theInstance.request.send_money)) * theInstance.request.charge))

                    theInstance.request.receive_money = numeral(Math.round((i * parseFloat(theInstance.request.send_money)) + parseFloat((i * parseFloat(theInstance.request.send_money)) * theInstance.request.commission) - parseFloat((i * parseFloat(theInstance.request.send_money)) * theInstance.request.charge))).format('0.00');

                    if(theInstance.request.send_money_type == "without_charge" && theInstance.request.sending_currency != "<?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?>")
                    {
                        theInstance.request.receive_money = numeral(Math.round((i * parseFloat(theInstance.request.send_money)) + parseFloat((i * parseFloat(theInstance.request.send_money)) * theInstance.request.commission))).format('0.00');
                    }

                },
                comChargClacu2() {

                    var i = 1;

                    if(theInstance.request.sending_currency != "<?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?>")
                    {
                        theInstance.calculateServiceCharge();
                        i = parseFloat(theInstance.request.conv_rate);
                        //theInstance.request.charge = parseFloat(theInstance.request.reseller_servie_charge);
                        theInstance.request.commission = 0;

                        //theInstance.request.send_money = numeral((theInstance.request.receive_money / (1 - theInstance.request.charge) / i)).format('0.00');
                        //theInstance.request.send_money = numeral((theInstance.request.receive_money + (parseFloat(theInstance.request.reseller_servie_charge) * i)) / i).format('0.00');

                        theInstance.request.send_money = numeral(((parseFloat(theInstance.request.receive_money) / i) + parseFloat(theInstance.request.reseller_servie_charge))).format('0.00');
                        if(theInstance.request.send_money_type == "without_charge")
                        {
                            theInstance.request.send_money = numeral((parseFloat(theInstance.request.receive_money) / i)).format('0.00');
                        }
                    }
                    else
                    {
                        theInstance.request.charge = parseFloat(theInstance.mfs_list_by_id[theInstance.request.selected_mfs].default_charge);
                        if(theInstance.request.charge > 0) theInstance.request.charge = theInstance.request.charge / 100;

                        theInstance.request.commission = parseFloat(theInstance.mfs_list_by_id[theInstance.request.selected_mfs].default_commission);
                        if(theInstance.request.commission > 0) theInstance.request.commission = theInstance.request.commission / 100;

                        theInstance.request.send_money = numeral(Math.round(parseFloat(theInstance.request.receive_money) - parseFloat(theInstance.request.receive_money * theInstance.request.commission) + parseFloat(theInstance.request.receive_money * theInstance.request.charge))).format('0.00');
                    }

                    theInstance.request.visualCharge = parseFloat(parseFloat(theInstance.request.send_money * i) * theInstance.request.charge);
                    theInstance.request.visualSendMoney = (i * parseFloat(theInstance.request.send_money));
                },
                calculateServiceCharge()
                {
                    for(var m in theInstance.euroServiceChargeList)
                    {
                        if(parseFloat(theInstance.euroServiceChargeList[m].from) < parseFloat(theInstance.request.send_money)
                            && parseFloat(theInstance.euroServiceChargeList[m].to) >= parseFloat(theInstance.request.send_money))
                        {
                            theInstance.request.reseller_servie_charge = parseFloat(theInstance.euroServiceChargeList[m].charge)
                        }
                    }
                },
                updateConversionRate()
                {
                    var conv_rate = prompt("Set New Conversion Rate", "<?php echo $euro_conv; ?>")
                    if (conv_rate != null && parseFloat(conv_rate) > 0) {
                        axios.post("/api/stores/save_store_conversion_rate", {"conversion_rate":conv_rate},
                            {
                                headers: {
                                    Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                },
                            }).then(response => {
                            location.reload();
                        })
                            .catch(error => {
                                if (error.response) {
                                    switch (error.response.status) {
                                        case 401:
                                            theInstance.makeForceLogout()
                                            break;
                                        default:
                                            alert(error.response.data.message.join(","))
                                            break;
                                    }
                                }
                            });
                    } else {
                        alert("Invalid Input. try Again.")
                    }
                },
                toNum(info)
                {
                    return numeral(parseFloat(info)).format('0,0.00');
                },
                toNumNoDecimal(info)
                {
                    return numeral(parseFloat(info)).format('0,0');
                },
                changeSelectedCurrency(cur)
                {
                    theInstance.request.sending_currency = cur;
                    theInstance.comChargClacu();
                },
                changeSendMoneyType(send_money_type)
                {
                    theInstance.request.send_money_type = send_money_type;
                    theInstance.comChargClacu();
                },
                loadPageData(){
                    this.masterTable.ajax.reload();
                },
                isMobile() {
                    if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                        return true
                    } else {
                        return false
                    }
                },
                selectTheOption(mfs_id, selected_mfs_name, selected_mfs_logo_path, selected_mfs_type)
                {
                    theInstance.request.selected_mfs = mfs_id;
                    theInstance.request.selected_mfs_name = selected_mfs_name;
                    theInstance.request.selected_mfs_name_type = selected_mfs_type;
                    theInstance.request.mfs_type = "";
                    theInstance.request.selected_mfs_logo_path = "/"+selected_mfs_logo_path;

                    if(selected_mfs_type == "mobile_recharge")
                    {
                        theInstance.euroServiceChargeList = theInstance.euroServiceChargeListT2
                        theInstance.request.mfs_type = "prepaid";
                    } else {
                        theInstance.euroServiceChargeList = theInstance.euroServiceChargeListT1
                        theInstance.request.mfs_type = "personal";
                    }

                    if(theInstance.request.send_money.length < 1) theInstance.request.send_money = 0;

                    <?php /*if($resellerCreation):?>
                    if(theInstance.request.send_money_euro.length < 1) theInstance.request.send_money_euro = 1;
                    theInstance.request.conv_rate = 120;
                    <?php endif;*/ ?>

                    theInstance.request.charge = parseFloat(theInstance.mfs_list_by_id[theInstance.request.selected_mfs].default_charge);
                    if(theInstance.request.charge > 0) theInstance.request.charge = theInstance.request.charge / 100;

                    theInstance.request.commission = parseFloat(theInstance.mfs_list_by_id[theInstance.request.selected_mfs].default_commission);
                    if(theInstance.request.commission > 0) theInstance.request.commission = theInstance.request.commission / 100;

                    theInstance.comChargClacu();

                    <?php /* if($resellerCreation):?>
                    theInstance.comChargClacu3();
                    <?php else: ?>
                    theInstance.comChargClacu();
                    <?php endif; */?>
                },
                setThePackage(package_id)
                {
                    theInstance.request.selected_mfs_package = package_id

                    if(theInstance.mfs_package_list_id[package_id])
                    {
                        theInstance.request.charge = parseFloat(theInstance.mfs_package_list_id[package_id].charge);
                        if(theInstance.request.charge > 0) theInstance.request.charge = theInstance.request.charge / 100;

                        theInstance.request.commission = parseFloat(theInstance.mfs_package_list_id[package_id].discount);
                        if(theInstance.request.commission > 0) theInstance.request.commission = theInstance.request.commission / 100;

                        theInstance.request.send_money = theInstance.mfs_package_list_id[package_id].amount;

                        theInstance.comChargClacu();
                    }
                },
                verifyRequest1()
                {
                    if(!theInstance.request.mobile_number.match(/(^(\+01|01))[3-9]{1}(\d){9}$/))
                    {
                        alert("Invalid Bangladeshi Mobile Number.")
                        return;
                    }

                    var foundMobileNumber = false;
                    for (let i in this.saved_numbers) {
                        if(this.saved_numbers[i].phone_number == theInstance.request.mobile_number)
                        {
                            foundMobileNumber = true;
                        }
                    }

                    if(theInstance.doSaveDone) foundMobileNumber = true;

                    if(!foundMobileNumber)
                    {
                        bootbox.prompt(
                            "Want to save Mobile Number ("+theInstance.request.mobile_number+") by name ? Put the name here"
                            ,function(result){
                                if(result)
                                {
                                    axios.post("<?php echo env('APP_URL', ''); ?>/api/recharge/save_number", {"mobile_number":theInstance.request.mobile_number, "name":result},
                                        {
                                            headers: {
                                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                            },
                                        })
                                        .then(response => {
                                            theInstance.doSaveDone = true;
                                            theInstance.sendARequest();
                                        })
                                        .catch(error => {
                                            console.log(error)
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
                                else
                                {
                                    theInstance.sendARequest()
                                }
                            }
                        );
                    } else {
                        theInstance.sendARequest()
                    }
                },
                sendARequest()
                {
                    if(theInstance.request.selected_mfs_name !== "Rocket" && !theInstance.request.mobile_number.match(/(^(\+01|01))[3-9]{1}(\d){8}$/))
                    {
                        alert("Invalid Bangladeshi Mobile Number.")
                        return;
                    }

                    if(theInstance.request.selected_mfs_name === "Rocket" && !theInstance.request.mobile_number.match(/(^(\+01|01))[3-9]{1}(\d){9}$/))
                    {
                        alert("Invalid Rocket Account.")
                        return;
                    }

                    if(theInstance.request.mfs_type.length === 0)
                    {
                        alert("Please select a type")
                        return;
                    }

                    if(theInstance.request.selected_mfs_name_type == "mobile_recharge" && parseFloat(theInstance.request.send_money) > 1000)
                    {
                        alert("You cannot recharge more then 1000/=")
                        return;
                    }

                    if(theInstance.request.selected_mfs_name_type == "financial_transaction" && theInstance.request.sending_currency == 'EURO' && parseFloat(theInstance.request.visualSendMoney) < 1000)
                    {
                        alert("You cannot send less then 1000/=")
                        return;
                    }

                    if(theInstance.request.selected_mfs_name_type == "financial_transaction" && theInstance.request.sending_currency != 'EURO' && parseFloat(theInstance.request.send_money) < 1000)
                    {
                        alert("You cannot send less then 1000/=")
                        return;
                    }

                    theInstance.requestDialogBox = bootbox.dialog({
                        title: 'Final Confirmation.',
                        show: false,
                        message: (
                            '<div class="price-details">'+
                            '<ul class="list-unstyled">'+
                                '<li class="price-detail">'+
                                    '<div class="detail-title">Mobile Number</div>'+
                                    '<div class="detail-amt font-weight-bolder">'+theInstance.request.mobile_number+'</div>'+
                                '</li>'+
                            '</ul>'+
                            '<hr>'+
                                '<ul class="list-unstyled">'+
                                    '<li class="price-detail">'+
                                        '<div class="detail-title detail-total">Gateway</div>'+
                                        '<div class="detail-amt font-weight-bolder">'+(theInstance.request.selected_mfs_name +' ('+theInstance.request.mfs_type.toUpperCase()+')')+'</div>'+
                                    '</li>'+
                                '</ul>'+

                            '<hr>'+

                            '<ul class="list-unstyled">'+
                            '<li class="price-detail">'+
                            '<div class="detail-title detail-total">Send Money</div>'+

                            (theInstance.request.sending_currency == 'EURO'?
                                ('<div class="detail-amt font-weight-bolder">'+theInstance.request.sending_currency+" "+theInstance.toNum(
                                    theInstance.request.send_money -
                                    (theInstance.request.reseller_servie_charge * (theInstance.request.send_money_type == "with_charge"?+1:-1) )
                                )+'/=</div>')
                                :
                                '<div class="detail-amt font-weight-bolder">'+theInstance.request.sending_currency+" "+theInstance.toNum(theInstance.request.send_money)+'/=</div>')+


                            '</li>'+
                            '</ul>'+
                            (
                                theInstance.request.sending_currency == 'EURO'?
                                ('<hr>'+
                                '<ul class="list-unstyled">'+
                                '<li class="price-detail">'+
                                '<div class="detail-title detail-total">Send Money</div>'+
                                '<div class="detail-amt font-weight-bolder"><?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?> '+theInstance.toNum(theInstance.request.visualSendMoney)+'/=</div>'+
                                '</li>'+
                                '</ul>'):''
                            )+

                            (
                                theInstance.request.sending_currency != 'EURO'?(
                            '<hr>'+
                            '<ul class="list-unstyled">'+
                            '<li class="price-detail">'+
                            '<div class="detail-title detail-total">Received Money</div>'+
                            '<div class="detail-amt font-weight-bolder"><?php echo strtoupper(implode(" ", explode("_", $userInfo->storeBaseCurrency))); ?> '+theInstance.toNum(theInstance.request.receive_money)+'/=</div>'+
                            '</li>'+
                            '</ul>'):''
                            )+

                            '</div>'+
                             '<br><h6>Please confirm that all the above information are correct and you are ready to confirm.</h6><br>'

                            +'<div class="form-group"><label>Transaction Pin</label><input class="form-control" id="transaction_pin_val" type="password" placeholder="Put you Transaction Pin here."></div>'
                            +'<div class="form-group"><label>Note</label><textarea class="form-control" id="note_val" rows="3" placeholder="Enter your note here."></textarea></div>'
                        ),
                        buttons: {
                            cancel: {
                                label: "Cancel",
                                className: 'btn-danger',
                                callback: function(){

                                }
                            },
                            ok: {
                                label: "Confirmed",
                                className: 'btn-info',
                                callback: function(){

                                    theInstance.request.transaction_pin = $("#transaction_pin_val").val()
                                    theInstance.request.mfs_id = theInstance.request.selected_mfs
                                    theInstance.request.recharge_amount = theInstance.request.receive_money
                                    //theInstance.request.recharge_amount = theInstance.request.send_money

                                    if(theInstance.request.sending_currency == 'EURO')
                                    {
                                        theInstance.request.recharge_amount = theInstance.request.visualSendMoney;
                                    }

                                    theInstance.request.note = $("#note_val").val()

                                    axios.post("<?php echo env('APP_URL', ''); ?>/api/recharge/create", theInstance.request,
                                        {
                                            headers: {
                                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                            },
                                        }).then(response => {
                                        location.reload();
                                    })
                                        .catch(error => {
                                            if (error.response) {
                                                switch (error.response.status) {
                                                    case 401:
                                                        this.makeForceLogout()
                                                        break;
                                                    default:
                                                        alert(error.response.data.message.join(","));
                                                        theInstance.sendARequest();
                                                        break;
                                                }
                                            }
                                        });
                                }
                            }
                        }
                    });

                    theInstance.requestDialogBox.modal('show');
                }
            }
        })
    });
</script>
<style>
    .select-mfs2 + .select2-container{
        width: 100%;
    }

    .price-details .price-detail {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }
    .select2-container .select2-selection--single, .select2-container--default .select2-selection--single .select2-selection__rendered, .select2-container--default .select2-selection--single .select2-selection__arrow
    {
        height: 36px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow
    {
        right: 5px;
    }
</style>
<!-- END: Content-->
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>
@include('inc.footer', ['load_datatable_scripts' => true, 'load_datatable_scripts' => true])
