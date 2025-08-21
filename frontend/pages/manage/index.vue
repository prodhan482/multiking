<template>
  <section id="dashboard-analytics">
    <store_dashboard v-if="showStoreDashboard"/>
    <vendor_dashboard v-if="showVendorDashboard"/>
    <admin_dashboard v-if="showAdminDashboard"/>
  </section>
</template>
<script>
    import Store_dashboard from "@/components/store_dashboard";
    import Vendor_dashboard from "@/components/vendor_dashboard";
    import Admin_dashboard from "@/components/admin_dashboard";
    export default {
      name: "index",
      components: {Vendor_dashboard, Store_dashboard, Admin_dashboard},
      middleware: ['auth', 'permission_check'],
      head: {
        title: 'Dashboard :: bKashBD.EU',
        link: [
          //{ rel: 'stylesheet', type: 'text/css', href: '/assets/css/pages/page-auth.css' },
        ]
      },
      data() {
        return {
          formErrorMessage:'',
          page_message:'',
          masterTable:{},
          windowHeight: 0,
          windowWidth: 0,
          scrollPosition:0,
          formToDoUpdate:false,
          showStoreDashboard:false,
          showVendorDashboard:false,
          showAdminDashboard:false,
          tableFilter:{},
          page: 1
        }
      },
      mounted() {
        if (feather) {
          feather.replace({
            width: 14,
            height: 14
          });
        }

        if((typeof localStorage !== 'undefined'))
        {
          if(JSON.parse(localStorage.userInfo).user_type === "vendor")
          {
            this.showVendorDashboard = true
          } else if(JSON.parse(localStorage.userInfo).user_type === "store") {
            this.showStoreDashboard = true
          } else {
            this.showAdminDashboard = false
          }
        }

        /*
            if((typeof localStorage !== 'undefined'))
            {
            if(route.fullPath === "/users" && !JSON.parse(localStorage.userInfo).permission_lists.includes("UserController::list"))
          return redirect('/manage');
        * */
      }
    }
</script>
