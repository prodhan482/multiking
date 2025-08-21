<template>

  <section class="app-user-list">
    <!-- users filter start -->
    <div class="card" v-if="false">
      <h5 class="card-header">Search Filter</h5>
      <div class="d-flex justify-content-between align-items-center mx-50 row pt-0 pb-2">
        <div class="col-md-4 user_role">
          <select class="form-control" id="normalMultiSelect">
            <option selected="selected">Square</option>
            <option>Rectangle</option>
            <option selected="selected">Rombo</option>
            <option>Romboid</option>
            <option>Trapeze</option>
            <option>Triangle</option>
            <option selected="selected">Polygon</option>
            <option>Regular polygon</option>
            <option>Circumference</option>
            <option>Circle</option>
          </select>
        </div>
        <div class="col-md-4 user_plan"></div>
        <div class="col-md-4 user_status"></div>
      </div>
    </div>
    <!-- users filter end -->
    <!-- list section start -->
    <div class="card">
      <div class="card-datatable table-responsive pt-0">
        <table class="table table-xs dataTable">
          <thead>
          <tr>
            <th>#</th>
            <th>User Name</th>
            <th>Reseller/Vendor Name</th>
            <th>Created On</th>
            <th>Actions</th>
          </tr>
          </thead>
        </table>
      </div>
      <!-- Modal to add new user starts-->
      <div class="modal modal-slide-in new-user-modal fade" id="modals-slide-in">
        <div class="modal-dialog">
          <form class="add-new-user modal-content pt-0">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
            <div class="modal-header mb-1">
              <h5 class="modal-title" id="exampleModalLabel">New User</h5>
            </div>
            <div class="modal-body flex-grow-1">
              <div class="form-group">
                <label class="form-label" for="basic-icon-default-fullname">Full Name</label>
                <input type="text" class="form-control dt-full-name" id="basic-icon-default-fullname" placeholder="John Doe" name="user-fullname" aria-label="John Doe" aria-describedby="basic-icon-default-fullname2" />
              </div>
              <div class="form-group">
                <label class="form-label" for="basic-icon-default-uname">Username</label>
                <input type="text" id="basic-icon-default-uname" class="form-control dt-uname" placeholder="Web Developer" aria-label="jdoe1" aria-describedby="basic-icon-default-uname2" name="user-name" />
              </div>
              <div class="form-group">
                <label class="form-label" for="basic-icon-default-email">Email</label>
                <input type="text" id="basic-icon-default-email" class="form-control dt-email" placeholder="john.doe@example.com" aria-label="john.doe@example.com" aria-describedby="basic-icon-default-email2" name="user-email" />
                <small class="form-text text-muted"> You can use letters, numbers & periods </small>
              </div>
              <div class="form-group">
                <label class="form-label" for="user-role">User Role</label>
                <select id="user-role" class="form-control">
                  <option value="subscriber">Subscriber</option>
                  <option value="editor">Editor</option>
                  <option value="maintainer">Maintainer</option>
                  <option value="author">Author</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
              <div class="form-group mb-2">
                <label class="form-label" for="user-plan">Select Plan</label>
                <select id="user-plan" class="form-control">
                  <option value="basic">Basic</option>
                  <option value="enterprise">Enterprise</option>
                  <option value="company">Company</option>
                  <option value="team">Team</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary mr-1 data-submit">Submit</button>
              <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
      <!-- Modal to add new user Ends-->
    </div>
    <!-- list section end -->

    <div id="modal_iconified" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="fa fa-user-plus"></i> Create User</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div v-if="(userCreationErrorMessage.length > 0)" class="alert alert-warning alert-dismissible">
              <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
              <span class="font-weight-semibold">Warning!</span> {{userCreationErrorMessage}}
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">User Name</label>
              <div class="col-sm-9">
                <input type="text" placeholder="Type User Name" class="form-control" v-model="enteredUserName">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">User Password</label>
              <div class="col-sm-9">
                <input type="password" placeholder="Type User Password" class="form-control" v-model="enteredUserPassword">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" data-dismiss="modal">Close</button>
            <button class="btn btn-danger" v-on:click="createUser()">Save</button>
          </div>
        </div>
      </div>
    </div>

    <div id="modal_user_password_change" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Update Password</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div v-if="(userCreationErrorMessage.length > 0)" class="alert alert-warning alert-dismissible">
              <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
              <span class="font-weight-semibold">Warning!</span> {{userCreationErrorMessage}}
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">New Password</label>
              <div class="col-sm-9">
                <input type="password" placeholder="Type New Password" class="form-control" v-model="enteredUserPassword">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" data-dismiss="modal">Close</button>
            <button class="btn btn-danger" v-on:click="resetPassword()">Save</button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
  const numeral = !process.client ? null : require('numeral');
  var theInstance= {};

  export default {
    name: "UserList",
    middleware: ['auth', 'permission_check'],
    components: {},
    data() {
      return {
        userCreationErrorMessage:'',
        page_message:'',
        enteredUserName:'',
        enteredUserPassword:'',
        windowHeight: 0,
        windowWidth: 0,
        defaultUserId:'',
        masterTable:{},
        data: [],
      }
    },
    mounted() {
      if (feather) {
        feather.replace({
          width: 14,
          height: 14
        });
      }
      this.windowHeight = window.innerHeight
      this.windowWidth = window.innerWidth
      window.addEventListener('resize', () => {
        this.windowHeight = window.innerHeight
        this.windowWidth = window.innerWidth
      });
      theInstance = this;
      //this.loadPageData();



      var select = $('#normalMultiSelect');

      select.each(function () {
        var $this = $(this);
        $this.wrap('<div class="position-relative"></div>');
        $this.select2({
          // the following code is used to disable x-scrollbar when click in select input and
          // take 100% width in responsive also
          dropdownAutoWidth: true,
          width: '100%',
          dropdownParent: $this.parent()
        });
      });

      this.masterTable = $('.dataTable').DataTable({
          //scrollX: true,
          //scrollY: (this.windowHeight - 260)+'px',
          scrollCollapse: true,
          //"searching": false,
          //"info": false,
          //"paging": false,
          "ordering": false,
          "preDrawCallback": function(settings)
          {
            theInstance.scrollPosition = $(".dataTables_scrollBody").scrollTop();
          },
          "drawCallback": function(settings)
          {
            //var api = this.api();
            $(".dataTables_scrollBody").scrollTop(theInstance.scrollPosition);
            //theInstance.dTableMount();
            theInstance.page_message = ''
          },
          "columnDefs": [
            {
              'targets': 4,'searchable': false, 'orderable': false, 'width':'10%',
              'render': function (data, type, full, meta)
              {
                var info = $('<div/>').text(data).html();

                var dta = info.split('|');

                return '<div class="btn-group btn-group-sm">'+
                  ((dta[0].length>0 && dta[1] && (dta[1] !=='store' && dta[1] !=='vendor'))?'<button type="button" class="btn btn-danger removeBtt" data-id="'+dta[0]+'">Remove</button>':'')+
                  (dta[0].length>0?'<button type="button" class="btn btn-primary changePassword" data-id="'+dta[0]+'">Change Password</button>':'')+
                  ((dta[0].length>0 && dta[1] && (dta[1] !=='store' && dta[1] !=='vendor'))?'<a class="btn btn-warning changePermission" href="/'+dta[0]+'/user_permission">Change Permission</a>':'')+
                  '</div>';

              }
            }
          ],
        dom:
          '<"row d-flex justify-content-between align-items-center m-1"' +
          '<"col-lg-6 d-flex align-items-center"l<"dt-action-buttons text-xl-right text-lg-left text-lg-right text-left "B>>' +
          '<"col-lg-6 d-flex align-items-center justify-content-lg-end flex-lg-nowrap flex-wrap pr-lg-1 p-0"f<"invoice_status ml-sm-2">>' +
          '>t' +
          '<"d-flex justify-content-between mx-2 row"' +
          '<"col-sm-12 col-md-6"i>' +
          '<"col-sm-12 col-md-6"p>' +
          '>',
          buttons: [
            {
              text: 'Add Record',
              className: 'btn btn-danger btn-add-record ml-2',
              action: function (e, dt, button, config) {
                $('#modal_iconified').modal('show')
              }
            }
          ],
          "processing": true,
          "serverSide": true,
          "pageLength": 100,
          "language": {
            "emptyTable": "No Adjustment History Data Found.",
          },
          "ajax": {
            "url": this.$axios.defaults.baseURL+'/api/user',
            "type": "GET",
            'beforeSend': function (request) {
              request.setRequestHeader("Authorization", theInstance.$auth.getToken('local'));
            },
            "data": function ( d )
            {
              d = theInstance.tableFilter
            },
            complete: function(data)
            {
              theInstance.dTableMount();
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
      loadPageData(){
        this.masterTable.ajax.reload();
      },
      dTableMount() {
        $(".removeBtt").click(function(e){
          theInstance.removeUser($(this).data("id"));
        });

        $(".changePassword").click(function(e){
          theInstance.setDefaultUserId($(this).data("id"));
          $('#modal_user_password_change').modal('show');
        });
      },
      removeUser(user_id){
        if(confirm("Are you sure?"))
        {
          this.$axios.delete("/api/user/"+user_id, {
              headers: {
                Authorization: this.$auth.getToken('local')
              }
            })
            .then(response => {
              this.page_message = 'User Removed Successfully. Reloading User Table....'
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
      setDefaultUserId(uid){
        this.defaultUserId = uid;
      },
      resetPassword(){
        $('#modal_user_password_change').modal('hide');
        this.$axios.patch("/api/user/"+this.defaultUserId, {
            password: this.enteredUserPassword
          },{
            headers: {
              Authorization: this.$auth.getToken('local')
            },
          })
          .then(response => {
            this.page_message = 'User Updated Successfully. Reloading User Table....'
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
                  this.userCreationErrorMessage = error.response.data.message.join(",")
                  break;
              }
              $('#modal_user_password_change').modal('show');
              this.page_message = ''
            }
          });

        this.page_message = 'Please Wait. We Are Updating User .....'
      },
      createUser(){
        $('#modal_iconified').modal('hide');

        this.$axios.post("/api/user", {
            user_name: this.enteredUserName,
            password: this.enteredUserPassword
          },
          {
            headers: {
              Authorization: this.$auth.getToken('local')
            },
          }).then(response => {
            this.page_message = 'User Created Successfully. Reloading User Table....'
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
                  this.userCreationErrorMessage = error.response.data.message.join(",")
                  break;
              }
              $('#modal_iconified').modal('show');
              this.page_message = ''
            }
          });

        this.page_message = 'Please Wait. We Are Creating User .....'
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
