import Vue from 'vue';
import Router from 'vue-router';
import paths from './paths';
import store from '../store/index';
import { EventBus, eventNames } from '../eventbus';
import { i18n } from '../i18n/i18n';

Vue.use(Router);
const router = new Router({
  base: '/',
  mode: 'hash',
  linkActiveClass: 'active',
  routes: paths,
});

router.beforeEach((to, from, next) => {
  store.dispatch('Authorization/awaitFinishedLoading', () => {
    let redirected = false;
    if ((!to.meta.hasOwnProperty('public') || !to.meta.public) && !store.getters['Authorization/isLoggedIn']) {
      redirected = true;
      next('/login');
    }
    else if (to.meta.hasOwnProperty('role') && !store.getters['Authorization/isAuthorized'](to.meta.role)) {
      redirected = true;
      next('/403');
    }
    else if (to.meta.hasOwnProperty('getter') && !store.getters[to.meta.getter]) {
      redirected = true;
      next('/403');
    }
    else {
      store.state.apiClient.employee.hasSetPin().then(result => {
        if (!result.hasSetPin) {
          EventBus.$emit(eventNames.showFirstLoginWindow);
        } else if (!store.state.pinEntered) {
          EventBus.$emit(eventNames.showPinWindow);
        } else if (store.getters['Employees/redirectToUserEdit']) {
          EventBus.$emit(eventNames.showFirstLoginWindow);
        } else if (store.getters['Employees/redirectToGdpr']) {
          const redirectUrl = '/agreements/general';
          if (redirectUrl !== to.fullPath) {
            redirected = true;
            store.commit('showSnackbar', { text: i18n.t('snackbar_redirect.agreements'), color: 'green' });
            next(redirectUrl);
          }
        }
      });
    }
    if (!redirected) {
      next();
    }
  });
});

export default router;
