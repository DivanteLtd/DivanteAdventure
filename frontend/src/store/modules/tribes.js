export default {
  namespaced: true,
  state: {
    tribes: [],
  },
  getters: {
    filteredDepartments: state => state.tribes.filter(tribe => tribe.isVirtual && !tribe.visibility),
  },
  mutations: {
    setTribes(state, tribes) {
      state.tribes = tribes.map(tribe => {
        tribe.photoUrl = typeof(tribe.photoUrl) === 'undefined' ? '' : tribe.photoUrl;
        tribe.url = typeof(tribe.url) === 'undefined' ? '' : tribe.url;
        return tribe;
      });
    },
  },
  actions: {
    async loadTribes(context) {
      try {
        const tribes = await context.rootState.apiClient.tribes.get();
        context.commit('setTribes', tribes);
        return tribes;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async new(context, data) {
      try {
        await context.rootState.apiClient.tribes.new(data);
        return await context.dispatch('loadTribes');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async update(context, data) {
      try {
        await context.rootState.apiClient.tribes.update(data.id, data);
        return await context.dispatch('loadTribes');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async delete(context, id) {
      try {
        await context.rootState.apiClient.tribes.delete(id);
        return await context.dispatch('loadTribes');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async disconnectFromSlack(context, id) {
      return context.rootState.apiClient.tribes.disconnectFromSlack(id);
    },
  },
};
