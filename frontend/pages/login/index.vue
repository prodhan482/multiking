<template>
  <div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
      <div class="content-header row">
      </div>
      <div class="content-body">
        <div class="auth-wrapper auth-v2">
          <div class="auth-inner row m-0">
            <!-- Brand logo-->
            <a class="brand-logo" href="javascript:void(0);">
              <img height="70" src="/assets/images/Logo.png" alt="Under maintenance page" />
            </a>
            <!-- /Brand logo-->
            <!-- Left Text-->
            <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
              <div class="w-100 d-lg-flex align-items-center justify-content-center px-5"><img class="img-fluid" src="/assets/images/login-v2.svg" alt="Login V2" /></div>
            </div>
            <!-- /Left Text-->
            <!-- Login-->
            <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
              <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                <h2 class="card-title font-weight-bold mb-1">Welcome to bKashBD.EU 👋</h2>
                <p class="card-text mb-2">Please sign-in to your account and start the adventure</p>
                <form class="auth-login-form mt-2" @submit="userLogin">
                  <div class="form-group">
                    <label class="form-label" for="login-email">Email</label>
                    <input  v-model="login.user_name" class="form-control" id="login-email" type="text" name="login-email" placeholder="john@example.com" aria-describedby="login-email" autofocus="" tabindex="1" />
                  </div>

                  <div class="form-group">
                    <div class="input-group input-group-merge form-password-toggle">
                      <input v-model="login.user_password" class="form-control form-control-merge" id="login-password" type="password" name="login-password" placeholder="············" aria-describedby="login-password" tabindex="2" />
                      <div class="input-group-append"><span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span></div>
                    </div>
                  </div>
                  <span class="text text-danger" v-if="showLoginErrorMessage">Your Provided Username and Password is Wrong<br><br></span>
                  <button class="btn btn-danger btn-block" tabindex="4">Sign in</button>
                </form>
              </div>
            </div>
            <!-- /Login-->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    export default {
        name: "index.vue",
        layout: 'login',
        auth: 'guest',
        data() {
          return {
            login: {
              user_name: '',
              user_password: ''
            },
            showLoginErrorMessage:false
          }
        },
        mounted() {
          if(this.$auth.loggedIn){
            this.$router.push('/manage')
          }
        },
        methods: {
          async userLogin(e) {
            this.showLoginErrorMessage = false;
            e.preventDefault();
            try {
              this.$nuxt.$loading.start()
              let response = await this.$auth.loginWith('local', { data: this.login })
              this.showLoginErrorMessage = false;
              //console.log(response.data)
              await this.loadUserData();
            } catch (err) {
              console.log(err)
              this.showLoginErrorMessage = true;
              setTimeout(() => this.$nuxt.$loading.finish(), 500)
            }
          },
          async loadUserData(){
            await this.$axios.get("/api/profile",{
              headers: {
                Authorization: this.$auth.getToken('local')
              },
            })
              .then(response => {
                localStorage.userInfo = JSON.stringify(response.data.data)
                window.location.href = "manage"
                setTimeout(() => this.$nuxt.$loading.finish(), 500)
              })
              .catch(error => {
              });
          }
        }
    }
</script>

<style scoped>

</style>
