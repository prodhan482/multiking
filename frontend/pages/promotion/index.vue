<template>
  <div class="card" style="margin-top: 20px">
    <div class="card-header bg-white header-elements-inline">
      <h6 class="card-title">Promotion List <span class="text-danger" v-if="(page_message.length > 0)">({{page_message}})</span></h6>
      <div class="header-elements">
        <div class="list-icons">
          <a href="#" data-toggle="modal" data-target="#modal_create_new_promotion" class="list-icons-item"><i class="icon-plus-circle2"></i> Add New Promotion</a>
        </div>
      </div>
    </div>
    <div class="card-body" :style="{'padding':'0'}">
      <table class="table datatable-basic dataTable no-footer datatable-scroll-y" style="width:100%">
        <thead>
        <tr>
          <th>Sl</th>
          <th>Logo</th>
          <th>Name</th>
          <th>MFS</th>
          <th>Promotional Amount</th>
          <th>Status</th>
          <th>Created On</th>
          <th>Action</th>
        </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
    <div id="modal_create_new_promotion" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="icon-user-plus mr-2"></i> Create Promotion</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Promotion Title</label>
              <div class="col-sm-9">
                <input type="text" placeholder="Promotion Title" class="form-control" v-model="newPromotion.promotion_name">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">MFS</label>
              <div class="col-sm-9">
                <select class="form-control" v-model="newPromotion.mfs">
                  <option value="">Select A MFS</option>
                  <option v-for="option in mfs_list" v-bind:value="option.mfs_id">
                    {{ option.mfs_name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Promotional Amount</label>
              <div class="col-sm-9">
                <input type="number" placeholder="Promotional Amount" class="form-control" v-model="newPromotion.promotional_amount">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Promotion Logo</label>
              <div class="col-sm-9">
                <input type="file" class="form-control h-auto" ref="promotionCreateFile" accept="image/*" v-on:change="createImageSelected">
              </div>
            </div>
            <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
              <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
              <span class="font-weight-semibold">Error!</span> {{formErrorMessage}}
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-link" data-dismiss="modal"><i class="icon-cross2 font-size-base mr-1"></i> Close</button>
            <button class="btn bg-primary" v-on:click="createUpdatePromotion(false)"><i class="icon-checkmark3 font-size-base mr-1"></i> Save</button>
          </div>
        </div>
      </div>
    </div>
    <div id="modal_update_existing_promotion" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="icon-user-plus mr-2"></i> Update Promotion {{newPromotion.promotion_name}}</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Promotion Title</label>
              <div class="col-sm-9">
                <input type="text" placeholder="Promotion Title" class="form-control" v-model="newPromotion.promotion_name">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Promotional Amount</label>
              <div class="col-sm-9">
                <input type="number" placeholder="Promotional Amount" class="form-control" v-model="newPromotion.promotional_amount">
              </div>
            </div>
            <div class="alert alert-danger alert-dismissible" v-if="formErrorMessage.length > 0">
              <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
              <span class="font-weight-semibold">Error!</span> {{formErrorMessage}}
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-link" data-dismiss="modal"><i class="icon-cross2 font-size-base mr-1"></i> Close</button>
            <button class="btn bg-primary" v-on:click="createUpdatePromotion(true)"><i class="icon-checkmark3 font-size-base mr-1"></i> Save</button>
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
    name: "PromotionList",
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
        defaultPromotionId:'',
        formToDoUpdate:false,
        page: 1,
        newPromotion:{
          promotion_name:'',
          file:'',
          mfs:'',
          user_id:'',
          promotional_amount:0.00
        },
        mfs_list:[]
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
          scrollX: true,
          scrollY: (this.windowHeight - 260)+'px',
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
              'targets': 5,'searchable': false, 'orderable': false, 'width':'10%',
              'render': function (data, type, full, meta)
              {
                var info = $('<div/>').text(data).html();

                if(info === "disabled") return '<span class="badge badge-danger badge-pill">Disabled</span>';
                if(info === "enabled") return '<span class="badge badge-success badge-pill">Enabled</span>';

                return '';
              }
            },
            {
              'targets': 7,'searchable': false, 'orderable': false, 'width':'10%',
              'render': function (data, type, full, meta)
              {
                var info = $('<div/>').text(data).html();

                return '<div class="btn-group-sm">'+
                  //'<button type="button" class="btn btn-danger removePromotionBtt" data-id="'+info.split('|')[0]+'">Remove</button>'+
                  '<button type="button" class="btn bg-purple changePromotionStatus" data-id="'+info.split('|')[0]+'" data-current_status="'+info.split('|')[1]+'">Change Status</button>'+
                  '<button type="button" class="btn btn-warning updatePromotion" data-id="'+info.split('|')[0]+'">Update</button>'+
                  '</div>';
              }
            }
          ],
          "processing": true,
          "serverSide": true,
          "pageLength": 500,
          "language": {
            "emptyTable": "No Promotion Found.",
          },
          "ajax": {
            "url": this.$axios.defaults.baseURL+'/api/promotion',
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
              theInstance.mfs_list = data.responseJSON.mfs_list
              //console.log(data.responseJSON);
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
        $(".updatePromotion").click(function(e){
          theInstance.setDefaultPromotionId($(this).data("id"));
        });

        $(".removePromotionBtt").click(function(e){
          theInstance.removeStore($(this).data("id"));
        });

        $(".changePromotionStatus").click(function(e){
          theInstance.changeStoreStatus($(this).data("id"), ($(this).data("current_status")==="enabled"?"disabled":"enabled"));
        });
      },
      loadPageData(){
        this.masterTable.ajax.reload();
      },
      createImageSelected()
      {
        this.newPromotion.file = this.$refs.promotionCreateFile.files[0];
      },
      resetFilter(){
        this.loadPageData();
      },
      changeStoreStatus(uid, new_status){
        if(confirm("Are you sure?"))
        {
          this.page_message = 'Updating Store Status...';

          this.$axios.patch("/api/promotion/"+uid, {"status":new_status},{
            headers: {
              Authorization: this.$auth.getToken('local')
            }
          })
            .then(response => {
              this.page_message = 'Promotion Updated Successfully. Reloading Table....'
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
      setDefaultPromotionId(uid){
        this.page_message = "Gathering STore Details. Please wait...."
        this.defaultPromotionId = uid;
        this.formToDoUpdate = true;
        this.$axios.get("/api/promotion/"+uid, {
          headers: {Authorization: this.$auth.getToken('local')}
        })
          .then(response => {
            this.page_message = "";
            this.newPromotion.promotion_name = response.data.data.promotion_name;
            this.newPromotion.promotional_amount = response.data.data.b1;
            $('#modal_update_existing_promotion').modal('show');
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
      createUpdatePromotion(update){
        if(update)
        {
          if(confirm("Are you sure?"))
          {
            $('#modal_update_existing_promotion').modal('hide');

            this.page_message = 'Updating Promotion Status...';

            this.$axios.patch("/api/promotion/"+this.defaultPromotionId, this.newPromotion,{
              headers: {
                Authorization: this.$auth.getToken('local')
              }
            })
              .then(response => {
                this.page_message = 'Promotion Updated Successfully. Reloading Table....'
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
          $('#modal_create_new_promotion').modal('hide');

          let formData = new FormData();

          Object.keys(this.newPromotion).forEach(key => {
            formData.append(key, this.newPromotion[key])
          });

          this.$axios.post("/api/promotion_c", formData,
            {
              headers: {
                Authorization: this.$auth.getToken('local')
              },
            }).then(response => {
            this.page_message = 'Promotion Created Successfully. Reloading Promotion Table....'
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
                $('#modal_create_new_promotion').modal('show');
                this.page_message = ''
              }
            });

          this.page_message = 'Please Wait. We Are Creating Promotion .....'
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
