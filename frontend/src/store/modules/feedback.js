export default {
  namespaced: true,
  state: {
    feedback: [],
    planned: [],
    myStructure: [],
    tribeStructure: [],
    feedbackProvided: [],
  },
  mutations: {
    setFeedback(state, feedback) {
      state.feedback = feedback;
    },
    setPlannedFeedbacks(state, feedbacks) {
      state.planned = feedbacks;
    },
    updateFeedback(state, feedback) {
      const feedbacks = [
        ...state.feedback.filter(val => val.id !== feedback.id),
        feedback,
      ];
      state.feedback = feedbacks.sort((a, b) => b.dateCreated - a.dateCreated);
    },
    deleteFeedback(state, id) {
      state.feedback = state.feedback.filter(val => val.id !== id);
      if (state.feedbackProvided.length) {
        state.feedbackProvided = state.feedbackProvided.filter(val => val.id !== id);
      }
    },
    getMyStructure(state, structure) {
      state.myStructure = structure.sort((a, b) => b.dateCreated - a.dateCreated);
    },
    getTribeStructure(state, structure) {
      state.tribeStructure = structure.sort((a, b) => b.dateCreated - a.dateCreated);
    },
    getFeedbackProvided(state, feedback) {
      state.feedbackProvided = feedback;
    },
  },
  actions: {
    async loadFeedback(context, id) {
      try {
        const feedback = await context.rootState.apiClient.feedback.getFeedback(id);
        context.commit('setFeedback', feedback);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadPlannedFeedbacks(context, id) {
      try {
        const feedbacks = await context.rootState.apiClient.feedback.getPlannedByEmployee(id);
        context.commit('setPlannedFeedbacks', feedbacks);
        return feedbacks;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async addFeedback(context, data) {
      try {
        await context.rootState.apiClient.feedback.addFeedback(data);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async updateFeedback(context, data) {
      try {
        await context.rootState.apiClient.feedback.updateFeedback(data.id, data);
        context.commit('updateFeedback', data);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deleteFeedback(context, id) {
      try {
        await context.rootState.apiClient.feedback.deleteFeedback(id);
        context.commit('deleteFeedback', id);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async getMyStructure(context, id) {
      try {
        const structure = await context.rootState.apiClient.feedback.getMyStructure(id);
        context.commit('getMyStructure', structure);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async getTribeStructure(context, id) {
      try {
        const structure = await context.rootState.apiClient.feedback.getTribeStructure(id);
        context.commit('getTribeStructure', structure);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async getFeedbackProvided(context, id) {
      try {
        const feedback = await context.rootState.apiClient.feedback.getFeedbackProvided(id);
        context.commit('getFeedbackProvided', feedback);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
