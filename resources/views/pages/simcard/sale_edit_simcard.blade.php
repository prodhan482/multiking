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
                        <h4 class="card-title">Sale Sim Card</h4>
                        <div class="header-elements">
                            <div class="list-icons  btn-group-sm">
                                <?php if(empty($doUpdate)): ?>
                                <button type="button" class="btn btn-success btn-sm" v-on:click="sale()">Sale</button>
                                <?php endif; ?>
                                    <?php if(!empty($doUpdate)): ?>
                                    <button type="button" class="btn btn-warning btn-sm" v-on:click="update()">Update</button>
                                    <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5 order-md-0 order-1">
                            <div class="card-body">
                                <!-- form -->
                                <form id="createApiForm">
                                    <div class="mb-2">
                                        <label for="ApiKeyType" class="form-label">Choose offer</label>
                                        <select class="select2 form-control" v-model="simCardInfo.product_offer_id" @change="changeOfferDescription($event)">
                                            <option value="">No Offer</option>
                                            <?php $offerByID = array(); ?>
                                            <?php foreach($SimCardOffer as $row): ?>
                                            <?php $offerByID[$row->id] = $row; ?>
                                            <option value="<?php echo $row->id; ?>"><?php echo $row->title; ?></option>
                                            <?php endforeach; ?>
                                            <option value="-1">Other Offer</option>
                                        </select>
                                    </div>
                                    <div class="mb-2" style="background: #F83821; padding: 7px 10px; border-radius: 8px; display: inline-block">
                                        <input type="checkbox" id="applyMnp" class="form-check-input ml-0" v-model="applyMnp"> <label style="font-weight: bold; color: #FFFFFF;" for="applyMnp" class="form-check-label ml-2 pointer-event"> Apply MNP Portability</label>
                                    </div>
                                    <div class="mb-2" v-if="simCardInfo.product_offer_id == -1">
                                        <label for="sur_name" class="form-label">Other Product Offer</label>
                                        <input class="form-control" type="text" name="sur_name" placeholder="Your Product Offer" v-model="simCardInfo.custom_product_offer">
                                    </div>
                                    <div class="mb-2">
                                        <label for="sur_name" class="form-label">Client Nome- Cognome *</label>
                                        <input class="form-control" type="text" name="sur_name" placeholder="First Name - Last Name" v-model="simCardInfo.sur_name">
                                    </div>
                                    <div class="mb-2">
                                        <label for="sur_name" class="form-label">Codicifiscale</label>
                                        <input class="form-control" type="text" name="sur_name" placeholder="Put the Codicifiscale" v-model="simCardInfo.codicifiscale">
                                    </div>

                                    <div class="mb-2">
                                        <label for="sur_name" class="form-label">Date of Birth</label>
                                        <input class="form-control" type="text" name="date_of_birth" placeholder="Put Date of Birth" v-model="simCardInfo.date_of_birth">
                                    </div>
                                    <div class="mb-2">
                                        <label for="sur_name" class="form-label">Country</label>
                                        <input class="form-control" type="text" name="country_name" placeholder="Put Country Name" v-model="simCardInfo.country_name">
                                    </div>

                                    <div class="mb-2">
                                        <label for="sur_name" class="form-label">Reseller Price *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">€</span>
                                            <input type="text" class="form-control" placeholder="Enter Reseller Price" aria-label="Enter Reseller Price"  v-model="simCardInfo.reseller_price" :disabled="(simCardInfo.product_offer_id.length > 2)">
                                        </div>
                                    </div>
                                    <div id="offerImage"></div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-7 order-md-1 order-0">
                            <div class="text-left">
                                <h6>Product : <?php echo $sim_card_info->product_name; ?></h6>
                                <h6>Sim Card ICCID : <?php echo $sim_card_info->sim_card_iccid; ?></h6>
                                <h6>Sim Card Mobile Number : <?php echo $sim_card_info->sim_card_mobile_number; ?></h6>
                            </div><br>
                            <div v-if="SimCardOffers[simCardInfo.product_offer_id]">
                                <div class="border border-primary position-relative rounded p-2">
                                    <div v-html="SimCardOffers[simCardInfo.product_offer_id].description"></div>
                                    <div v-if="(selectedOfferFileData.length>0)" v-html="selectedOfferFileData"></div>
                                    <!--<div class="row">
                                        <div :class="[(selectedOfferFileData.length>0)?'col-8':'col-12']">
                                            <div class="d-flex flex-wrap" v-if="false">
                                                <h4 class="mb-1 me-1" v-html="SimCardOffers[simCardInfo.product_offer_id].title">&nbsp;</h4>
                                            </div>
                                            <div class="d-flex fw-bolder">
                                                <span class="me-50" v-html="SimCardOffers[simCardInfo.product_offer_id].description">&nbsp;</span>
                                            </div>
                                        </div>
                                        <div class="col-4" v-if="(selectedOfferFileData.length>0)" v-html="selectedOfferFileData"></div>
                                    </div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6" v-if="applyMnp">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">MNP- Number Portability</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label for="ApiKeyType" class="form-label">Operator Name</label>
                                    <select class="select2 form-control" id="ApiKeyType" v-model="simCardInfo.mnp_operator_name" @change="changeOtherOperatorDetailsV2($event)">
                                        <option value="">None</option>
                                        <?php  $resellerBonusAgainstID = array(); ?>
                                        <?php foreach($MnpOperators as $row): ?>
                                        <?php  $resellerBonusAgainstID[$row->id] = $row; ?>
                                        <option value="<?php echo $row->id; ?>"><?php echo $row->title; ?></option>
                                        <?php endforeach; ?>
                                        <option value="Others"> Others </option>
                                    </select>
                                </div>
                                <div class="mb-2" v-if="simCardInfo.mnp_operator_name == 'Others'">
                                    <label for="sur_name" class="form-label">Other Operator Name</label>
                                    <input class="form-control" type="text" placeholder="Other Operator Name" v-model="simCardInfo.other_operator_name">
                                </div>
                                <div class="mb-2">
                                    <label for="sur_name" class="form-label">19 Digit ICCID Number</label>
                                    <input class="form-control" type="text"  placeholder="19 Digit ICCID Number" v-model="simCardInfo.mnp_iccid_number">
                                </div>

                                <div class="mb-2">
                                    <label for="sur_name" class="form-label">Mobile Number</label>
                                    <input class="form-control" type="text" placeholder="Mobile Number" v-model="simCardInfo.mnp_iccid_mobile_number">
                                </div>
                                <div class="mb-2">
                                    <label for="sur_name" class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" v-model="simCardInfo.mnp_notes"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div :class="[(applyMnp?'col-6':'col-12')]">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Upload Document</h4>
                            </div>
                            <div class="card-body">
                                <div>
                                    Upload Instruction:<br>
                                    1. Max size per file is <?php echo ini_get('upload_max_filesize'); ?>.<br>
                                    2. Please upload PNG, JPEG, PDF file only.<br>
                                </div>
                                <br><br>
                                <input id="choose" name="upl_files[]" ref="uploadCreateFile1" type="file" accept="application/pdf, image/jpeg, image/png" multiple v-on:change="uploadFileSelected(0)"><br>
                                <input id="choose1" name="upl_files1[]" ref="uploadCreateFile2" type="file" accept="application/pdf, image/jpeg, image/png" multiple v-on:change="uploadFileSelected(1)"><br>
                                <input id="choose2" name="upl_files1[]" ref="uploadCreateFile3" type="file" accept="application/pdf, image/jpeg, image/png" multiple v-on:change="uploadFileSelected(2)"><br>
                                <input id="choose3" name="upl_files3[]" ref="uploadCreateFile4" type="file" accept="application/pdf, image/jpeg, image/png" multiple v-on:change="uploadFileSelected(3)"><br>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Others</h4>
                    </div>
                    <div class="card-body">

                        <div class="mb-2">
                            <label for="sur_name" class="form-label">Any Other Information:</label>
                            <textarea name="other_information" class="form-control" tabindex="9" v-html="simCardInfo.other_information"></textarea>
                        </div>
                        <button type="button" class="btn btn-success float-right" v-on:click="sale()">Sale</button>
                    </div>
                </div>

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
                    fromPicker:"",
                    toPicker:"",
                    defaultMfsId:'',
                    formToDoUpdate:false,
                    selectedSimcardOffer:"",
                    selectedSimcardOfferMnpBonus:"",
                    applyMnp:false,
                    waitingDialogInShow:false,
                    SimCardOffers:<?php echo json_encode($offerByID); ?>,
                    MnpResellerBonusList:<?php echo json_encode($resellerBonusAgainstID); ?>,
                    orderStatus:{'':'All','approved':'Approved','pending':'Pending','rejected':'Rejected'},
                    page: 1,
                    selectedOfferFileData:"",
                    simCardInfo:{
                        product_offer_id:"",
                        custom_product_offer:"",
                        sur_name:"",
                        codicifiscale:"",
                        reseller_price:0,
                        resellerBonus:0,
                        reseller_price_c:0,
                        mnp_operator_name:"",
                        other_operator_name:"",
                        mnp_iccid_number:"",
                        mnp_iccid_mobile_number:"",
                        mnp_notes:"",
                        date_of_birth:"",
                        country_name:"",
                        other_information:"",
                        file:[]
                    },
                }
            },
            mounted() {
                theInstance = this;
            },
            methods: {
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
                sale()
                {
                    let formData = new FormData();

                    Object.keys(this.simCardInfo).forEach(key => {
                        if(key != "file") formData.append(key, this.simCardInfo[key])
                    });

                    var pos = 0

                    console.log(theInstance.simCardInfo.file);

                    Object.keys(this.simCardInfo.file).forEach(key => {
                        formData.append("file["+pos+"]", this.simCardInfo.file[key]);
                        pos = pos + 1;
                    });

                    theInstance.showWaitingDialog();
                    axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/sale/<?php echo $sim_card_id; ?>", formData,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        }).then(response => {
                        theInstance.hideWaitingDialog();
                        alert("Sim card have been sold successfully.")
                        window.location.href = "/simcard/list/sold";
                    })
                        .catch(error => {
                            if (error.response) {
                                switch (error.response.status)
                                {
                                    case 401:
                                        this.makeForceLogout()
                                        break;
                                    case 406:
                                    default:
                                        console.log(error.response)
                                        alert(error.response.data.message.join(","))
                                        break;
                                }
                                $('#modal_create_new_order').modal('show');
                                this.page_message = ''
                            }
                            theInstance.hideWaitingDialog();
                        });
                },
                uploadFileSelected(pos)
                {
                    theInstance.simCardInfo.file = []
                    Object.keys(theInstance.$refs).forEach(key => {
                            Object.keys(theInstance.$refs[key].files).forEach(fkeys => {
                                theInstance.simCardInfo.file.push(theInstance.$refs[key].files[fkeys])
                            })
                    });
                    //this.simCardInfo.file[pos] = this.$refs.uploadCreateFile.files;
                },
                changeOfferDescription(event)
                {
                    var offerID = event.target.value
                    theInstance.selectedSimcardOffer = offerID;
                    if(!theInstance.SimCardOffers[offerID]) return;

                    var ResOffer = JSON.parse(theInstance.SimCardOffers[offerID].sc_simcard_reseller_offer);
                    theInstance.simCardInfo.reseller_price_c = parseFloat(theInstance.SimCardOffers[offerID].reseller_price);

                    console.log(ResOffer)

                    for (let i in ResOffer) {
                        var eee = ResOffer[i].split("|");
                        if(eee[0] == "<?php echo $sim_card_info->store_id; ?>")
                        {
                            theInstance.simCardInfo.reseller_price_c = eee[1];
                            theInstance.selectedSimcardOfferMnpBonus = eee[2];
                            break;
                        }
                    }

                    var ResOffer2 = JSON.parse(theInstance.SimCardOffers[offerID].sc_simcard_resellers_reseller_offer);

                    console.log(ResOffer2)

                    for (let i in ResOffer2) {
                        var eee = ResOffer2[i].split("|");
                        if(eee[0] == "<?php echo $sim_card_info->store_id; ?>")
                        {
                            theInstance.simCardInfo.reseller_price_c = eee[1];
                            theInstance.selectedSimcardOfferMnpBonus = eee[2];
                            break;
                        }
                    }

                    theInstance.simCardInfo.reseller_price = (parseFloat(theInstance.simCardInfo.reseller_price_c) - parseFloat(theInstance.simCardInfo.resellerBonus))

                    theInstance.selectedOfferFileData = "";

                    if(theInstance.SimCardOffers[offerID].upload_path && theInstance.SimCardOffers[offerID].upload_path.length > 2)
                    {
                        var infodd = ""
                        var fileExt = theInstance.SimCardOffers[offerID].upload_path.split('.').pop()
                        if (fileExt == "jpg" || fileExt == "jpeg" || fileExt == "png" || fileExt == "gif")
                        {
                            infodd = "";
                            //infodd = '<a target="_blank" href="/'+theInstance.SimCardOffers[offerID].upload_path+'"><img class="img-fluid" style="max-height: 200px;" src="/'+theInstance.SimCardOffers[offerID].upload_path+'"></a>'

                            $("#offerImage").html('<a target="_blank" href="'+(theInstance.SimCardOffers[offerID].space_uploaded == 'uploaded'?"<?php echo config('constants.dgSpaceURL'); ?>":"/")+''+theInstance.SimCardOffers[offerID].upload_path+'"><img class="img-fluid" style="max-height: 400px;" src="'+(theInstance.SimCardOffers[offerID].space_uploaded == 'uploaded'?"<?php echo config('constants.dgSpaceURL'); ?>":"/")+''+theInstance.SimCardOffers[offerID].upload_path+'"></a>')

                        } else {
                            infodd = '<a target="_blank" href="/'+theInstance.SimCardOffers[offerID].upload_path+'" class="btn btn-primary">View Offer Details</a>'
                        }

                        theInstance.selectedOfferFileData = infodd
                    }
                    else
                    {
                        theInstance.selectedOfferFileData = ""
                    }
                },
                changeOtherOperatorDetailsV2(event)
                {
                    var mnp_operator_name = event.target.value
                    if(mnp_operator_name.length > 2 && mnp_operator_name != "Others" && parseFloat(theInstance.selectedSimcardOfferMnpBonus) > 0)
                    {
                        theInstance.simCardInfo.reseller_price = theInstance.selectedSimcardOfferMnpBonus
                    } else {
                        theInstance.simCardInfo.reseller_price = theInstance.simCardInfo.reseller_price_c;
                    }
                },
                changeOtherOperatorDetails(event)
                {
                    var mnp_operator_name = event.target.value
                    if(mnp_operator_name != "Others")
                    {
                        var ResOffer = JSON.parse(theInstance.MnpResellerBonusList[mnp_operator_name].reseller_offer);
                        theInstance.simCardInfo.resellerBonus = theInstance.MnpResellerBonusList[mnp_operator_name].reseller_bonus;

                        for (let i in ResOffer) {
                            var eee = ResOffer[i].split("|");
                            if(eee[0] == "<?php echo $userInfo->store_vendor_id; ?>")
                            {
                                theInstance.simCardInfo.resellerBonus = eee[1];
                                break;
                            }
                        }

                        console.log(theInstance.MnpResellerBonusList[mnp_operator_name].reseller_bonus)

                    } else {
                        theInstance.simCardInfo.resellerBonus = 0
                    }

                    theInstance.simCardInfo.reseller_price = (parseFloat(theInstance.simCardInfo.reseller_price_c) - parseFloat(theInstance.simCardInfo.resellerBonus))
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
@include('inc.footer', [])
