<template>
    <div class="card" style="margin-top: 20px">
    <div class="card-header bg-white header-elements-inline">
      <h6 class="card-title">Reseller List <span class="text-danger" v-if="(page_message.length > 0)">({{page_message}})</span></h6>
      <div class="header-elements">
        <div class="list-icons">
          <a href="#" data-toggle="modal" data-target="#modal_filter" class="list-icons-item"><i class="icon-filter3"></i> Filter</a>&nbsp;&nbsp;&nbsp;
          <a href="/store/add" class="list-icons-item"><i class="icon-plus-circle2"></i> Add New Reseller</a>
        </div>
      </div>
    </div>
    <div class="card-body" :style="{'padding':'0'}">
      <table class="table datatable-basic dataTable no-footer datatable-scroll-y">
        <thead>
          <tr>
            <th>Sl</th>
            <th>Logo</th>
            <th>Reseller Name</th>

            <th>Current Balance</th>
            <th>Loan Balance</th>
            <th>Loan Limit</th>

            <th>Conversion (BDT)</th>
            <th>Status</th>
            <th>Created On</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
    <div id="modal_filter" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="icon-user-plus mr-2"></i> Filter</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Reseller Name</label>
              <div class="col-sm-9">
                <input type="text" placeholder="Type Reseller name" class="form-control" v-model="tableFilter.store_owner_name">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Phone Number</label>
              <div class="col-sm-9">
                <input type="text" placeholder="Type Phone Number" class="form-control" v-model="tableFilter.store_phone_number">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-link" data-dismiss="modal"><i class="icon-cross2 font-size-base mr-1"></i> Close</button>
            <button class="btn bg-warning" data-dismiss="modal" v-on:click="resetFilter()"><i class="icon-cross2 font-size-base mr-1"></i> Reset</button>
            <button class="btn bg-primary" v-on:click="loadPageData()"><i class="icon-checkmark3 font-size-base mr-1"></i> Search</button>
          </div>
        </div>
      </div>
    </div>
    <div id="modal_create_new_store" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="icon-user-plus mr-2"></i> Create Reseller</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
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
            <legend class="text-uppercase font-size-sm font-weight-bold">Reseller Web Access</legend>

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
            <legend class="text-uppercase font-size-sm font-weight-bold">Configurations</legend>
            <div class="row">
              <div class="col-6">
                <div class="form-group row">
                  <label class="col-form-label col-sm-3">Loan Limit <span class="text-danger">*</span></label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <input type="number" class="form-control" placeholder="In Euro" value="100" v-model="newStore.loan_slab">
                      <span class="input-group-append">
										<span class="input-group-text">&#8364;</span>
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
												<span class="input-group-text">1 &#8364; = </span>
											</span>
                      <input type="number" class="form-control" placeholder="BDT" value="100" v-model="newStore.conversion_rate">
                      <span class="input-group-append">
												<span class="input-group-text">&#2547;</span>
											</span>
                    </div>
                    <span class="form-text text-muted">Enter amount (Euro 1 = ? BDT)</span>
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
            <legend class="text-uppercase font-size-sm font-weight-bold">Mfs Commissions</legend>
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


            <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
              <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
              <span class="font-weight-semibold">Error!</span> {{formErrorMessage}}
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-link" data-dismiss="modal"><i class="icon-cross2 font-size-base mr-1"></i> Close</button>
            <button class="btn bg-primary" v-on:click="createUpdateStore(false)"><i class="icon-checkmark3 font-size-base mr-1"></i> Save</button>
          </div>
        </div>
      </div>
    </div>
    <div id="modal_update_existing_store" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="icon-user-plus mr-2"></i> Update Reseller {{newStore.store_name}}</h5>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="form-group row">
                <label class="col-form-label col-sm-3">Reseller Title</label>
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
              <legend class="text-uppercase font-size-sm font-weight-bold">Configurations</legend>
              <div class="form-group row">
                <label class="col-form-label col-sm-3">Conversion Rate <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <div class="input-group">
											<span class="input-group-prepend">
												<span class="input-group-text">1 &#8364; = </span>
											</span>
                    <input type="number" class="form-control" placeholder="BDT" value="100" v-model="newStore.conversion_rate">
                    <span class="input-group-append">
												<span class="input-group-text">&#2547;</span>
											</span>
                  </div>
                  <span class="form-text text-muted">Enter amount (Euro 1 = ? BDT)</span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-form-label col-sm-3">Loan Limit <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <div class="input-group">
                    <input type="number" class="form-control" placeholder="In Euro" value="100" v-model="newStore.loan_slab">
                    <span class="input-group-append">
										<span class="input-group-text">&#8364;</span>
									</span>
                  </div>
                  <span class="form-text text-muted">A loan slab that how much extra user can spend. If store have zero balance, user will move to loan balance.</span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-form-label col-sm-3">Trans. PIN <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <div class="input-group">
                    <input type="number" minlength="6" class="form-control" placeholder="Keep blank to save old PIN." value="" v-model="newStore.transaction_pin">
                  </div>
                  <span class="form-text text-muted">For every recharge activity, you have to use transaction pin.</span>
                </div>
              </div>

              <legend class="text-uppercase font-size-sm font-weight-bold">Mfs Commissions</legend>
              <div class="row">
                <div class="col-6" v-for="(item, index) in newStore.mfsList"
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

              <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
                <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                <span class="font-weight-semibold">Error!</span> {{formErrorMessage}}
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-link" data-dismiss="modal"><i class="icon-cross2 font-size-base mr-1"></i> Close</button>
              <button class="btn bg-primary" v-on:click="createUpdateStore(true)"><i class="icon-checkmark3 font-size-base mr-1"></i> Save</button>
            </div>
          </div>
        </div>
      </div>
      <div id="modal_adjustBalance" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Adjust Balance</h5>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="form-group row">
                <label class="col-form-label col-sm-6">Current Balance</label>
                <div class="col-sm-6" :style="{
                  'font-size':'20px',
                  'font-weight':'bold',
                  'color':(adjustmentForm.current_balance < 0?'red':(adjustmentForm.current_balance == 0?'black':'green'))
                }">
                  &#2547; {{adjustmentForm.current_balance}} <span style="color: red" v-if="(adjustmentForm.loan_amount > 0)">&#2547; {{adjustmentForm.loan_amount}}</span>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-form-label col-sm-6">Adjusted Amount (Use - [minus] for negative adjustment)</label>
                <div class="col-sm-6">
                  <input type="text" placeholder="Reseller Title" class="form-control" v-model="adjustmentForm.new_balance">
                </div>
              </div>
              <div class="form-group row">
                <label class="col-form-label col-sm-6">Balance After Adjustment</label>
                <div class="col-sm-6" :style="{
                  'font-size':'20px',
                  'font-weight':'bold',
                  'color':((parseFloat(adjustmentForm.current_balance) - parseFloat(adjustmentForm.loan_amount) + parseFloat(adjustmentForm.new_balance)) < 0?'red':((parseFloat(adjustmentForm.current_balance) - parseFloat(adjustmentForm.loan_amount) + parseFloat(adjustmentForm.new_balance)) == 0?'black':'green'))
                }">
                  &#2547; {{((parseFloat(adjustmentForm.current_balance) - parseFloat(adjustmentForm.loan_amount) + parseFloat(adjustmentForm.new_balance)))}}
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-link" data-dismiss="modal">Close</button>
              <button class="btn btn-danger" v-on:click="doBalanceAdjustment()">Adjust</button>
            </div>
          </div>
        </div>
      </div>

  </div>
