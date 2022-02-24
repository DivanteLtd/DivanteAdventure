import uploader from '../../util/uploader';

export default {
  namespaced: true,
  state: {
    agreements: [],
    GDPRAcceptationList: [],
    marketingAcceptationList: [],
    iSOAcceptationList: [],
    marketingConsents: [],
    attachments: [],
    payload: {},
  },
  mutations: {
    setAgreements(state, agreements) {
      state.agreements = agreements.sort((a, b) => a.priority - b.priority);
    },
    setGDPRAcceptationList(state, GDPRAcceptationList) {
      state.GDPRAcceptationList = GDPRAcceptationList;
    },
    setMarketingAcceptationList(state, marketingAcceptationList) {
      state.marketingAcceptationList = marketingAcceptationList;
    },
    setISOAcceptationList(state, iSOAcceptationList) {
      state.iSOAcceptationList = iSOAcceptationList;
    },
    setMarketingConsents(state, agreements) {
      state.marketingConsents = agreements.sort((a, b) => a.priority - b.priority);
    },
    setAgreementAttachments(state, attachments) {
      state.attachments = attachments;
    },
  },
  actions: {
    async loadAgreements(context) {
      try {
        const userId = context.rootState.Authorization.payload.employeeId;
        const agreements = await context.rootState.apiClient.agreements.getById(userId);
        context.commit('setAgreements', agreements);
        return agreements;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadMarketingConsents(context) {
      try {
        const userId = context.rootState.Authorization.payload.employeeId;
        const agreements = await context.rootState.apiClient.agreements.getMarketingById(userId);
        context.commit('setMarketingConsents', agreements);
        return agreements;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadAgreementAttachments(context) {
      try {
        const attachments = await context.rootState.apiClient.agreements.get();
        context.commit('setAgreementAttachments', attachments);
        return attachments;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deleteAttachment(context, id) {
      try {
        await context.rootState.apiClient.agreements.deleteAttachment(id);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deleteAgreement(context, id) {
      try {
        await context.rootState.apiClient.agreements.deleteAgreement(id);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async newEmployeeAgreements(context, data) {
      try {
        const userId = context.rootState.Authorization.payload.employeeId;
        return await context.rootState.apiClient.agreements.update(userId, data);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadGDPRAcceptationList(context) {
      try {
        const list = await context.rootState.apiClient.agreements.getGDPRAcceptationList();
        context.commit('setGDPRAcceptationList', list);
        return list;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadMarketingAcceptationList(context) {
      try {
        const list = await context.rootState.apiClient.agreements.getMarketingAcceptationList();
        context.commit('setMarketingAcceptationList', list);
        return list;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadISOAcceptationList(context) {
      try {
        const list = await context.rootState.apiClient.agreements.getISOAcceptationList();
        context.commit('setISOAcceptationList', list);
        return list;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async addAttachment(context, message) {
      try {
        const { token } = context.rootState.Authorization;
        const url = `${window.ADVENTURE_BACKEND_URL}/api/attachment/upload`;
        const promises = message.map(attachment => uploader(attachment, url, token));
        const result = await Promise.all(promises);
        context.dispatch('loadAgreementAttachments');
        return result;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async addAgreement(context, data) {
      try {
        await context.rootState.apiClient.agreements.newAgreement(data);
        await context.dispatch('loadAgreements');
        await context.dispatch('loadMarketingConsents');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async editAgreement(context, data) {
      try {
        await context.rootState.apiClient.agreements.editAgreement(data.id, data);
        await context.dispatch('loadAgreements');
        await context.dispatch('loadMarketingConsents');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async downloadAttachment(context, attachmentId) {
      try {
        const { token } = await context.rootState.apiClient.agreements.createDownloadToken(attachmentId);
        const url = `${window.ADVENTURE_BACKEND_URL}/download/agreementattachment?token=${token}`;
        window.location.replace(url);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
