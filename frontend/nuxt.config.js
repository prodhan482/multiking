
export default {
  server: {
    port: 3000, // default: 9693
    host: 'localhost'//'0.0.0.0' // default: localhost
  },
  /*
  ** Nuxt rendering mode
  ** See https://nuxtjs.org/api/configuration-mode
  */
  mode: 'spa',
  /*
  ** Nuxt target
  ** See https://nuxtjs.org/api/configuration-target
  */
  target: 'server',

  router: {
    base: '/'
  },
  /*
  ** Headers of the page
  ** See https://nuxtjs.org/api/configuration-head
  */
  loading: {
    color: 'red',
    height: '5px'
  },
  head: {
    title: 'bKashBD.EU',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: process.env.npm_package_description || '' }
    ],
    bodyAttrs: {
      'class': 'vertical-layout vertical-menu-modern blank-page navbar-floating footer-static',
      'data-open': "click",
      'data-menu':"vertical-menu-modern"
    },
    script: [
      { src: "/assets/vendors/js/vendors.min.js"},

      { src: "/assets/vendors/js/tables/datatable/jquery.dataTables.min.js"},
      { src: "/assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"},
      { src: "/assets/vendors/js/tables/datatable/dataTables.responsive.min.js"},
      { src: "/assets/vendors/js/tables/datatable/responsive.bootstrap4.js"},
      { src: "/assets/vendors/js/tables/datatable/datatables.buttons.min.js"},
      { src: "/assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js"},
      { src: "/assets/vendors/js/forms/validation/jquery.validate.min.js"},
      { src: "/assets/vendors/js/forms/select/select2.full.min.js"},
      { src: "/assets/js/scripts/moment/moment.min.js"},
      { src: "/assets/js/scripts/daterangepicker.js"},

      { src: "/assets/js/core/app-menu.js", body:true},
      { src: "/assets/js/core/app.js", body:true},

      //{ src: "/assets/js/core/custom.js"},
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
      { rel: 'apple-touch-icon', sizes:"180x180", type: 'image/x-icon', href: '/apple-touch-icon.png' },
      { rel: 'icon', type: 'image/png', sizes:"32x32", href: '/favicon-32x32.png' },
      { rel: 'icon', type: 'image/png', sizes:"16x16", href: '/favicon-16x16.png' },
      { rel: 'icon', href: '/site.webmanifest' },
      { rel: 'stylesheet', type: 'text/css', href: 'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600' },
      { rel: 'stylesheet', type: 'text/css', href: '/assets/vendors/css/vendors.min.css' },
      { rel: 'stylesheet', type: 'text/css', href: '/assets/vendors/css/forms/select/select2.min.css' },
      { rel: 'stylesheet', type: 'text/css', href: '/assets/css/bootstrap.css' },
      { rel: 'stylesheet', type: 'text/css', href: '/assets/css/bootstrap-extended.css' },
      { rel: 'stylesheet', type: 'text/css', href: '/assets/css/colors.css' },
      { rel: 'stylesheet', type: 'text/css', href: '/assets/css/components.css' },
      { rel: 'stylesheet', type: 'text/css', href: '/assets/css/themes/dark-layout.css' },
      { rel: 'stylesheet', type: 'text/css', href: '/assets/css/themes/bordered-layout.css' },
      { rel: 'stylesheet', type: 'text/css', href: '/assets/css/themes/semi-dark-layout.css' },
      { rel: 'stylesheet', type: 'text/css', href: '/assets/css/pages/page-misc.css' },

      { rel: 'stylesheet', type: 'text/css', href: '/assets/css/core/menu/menu-types/vertical-menu.css' },
      { rel: 'stylesheet', type: 'text/css', href: '/assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css' },
      { rel: 'stylesheet', type: 'text/css', href: '/assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css' },
      { rel: 'stylesheet', type: 'text/css', href: '/assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css' },

      { rel: 'stylesheet', type: 'text/css', href: '/assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css' }
    ]
  },
  /*
  ** Global CSS
  */
  css: [
  ],
  /*
  ** Auto import components
  ** See https://nuxtjs.org/api/configuration-components
  */
  components: true,
  /*
  ** Nuxt.js dev-modules
  */
  buildModules: [
  ],
  /*
  ** Nuxt.js modules
  */
  modules: [
    // Doc: https://axios.nuxtjs.org/usage
    '@nuxtjs/axios',
    '@nuxtjs/auth'
  ],
  auth: {
    strategies: {
      local: {
        endpoints: {
          login: { url: '/api/login', method: 'post', propertyName: 'token' },
          logout: { url: '/api/logout', method: 'post' },
          user: { url: '/api/me', method: 'get', propertyName: false }
        },
      }
    },
    redirect: {
      logout: '/login',
      home: '/manage'
    }
  },
  /*
  ** Axios module configuration
  ** See https://axios.nuxtjs.org/options
  */
  axios: {
    baseURL: 'https://bkashbd.eu/rest',
    retry: { retries: 3 }
  },
  /*
  ** Build configuration
  ** See https://nuxtjs.org/api/configuration-build/
  */
  build: {
    vendor: ['handsontable'],
  },
  plugins: [
    { src: '~/plugins/vue-handsontable', ssr: false },
  ]
}