</template>

<script>
  const numeral = !process.client ? null : require('numeral');
  var theInstance= {};

  export default {
    name: "StoreList",
    middleware: ['auth', 'permission_check'],
    components: {},
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
        defaultStoreId:'',
        formToDoUpdate:false,
        page: 1,
        tableFilter:{
          store_owner_name:'',
          store_phone_number:''
        },
        newStore:{
          store_name:'',
          user_id:'',
          manager_user_name:'',
          manager_user_password:'',
          commission:'2.5',
          conversion_rate:'100',
          loan_slab:'100',
          transaction_pin:'',
          store_owner_name:'',
          store_address:'',
          store_phone_number:'',
          mfsList:[],
          file:''
        },
        adjustmentForm:{
          conversion_rate:0,
          current_balance:0,
          loan_amount:0,
          new_balance:0
        }
      }
    },
    mounted() {
      this.windowHeight = window.innerHeight
      this.windowWidth = window.innerWidth
      window.addEventListener('resize', () => {
        this.windowHeight = window.innerHeight
        this.windowWidth = window.innerWidth
      })
      theInstance = this;

      this.$parent.$parent.setPageTitle("Reseller")

      this.masterTable = $('.dataTable').DataTable({
          scrollCollapse: true,
          "searching": false,
          "info": false,
          "paging": false,
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
          "columnDefs": [
            {
              'targets': 4,'searchable': false, 'orderable': false, 'width':'10%',
              'render': function (data, type, full, meta)
              {
                var info = $('<div/>').text(data).html();

                if(parseFloat(info) > 0 )
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
              'targets': 7,'searchable': false, 'orderable': false, 'width':'10%',
              'render': function (data, type, full, meta)
              {
                var info = $('<div/>').text(data).html();

                if(info === "disabled") return '<span class="badge badge-danger badge-pill">Disabled</span>';
                if(info === "enabled") return '<span class="badge badge-success badge-pill">Enabled</span>';

                return '';
              }
            },
            {
              'targets': 9,'searchable': false, 'orderable': false, 'width':'10%',
              'render': function (data, type, full, meta)
              {
                var info = $('<div/>').text(data).html();

                return '<div class="btn-group btn-group-sm">'+
                  //'<button type="button" class="btn btn-danger removeStoreBtt" data-id="'+info.split('|')[0]+'">Remove</button>'+
                  '<button type="button" class="btn btn-primary changeStoreStatus" data-id="'+info.split('|')[0]+'" data-current_status="'+info.split('|')[1]+'">Change Status</button>'+
                  '<a type="button" class="btn btn-warning updateStore" href="/store/update/'+info.split('|')[0]+'" target="_blank">Update</a>'+
                  '<button type="button" class="btn btn-success gatherInfoForAdjustStoreBalance" data-id="'+info.split('|')[0]+'">Adjust Balance</button>'+
                  '</div>';
              }
            }
          ],
          "processing": true,
          "serverSide": true,
          "pageLength": 500,
          "language": {
            "emptyTable": "No Store Found.",
          },
          "ajax": {
            "url": this.$axios.defaults.baseURL+'/api/store',
            "type": "POST",
            'beforeSend': function (request) {
              request.setRequestHeader("Authorization", theInstance.$auth.getToken('local'));
            },
            "data": function ( d )
            {
              d.store_owner_name = theInstance.tableFilter.store_owner_name
              d.store_phone_number = theInstance.tableFilter.store_phone_number
            },
            complete: function(data)
            {
              theInstance.page_message = ''
              //theInstance.newStore.mfsList = data.responseJSON.mfs_list
              console.log("Done...");
            },
            error: function (xhr, error, thrown)
            {
              console.log("Error");
            }
          },
        }
      );

      //this.loadPageData();
      //$('#theDataTable').DataTable();
    },
    methods: {
      dTableMount() {
        $(".removeStoreBtt").click(function(e){
          theInstance.removeStore($(this).data("id"));
        });

        $(".changeStoreStatus").click(function(e){
          theInstance.changeStoreStatus($(this).data("id"), ($(this).data("current_status")==="enabled"?"disabled":"enabled"));
        });

        $(".updateStore").click(function(e){
          //theInstance.setDefaultStoreId($(this).data("id"));
        });

        $(".gatherInfoForAdjustStoreBalance").click(function(e){
          theInstance.gatherInfoForAdjustStoreBalance($(this).data("id"));
        });
      },
      loadPageData(){
        $('#modal_filter').modal('hide');
        this.masterTable.ajax.reload();
      },
      storeCreateImageSelected()
      {
        this.newStore.file = this.$refs.storeCreateFile.files[0];
      },
      resetFilter(){
        $('#modal_filter').modal('hide');
        this.tableFilter = {
          store_owner_name:'',
          store_phone_number:''
        };
        this.loadPageData();
      },
      gatherInfoForAdjustStoreBalance(store_id){
        this.page_message = "Gathering STore Details. Please wait...."
        this.defaultStoreId = store_id;
        this.formToDoUpdate = true;
        this.$axios.get("/api/store/"+store_id, {
          headers: {Authorization: this.$auth.getToken('local')}
        })
          .then(response => {
            this.page_message = "";
            this.adjustmentForm.current_balance = parseFloat(response.data.data.balance)
            this.adjustmentForm.conversion_rate = parseFloat(response.data.data.conversion_rate)
            this.adjustmentForm.loan_amount = parseFloat(response.data.data.loan_balance)
            $('#modal_adjustBalance').modal('show');
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
      doBalanceAdjustment(){
        $('#modal_adjustBalance').modal('hide');
        this.$axios.put("/api/store/"+this.defaultStoreId, this.adjustmentForm,{
          headers: {Authorization: this.$auth.getToken('local')}
        })
          .then(response => {
            this.page_message = "";
            this.loadPageData();
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
      removeStore(store_id){
        if(confirm("Are you sure?"))
        {
          this.$axios.delete("/api/store/"+store_id, {
            headers: {
              Authorization: this.$auth.getToken('local')
            }
          })
            .then(response => {
              this.page_message = 'Store Removed Successfully. Reloading Store Table....'
              this.loadPageData();
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
      changeStoreStatus(store_id, new_status){
        if(confirm("Are you sure?"))
        {
          this.page_message = 'Updating Store Status...';

          this.$axios.patch("/api/store/"+store_id, {"status":new_status},{
            headers: {
              Authorization: this.$auth.getToken('local')
            }
          })
            .then(response => {
              this.page_message = 'Store Updated Successfully. Reloading Table....'
              this.loadPageData();
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
            this.newStore.conversion_rate = numeral(response.data.data.conversion_rate).format('0.00');
            this.newStore.loan_slab = response.data.data.loan_slab
            this.newStore.transaction_pin = '';
            $('#modal_update_existing_store').modal('show');
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
      createUpdateStore(update){
        if(update)
        {
          if(confirm("Are you sure?"))
          {
            $('#modal_update_existing_store').modal('hide');

            this.page_message = 'Updating Store Status...';

            this.$axios.patch("/api/store/"+this.defaultStoreId, this.newStore,{
              headers: {
                Authorization: this.$auth.getToken('local')
              }
            })
              .then(response => {
                this.page_message = 'Store Updated Successfully. Reloading Table....'
                this.loadPageData();
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
        } else {
          let formData = new FormData();
          //this.newStore.mfsList = JSON.stringify(this.newStore.mfsList);

          Object.keys(this.newStore).forEach(key => {
            if(key === "mfsList")
            {
              formData.append(key, JSON.stringify(this.newStore.mfsList))
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
            $('#modal_create_new_store').modal('hide');
            this.page_message = 'Store Created Successfully. Reloading Store Table....'
            this.loadPageData();
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
        }
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
  }
</script>

<style scoped>

</style>
