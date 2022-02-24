import DataStorage from '@divante/data-storage';
import jwt from 'jsonwebtoken';
import moment from '@divante-adventure/work-moment';
import { BOTTOM_ROLE, appRoles } from '../../util/roles';

export default {
  namespaced: true,
  state: {
    isLoggedIn: false,
    token: '',
    payload: {},
    refreshToken: '',
    isTokenLoaded: false,
  },
  getters: {
    isLoggedIn: state => state.isLoggedIn,
    isAuthorized: state => requestedRole => {
      const rolesFromPayload = state.payload.roles || [BOTTOM_ROLE];
      let userRoles = [];
      if (Array.isArray(rolesFromPayload)) {
        userRoles = rolesFromPayload;
      } else if (typeof rolesFromPayload === 'object') {
        for (const key in rolesFromPayload) {
          if (rolesFromPayload.hasOwnProperty(key)) {
            userRoles.push(rolesFromPayload[key]);
          }
        }
      }
      for (const userRole of userRoles) {
        if (!appRoles.hasOwnProperty(userRole)) {
          continue;
        }
        if (appRoles[userRole].includes(requestedRole)) {
          return true;
        }
      }
      return false;
    },
    isUser: (state, getters) => getters.isAuthorized('ROLE_USER'),
    isManager: (state, getters) => getters.isAuthorized('ROLE_MANAGER'),
    isTribeMaster: (state, getters) => getters.isAuthorized('ROLE_TRIBE_MASTER'),
    isSuperAdmin: (state, getters) => getters.isAuthorized('ROLE_SUPER_ADMIN'),
    isHr: (state, getters) => getters.isAuthorized('ROLE_HR'),
    isHelpdesk: (state, getters) => getters.isAuthorized('ROLE_HELPDESK'),
    getUserId: state => state.payload.employeeId,
  },
  mutations: {
    logout(state) {
      const storage = new DataStorage();
      storage.setObjectValue('frontendAuth', { isLoggedIn: false });
      storage.setValue('pinSet', false);
      state.isLoggedIn = false;
      state.isTokenLoaded = true;
    },
    login(state, data) {
      state.payload = data.payload;
      state.isLoggedIn = true;
      state.token = data.token;
      state.refreshToken = data.refreshToken;
      const storage = new DataStorage();
      const authData = {
        isLoggedIn: true,
        token: data.token,
        refreshToken: data.refreshToken,
        exp: moment().add(6, 'hours').unix(),
      };
      storage.setObjectValue('frontendAuth', authData);
    },
  },
  actions: {
    async setToken(context, token) {
      try {
        const payload = jwt.decode(token.token);
        context.commit('login', {
          isLoggedIn: true,
          token: token.token,
          refreshToken: token.refresh_token,
          payload,
        });
      } catch (e) {
        context.commit('logout');
        throw e;
      }
      await context.dispatch('loadConfig', {}, { root: true });
      await context.dispatch('Employees/loadLoggedEmployee', {}, { root: true });
      context.commit('markLoaded', {}, { root: true });
    },
    async readTokenFromStorage(context) {
      const storage = new DataStorage();
      const auth = storage.getObjectValue('frontendAuth', { isLoggedIn: false });
      const timestamp = moment().unix();
      if (auth.isLoggedIn && auth.exp > timestamp) {
        const payload = jwt.decode(auth.token);
        context.commit('login', {
          isLoggedIn: true,
          token: auth.token,
          refreshToken: auth.refreshToken,
          payload,
        });
        await context.dispatch('loadConfig', {}, { root: true });
        await context.dispatch('Employees/loadLoggedEmployee', {}, { root: true });
        context.commit('markLoaded', {}, { root: true });
      } else {
        context.commit('logout');
        context.commit('markLoaded', {}, { root: true });
      }
    },
    awaitFinishedLoading(context, callback = () => {}) {
      return new Promise(resolve => {
        const tester = () => {
          const apiLoaded = context.rootState.loaded;
          if (apiLoaded === null || !apiLoaded) {
            setTimeout(tester, 100);
          } else {
            resolve();
          }
        };
        tester();
      }).then(() => callback());
    },
  },
};
