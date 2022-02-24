export default {
  namespaced: true,
  state: {
    startSelection: {},
    endSelection: {},
  },
  mutations: {
    setStart(state, startSelection) {
      state.startSelection = startSelection;
    },
    setEnd(state, endSelection) {
      state.endSelection = endSelection;
    },
  },
};
