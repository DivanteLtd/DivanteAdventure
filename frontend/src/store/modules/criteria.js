export default {
  namespaced: true,
  state: {
    criteria: [],
  },
  mutations: {
    setCriteria(state, criteria) {
      state.criteria = criteria;
    },
  },
  actions: {
    async loadCriteria(context) {
      try {
        const list = await context.rootState.apiClient.criteria.list();
        context.commit('setCriteria', list);
        return list;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async addCriteria(context, data) {
      try {
        return await context.rootState.apiClient.criteria.create(data);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async editCriteria(context, data) {
      try {
        return await context.rootState.apiClient.criteria.update(data);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deleteCriteria(context, id) {
      try {
        return await context.rootState.apiClient.criteria.delete(id);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
