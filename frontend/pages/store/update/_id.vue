<template>
  <div>
    <div class="card" style="margin-top: 20px">
      <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title" style="font-weight: bold;">Update Reseller</h6>
      </div>
      <div class="card-body">
        <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
          <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
          <span class="font-weight-semibold">Error!</span> {{formErrorMessage}}
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
        <h6 class="card-title" style="font-weight: bold;">Configurations</h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Loan Limit <span class="text-danger">*</span></label>
              <div class="col-sm-9">
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
              <label class="col-form-label col-sm-4">Conv. Rate <span class="text-danger">*</span></label>
              <div class="col-sm-8">
                <div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text">1 {{ newStore.baseCurrency.toUpperCase() }} = </span>
											</span>
                  <input type="number" class="form-control" placeholder="BDT" v-model="newStore.conversion_rate">
                  <span class="input-group-append">
												<span class="input-group-text">BDT</span>
											</span>
                </div>
                <span class="form-text text-muted">Enter amount ({{ newStore.baseCurrency.toUpperCase() }} 1 = ? BDT)</span>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Trans. PIN <span class="text-danger">*</span></label>
              <div class="col-sm-9">
                <div class="input-group">
                  <input type="number" minlength="6" class="form-control" placeholder="Enter 6 digit Transaction PIN" value="" v-model="newStore.transaction_pin">
                </div>
                <span class="form-text text-muted">For every recharge activity, you have to use transaction pin.</span>
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
              <td><input type="text" class="form-control" placeholder="Commission (%)" v-model="r_item.commission"></td>
              <td><input type="text" class="form-control" placeholder="Charge (%)" v-model="r_item.charge"></td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="text-right">
      <button type="button" v-on:click="updateStore()" class="btn btn-primary">Submit</button>
    </div>
  </div>
</template>



<script>
export default {
  name: "store_update",
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
        conversion_rate: '',
        baseCurrency: 'bdt',
        loan_slab: '100',
        transaction_pin: '',
        store_owner_name: '',
        store_address: '',
        store_phone_number: '',
        mfsList: [],
        mfsSlab:[],
        file: ''
      }
    }
  },
  mounted() {
    this.$parent.$parent.setPageTitle("Reseller")
    this.setDefaultStoreId(this.$route.params.id)
  },
  methods: {
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
            else if(key === "mfsSlab")
            {
              formData.append(key, JSON.stringify(this.newStore.mfsSlab))
            }
            else
            {
              formData.append(key, this.newStore[key])
            }
          });

          this.$axios.post("/api/store/"+this.defaultStoreId, formData,
            {
              headers: {
                Authorization: this.$auth.getToken('local')
              },
            })
          .then(response => {
            window.location.href = '/store';
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
      }
    },
    setDefaultStoreId(uid){
      this.page_message = "Gathering Store Details. Please wait...."
      this.defaultStoreId = uid;
      this.formToDoUpdate = true;
      this.$axios.get("/api/store/"+uid, {
        headers: {Authorization: this.$auth.getToken('local')}
      })
        .then(response => {
          this.page_message = "";
          this.newStore.store_name = response.data.data.store_name;
          this.newStore.store_owner_name = response.data.data.store_owner_name;
          this.newStore.store_address = response.data.data.store_address;
          this.newStore.store_phone_number = response.data.data.store_phone_number;
          this.newStore.mfsList = JSON.parse(response.data.data.commission_percent);
          //this.newStore.mfsSlab = JSON.parse(response.data.data.mfs_slab);
          this.newStore.conversion_rate = response.data.data.conversion_rate;
          this.newStore.loan_slab = response.data.data.loan_slab
          this.newStore.baseCurrency = response.data.data.base_currency
          this.newStore.transaction_pin = '';
          console.log(this.newStore.mfsList)
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
}
</script>

<style scoped>

</style>
