<template>
  <div id="app">
    <div class="page-content pt-0">
      <div class="content-wrapper">
        <div class="content">
          <Nuxt />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    export default {
        name: "login",
        head: {
          title: 'Login :: bKashBD.EU',
          link: [
            { rel: 'stylesheet', type: 'text/css', href: '/assets/css/plugins/forms/form-validation.css' },
            { rel: 'stylesheet', type: 'text/css', href: '/assets/css/pages/page-auth.css' },
          ]
        },
    }
</script>

<style scoped>

</style>
