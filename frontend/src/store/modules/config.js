export default {
  namespaced: true,
  state: {
    config: [],
    contentConfig: [],
    freeDays: [],
  },
  mutations: {
    setConfig(state, config) {
      state.config = config;
    },
    setContentConfig(state, config) {
      state.contentConfig = config;
    },
    setFreeDays(state, freeDays) {
      state.freeDays = freeDays;
    },
  },
  actions: {
    async loadConfig(context) {
      try {
        const config = await context.rootState.apiClient.config.getCurrentConfiguration();
        context.commit('setConfig', config);
        return config;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadContentConfig(context) {
      try {
        const config = await context.rootState.apiClient.config.getContentConfiguration();
        context.commit('setContentConfig', config);
        return config;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async updateEntry(context, { key, value }) {
      try {
        await context.rootState.apiClient.config.updateConfig(key, value);
        return key.split('.')[0] === 'content' ? context.dispatch('loadContentConfig') : context.dispatch('loadConfig');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadFreeDays(context) {
      try {
        const freeDays = await context.rootState.apiClient.freeDays.getEntries();
        context.commit('setFreeDays', freeDays);
        return freeDays;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async switchFreeDay(context, id) {
      try {
        await context.rootState.apiClient.freeDays.switchEntry(id);
        return await context.dispatch('loadFreeDays');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deleteFreeDay(context, id) {
      try {
        await context.rootState.apiClient.freeDays.deleteEntry(id);
        return await context.dispatch('loadFreeDays');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async createFreeDay(context, { name, repeating, date }) {
      try {
        await context.rootState.apiClient.freeDays.createEntry({ date, name, repeating });
        return await context.dispatch('loadFreeDays');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
