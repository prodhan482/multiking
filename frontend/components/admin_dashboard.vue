<template>
  <div class="row match-height">

    <div class="col-lg-3 col-sm-6 col-12">
      <div class="card">
        <div class="card-header flex-column align-items-start pb-0">
          <div class="avatar bg-light-warning p-50 m-0">
            <div class="avatar-content">
              <i data-feather="package" class="font-medium-5"></i>
            </div>
          </div>
          <h2 class="font-weight-bolder mt-1">38.4K</h2>
          <p class="card-text">Request Received Today</p><br>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
      <div class="card">
        <div class="card-header flex-column align-items-start pb-0">
          <div class="avatar bg-light-warning p-50 m-0">
            <div class="avatar-content">
              <i data-feather="package" class="font-medium-5"></i>
            </div>
          </div>
          <h2 class="font-weight-bolder mt-1">38.4K</h2>
          <p class="card-text">Request Received Today</p><br>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
      <div class="card">
        <div class="card-header flex-column align-items-start pb-0">
          <div class="avatar bg-light-warning p-50 m-0">
            <div class="avatar-content">
              <i data-feather="package" class="font-medium-5"></i>
            </div>
          </div>
          <h2 class="font-weight-bolder mt-1">38.4K</h2>
          <p class="card-text">Request Received Today</p><br>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
      <div class="card">
        <div class="card-header flex-column align-items-start pb-0">
          <div class="avatar bg-light-warning p-50 m-0">
            <div class="avatar-content">
              <i data-feather="package" class="font-medium-5"></i>
            </div>
          </div>
          <h2 class="font-weight-bolder mt-1">38.4K</h2>
          <p class="card-text">Request Received Today</p><br>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
export default {
  name: "admin_dashboard",
  middleware: ['auth', 'permission_check'],
  components: {},
  data() {
    return {

    }
  },
  mounted() {

  },
  methods: {
    async makeForceLogout() {
      if (confirm("Your Session have been Expired. You have to re-login to continue. Press ok to logout")) {
        try {
          let response = await this.$auth.logout()
          console.log(response.data)
        } catch (err) {
          console.log(err)
        }
        window.location.href = "login"
      }
    }
  }
}
</script>

<style scoped>

</style>
