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
        <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="formErrorMessage.length > 0">
            <div class="alert-body" v-html="formErrorMessage"></div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
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
                    <div class="col">
                        <div class="card" style="margin-top: 20px">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title" style="font-weight: bold;">Update Reseller</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">Reseller Store Title <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" placeholder="Reseller Title" class="form-control" v-model="newStore.store_name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">Name <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" placeholder="Reseller Name" class="form-control" v-model="newStore.store_owner_name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">Phone <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" placeholder="Reseller Phone Number" class="form-control" v-model="newStore.store_phone_number">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card" style="margin-top: 20px">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title" style="font-weight: bold;">Update Reseller</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">Address <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" placeholder="Store Address" class="form-control" v-model="newStore.store_address">
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
                        <div class="text-right">
                            <button type="button" v-on:click="updateStore()" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header bg-white header-elements-inline">
                                <h6 class="card-title" style="font-weight: bold;">Configurations</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group row d-none">
                                    <label class="col-form-label col-sm-3">Loan Limit <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="number" class="form-control" placeholder="In Euro" value="100" v-model="newStore.loan_slab">
                                            <span class="input-group-append">
										<span class="input-group-text" v-html="newStore.baseCurrency.toUpperCase()"></span>
									</span>
                                        </div>
                                        <span class="form-text text-muted">A loan slab that how much extra user can spend. If store have zero balance, user will move to loan balance.</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">Base Commission <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
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
                                            <input type="number" class="form-control" placeholder="BDT" v-model="newStore.conversion_rate">
                                            <span class="input-group-append">
												<span class="input-group-text">BDT</span>
											</span>
                                        </div>
                                        <span class="form-text text-muted" v-html='("Enter amount "+newStore.baseCurrency.toUpperCase()+" 1 = ? BDT")'></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">Allow Reseller Creation</label>
                                    <div class="col-sm-9">
                                        <select class="form-control mfs-type" v-model="newStore.allow_reseller_creation">
                                            <option value="No" selected>Don't Allow Reseller Creation</option>
                                            <option value="Yes">Allow Reseller Creation</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">Trans. PIN <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="number" minlength="6" class="form-control" placeholder="Keep Empty or Put New 4 digit Transaction PIN" value="" v-model="newStore.transaction_pin">
                                        </div>
                                        <span class="form-text text-muted">For every recharge activity, you have to use transaction pin.</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">Allowed MFS</label>
                                    <div class="col-sm-9">
                                        <select class="form-control select2box2" v-model="newStore.mfs" multiple="multiple">
                                            <option value="">Select A MFS Type</option>
                                            <?php foreach($mfs_list as $value){ ?>
                                            <option value="<?php echo $value->mfs_id; ?>"><?php echo $value->mfs_name; ?></option>
                                            <?php } ?>
                                        </select>
                                        <a href="javascript:void(0)" v-on:click="removeSelectedMFS">Remove All</a>
                                    </div>
                                </div>

                                <?php if(in_array("Simcard::list", $userInfo->permission_lists)): ?>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">Allow Sim Card Management</label>
                                    <div class="col-sm-9">
                                        <select class="form-control mfs-type" v-model="newStore.allow_simcard_management">
                                            <option value="No" selected>Don't Allow Sim Card Management</option>
                                            <option value="Yes">Allow Sim Card Management</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3">Allowed Sim Card Products</label>
                                    <div class="col-sm-9">
                                        <select class="form-control select2box2SmCard" v-model="newStore.allowed_products" multiple="multiple">
                                            <?php foreach($product_list as $value){ ?>
                                            <option selected value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                                            <?php } ?>
                                        </select>
                                        <a href="javascript:void(0)" v-on:click="removeSelectedSimcardProducts">Remove All</a>
                                    </div>
                                </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                    <div class="col">
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
                                            <td><input type="text" class="form-control" placeholder="Commission (%)" v-model="r_item.commission"></td>
                                            <td><input type="text" class="form-control" placeholder="Charge (%)" v-model="r_item.charge"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="button" v-on:click="updateStore()" class="btn btn-success">Submit</button>
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
                default_mfs_list:[],
                default_mfs_list_by_id:[],
                newStore: {
                    store_name: '',
                    user_id: '',
                    manager_user_name: '',
                    manager_user_password: '',
                    base_add_balance_commission_rate:'2.0',
                    commission: '2.5',
                    conversion_rate: '0',
                    allow_reseller_creation:'No',
                    allow_simcard_management:'No',
                    baseCurrency: 'bdt',
                    loan_slab: '100',
                    transaction_pin: '',
                    store_owner_name: '',
                    store_address: '',
                    store_phone_number: '',
                    allowed_products:[],
                    allowed_productsList:[],
                    mfsList: [],
                    mfsSlab:[],
                    mfs:[],
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

            this.setDefaultStoreId('<?php echo $reseller_id; ?>')
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
                    if(this.newStore.mfsSlab[theInstance.newStore.mfs[key]]){
                        theInstance.newStore.mfsList.push(this.newStore.mfsSlab[theInstance.newStore.mfs[key]]);
                    } else {
                        theInstance.newStore.mfsList.push(theInstance.default_mfs_list_by_id[theInstance.newStore.mfs[key]]);
                    }
                }
            },
            updateStore()
            {
                if(confirm("Are you sure?"))
                {
                    let formData = new FormData();

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

                    axios.post("<?php echo env('APP_URL', ''); ?>/api/store/"+this.defaultStoreId, formData,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        })
                        .then(response => {
                            window.location.href = '/reseller';
                            theInstance.hideWaitingDialog();
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
                                        this.formErrorMessage = error.response.data.message.join(",");
                                        $('html, body').animate({ scrollTop: 0 }, 500);
                                        break;
                                }
                            }
                            theInstance.hideWaitingDialog();
                        });
                }
            },
            setDefaultStoreId(uid){
                this.page_message = "Gathering Store Details. Please wait...."
                this.defaultStoreId = uid;
                this.formToDoUpdate = true;
                axios.get("<?php echo env('APP_URL', ''); ?>/api/store/"+uid, {
                    headers: {Authorization: '<?php echo session('AuthorizationToken'); ?>'}
                })
                    .then(response => {
                        this.page_message = "";
                        this.newStore.store_name = response.data.data.store_name;
                        this.newStore.store_owner_name = response.data.data.store_owner_name;
                        this.newStore.store_address = response.data.data.store_address;
                        this.newStore.store_phone_number = response.data.data.store_phone_number;
                        this.newStore.base_add_balance_commission_rate = response.data.data.base_add_balance_commission_rate;
                        //this.newStore.mfsList = JSON.parse(response.data.data.commission_percent);
                        // /// ////this.newStore.mfsSlab = JSON.parse(response.data.data.mfs_slab);

                        var commission_percent = JSON.parse(response.data.data.commission_percent);
                        for (var key in commission_percent) {
                            this.newStore.mfs.push(commission_percent[key].id);
                            this.newStore.mfsSlab[commission_percent[key].id] = commission_percent[key];
                        }

                        var allowed_products = JSON.parse(response.data.data.allowed_products);
                        for (var key in allowed_products) {
                            this.newStore.allowed_products.push(allowed_products[key]);
                            this.newStore.allowed_productsList.push(allowed_products[key]);
                        }

                        this.default_mfs_list = response.data.data.default_mfs_list;
                        for (var key in response.data.data.default_mfs_list) {
                            this.default_mfs_list_by_id[response.data.data.default_mfs_list[key].id] = response.data.data.default_mfs_list[key];
                        }

                        this.newStore.conversion_rate = response.data.data.conversion_rate;
                        this.newStore.loan_slab = response.data.data.loan_slab
                        this.newStore.baseCurrency = response.data.data.base_currency
                        this.newStore.allow_reseller_creation = response.data.data.allow_reseller_creation
                        this.newStore.allow_simcard_management = response.data.data.allow_simcard_management
                        this.newStore.transaction_pin = '';
                        console.log("ok1");
                        this.kko();
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
            addMFSCommissionSlab()
            {
                this.newStore.mfsSlab.push(
                    {id:(new Date().getTime()), mfs_id:"", amount_from:0.0, amount_to:0.0, commission_euro:0.0},
                )
            },
            removeMFSCommissionSlab(id)
            {
                var temp = this.newStore.mfsSlab
                var l = []
                for (var x in temp) {
                    if(temp[x].id != id)
                    {
                        l.push(temp[x])
                    }
                }
                this.newStore.mfsSlab = l
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
                window.location.href = "login"
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
