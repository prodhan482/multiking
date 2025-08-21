@include('inc.header', ['load_vuejs' => true])
@include('inc.menu')

<?php $product_list_ids = []; ?>
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
        <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="formErrorMessage.length > 0">
            <div class="alert-body" v-html="formErrorMessage"></div>
        </div>
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
                <div class="row">
                    <div class="col-lg-6 col-xs-12">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title" style="font-weight: bold;">Create New Reseller</h6>
                            </div>
                            <div class="card-body">
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
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">Reseller Store Title <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" placeholder="Put Reseller Title" class="form-control" v-model="newStore.store_name">
                                    </div>
                                </div>
                                    <?php if($userInfo->user_type == "super_admin"): ?>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3">Parent Reseller</label>
                                        <div class="col-sm-9">
                                            <select class="form-control parent_store_id" v-model="newStore.parent_store_id">
                                                <option value="by_admin">No Parent Reseller</option>
                                                <?php foreach($store_list as $row): ?>
                                                <option value="<?php echo $row->store_id; ?>"><?php echo $row->store_name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3">Reseller Code <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" placeholder="Put Reseller Code" class="form-control" v-model="newStore.store_code">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3">Name <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" placeholder="Put Reseller Name" class="form-control" v-model="newStore.store_owner_name">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-3">Phone <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" placeholder="Put Reseller Phone Number" class="form-control" v-model="newStore.store_phone_number">
                                        </div>
                                    </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">Address <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" placeholder="Put Store Address" class="form-control" v-model="newStore.store_address">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">Reseller Logo</label>
                                    <div class="col-sm-9">
                                        <input type="file" class="form-control h-auto" ref="storeCreateFile" accept="image/*" v-on:change="storeCreateImageSelected">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xs-12">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title" style="font-weight: bold;">Reseller Web Access</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">User Name <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" placeholder="Type User Name" class="form-control" v-model="newStore.manager_user_name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">User Password <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="password" placeholder="Type User Password" class="form-control" v-model="newStore.manager_user_password">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">Trans. PIN <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="number" minlength="4" class="form-control" placeholder="Enter 4 digit Transaction PIN" value="" v-model="newStore.transaction_pin">
                                        </div>
                                        <span class="form-text text-muted">For every request activity, you have to use transaction pin.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="button" v-on:click="createStore()" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-xs-12">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title" style="font-weight: bold;">Configurations</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group row d-none">
                                    <label class="col-form-label col-sm-4">Loan Limit <span class="text-danger">*</span></label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <input type="number" class="form-control" :placeholder='("In "+newStore.baseCurrency.toUpperCase())' value="0" v-model="newStore.loan_slab">
                                            <span class="input-group-append">
										<span class="input-group-text" v-html="newStore.baseCurrency.toUpperCase()"></span>
									</span>
                                        </div>
                                        <span class="form-text text-muted">A loan slab that how much extra user can spend. If store have zero balance, user will move to loan balance.</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-4">Base Currency <span class="text-danger">*</span></label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <select class="form-control" v-model="newStore.baseCurrency" v-on:change="resetConversionRate()">
                                                <option value="bdt">BDT (Bangladesh Taka)</option>
                                                <option value="euro">EURO (European Union Currency)</option>
                                                <option value="gbp">GBP (Great Britain Pound)</option>
                                                <option value="usd">USD (United States Dollar)</option>
                                                <option value="cfa_franc">CFA Franc (Central African CFA franc)</option>
                                            </select>
                                        </div>
                                        <span class="form-text text-muted">Base Currency for Calculation and Others. This cannot be changeable</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-4">Base Commission <span class="text-danger">*</span></label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <input type="number" class="form-control" placeholder="Percentage" value="100" v-model="newStore.base_add_balance_commission_rate">
                                            <span class="input-group-append">
                                                <span class="input-group-text">%</span>
											</span>
                                        </div>
                                        <span class="form-text text-muted">Add Balance Base Commission</span>
                                    </div>
                                </div>
                                <div class="form-group row d-none">
                                    <label class="col-form-label col-sm-4">Conv. Rate <span class="text-danger">*</span></label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text" v-html='("1 "+newStore.baseCurrency.toUpperCase()+" /=")'></span>
											</span>
                                            <input type="number" class="form-control" placeholder="BDT" value="100" v-model="newStore.conversion_rate">
                                            <span class="input-group-append">
                                                <span class="input-group-text">BDT</span>
											</span>
                                        </div>
                                        <span class="form-text text-muted" v-html='("Enter amount "+newStore.baseCurrency.toUpperCase()+" 1 = ? BDT")'></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-4">Allow Reseller Creation</label>
                                    <div class="col-sm-8">
                                        <select class="form-control mfs-type" v-model="newStore.allow_reseller_creation">
                                            <option value="No" selected>Don't Allow Reseller Creation</option>
                                            <option value="Yes">Allow Reseller Creation</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-4">Allowed MFS</label>
                                    <div class="col-sm-8">
                                        <select class="form-control select2box2" v-model="newStore.mfs" multiple="multiple">
                                            <?php $mfs_ids = []; ?>
                                            <?php foreach($mfs_list as $value){ ?>
                                            <?php $mfs_ids[] = $value->mfs_id; ?>
                                            <option selected value="<?php echo $value->mfs_id; ?>"><?php echo $value->mfs_name; ?></option>
                                            <?php } ?>
                                        </select>
                                        <a href="javascript:void(0)" v-on:click="removeSelectedMFS">Remove All</a>
                                    </div>
                                </div>

                                <?php if(in_array("Simcard::list", $userInfo->permission_lists)): ?>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-4">Allow Sim Card Management</label>
                                    <div class="col-sm-8">
                                        <select class="form-control mfs-type" v-model="newStore.allow_simcard_management">
                                            <option value="No" selected>Don't Allow Sim Card Management</option>
                                            <option value="Yes">Allow Sim Card Management</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-4">Allowed Sim Card Products</label>
                                    <div class="col-sm-8">
                                        <select class="form-control select2box2SmCard" v-model="newStore.allowed_products" multiple="multiple">
                                            <?php foreach($product_list as $value){ ?>
                                            <?php $product_list_ids[] = $value->id; ?>
                                            <option selected value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                                            <?php } ?>
                                        </select>
                                        <a href="javascript:void(0)" v-on:click="removeSelectedSimcardProducts">Remove All</a>
                                    </div>
                                </div>
                                <?php endif; ?>


                                <div class="form-group row">
                                    <label class="col-form-label col-sm-4">Note</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" placeholder="Put if you have any note" v-model="newStore.note"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xs-12">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title" style="font-weight: bold;">MFS Configurations</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>MFS Name</th>
                                            <th>Commission (%)</th>
                                            <th>Charge (%)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(r_item, r_index) in newStore.mfsList">
                                            <td v-html="r_item.name"></td>
                                            <td><input type="text" class="form-control" placeholder="Commission (%)" v-model="newStore.mfsSlab[r_item.id].commission"></td>
                                            <td><input type="text" class="form-control" placeholder="Charge (%)" v-model="newStore.mfsSlab[r_item.id].charge"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="button" v-on:click="createStore()" class="btn btn-success">Submit</button>
                </div>

                <!-- list section end -->
            </section>
        </div>
    </div>
