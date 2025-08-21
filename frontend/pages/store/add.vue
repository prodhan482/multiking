<template>
  <div>
  <div class="card" style="margin-top: 20px">
    <div class="card-header bg-white header-elements-inline">
      <h6 class="card-title" style="font-weight: bold;">Add Reseller</h6>
    </div>
    <div class="card-body">

      <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="formErrorMessage.length > 0">
        <div class="alert-body">
          <span class="font-weight-semibold">Error!</span> {{formErrorMessage}}
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="form-group row">
        <label class="col-form-label col-sm-3">Reseller Store Title <span class="text-danger">*</span></label>
        <div class="col-sm-9">
          <input type="text" placeholder="Reseller Title" class="form-control" v-model="newStore.store_name">
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="form-group row">
            <label class="col-form-label col-sm-3">Name <span class="text-danger">*</span></label>
            <div class="col-sm-9">
              <input type="text" placeholder="Reseller Name" class="form-control" v-model="newStore.store_owner_name">
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group row">
            <label class="col-form-label col-sm-3">Phone <span class="text-danger">*</span></label>
            <div class="col-sm-9">
              <input type="text" placeholder="Reseller Phone Number" class="form-control" v-model="newStore.store_phone_number">
            </div>
          </div>
        </div>
      </div>
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
  <div class="card">
    <div class="card-header bg-white header-elements-inline">
      <h6 class="card-title" style="font-weight: bold;">Reseller Web Access</h6>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col">
          <div class="form-group row">
            <label class="col-form-label col-sm-3">User Name <span class="text-danger">*</span></label>
            <div class="col-sm-9">
              <input type="text" placeholder="Type User Name" class="form-control" v-model="newStore.manager_user_name">
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group row">
            <label class="col-form-label col-sm-3">User Password <span class="text-danger">*</span></label>
            <div class="col-sm-9">
              <input type="password" placeholder="Type User Password" class="form-control" v-model="newStore.manager_user_password">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    <div class="card">
      <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title" style="font-weight: bold;">Configurations</h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <div class="form-group row">
              <label class="col-form-label col-sm-4">Loan Limit <span class="text-danger">*</span></label>
              <div class="col-sm-8">
                <div class="input-group">
                  <input type="number" class="form-control" placeholder="In Euro" value="100" v-model="newStore.loan_slab">
                  <span class="input-group-append">
										<span class="input-group-text">{{ newStore.baseCurrency.toUpperCase() }}</span>
									</span>
                </div>
                <span class="form-text text-muted">A loan slab that how much extra user can spend. If store have zero balance, user will move to loan balance.</span>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="form-group row">
              <label class="col-form-label col-sm-4">Base Currency <span class="text-danger">*</span></label>
              <div class="col-sm-8">
                <div class="input-group">
                  <select class="form-control" v-model="newStore.baseCurrency" v-on:change="resetConversionRate()">
                    <option value="bdt">BDT (Bangladesh Taka)</option>
                    <option value="euro">EURO (European Union Currency)</option>
                    <option value="gbp">GBP (Great Britain Pound)</option>
                    <option value="usd">USD (United States Dollar)</option>
                  </select>
                </div>
                <span class="form-text text-muted">Base Currency for Calculation and Others. This cannot be changeable</span>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="form-group row">
              <label class="col-form-label col-sm-4">Trans. PIN <span class="text-danger">*</span></label>
              <div class="col-sm-8">
                <div class="input-group">
                  <input type="number" minlength="6" class="form-control" placeholder="Enter 6 digit Transaction PIN" value="" v-model="newStore.transaction_pin">
                </div>
                <span class="form-text text-muted">For every request activity, you have to use transaction pin.</span>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="form-group row">
              <label class="col-form-label col-sm-4">Conv. Rate <span class="text-danger">*</span></label>
              <div class="col-sm-8">
                <div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text">1 {{ newStore.baseCurrency.toUpperCase() }} = </span>
											</span>
                  <input type="number" class="form-control" placeholder="BDT" value="100" v-model="newStore.conversion_rate">
                  <span class="input-group-append">
												<span class="input-group-text">BDT</span>
											</span>
                </div>
                <span class="form-text text-muted">Enter amount ({{ newStore.baseCurrency.toUpperCase() }} 1 = ? BDT)</span>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

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
                <td>{{ r_item.name }}</td>
                <td><input type="text" class="form-control" placeholder="Commission (%)" v-model="newStore.mfsSlab[r_item.id].commission"></td>
                <td><input type="text" class="form-control" placeholder="Charge (%)" v-model="newStore.mfsSlab[r_item.id].charge"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="card" v-if="false">
      <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title" style="font-weight: bold;">Mfs Commissions</h6>
      </div>
      <div class="card-body">
        <div class="row">

          <div class="col-4" v-for="(item, index) in newStore.mfsList"
               v-bind:item="item"
               v-bind:index="index"
               v-bind:key="item.id">
            <div class="form-group row">
              <label class="col-form-label col-sm-5">{{ item.name }} <span class="text-danger">*</span></label>
              <div class="col-sm-7">
                <div class="input-group">
                  <input type="number" class="form-control" placeholder="In Percentage" value="0.0" v-model="item.value">
                  <span class="input-group-append">
                        <span class="input-group-text">%</span>
                      </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <legend class="text-uppercase font-size-sm font-weight-bold">MFS Slab System</legend>
        <div class="row" v-for="(item, index) in newStore.mfsSlab"
             v-bind:item="item"
             v-bind:index="index"
             v-bind:key="item.id">
          <div class="col-3">
            <div class="input-group">
						<span class="input-group-prepend">
							<span class="input-group-text">{{ index + 1 }}. </span>
						</span>
              <select class="form-control select" v-model="item.mfs_id">
                <option value="">Select A MFS</option>
                <option v-for="(r_item, r_index) in newStore.mfsList" :value="r_item.id" :selected="(item.mfs_id == r_item.id)">{{ r_item.name }}</option>
              </select>
            </div>
          </div>
          <div class="col-3">
            <div class="input-group">
						<span class="input-group-prepend">
							<span class="input-group-text">From</span>
						</span>
              <input type="number" v-model="item.amount_from" class="form-control" placeholder="Enter From Amount">
              <span class="input-group-append">
							<span class="input-group-text">&#2547;</span>
						</span>
            </div>
          </div>
          <div class="col-3">
            <div class="input-group">
						<span class="input-group-prepend">
							<span class="input-group-text">To</span>
						</span>
              <input type="number" v-model="item.amount_to" class="form-control" placeholder="Enter To Amount">
              <span class="input-group-append">
							<span class="input-group-text">&#2547;</span>
						</span>
            </div>
          </div>
          <div class="col-2">
            <div class="input-group">
						<span class="input-group-prepend">
							<span class="input-group-text">Com.</span>
						</span>
              <input type="number" v-model="item.commission_euro" class="form-control" placeholder="Enter Commission €">
              <span class="input-group-append">
							<span class="input-group-text">&#8364;</span>
						</span>
            </div>
          </div>
          <div class="col-1"><button v-on:click="removeMFSCommissionSlab(item.id)" type="button" class="btn btn-danger"><i class="icon-trash"></i></button></div>
        </div>
        <button v-on:click="addMFSCommissionSlab()" type="button" class="btn btn-primary">Add <i class="icon-add-to-list ml-2"></i></button>
        <br><br>
      </div>
    </div>
    <div class="text-right">
      <button type="button" v-on:click="createStore()" class="btn btn-success">Submit</button>
    </div>
  </div>
