import 'babel-polyfill'
import Vue from 'vue'
import App from './App.vue'
import axios from 'axios'
import Lockr from 'lockr'
import Cookies from 'js-cookie'
import _ from 'lodash'
import moment from 'moment'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import routes from './routes'
import VueRouter from 'vue-router'
import store from './vuex/store'
import _g from './assets/js/global'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import 'assets/css/global.css'
import 'assets/css/base.css'
if (SERVER_ENV === 'production') {
    axios.defaults.baseURL = HOST + 'vuethink/php/index.php/';
} else {
    axios.defaults.baseURL = 'http://localhost:8088/' + 'api/';
}
axios.defaults.timeout = 1000 * 15;
axios.defaults.headers.authKey = Lockr.get('authKey');
axios.defaults.headers.sessionId = Lockr.get('sessionId');
axios.defaults.headers['Content-Type'] = 'application/json';
// axios.defaults.headers['Content-Type'] = 'application/x-www-form-urlencoded';

const router = new VueRouter({
  //mode: 'history',
  base: 'frontEnd/dist',
  routes
});
router.beforeEach((to, from, next) => {
  const hideLeft = to.meta.hideLeft;
  store.dispatch('showLeftMenu', hideLeft);
  store.dispatch('showLoading', true);
  NProgress.start();
  next()
});

router.afterEach(transition => {
  NProgress.done()
});

Vue.use(ElementUI);
Vue.use(VueRouter);
window.router = router;
window.store = store;
if (SERVER_ENV === 'production') {
    window.HOST = HOST +'vuethink/php/index.php/';
} else {
    window.HOST = 'http://localhost:8088/' +'api/';
}
window.axios = axios;
window._ = _;
window.moment = moment;
window.Lockr = Lockr;
window.Cookies = Cookies;
window._g = _g;
window.pageSize = 15;

const bus = new Vue();
window.bus = bus;

new Vue({
  el: '#app',
  template: '<App/>',
  router,
  store,
  components: { App }
// render: h => h(Login)
}).$mount('#app');