</div>
<script>
    var theInstance = '';
    var app = new Vue({
        el: '#app',
        data() {
            return {
                formErrorMessage: '',
                page_message: '',
                enteredUserName: '',
                enteredUserPassword: '',
                masterTable: {},
                windowHeight: 0,
                windowWidth: 0,
                scrollPosition: 0,
                defaultStoreId: '',
                formToDoUpdate: false,
                waitingDialogInShow:false,
                newStore: {
                    store_name: '',
                    user_id: '',
                    parent_store_id:'by_admin',
                    manager_user_name: '',
                    manager_user_password: '',
                    base_add_balance_commission_rate:'2.0',
                    commission: '2.5',
                    conversion_rate: '0',
                    allow_reseller_creation:'No',
                    allow_simcard_management:'No',
                    allowed_products:<?php echo json_encode($product_list_ids); ?>,
                    allowed_productsList:[],
                    loan_slab: '0',
                    transaction_pin: '',
                    store_owner_name: '',
                    baseCurrency: 'bdt',
                    store_address: '',
                    store_phone_number: '',
                    note:'',
                    mfsList: [],
                    mfsSlab: [],
                    mfs:<?php echo json_encode($mfs_ids); ?>,
                    file: ''
                }
            }
        },
        mounted() {
            theInstance = this;
            setTimeout(function(){
                $('.select2box2').select2({'width':'100%', 'placeholder':'Allow some MFS'});
                $('.select2box2').on('select2:select',function(e)
                {
                    var data = e.params.data;
                    theInstance.newStore.mfs.push(data.id);
                    theInstance.kko();
                });

                $('.select2box2').on('select2:unselect',function(e)
                {
                    var data = e.params.data;
                    var l = theInstance.newStore.mfs.indexOf(data.id)
                    if(l !== -1)
                    {
                        theInstance.newStore.mfs.splice(l, 1);
                    }
                    theInstance.kko();
                });

                $('.parent_store_id').select2({'width':'100%'});
                $('.parent_store_id').on('select2:select',function(e)
                {
                    var data = e.params.data;
                    theInstance.newStore.parent_store_id = data.id;
                });



                $('.select2box2SmCard').select2({'width':'100%', 'placeholder':'Allow some Products'});
                $('.select2box2SmCard').on('select2:select',function(e)
                {
                    var data = e.params.data;
                    theInstance.newStore.allowed_products.push(data.id);
                    theInstance.kko2();
                });
                $('.select2box2SmCard').on('select2:unselect',function(e)
                {
                    var data = e.params.data;
                    var l = theInstance.newStore.allowed_products.indexOf(data.id)
                    if(l !== -1)
                    {
                        theInstance.newStore.allowed_products.splice(l, 1);
                    }
                    theInstance.kko2();
                });


            }, 200);

            this.loadConfig();
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
            removeSelectedMFS()
            {
                if(confirm("Are You Sure? User will Not get access to any MFS Transaction?"))
                {
                    theInstance.newStore.mfs = [];
                    $('.select2box2').val("").trigger('change');
                    theInstance.kko();
                }
            },
            removeSelectedSimcardProducts()
            {
                if(confirm("Are You Sure? User will Not able to order or sale any SimCard?"))
                {
                    theInstance.newStore.allowed_products = [];
                    $('.select2box2SmCard').val("").trigger('change');
                    theInstance.kko2();
                }
            },
            kko2()
            {
                theInstance.newStore.allowed_productsList = [];
                for (var key in theInstance.newStore.allowed_products) {
                    theInstance.newStore.allowed_productsList.push(this.newStore.mfsSlab[theInstance.newStore.allowed_products[key]]);
                }
            },
            kko()
            {
                theInstance.newStore.mfsList = [];
                for (var key in theInstance.newStore.mfs) {
                    theInstance.newStore.mfsList.push(this.newStore.mfsSlab[theInstance.newStore.mfs[key]]);
                }
            },
            loadConfig()
            {
                axios.get("<?php echo env('APP_URL', ''); ?>/api/stores/load_conf", {
                    headers: {
                        Authorization: '<?php echo session('AuthorizationToken'); ?>'
                    }
                })
                    .then(response => {
                        for (var key in response.data.mfs_list) {
                            this.newStore.mfsSlab[response.data.mfs_list[key].id] = response.data.mfs_list[key]
                        }
                        //this.newStore.mfsList = response.data.mfs_list
                        theInstance.kko();
                        theInstance.kko2();
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
            storeCreateImageSelected()
            {
                this.newStore.file = this.$refs.storeCreateFile.files[0];
            },
            resetConversionRate()
            {
                if(this.newStore.baseCurrency === "bdt") this.newStore.conversion_rate = 1;
            },
            createStore()
            {
                let formData = new FormData();
                //this.newStore.mfsList = JSON.stringify(this.newStore.mfsList);

                console.log(this.newStore)

                Object.keys(this.newStore).forEach(key => {
                    if(key === "mfsList")
                    {
                        formData.append(key, JSON.stringify(this.newStore.mfsList))
                    }
                    else if(key === "allowed_products")
                    {
                        formData.append(key, JSON.stringify(this.newStore.allowed_products))
                    }
                    else if(key === "mfsSlab")
                    {
                        formData.append(key, JSON.stringify(this.newStore.mfsSlab))
                    }
                    else
                    {
                        formData.append(key, this.newStore[key])
                    }
                });

                theInstance.showWaitingDialog();

                axios.post("<?php echo env('APP_URL', ''); ?>/api/store_c", formData,
                    {
                        headers: {
                            Authorization: '<?php echo session('AuthorizationToken'); ?>'
                        },
                    }).then(response => {
                    alert('Store Created Successfully');
                    theInstance.hideWaitingDialog();
                    window.location.href = '/reseller';
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
                                    theInstance.formErrorMessage = error.response.data.message.join(",");
                                    $('html, body').animate({ scrollTop: 0 }, 500);
                                    break;
                            }
                            this.page_message = ''
                        }
                    });

                this.page_message = 'Please Wait. We Are Creating Store .....';
            },
        }
    })
</script>
<style>
    .select-mfs2 + .select2-container{
        width: 100%;
    }
</style>
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>
@include('inc.footer')
