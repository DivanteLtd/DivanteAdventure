import moment from '@divante-adventure/work-moment';
import Vue from 'vue';
import { leaveRequestsStatuses as reqStatus } from '../../util/freeDays';
import uploader from '../../util/uploader';

export default {
  namespaced: true,
  state: {
    myPeriods: [],
    freeDays: [],
    freeDaysMonth: [],
    dashboardDate: moment(),
    dashboardDays: [],
    statutoryFreeDays: [],
    freeDaysReport: [],
    confirmedLeaveDays: [],
  },
  mutations: {
    setMyPeriods(state, periods) {
      state.myPeriods = periods
          .map(period => {
            period.dateFromMoment = moment(period.dateFrom, 'YYYY-MM-DD');
            period.dateToMoment = moment(period.dateTo, 'YYYY-MM-DD');
            return period;
          })
          .sort((a, b) => b.dateFromMoment.unix() - a.dateFromMoment.unix());
    },
    addFreeDays(state, freeDays) {
      const newFreeDays = state.freeDays.concat(freeDays);
      Vue.set(state, 'freeDays', [...newFreeDays]);
    },
    addFreeDaysMonth(state, month) {
      state.freeDaysMonth.push(month);
    },
    setDashboardMonth(state, newMonth) {
      state.dashboardDate = newMonth;
    },
    setDashboardEntries(state, entries) {
      state.dashboardDays = entries.sort((a, b) => {
        const employeeA = `${a.employee.lastName} ${a.employee.name}`;
        const employeeB = `${b.employee.lastName} ${b.employee.name}`;
        return employeeA.localeCompare(employeeB);
      });
    },
    setStatutoryFreeDays(state, freeDays) {
      state.statutoryFreeDays = freeDays;
    },
    setFreeDaysReport(state, report) {
      state.freeDaysReport = report;
    },
    setMyConfirmedLeaveDays(state, confirmedLeaveDays) {
      state.confirmedLeaveDays = confirmedLeaveDays;
    },
  },
  actions: {
    async loadStatutoryFreeDays(context, { start, end }) {
      try {
        const startBordered = start.subtract({ day: 1 }).unix();
        const endBordered = end.add({ day: 1 }).unix();
        const { apiClient } = context.rootState;
        const freeDays = await apiClient.freeDays.getByStartAndEndTimestamp(startBordered, endBordered);
        context.commit('setStatutoryFreeDays', freeDays);
        return freeDays;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadMyPeriods(context) {
      try {
        const periods = await context.rootState.apiClient.leavePeriods.getMyPeriods();
        context.commit('setMyPeriods', periods);
        return periods;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadPeriodsByEmployee(context, employeeId) {
      try {
        const periods = await context.rootState.apiClient.leavePeriods.getEmployeePeriods(employeeId);
        context.commit('setMyPeriods', periods);
        return periods;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadConfirmedLeaveDaysByEmployee(context) {
      try {
        while (typeof context.rootState.apiClient.leave === 'undefined') {
          setTimeout(() => {}, 100);
        }
        const confirmedLeaveDays = await context.rootState.apiClient.leave.getConfirmedLeaveDays();
        context.commit('setMyConfirmedLeaveDays', confirmedLeaveDays);
        return confirmedLeaveDays;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async createNewPeriod(context, periodData) {
      try {
        return await context.rootState.apiClient.leavePeriods.createNewPeriod(periodData);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async editPeriod(context, periodData) {
      try {
        return await context.rootState.apiClient.leavePeriods.updatePeriod(periodData.periodId, periodData);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deletePeriod(context, periodId) {
      try {
        return await context.rootState.apiClient.leavePeriods.deletePeriod(periodId);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadFreeDaysForMonth(context, month) {
      try {
        const timeBorder = {
          start: moment(month, 'YYYY-MM').startOf('month'),
          end: moment(month, 'YYYY-MM').endOf('month'),
        };
        const freeDays = await context.dispatch('loadFreeDays', timeBorder);
        context.commit('addFreeDaysMonth', month);
        return freeDays;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadFreeDays(context, { start, end }) {
      try {
        if (typeof(context.rootState.apiClient.freeDays) === 'undefined') {
          return [];
        }
        const startBordered = start.subtract({ day: 1 }).unix();
        const endBordered = end.add({ day: 1 }).unix();
        const { apiClient } = context.rootState;
        const freeDays = await apiClient.freeDays.getByStartAndEndTimestamp(startBordered, endBordered);
        context.commit('addFreeDays', freeDays);
        return freeDays;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadDashboardDays(context) {
      try {
        if (typeof(context.rootState.apiClient.leave) === 'undefined') {
          return [];
        }
        let start = moment(context.state.dashboardDate.startOf('month')).format('YYYY-MM-DD');
        let end = moment(context.state.dashboardDate.endOf('month')).format('YYYY-MM-DD');
        const dashboardDays = await context.rootState.apiClient.leave.getDashboardByStartAndEnd(start, end);
        context.commit('setDashboardEntries', dashboardDays);
        start = moment(context.state.dashboardDate.startOf('month')).subtract({ day: 1 }).unix();
        end = moment(context.state.dashboardDate.endOf('month')).add({ day: 1 }).unix();
        await context.dispatch('loadStatutoryFreeDays', { start: moment.unix(start), end: moment.unix(end) });
        return dashboardDays;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async createNewRequest(context, message) {
      try {
        const { token } = context.rootState.Authorization;
        const uploadUrl = `${window.ADVENTURE_BACKEND_URL}/api/sickleaveattachment/upload`;
        const promises = message.attachments.map(attachment => uploader(attachment, uploadUrl, token));
        const responses = await Promise.all(promises);
        message.attachments = responses.map(response => JSON.parse(response).id);
        return await context.rootState.apiClient.leaveRequests.createNewRequest(message.periodId, message);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async resignFromRequest(context, request) {
      try {
        const oldStatus = request.status;
        const data = {};
        if (oldStatus === reqStatus.pending) {
          data.status = reqStatus.resigned;
        } else if (oldStatus === reqStatus.planned) {
          data.status = reqStatus.resigned;
        } else if (oldStatus === reqStatus.accepted) {
          data.status = reqStatus.pendingResignation;
        } else if (oldStatus === reqStatus.pendingResignation) {
          data.status = reqStatus.accepted;
        }
        return await context.rootState.apiClient.leaveRequests.updateRequest(request.id, data);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async acceptRequest(context, request) {
      try {
        const oldStatus = request.status;
        const data = {};
        if (oldStatus === reqStatus.pending || oldStatus === reqStatus.planned) {
          data.status = reqStatus.accepted;
        } else if (oldStatus === reqStatus.pendingResignation) {
          data.days = request.days;
          data.status = reqStatus.resigned;
        }
        data.comment = request.comment;
        return await context.rootState.apiClient.leaveRequests.updateRequest(request.id, data);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async updateRequest(context, request) {
      try {
        if (request.status === reqStatus.accepted) {
          request.status = reqStatus.pendingResignation;
        }
        return await context.rootState.apiClient.leaveRequests.updateRequest(request.id, request);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async rejectRequest(context, request) {
      try {
        const oldStatus = request.status;
        const data = {};
        if (oldStatus === reqStatus.pending || oldStatus === reqStatus.planned) {
          data.status = reqStatus.rejected;
        } else if (oldStatus === reqStatus.pendingResignation) {
          data.days = request.days;
          data.status = reqStatus.accepted;
        }
        return await context.rootState.apiClient.leaveRequests.updateRequest(request.id, data);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async downloadAttachment(context, attachmentId) {
      try {
        const { token } = await context.rootState.apiClient.leaveRequests.createDownloadToken(attachmentId);
        const redirect = `${window.ADVENTURE_BACKEND_URL}/download/sickleaveattachment?token=${token}`;
        window.location.replace(redirect);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async createFreeDaysReport(context) {
      try {
        const report = await context.rootState.apiClient.leavePeriods.getFreeDaysReport();
        context.commit('setFreeDaysReport', report);
        return report;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deleteRequest(context, requestId) {
      try {
        return await context.rootState.apiClient.leaveRequests.deleteRequest(requestId);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
