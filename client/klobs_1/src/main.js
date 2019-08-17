// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import './plugins/vuetify'
import App from './App'
import router from './router'

import 'vuetify/dist/vuetify.min.css' // Ensure you are using css-loader
import './registerServiceWorker'

Vue.config.productionTip = false
// Vue.prototype.$locations = {}
// Object.defineProperty(Vue.prototype, '$locations', { value: {} })
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
})
