<template>
  <div>
    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow">
      <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
          <ul class="nav navbar-nav d-xl-none">
            <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i class="fa fa-bars" aria-hidden="true"></i></a></li>
          </ul>
          <ul class="nav navbar-nav bookmark-icons">
            <li v-if="userBalance.length > 0" class="nav-item d-none d-lg-block"><a class="nav-link" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Balance"><span style="font-weight: bold;">{{ userBalance }}</span></a></li>
          </ul>
        </div>
        <ul class="nav navbar-nav align-items-center ml-auto">

          <li v-if="userBalance.length > 0" class="nav-item d-block d-sm-block d-md-block d-lg-none"><a class="nav-link" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Balance"><span style="font-weight: bold;">{{ userBalance }}</span></a></li>

          <li v-if="false" class="nav-item dropdown dropdown-notification mr-25"><a class="nav-link" href="javascript:void(0);" data-toggle="dropdown"><i class="ficon" data-feather="bell"></i><span class="badge badge-pill badge-danger badge-up">5</span></a>
            <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
              <li class="dropdown-menu-header">
                <div class="dropdown-header d-flex">
                  <h4 class="notification-title mb-0 mr-auto">Notifications</h4>
                  <div class="badge badge-pill badge-light-primary">6 New</div>
                </div>
              </li>
              <li class="scrollable-container media-list">
                <a class="d-flex" href="javascript:void(0)">
                <div class="media d-flex align-items-start">
                  <div class="media-left">
                    <div class="avatar bg-light-danger">
                      <div class="avatar-content">MD</div>
                    </div>
                  </div>
                  <div class="media-body">
                    <p class="media-heading"><span class="font-weight-bolder">Revised Order 👋</span>&nbsp;checkout</p><small class="notification-text"> MD Inc. order updated</small>
                  </div>
                </div>
              </a>

                <a class="d-flex" href="javascript:void(0)">
                  <div class="media d-flex align-items-start">
                    <div class="media-left">
                      <div class="avatar bg-light-danger">
                        <div class="avatar-content"><i class="avatar-icon" data-feather="x"></i></div>
                      </div>
                    </div>
                    <div class="media-body">
                      <p class="media-heading"><span class="font-weight-bolder">Server down</span>&nbsp;registered</p><small class="notification-text"> USA Server is down due to hight CPU usage</small>
                    </div>
                  </div>
                </a><a class="d-flex" href="javascript:void(0)">
                  <div class="media d-flex align-items-start">
                    <div class="media-left">
                      <div class="avatar bg-light-success">
                        <div class="avatar-content"><i class="avatar-icon" data-feather="check"></i></div>
                      </div>
                    </div>
                    <div class="media-body">
                      <p class="media-heading"><span class="font-weight-bolder">Sales report</span>&nbsp;generated</p><small class="notification-text"> Last month sales report generated</small>
                    </div>
                  </div>
                </a><a class="d-flex" href="javascript:void(0)">
                  <div class="media d-flex align-items-start">
                    <div class="media-left">
                      <div class="avatar bg-light-warning">
                        <div class="avatar-content"><i class="avatar-icon" data-feather="alert-triangle"></i></div>
                      </div>
                    </div>
                    <div class="media-body">
                      <p class="media-heading"><span class="font-weight-bolder">High memory</span>&nbsp;usage</p><small class="notification-text"> BLR Server using high memory</small>
                    </div>
                  </div>
                </a>
              </li>
              <li class="dropdown-menu-footer"><a class="btn btn-primary btn-block" href="javascript:void(0)">Read all notifications</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown dropdown-user">
            <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder">{{ toTitleCase(userName) }}</span><span class="user-status">{{ toTitleCase(userType.replace(/_/g, ' ')) }}</span></div><span class="avatar"><img class="round" src="/assets/images/ff.png" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
          </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
              <a class="dropdown-item" href="#" data-toggle="modal" data-target="#changePassword"><i class="mr-50" data-feather="user"></i> Change Pass.</a>
              <a class="dropdown-item" href="javascript:void(0)" v-on:click="userLogout"><i class="mr-50" data-feather="power"></i> Logout</a>
            </div>
          </li>
        </ul>
      </div>
    </nav>
    <ul class="main-search-list-defaultlist d-none">
      <li class="d-flex align-items-center"><a href="javascript:void(0);">
        <h6 class="section-label mt-75 mb-0">Files</h6>
      </a></li>
      <li class="d-flex align-items-center"><a href="javascript:void(0);">
        <h6 class="section-label mt-75 mb-0">Members</h6>
      </a></li>
    </ul>
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
      <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
          <li class="nav-item mr-auto">
            <a class="navbar-brand" href="/manage">
              <img width="50" src="/assets/images/Logo.png"/>
            <h2 class="brand-text">bKashBD.EU</h2>
          </a></li>
          <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a></li>
        </ul>
      </div>
      <div class="shadow-bottom"></div>
      <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
          <li :class='[checkIfRouterActive("manage"), "nav-item"]'><a class="d-flex align-items-center" href="/manage"><i class="fa fa-tachometer"></i><span class="menu-title text-truncate" data-i18n="Dashboards">Dashboards</span></a></li>

          <li :class='[checkIfRouterActive("store"), "nav-item"]' v-if="(permissionLists.includes('StoreController::list'))"><a class="d-flex align-items-center" href="/store"><i class="fa fa-user-circle"></i><span class="menu-title text-truncate" data-i18n="Typography">Reseller</span></a></li>

          <li :class='[checkIfRouterActive("vendor"), "nav-item"]' v-if="(permissionLists.includes('VendorController::list'))"><a class="d-flex align-items-center" href="/vendor"><i class="fa fa-address-book-o"></i><span class="menu-title text-truncate" data-i18n="Colors">Vendor</span></a></li>

          <li :class='[checkIfRouterActive("recharge"), "nav-item"]' v-if="(permissionLists.includes('RechargeController::list'))"><a class="d-flex align-items-center" href="/recharge"><i class="fa fa-bolt"></i><span class="menu-title text-truncate" data-i18n="Feather">Request</span></a></li>

          <li :class='[checkIfRouterActiveExact("mfs"), "nav-item"]' v-if="(permissionLists.includes('MFSController::list'))"><a class="d-flex align-items-center" href="/mfs"><i class="fa fa-mobile"></i><span class="menu-title text-truncate" data-i18n="Feather">MFS</span></a></li>

          <li :class='[checkIfRouterActive("mfs_package"), "nav-item"]' v-if="(permissionLists.includes('MFSController::package_list'))"><a class="d-flex align-items-center" href="/mfs_package"><i class="fa fa-calculator"></i><span class="menu-title text-truncate" data-i18n="Feather">MFS Package</span></a></li>

          <li v-if="false" :class='[checkIfRouterActive("settings"), "nav-item"]'><a class="d-flex align-items-center" href="/settings"><i class="fa fa-cogs"></i><span class="menu-title text-truncate" data-i18n="Feather">Global Settings</span></a></li>

          <li :class='[checkIfRouterActive("users"), "nav-item"]' v-if="(permissionLists.includes('UserController::list'))"><a class="d-flex align-items-center" href="/users"><i class="fa fa-users"></i><span class="menu-title text-truncate" data-i18n="Feather">User</span></a></li>

          <li class=" navigation-header"><span data-i18n="Apps &amp; Pages">Reports</span><i data-feather="more-horizontal"></i></li>

