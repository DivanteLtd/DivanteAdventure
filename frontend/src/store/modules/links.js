export default {
  namespaced: true,
  state: {
    links: [],
  },
  mutations: {
    setLinks(state, links) {
      state.links = links;
    },
  },
  actions: {
    async loadLinks(context) {
      try {
        const links = await context.rootState.apiClient.links.getAll();
        context.commit('setLinks', links);
        return links;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async new(context, data) {
      try {
        await context.rootState.apiClient.links.add(data);
        return await context.dispatch('loadLinks');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async update(context, data) {
      try {
        await context.rootState.apiClient.links.update(data.id, data);
        return await context.dispatch('loadLinks');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async delete(context, id) {
      try {
        await context.rootState.apiClient.links.delete(id);
        return await context.dispatch('loadLinks');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
