<template>
  <div class="card" style="margin-top: 20px">
    <div class="card-header bg-white header-elements-inline">
      <h6 class="card-title">{{pageTitle}} <span class="text-danger" v-if="(page_message.length > 0)">({{page_message}})</span></h6>
      <div class="header-elements">
        <button type="button" class="btn btn-primary" v-on:click="updatePermission()">Update</button>
      </div>
    </div>
    <div class="card-body" :style="{ 'height': `${windowHeight - 170}px`}">

      <div class="row">
        <div v-for="(dta, index) in data.user_permissions" v-bind:key="index" class="col-xs-12 col-md-3" style="margin-bottom: 20px;">
          <input value="" type="checkbox" :class="['mother_'+index.replace(/\s/g, '')]" v-on:click="toggleCheckUncheck(index.replace(/\s/g, ''), ('mother_'+index.replace(/\s/g, '')))"> <span style="font-size: 18px; font-weight: bold">{{index}}</span><br>

          <span v-for="(d, dindex) in dta" v-bind:key="dindex">
                        <input type="checkbox" name="user_permission[]" :checked="(data.permission_list.includes(d.id)?'checked':'')" :class="[''+index.replace(/\s/g, '')]" :value="(d.id)">
                        {{d.definition}}<br>
                    </span>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
  export default {
    name: "UserPermission",
    middleware: ['auth', 'permission_check'],
    data() {
      return {
        page_message:'',
        windowHeight: 0,
        windowWidth: 0,
        pageTitle:"User () Permission",
        data: [],
      }
    },
    mounted() {
      this.windowHeight = window.innerHeight
      this.windowWidth = window.innerWidth
      window.addEventListener('resize', () => {
        this.windowHeight = window.innerHeight
        this.windowWidth = window.innerWidth
      })
      this.loadPageData();
    },
    methods: {
      toggleCheckUncheck(__class_name, __parent_class_name)
      {
        //var checkBoxes = $("."+__class_name);
        //checkBoxes.prop("checked", !checkBoxes.prop("checked"));
        var checkBoxes = $("."+__class_name);
        checkBoxes.prop("checked", $("."+__parent_class_name).prop("checked"));
      },
      updatePermission()
      {
        if(confirm("Are You Sure ?"))
        {
          this.page_message = 'Please Wait.....'
          var newPermissionList = []

          $('input[type=checkbox]').each(function () {
            if(this.checked && $(this).val().length > 0)
            {
              newPermissionList.push($(this).val())
            }
          });

          this.$axios.post("api/user/"+this.$route.params.slug+"/permissions", {
              permission_ids: newPermissionList.join("|")
            },{
            headers: {
              Authorization: this.$auth.getToken('local')
            },
          })
            .then(response => {
              this.page_message = 'User Updated Successfully. Reloading User Table....'
              window.location.href = `/users`;
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
                this.page_message = ''
              }
            });
        }
      },
      loadPageData(){
        this.page_message = 'Preparing Permission Data'
        this.$axios.get("/api/user/"+this.$route.params.slug+"/permissions", {
            headers: {
              Authorization: this.$auth.getToken('local')
            },
          })
          .then(response => {
            this.page_message = ''
            this.data = response.data.data
            this.pageTitle = "User ("+response.data.data.user_details.username+") Permission"
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
</script>

<style scoped>

</style>
