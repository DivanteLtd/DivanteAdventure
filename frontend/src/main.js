// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import App from './App';
import router from './router/index';
import store from './store/index';
import Vuex from 'vuex/dist/vuex';
import './util/latinizeString';
import VeeValidate from 'vee-validate';
import Truncate from 'lodash.truncate';
import { i18n } from './i18n/i18n';
import DataStorage from '@divante/data-storage';
import Fragment from 'vue-fragment';
import 'material-design-icons-iconfont/dist/material-design-icons.css';
import './registerServiceWorker';
import vuetify from './plugins/vuetify';


Vue.use(Fragment.Plugin);
Vue.config.productionTip = false;
// Helpers
// Global filters
Vue.filter('truncate', Truncate);
Vue.use(VeeValidate, { fieldsBagName: 'formFields' });

// Bootstrap application components

Vue.use(Vuex);

new Vue({
  el: '#app',
  router,
  store,
  i18n,
  components: { App },
  vuetify,
  template: '<App/>',
});

if (process.env.NODE_ENV === 'development') {
  const storage = new DataStorage();

  window._adv_setToken = function(token) {
    const tokenObject = {
      isLoggedIn: true,
      token,
      refreshToken: '',
      exp: Math.round(new Date().getTime() / 1000) + (60 * 60 * 24),
    };
    storage.setObjectValue('frontendAuth', tokenObject);
  };
}
