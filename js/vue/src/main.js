import Vue from 'vue'
import App from './App.vue'
//vue-router
import router from './router.js'

//element-ui
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
Vue.use(ElementUI);

//axios
import axios from 'axios'
Vue.prototype.$http= axios

//vuex
import store from './store.js'

Vue.config.productionTip = false

new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App)
}).$mount('#app')