</template>

<script>
export default {
  name: "store_add",
  middleware: ['auth', 'permission_check'],
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
      newStore: {
        store_name: '',
        user_id: '',
        manager_user_name: '',
        manager_user_password: '',
        commission: '2.5',
        conversion_rate: '1',
        loan_slab: '100',
        transaction_pin: '',
        store_owner_name: '',
        baseCurrency: 'bdt',
        store_address: '',
        store_phone_number: '',
        mfsList: [],
        mfsSlab: [],
        file: ''
      }
    }
  },
  mounted() {
    this.loadConfig()
    this.$parent.$parent.setPageTitle("Reseller")
  },
  methods: {
    loadConfig()
    {
      this.$axios.get("/api/stores/load_conf", {
        headers: {
          Authorization: this.$auth.getToken('local')
        }
      })
        .then(response => {
          for (var key in response.data.mfs_list) {
            this.newStore.mfsSlab[response.data.mfs_list[key].id] = response.data.mfs_list[key]
          }
          this.newStore.mfsList = response.data.mfs_list
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

      Object.keys(this.newStore).forEach(key => {
        if(key === "mfsList")
        {
          formData.append(key, JSON.stringify(this.newStore.mfsList))
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

      this.$axios.post("/api/store_c", formData,
        {
          headers: {
            Authorization: this.$auth.getToken('local')
          },
        }).then(response => {
        alert('Store Created Successfully');
        window.location.href = '/store';
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
            this.page_message = ''
          }
        });

      this.page_message = 'Please Wait. We Are Creating Store .....'
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
}
</script>

<style scoped>

</style>
