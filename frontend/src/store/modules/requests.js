import moment from '@divante-adventure/work-moment';
import { leaveRequestsStatuses } from '../../util/freeDays';
import Vue from 'vue';

export default {
  namespaced: true,
  state: {
    leaveRequests: [],
    overtimeRequests: [],
  },
  getters: {
    getAllRequestsWithTypes: state => {
      return [...state.leaveRequests, ...state.overtimeRequests].sort((a, b) => b._orderTimestamp - a._orderTimestamp);
    },
    getRequestsWithTypes: (state, getters) => {
      return getters.getAllRequestsWithTypes.filter(request => request._requiringAction);
    },
    getRequestsForDashboard: (state, getters) => {
      return getters.getAllRequestsWithTypes.filter(request => request._requiringAction || request._planned);
    },
    getArchivalRequestsWithTypes: (state, getters) => {
      return getters.getAllRequestsWithTypes.filter(request => !request._requiringAction && !request._planned);
    },
    getPlannedRequests: (state, getters) => {
      return getters.getAllRequestsWithTypes.filter(request => request._planned);
    },
  },
  mutations: {
    updateRequestStatus(state, { id, type }) {
      let baseArray = [];
      if (type === 'overtime') {
        baseArray = state.overtimeRequests;
      } else if (type === 'leave') {
        baseArray = state.leaveRequests;
      }
      const editedIndex = baseArray.findIndex(request => request.id === id);
      const editedElement = baseArray[editedIndex];
      Vue.set(editedElement, '_inProgress', true);
      Vue.set(baseArray, editedIndex, editedElement);
    },
    removeRequest(state, { id, type }) {
      let baseArray = [];
      if (type === 'overtime') {
        baseArray = state.overtimeRequests;
      } else if (type === 'leave') {
        baseArray = state.leaveRequests;
      }
      const editedIndex = baseArray.findIndex(request => request.id === id);
      if (editedIndex !== -1) {
        baseArray.splice(editedIndex, 1);
      }
    },
    setLeaveRequests(state, requests) {
      const statusRequiringAction = [
          leaveRequestsStatuses.pendingResignation,
          leaveRequestsStatuses.pending,
      ];
      const statusPlanned = [ leaveRequestsStatuses.planned ];
      state.leaveRequests = requests.map(request => {
        request._reqType = 'leave';
        request._orderTimestamp = moment(request.createdAt).unix();
        request._requiringAction = statusRequiringAction.includes(request.status);
        request._planned = statusPlanned.includes(request.status);
        return request;
      });
    },
    setOvertimeRequests(state, requests) {
      state.overtimeRequests = requests.map(request => {
        request._reqType = 'overtime';
        request._orderTimestamp = request.createdAt;
        request._requiringAction = request.status === 1;
        return request;
      });
    },
  },
  actions: {
    async loadRequests(context) {
      try {
        const api = context.rootState.apiClient;
        if (typeof(api) === 'undefined' || typeof(api.leaveRequests) === 'undefined') {
          return [];
        }
        const promises = [
          context.rootState.apiClient.leaveRequests.getRequestsToMe(),
          context.rootState.apiClient.evidences.getMyRequests(),
        ];
        const [ leaveRequests, overtimeRequests ] = await Promise.all(promises);
        context.commit('setLeaveRequests', leaveRequests);
        context.commit('setOvertimeRequests', overtimeRequests);
        return [ ...leaveRequests, ...overtimeRequests ];
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
