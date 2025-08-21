<template>
    <div>
      <Nuxt />
    </div>
</template>

<script>
    export default {
        name: "public",
        head: {
          bodyAttrs: {
            'class': 'vertical-layout vertical-menu-modern blank-page navbar-floating footer-static',
            'data-col': "blank-page"
          }
        }
    }
</script>

<style scoped>

</style>