<!--          <li :class='[checkIfRouterActive("users"), "nav-item"]' v-if="(permissionLists.includes('ReportController::vendor_adjustment'))" class="nav-item"><a class="d-flex align-items-center" href="/reports/adjustment_history/vendor"><i class="fa fa-book"></i><span class="menu-title text-truncate" data-i18n="Feather">Vendor Adjustment</span></a></li>

          <li :class='[checkIfRouterActive("users"), "nav-item"]' v-if="(permissionLists.includes('ReportController::store_adjustment'))" class="nav-item"><a class="d-flex align-items-center" href="/reports/adjustment_history/store"><i class="fa fa-file-text"></i><span class="menu-title text-truncate" data-i18n="Feather">Reseller Adjustment</span></a></li>-->
        </ul>
      </div>
    </div>
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content ">
      <div class="content-overlay"></div>
      <div class="header-navbar-shadow"></div>
      <div class="content-wrapper">
        <div class="content-header">
          <h2 class="content-header-title"> {{ pageTitle }} </h2>
        </div>
        <div class="content-body">
          <Nuxt />
        </div>
      </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
      <p class="clearfix mb-0">
        <span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; <a class="ml-25" href="http://bkashbd.eu" target="_blank">bKashBD.EU</a>
          <span class="d-none d-sm-inline-block">, All rights Reserved</span>
        </span>
      </p>
    </footer>
    <!-- END: Footer-->


    <div id="changePassword" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Update Password</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-3">New Password</label>
              <div class="col-sm-9">
                <input type="text" placeholder="Enter New Password" class="form-control" v-model="updatePassword.new_password">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-warning" data-dismiss="modal"> Cancel</button>
            <button class="btn btn-danger" v-on:click="updatePasswordFun"> Update</button>
          </div>
        </div>
      </div>
    </div>


  </div>
