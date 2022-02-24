import Vue from 'vue';
import Vuex from 'vuex/dist/vuex';
import DataStorage from '@divante/data-storage';
import ApiClient from '../util/apiClient';
import { getSuggestedLanguage } from '../i18n/i18n';

import Authorization from './modules/authorization';
import Projects from './modules/projects';
import Employees from './modules/employees';
import FAQ from './modules/faq';
import Agreements from './modules/agreements';
import Hardware from './modules/hardware';
import Tribes from './modules/tribes';
import FreeDays from './modules/freeDays';
import Checklist from './modules/checklist';
import Requests from './modules/requests';
import Evidences from './modules/evidences';
import News from './modules/news';
import Positions from './modules/positions';
import Notifications from './modules/notifications';
import Criteria from './modules/criteria';
import Links from './modules/links';
import Hr from './modules/hr';
import Feedback from './modules/feedback';
import Stats from './modules/stats';
import Planner from './modules/planner/planner';
import Report from './modules/planner/report';
import PlannerStats from './modules/planner/plannerStats';
import Config from './modules/config';
import ContractsType from './modules/contractsType';

Vue.use(Vuex);
export default new Vuex.Store({
  modules: {
    Authorization,
    Projects,
    Employees,
    FAQ,
    Agreements,
    Tribes,
    FreeDays,
    Requests,
    Evidences,
    News,
    Positions,
    Notifications,
    Criteria,
    Links,
    Hr,
    Feedback,
    Planner,
    Stats,
    Report,
    Checklist,
    PlannerStats,
    Hardware,
    Config,
    ContractsType,
  },
  state: {
    /** @type {ApiClient|Object} */
    apiClient: {},
    searchData: [],
    loaded: false,
    pinEntered: false,
    appSnackbar: {
      show: false,
      text: '',
      color: '',
    },
  },
  getters: {
    sortedSearchData(state) {
      return state.searchData.sort((a, b) => {
        const aCategory = a.link.split('/')[2];
        const bCategory = b.link.split('/')[2];
        if (a.link.split('/').length === 3) {
          return 1;
        }
        const aLastName = aCategory === 'employees' ? a.displayLabel.split(' ')[1] : a.displayLabel;
        const bLastName = bCategory === 'employees' ? b.displayLabel.split(' ')[1] : b.displayLabel;
        if (aCategory > bCategory) {
          return 1;
        } else if (aCategory < bCategory) {
          return -1;
        } else {
          return aLastName.localeCompare(bLastName);
        }
      });
    },
  },
  mutations: {
    setConfig(state, token) {
      const language = getSuggestedLanguage();
      state.apiClient = new ApiClient({ token, language });
      state.apiClient.setLoaded();
    },
    setPinEntered(state) {
      state.pinEntered = true;
      const storage = new DataStorage();
      storage.setValue('pinSet', true);
    },
    setSearchData(state, data) {
      state.searchData = data;
    },
    markLoaded(state) {
      state.loaded = true;
    },
    showSnackbar(state, { text, color }) {
      state.appSnackbar.text = text;
      state.appSnackbar.color = color;
      state.appSnackbar.show = true;
    },
    hideSnackbar(state) {
      state.appSnackbar.show = false;
    },
  },
  actions: {
    async init(context) {
      const storage = new DataStorage();
      if (storage.getValue('pinSet', false) === true || storage.getValue('pinSet', false) === 'true') {
        context.commit('setPinEntered');
      }
      await context.dispatch('Authorization/readTokenFromStorage');
      await context.dispatch('loadConfig');
      if (context.state.pinEntered) {
        context.dispatch('loadGlobalSearch');
      }
    },
    async loadConfig(context) {
      context.commit('setConfig', context.state.Authorization.token);
    },
    async loadGlobalSearch(context) {
      const searchData = await context.state.apiClient.globalSearch.get();
      context.commit('setSearchData', searchData);
    },
    handleErrorResponse(context, error) {
      if (error === 'Invalid JWT Token') {
        context.commit('Authorization/logout');
        window.location.reload();
      }
    },
    showErrorSnackbar(context, text) {
      context.commit('showSnackbar', { text, color: 'error' });
    },
  },
});
