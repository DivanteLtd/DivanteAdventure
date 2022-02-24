export default {
  namespaced: true,
  state: {
    fAQCategories: [],
  },
  mutations: {
    setFAQCategories(state, fAQCategories) {
      state.fAQCategories = fAQCategories;
    },
    updateFAQCategories(state, fAQCategories) {
      state.fAQCategories = [
        ...state.fAQCategories.filter(element => element.id !== fAQCategories.id),
        fAQCategories,
      ];
    },
  },
  actions: {
    async newFAQCategory(context, data) {
      try {
        await context.rootState.apiClient.faq.createCategory(data);
        return await context.dispatch('loadFAQCategories');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadFAQCategories(context) {
      try {
        const fAQCategories = await context.rootState.apiClient.faq.getCategories();
        context.commit('setFAQCategories', fAQCategories);
        return fAQCategories;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async updateFAQCategory(context, data) {
      try {
        await context.rootState.apiClient.faq.updateCategory(data.id, data);
        return await context.dispatch('loadFAQCategories');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deleteFAQCategory(context, id) {
      try {
        return context.rootState.apiClient.faq.deleteCategory(id);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
