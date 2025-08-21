<template>
  <div class="card" style="margin-top: 20px">
    <div class="card-header bg-white header-elements-inline">
      <h6 class="card-title">Vendor List <span class="text-danger" v-if="(page_message.length > 0)">({{page_message}})</span></h6>
      <div class="header-elements">
        <div class="list-icons">
          <a href="#" v-if="false" data-toggle="modal" data-target="#modal_filter" class="list-icons-item"><i class="icon-filter3"></i> Filter</a>&nbsp;&nbsp;&nbsp;
          <a href="#" data-toggle="modal" data-target="#modal_create_new_vendor" class="list-icons-item"><i class="icon-plus-circle2"></i> Add New Vendor</a>
        </div>
      </div>
    </div>
    <div class="card-body" :style="{'padding':'0'}">

      <table class="table datatable-basic dataTable no-footer datatable-scroll-y">
        <thead>
        <tr>
          <th>Sl</th>
          <th>Logo</th>
          <th>Vendor Name</th>
          <th>Current Balance</th>
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
            <h5 class="modal-title">Filter</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Vendor Title</label>
              <div class="col-sm-9">
                <input type="text" placeholder="Vendor Title" class="form-control" v-model="tableFilter.vendor_name">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-link" data-dismiss="modal"><i class="icon-cross2 font-size-base mr-1"></i> Close</button>
            <button class="btn btn-warning" data-dismiss="modal" v-on:click="resetFilter()">Reset</button>
            <button class="btn btn-primary" v-on:click="loadPageData()">Search</button>
          </div>
        </div>
      </div>
    </div>
    <div id="modal_create_new_vendor" class="modal fade" tabindex="-1">
      <div class="modal-dialog  modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Create Vendor</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-2">Title</label>
              <div class="col-sm-10">
                <input type="text" placeholder="Vendor Title" class="form-control" v-model="newVendor.vendor_name">
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="form-group row">
                  <label class="col-form-label col-sm-3">Name <span class="text-danger">*</span></label>
                  <div class="col-sm-9">
                    <input type="text" placeholder="Vendor Name" class="form-control" v-model="newVendor.vendor_owner_name">
                  </div>
                </div>
              </div>
              <div class="col">
                <div class="form-group row">
                  <label class="col-form-label col-sm-3">Phone <span class="text-danger">*</span></label>
                  <div class="col-sm-9">
                    <input type="text" placeholder="Vendor Phone Number" class="form-control" v-model="newVendor.vendor_phone_number">
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Address <span class="text-danger">*</span></label>
              <div class="col-sm-9">
                <input type="text" placeholder="Store Address" class="form-control" v-model="newVendor.vendor_address">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Logo</label>
              <div class="col-sm-9">
                <input type="file" class="form-control h-auto" ref="vendorCreateFile" accept="image/*" v-on:change="vendorCreateImageSelected">
              </div>
            </div>
            <legend class="text-uppercase font-size-sm font-weight-bold">Vendor Web Access</legend>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">User Name</label>
              <div class="col-sm-9">
                <input type="text" placeholder="Type User Name" class="form-control" v-model="newVendor.manager_user_name">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">User Password</label>
              <div class="col-sm-9">
                <input type="password" placeholder="Type User Name" class="form-control" v-model="newVendor.manager_user_password">
              </div>
            </div>
            <legend class="text-uppercase font-size-sm font-weight-bold">Configuration</legend>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Selected MFS</label>
              <div class="col-sm-9">
                <select class="form-control select2box" v-model="newVendor.mfs" multiple="multiple">
                  <option value="">Select A MFS Type</option>
                  <option v-for="option in mfs_list" v-bind:value="option.mfs_id">
                    {{ option.mfs_name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
              <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
              <span class="font-weight-semibold">Error!</span> {{formErrorMessage}}
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-link" data-dismiss="modal">Close</button>
            <button class="btn btn-danger" v-on:click="createUpdateVendor(false)">Save</button>

          </div>
        </div>
      </div>
    </div>
    <div id="modal_update_existing_vendor" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Update Vendor {{newVendor.vendor_name}}</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-2">Title</label>
              <div class="col-sm-10">
                <input type="text" placeholder="Vendor Title" class="form-control" v-model="newVendor.vendor_name">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-2">Name</label>
              <div class="col-sm-10">
                <input type="text" placeholder="Reseller Name" class="form-control" v-model="newVendor.vendor_owner_name">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-2">Phone</label>
              <div class="col-sm-10">
                <input type="text" placeholder="Reseller Phone Number" class="form-control" v-model="newVendor.vendor_phone_number">
              </div>
            </div>

            <div class="form-group row">
              <label class="col-form-label col-sm-2">Address</label>
              <div class="col-sm-10">
                <input type="text" placeholder="Store Address" class="form-control" v-model="newVendor.vendor_address">
              </div>
            </div>
            <legend class="text-uppercase font-size-sm font-weight-bold">Configuration</legend>
            <div class="form-group row">
              <label class="col-form-label col-sm-2">Allowed MFS</label>
              <div class="col-sm-10">
                <select class="form-control select2box2" v-model="newVendor.mfs" multiple="multiple">
                  <option value="">Select A MFS Type</option>
                  <option v-for="option in mfs_list" v-bind:value="option.mfs_id">
                    {{ option.mfs_name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
              <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
              <span class="font-weight-semibold">Error!</span> {{formErrorMessage}}
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-link" data-dismiss="modal">Close</button>
            <button class="btn btn-primary" v-on:click="createUpdateVendor(true)">Save</button>
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
                &#2547; {{adjustmentForm.current_balance}}
              </div>
            </div>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-6">Adjusted Amount (Use - [minus] for negative adjustment)</label>
              <div class="col-sm-6">
                <input type="text" placeholder="Vendor Title" class="form-control" v-model="adjustmentForm.new_balance">
              </div>
            </div>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-6">Balance After Adjustment</label>
              <div class="col-sm-6" :style="{
                  'font-size':'20px',
                  'font-weight':'bold',
                  'color':((parseFloat(adjustmentForm.current_balance) + parseFloat(adjustmentForm.new_balance)) < 0?'red':((parseFloat(adjustmentForm.current_balance) + parseFloat(adjustmentForm.new_balance)) == 0?'black':'green'))
                }">
                &#2547; {{parseFloat(adjustmentForm.current_balance) + parseFloat(adjustmentForm.new_balance)}}
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-link" data-dismiss="modal">Close</button>
            <button class="btn btn-primary" v-on:click="doBalanceAdjustment()">Adjust</button>
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
    name: "VendorList",
    middleware: ['auth', 'permission_check'],
    components: {},
    data() {
      return {
        formErrorMessage:'',
        page_message:'',
        enteredUserName:'',
        enteredUserPassword:'',
        masterTable:{},
        mfs_list:[],
        windowHeight: 0,
        windowWidth: 0,
        scrollPosition:0,
        defaultVendorId:'',
        formToDoUpdate:false,
        page: 1,
        columns: [
          {label: 'SL', field: 'serial',align: 'left', sortable: false, headerAlign:'left'},
          {label: 'Vendor Name', field: 'vendor_name',align: 'left', sortable: false, headerAlign:'left'},
          {label: 'Current Balance', field: 'current_balance',align: 'left', sortable: false, headerAlign:'left'},
          {label: 'Status', field: 'status',align: 'left', sortable: false, headerAlign:'left'},
          {label: 'Created On', field: 'created_on',align: 'left', sortable: false, headerAlign:'left'},
          {label: 'Action', field: 'vendor_id',align: 'left', sortable: false, headerAlign:'left'}
        ],
        rows: [
        ],
        tableFilter:{
          vendor_name:''
        },
        newVendor:{
          vendor_name:'',
          vendor_owner_name:'',
          vendor_phone_number:'',
          vendor_address:'',
          user_id:'',
          manager_user_name:'',
          manager_user_password:'',
          file:'',
          mfs:[]
        },
        adjustmentForm:{
          current_balance:0,
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

                if(info === "disabled") return '<span class="badge badge-danger badge-pill">Disabled</span>';
                if(info === "enabled") return '<span class="badge badge-success badge-pill">Enabled</span>';

                return '';
              }
            },
            {
              'targets': 6,'searchable': false, 'orderable': false, 'width':'10%',
              'render': function (data, type, full, meta)
              {
                var info = $('<div/>').text(data).html();

                return '<div class="btn-group btn-group-sm">'+
                  //'<button type="button" class="btn btn-danger removeVendorBtt" data-id="'+info.split('|')[0]+'">Remove</button>'+
                  '<button type="button" class="btn btn-success changeVendorStatus" data-id="'+info.split('|')[0]+'" data-current_status="'+info.split('|')[1]+'">Change Status</button>'+
                  '<button type="button" class="btn btn-warning updateVendor" data-id="'+info.split('|')[0]+'">Update</button>'+
                  '<button type="button" class="btn btn-primary gatherInfoForAdjustVendorBalance" data-id="'+info.split('|')[0]+'">Adjust Balance</button>'+
                  '</div>';
              }
            }
          ],
          "processing": true,
          "serverSide": true,
          "pageLength": 500,
          "language": {
            "emptyTable": "No Vendor Found.",
          },
          "ajax": {
            "url": this.$axios.defaults.baseURL+'/api/vendor',
            "type": "POST",
            'beforeSend': function (request) {
              request.setRequestHeader("Authorization", theInstance.$auth.getToken('local'));
            },
            "data": function ( d )
            {
              d = theInstance.tableFilter
            },
            complete: function(data)
            {
              theInstance.page_message = ''
              theInstance.mfs_list = data.responseJSON.mfs_list
              console.log("Done...");
            },
            error: function (xhr, error, thrown)
            {
              console.log("Error");
            }
          },
        }
      );

      $('.select2box').select2();
      $('.select2box').on('select2:select',function(e)
      {
        var data = e.params.data;
        theInstance.newVendor.mfs.push(data.id);
      });

      $('.select2box').on('select2:unselect',function(e)
      {
        var data = e.params.data;
        var l = theInstance.newVendor.mfs.indexOf(data.id)
        if(l !== -1)
        {
          theInstance.newVendor.mfs.splice(l, 1);
        }
      });
      //this.loadPageData();
      //$('#theDataTable').DataTable();
    },
    methods: {
      dTableMount() {
        $(".removeVendorBtt").click(function(e){
          theInstance.removeVendor($(this).data("id"));
        });

        $(".changeVendorStatus").click(function(e){
          theInstance.changeVendorStatus($(this).data("id"), ($(this).data("current_status")==="enabled"?"disabled":"enabled"));
        });

        $(".updateVendor").click(function(e){
          theInstance.setDefaultVendorId($(this).data("id"));
        });

        $(".gatherInfoForAdjustVendorBalance").click(function(e){
          theInstance.gatherInfoForAdjustVendorBalance($(this).data("id"));
        });
      },
      loadPageData(){
        $('#modal_filter').modal('hide');
        this.masterTable.ajax.reload();
      },
      vendorCreateImageSelected()
      {
        this.newVendor.file = this.$refs.vendorCreateFile.files[0];
      },
      resetFilter(){
        $('#modal_filter').modal('hide');
        this.tableFilter = {
          vendor_name:''
        };
        this.loadPageData();
      },
      removeVendor(vendor_id){
        if(confirm("Are you sure?"))
        {
          this.$axios.delete("/api/vendor/"+vendor_id, {
            headers: {
              Authorization: this.$auth.getToken('local')
            }
          })
            .then(response => {
              this.page_message = 'Vendor Removed Successfully. Reloading Vendor Table....'
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
      changeVendorStatus(vendor_id, new_status){
        if(confirm("Are you sure?"))
        {
          this.page_message = 'Updating Vendor Status...';

          this.$axios.patch("/api/vendor/"+vendor_id, {"status":new_status},{
            headers: {
              Authorization: this.$auth.getToken('local')
            }
          })
            .then(response => {
              this.page_message = 'Vendor Updated Successfully. Reloading Table....'
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
      gatherInfoForAdjustVendorBalance(vendor_id){
        this.page_message = "Gathering Vendor Details. Please wait...."
        this.defaultVendorId = vendor_id;
        this.formToDoUpdate = true;
        this.$axios.get("/api/vendor/"+vendor_id, {
          headers: {Authorization: this.$auth.getToken('local')}
        })
          .then(response => {
            this.page_message = "";
            this.adjustmentForm.current_balance = parseFloat(response.data.data.b1)
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
      setDefaultVendorId(uid){
        this.page_message = "Gathering STore Details. Please wait...."
        this.defaultVendorId = uid;
        this.formToDoUpdate = true;
        this.$axios.get("/api/vendor/"+uid, {
          headers: {Authorization: this.$auth.getToken('local')}
        })
          .then(response => {
            this.page_message = "";
            this.newVendor.vendor_name = response.data.data.vendor_name;
            this.newVendor.mfs = JSON.parse(response.data.data.allowed_mfs);

            this.newVendor.vendor_owner_name = response.data.data.d1
            this.newVendor.vendor_phone_number = response.data.data.d2
            this.newVendor.vendor_address = response.data.data.d3

            $('#modal_update_existing_vendor').modal('show');

            $('.select2box2').select2();
            $('.select2box2').on('select2:select',function(e)
            {
              var data = e.params.data;
              theInstance.newVendor.mfs.push(data.id);
            });
            $('.select2box2').on('select2:unselect',function(e)
            {
              var data = e.params.data;
              var l = theInstance.newVendor.mfs.indexOf(data.id)
              if(l !== -1)
              {
                theInstance.newVendor.mfs.splice(l, 1);
              }
            });

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
        this.$axios.put("/api/vendor/"+this.defaultVendorId, this.adjustmentForm,{
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
      createUpdateVendor(update){
        if(update)
        {
          if(confirm("Are you sure?"))
          {
            $('#modal_update_existing_vendor').modal('hide');

            this.page_message = 'Updating Vendor Status...';

            this.$axios.patch("/api/vendor/"+this.defaultVendorId, this.newVendor,{
              headers: {
                Authorization: this.$auth.getToken('local')
              }
            })
              .then(response => {
                this.page_message = 'Vendor Updated Successfully. Reloading Table....'
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
          $('#modal_create_new_vendor').modal('hide');

          let formData = new FormData();

          Object.keys(this.newVendor).forEach(key => {
            formData.append(key, this.newVendor[key])
          });

          this.$axios.post("/api/vendor_c", formData,
            {
              headers: {
                Authorization: this.$auth.getToken('local')
              },
            }).then(response => {
            this.page_message = 'Vendor Created Successfully. Reloading Vendor Table....'
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
                $('#modal_create_new_vendor').modal('show');
                this.page_message = ''
              }
            });

          this.page_message = 'Please Wait. We Are Creating Vendor .....'
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