</template>

<style>
.brand-text{
  color: #E2266E !important;
}
</style>

<script>
  import axios from "axios";

  export default {
    name: "appvue",
    head() {
      return {
        bodyAttrs: {
          'class': this.body_class//'vertical-layout vertical-menu-modern  navbar-floating footer-static',
          //'class': 'vertical-layout navbar-floating footer-static  menu-hide vertical-overlay-menu'
        },
      }
    },
    data() {
      return {
        pageTitle: "",
        showPageTitle : true,
        permissionLists:[],
        userType:'',
        userName:'',
        userBalance:'',
        body_class:'vertical-layout vertical-menu-modern  navbar-floating footer-static menu-expanded',
        updatePassword:{
          new_password:''
        }
      }
    },
    computed: {
      currentRouteName() {
        return this.$route.name;
      },
    },
    mounted() {
      this.loadUserData();
      if(this.$isMobile())
      {
        this.body_class = 'vertical-layout navbar-floating footer-static  menu-hide vertical-overlay-menu'
      }

      $.app.menu.init()
      $.app.nav.init()
    },
    methods:{
      setPageTitle(__page_title)
      {
        this.pageTitle = __page_title
      },
      checkIfRouterActive(route_name) {
        return (this.$route.name.indexOf(route_name) !== -1?"active":"");
      },
      checkIfRouterActiveExact(route_name) {
        return (this.$route.name === route_name?"active":"");
      },
      getUserName(){
        return (typeof localStorage === 'undefined'?"":JSON.parse(localStorage.userInfo).username);
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
      updatePasswordFun(){
        $('#changePassword').modal('hide');
        this.$axios.patch("/api/me/update", this.updatePassword,{
          headers: {
            Authorization: this.$auth.getToken('local')
          }
        })
          .then(response => {
            alert("Your password have been updated. Please re-login")
            this.userLogout();
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
      toTitleCase(str) {
        return str.replace(
          /\w\S*/g,
          function(txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
          }
        );
      },
      loadUserData(){
        this.$axios.get("/api/profile",{
            headers: {
              Authorization: this.$auth.getToken('local')
            },
          })
          .then(response => {
            localStorage.userInfo = JSON.stringify(response.data.data)
            this.permissionLists = response.data.data.permission_lists
            this.userType = response.data.data.user_type
            this.userName = response.data.data.username
            if(response.data.current_balance.amount.length > 0) this.userBalance = response.data.current_balance.currency+" "+response.data.current_balance.amount+"/="
          })
          .catch(error => {
            if (error.response) {
              switch (error.response.status)
              {
                case 401:
                  this.makeForceLogout()
                  break;
                case 406:
                  //console.log(error.response)
                  break;
              }
            }
          });
        /*if(!localStorage.userInfo || (localStorage.userInfo && localStorage.userInfo.length == 0))
        {

        }
        else
        {
            this.permissionLists = JSON.parse(localStorage.userInfo).permission_lists
        }*/
      },
    }
  }
</script>
