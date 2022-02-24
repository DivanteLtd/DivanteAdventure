export default {
  namespaced: true,
  state: {
    agreements: [],
    agreementsToSign: [],
  },
  mutations: {
    setAgreements(state, agreements) {
      state.agreements = agreements;
    },
    setAgreementsToSign(state, agreements) {
      state.agreementsToSign = agreements;
    },
  },
  actions: {
    async loadHardwareAgreements(context) {
      try {
        const agreements = await context.rootState.apiClient.hardware.getEntries();
        context.commit('setAgreements', agreements);
        return agreements;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async generateHardwareAgreement(context, data) {
      try {
        return await context.rootState.apiClient.hardware.generateAgreement(data);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deleteEntry(context, id) {
      try {
        await context.rootState.apiClient.hardware.deleteEntry(id);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadHardwareForSigning(context) {
      try {
        const agreements = await context.rootState.apiClient.hardware.getAgreementsToSign();
        context.commit('setAgreementsToSign', agreements);
        return agreements;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async signAgreement(context, { id, password }) {
      try {
        await context.rootState.apiClient.hardware.signAgreement(id, password);
        return await context.dispatch('loadHardwareForSigning');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async downloadAgreement(context, { id, password, language }) {
      try {
        const url = `${window.ADVENTURE_BACKEND_URL}/download/hardware-agreement/${id}/${language}/${password}`;
        window.open(url, '_blank');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
