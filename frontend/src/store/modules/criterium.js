export default {
  namespaced: true,
  state: {
    criteriums: [],
  },
  mutations: {
    setCriteriums(state, criteriums) {
      state.criteriums = criteriums;
    },
  },
  actions: {
    async loadCriteriums(context) {
      try {
        const list = await context.rootState.apiClient.criteria.list();
        context.commit('setCriteriums', list);
        return list;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async addCriterium(context, data) {
      try {
        return await context.rootState.apiClient.criteria.create(data);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
