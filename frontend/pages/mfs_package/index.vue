<template>
  <div class="card" style="margin-top: 20px">
    <div class="card-header bg-white header-elements-inline">
      <h6 class="card-title">Mfs Package List <span class="text-danger" v-if="(page_message.length > 0)">({{page_message}})</span></h6>
      <div class="header-elements">
        <div class="list-icons">
          <a href="#" data-toggle="modal" data-target="#modal_create_new_mfs" class="list-icons-item"><i class="icon-plus-circle2"></i> Add New MFS Package</a>
        </div>
      </div>
    </div>
    <div class="card-body" :style="{'padding':'0'}">
      <table class="table datatable-basic dataTable no-footer datatable-scroll-y">
        <thead>
        <tr>
          <th>Sl</th>
          <th>Title</th>
          <th>MFS Name</th>
          <th>Range</th>
          <th>Amount</th>
          <th>Discount (%)</th>
          <th>Note</th>
          <th>Action</th>
        </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    <div id="modal_create_new_mfs" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Create MFS Package</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Title <span class="text-danger">*</span></label>
              <div class="col-sm-9">
                <input type="text" placeholder="Mfs Title" class="form-control" v-model="newMfsPackage.package_name">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">MFS <span class="text-danger">*</span></label>
              <div class="col-sm-9">
                <select class="form-control" v-model="newMfsPackage.mfs_id">
                  <option value="">Select A MFS</option>
                  <option v-for="option in mfsDropdown" v-bind:value="option.id">
                    {{ option.text }}
                  </option>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-form-label col-sm-3">Start Slab</label>
              <div class="col-sm-9">
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="BDT Amount" value="100" v-model="newMfsPackage.start_slab">
                  <span class="input-group-append">
                        <span class="input-group-text">BDT</span>
                      </span>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">End Slab</label>
              <div class="col-sm-9">
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="BDT Amount" value="200" v-model="newMfsPackage.end_slab">
                  <span class="input-group-append">
                        <span class="input-group-text">BDT</span>
                      </span>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Amount <span class="text-danger">*</span></label>
              <div class="col-sm-9">
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="BDT Amount" value="50" v-model="newMfsPackage.amount">
                  <span class="input-group-append">
                        <span class="input-group-text">BDT</span>
                      </span>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Discount</label>
              <div class="col-sm-9">
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="In Percentage" value="2.5" v-model="newMfsPackage.discount">
                  <span class="input-group-append">
                        <span class="input-group-text">%</span>
                      </span>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Note</label>
              <div class="col-sm-9">
                <textarea class="form-control" rows="3" placeholder="Put your Note Here." v-model="newMfsPackage.note">{{ newMfsPackage.note }}</textarea>
              </div>
            </div>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="formErrorMessage.length > 0">
              <div class="alert-body">
                <span class="font-weight-semibold">Error!</span> {{formErrorMessage}}
              </div>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-link" data-dismiss="modal">Close</button>
            <button class="btn btn-danger" v-on:click="createUpdateMfs(false)">Save</button>
          </div>
        </div>
      </div>
    </div>

   <div id="modal_update_existing_mfs" class="modal fade" tabindex="-1">
     <div class="modal-dialog modal-lg">
       <div class="modal-content">
         <div class="modal-header">
           <h5 class="modal-title">Update MFS {{newMfsPackage.mfs_name}}</h5>
           <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body">
           <div class="form-group row">
             <label class="col-form-label col-sm-3">Title <span class="text-danger">*</span></label>
             <div class="col-sm-9">
               <input type="text" placeholder="Mfs Title" class="form-control" v-model="newMfsPackage.package_name">
             </div>
           </div>
           <div class="form-group row">
             <label class="col-form-label col-sm-3">MFS <span class="text-danger">*</span></label>
             <div class="col-sm-9">
               <select class="form-control" v-model="newMfsPackage.mfs_id">
                 <option value="">Select A MFS</option>
                 <option v-for="option in mfsDropdown" v-bind:value="option.id">
                   {{ option.text }}
                 </option>
               </select>
             </div>
           </div>

           <div class="form-group row">
             <label class="col-form-label col-sm-3">Start Slab</label>
             <div class="col-sm-9">
               <div class="input-group">
                 <input type="text" class="form-control" placeholder="BDT Amount" value="100" v-model="newMfsPackage.start_slab">
                 <span class="input-group-append">
                        <span class="input-group-text">BDT</span>
                      </span>
               </div>
             </div>
           </div>
           <div class="form-group row">
             <label class="col-form-label col-sm-3">End Slab</label>
             <div class="col-sm-9">
               <div class="input-group">
                 <input type="text" class="form-control" placeholder="BDT Amount" value="200" v-model="newMfsPackage.end_slab">
                 <span class="input-group-append">
                        <span class="input-group-text">BDT</span>
                      </span>
               </div>
             </div>
           </div>
           <div class="form-group row">
             <label class="col-form-label col-sm-3">Amount <span class="text-danger">*</span></label>
             <div class="col-sm-9">
               <div class="input-group">
                 <input type="text" class="form-control" placeholder="BDT Amount" value="50" v-model="newMfsPackage.amount">
                 <span class="input-group-append">
                        <span class="input-group-text">BDT</span>
                      </span>
               </div>
             </div>
           </div>
           <div class="form-group row">
             <label class="col-form-label col-sm-3">Discount</label>
             <div class="col-sm-9">
               <div class="input-group">
                 <input type="text" class="form-control" placeholder="In Percentage" value="2.5" v-model="newMfsPackage.discount">
                 <span class="input-group-append">
                        <span class="input-group-text">%</span>
                      </span>
               </div>
             </div>
           </div>
           <div class="form-group row">
             <label class="col-form-label col-sm-3">Note</label>
             <div class="col-sm-9">
               <textarea class="form-control" rows="3" placeholder="Put your Note Here." v-model="newMfsPackage.note">{{ newMfsPackage.note }}</textarea>
             </div>
           </div>
           <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="formErrorMessage.length > 0">
             <div class="alert-body">
               <span class="font-weight-semibold">Error!</span> {{formErrorMessage}}
             </div>
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
               <span aria-hidden="true">×</span>
             </button>
           </div>
         </div>
         <div class="modal-footer">
           <button class="btn btn-link" data-dismiss="modal"> Close</button>
           <button class="btn btn-danger" v-on:click="createUpdateMfs(true)"> Save</button>
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
  name: "MfsPackageList",
  middleware: ['auth', 'permission_check'],
  components: {},
  data() {
    return {
      formErrorMessage:'',
      page_message:'',
      enteredUserName:'',
      enteredUserPassword:'',
      masterTable:{},
      mfsDropdown:[],
      windowHeight: 0,
      windowWidth: 0,
      scrollPosition:0,
      defaultMfsId:'',
      formToDoUpdate:false,
      page: 1,
      newMfsPackage:{
        package_name:'',
        mfs_id:'',
        discount:'',
        start_slab:0,
        end_slab:0,
        note:"",
        amount:""
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
            'targets': 7,'searchable': false, 'orderable': false, 'width':'10%',
            'render': function (data, type, full, meta)
            {
              var info = $('<div/>').text(data).html();

              return '<div class="btn-group-sm">'+
                '<button type="button" class="btn btn-warning updateMfs" data-id="'+info+'">Update</button>'+
                '</div>';
            }
          }
        ],
        "processing": true,
        "serverSide": true,
        "pageLength": 100,
        "language": {
          "emptyTable": "No Mfs Package Found. Add One.",
        },
        dom:
          '<"row d-flex justify-content-between align-items-center m-1"' +
          '<"col-lg-6 d-flex align-items-center"l>' +
          '<"col-lg-6 d-flex align-items-center justify-content-lg-end flex-lg-nowrap flex-wrap pr-lg-1 p-0"f<"invoice_status ml-sm-2">>' +
          '>t' +
          '<"d-flex justify-content-between mx-2 row"' +
          '<"col-sm-12 col-md-6"i>' +
          '<"col-sm-12 col-md-6"p>' +
          '>',
        "ajax": {
          "url": this.$axios.defaults.baseURL+'/api/mfs_package',
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
            theInstance.mfsDropdown = data.responseJSON.mfs;
            //console.log(data.responseJSON.mfs);
          },
          error: function (xhr, error, thrown)
          {
            console.log("Error");
          }
        },
      }
    );
  },
  methods: {
    dTableMount()
    {
      $(".updateMfs").click(function(e){
        theInstance.setDefaultMfsId($(this).data("id"));
      });
    },
    loadPageData(){
      this.masterTable.ajax.reload();
    },
    createImageSelected()
    {
      this.newMfsPackage.file = this.$refs.mfsCreateFile.files[0];
    },
    resetFilter(){
      this.loadPageData();
    },
    setDefaultMfsId(uid){
      this.page_message = "Gathering STore Details. Please wait...."
      this.defaultMfsId = uid;
      this.formToDoUpdate = true;
      this.$axios.get("/api/mfs_package/info/"+uid, {
        headers: {Authorization: this.$auth.getToken('local')}
      })
        .then(response => {
          this.page_message = "";
          this.newMfsPackage.mfs_name = response.data.data.mfs_name;
          this.newMfsPackage.default_commission = response.data.data.default_commission;

          this.newMfsPackage.package_name = response.data.data.package_name;
          this.newMfsPackage.mfs_id = response.data.data.mfs_id;
          this.newMfsPackage.discount = response.data.data.discount;
          this.newMfsPackage.start_slab = response.data.data.start_slab;
          this.newMfsPackage.end_slab= response.data.data.end_slab;
          this.newMfsPackage.note= response.data.data.note;
          this.newMfsPackage.amount= response.data.data.amount;

          $('#modal_update_existing_mfs').modal('show');
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
    createUpdateMfs(update){
      if(update)
      {
        if(confirm("Are you sure?"))
        {
          $('#modal_update_existing_mfs').modal('hide');

          this.page_message = 'Updating Mfs Status...';

          this.$axios.patch("/api/mfs_package/update/"+this.defaultMfsId, this.newMfsPackage,{
            headers: {
              Authorization: this.$auth.getToken('local')
            }
          })
            .then(response => {
              this.page_message = 'Mfs Updated Successfully. Reloading Table....'
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
        $('#modal_create_new_mfs').modal('hide');

        let formData = new FormData();

        Object.keys(this.newMfsPackage).forEach(key => {
          formData.append(key, this.newMfsPackage[key])
        });

        this.$axios.post("/api/mfs_package/create", formData,
          {
            headers: {
              Authorization: this.$auth.getToken('local')
            },
          }).then(response => {
          this.page_message = 'Mfs Created Successfully. Reloading Mfs Table....'
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
              $('#modal_create_new_mfs').modal('show');
              this.page_message = ''
            }
          });

        this.page_message = 'Please Wait. We Are Creating Mfs .....'
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
