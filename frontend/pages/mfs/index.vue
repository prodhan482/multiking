<template>
  <div class="card" style="margin-top: 20px">
    <div class="card-header bg-white header-elements-inline">
      <h6 class="card-title">Mfs List <span class="text-danger" v-if="(page_message.length > 0)">({{page_message}})</span></h6>
      <div class="header-elements">
        <div class="list-icons">
          <a href="#" data-toggle="modal" data-target="#modal_create_new_mfs" class="list-icons-item"><i class="icon-plus-circle2"></i> Add New MFS</a>
        </div>
      </div>
    </div>
    <div class="card-body" :style="{'padding':'0'}">
      <table class="table datatable-basic dataTable no-footer datatable-scroll-y">
        <thead>
        <tr>
          <th>Sl</th>
          <th>Logo</th>
          <th>MFS Name</th>
          <th>Default Commission (%)</th>
          <th>Default Charge (%)</th>
          <th>Created On</th>
          <th>Action</th>
        </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
    <div id="modal_create_new_mfs" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Create MFS</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-4">MFS Title</label>
              <div class="col-sm-8">
                <input type="text" placeholder="Mfs Title" class="form-control" v-model="newMfs.mfs_name">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-4">Commission <span class="text-danger">*</span></label>
              <div class="col-sm-8">
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="In Percentage" value="2.5" v-model="newMfs.default_commission">
                  <span class="input-group-append">
                        <span class="input-group-text">%</span>
                      </span>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-4">Charge <span class="text-danger">*</span></label>
              <div class="col-sm-8">
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="In Percentage" value="2.5" v-model="newMfs.default_charge">
                  <span class="input-group-append">
                        <span class="input-group-text">%</span>
                      </span>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-4">MFS Logo</label>
              <div class="col-sm-8">
                <input type="file" class="form-control h-auto" ref="mfsCreateFile" accept="image/*" v-on:change="createImageSelected">
              </div>
            </div>
            <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
              <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
              <span class="font-weight-semibold">Error!</span> {{formErrorMessage}}
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-link" data-dismiss="modal">Close</button>
            <button class="btn btn-primary" v-on:click="createUpdateMfs(false)">Save</button>
          </div>
        </div>
      </div>
    </div>
    <div id="modal_update_existing_mfs" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="icon-user-plus mr-2"></i> Update MFS {{newMfs.mfs_name}}</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-4">MFS Title</label>
              <div class="col-sm-8">
                <input type="text" placeholder="Mfs Title" class="form-control" v-model="newMfs.mfs_name">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-4">Commission <span class="text-danger">*</span></label>
              <div class="col-sm-8">
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="In Percentage" value="2.5" v-model="newMfs.default_commission">
                  <span class="input-group-append">
                        <span class="input-group-text">%</span>
                      </span>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-4">Charge <span class="text-danger">*</span></label>
              <div class="col-sm-8">
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="In Percentage" value="2.5" v-model="newMfs.default_charge">
                  <span class="input-group-append">
                        <span class="input-group-text">%</span>
                      </span>
                </div>
              </div>
            </div>
            <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
              <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
              <span class="font-weight-semibold">Error!</span> {{formErrorMessage}}
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-link" data-dismiss="modal">Close</button>
            <button class="btn btn-primary" v-on:click="createUpdateMfs(true)">Save</button>
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
    name: "MfsList",
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
        defaultMfsId:'',
        formToDoUpdate:false,
        page: 1,
        newMfs:{
          mfs_name:'',
          default_commission:'',
          default_charge:'',
          file:'',
          user_id:''
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
              'targets': 6,'searchable': false, 'orderable': false, 'width':'10%',
              'render': function (data, type, full, meta)
              {
                var info = $('<div/>').text(data).html();

                return '<div class="btn-group-sm">'+
                  '<button type="button" class="btn btn-warning updateMfs" data-id="'+info.split('|')[0]+'">Update</button>'+
                  '</div>';
              }
            }
          ],
          "processing": true,
          "serverSide": true,
          "pageLength": 500,
          "language": {
            "emptyTable": "No Mfs Found.",
          },
          "ajax": {
            "url": this.$axios.defaults.baseURL+'/api/mfs',
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
              console.log("Done...");
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
        this.newMfs.file = this.$refs.mfsCreateFile.files[0];
      },
      resetFilter(){
        this.loadPageData();
      },
      setDefaultMfsId(uid){
        this.page_message = "Gathering STore Details. Please wait...."
        this.defaultMfsId = uid;
        this.formToDoUpdate = true;
        this.$axios.get("/api/mfs/"+uid, {
          headers: {Authorization: this.$auth.getToken('local')}
        })
          .then(response => {
            this.page_message = "";
            this.newMfs.mfs_name = response.data.data.mfs_name;
            this.newMfs.default_commission = response.data.data.default_commission;
            this.newMfs.default_charge = response.data.data.default_charge;
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

            this.$axios.patch("/api/mfs/"+this.defaultMfsId, this.newMfs,{
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

          Object.keys(this.newMfs).forEach(key => {
            formData.append(key, this.newMfs[key])
          });

          this.$axios.post("/api/mfs_c", formData,
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
