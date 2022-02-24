export default {
  namespaced: true,
  actions: {
    create(context) {
      context.rootState.apiClient.report.create('test');
    },
  },
};
