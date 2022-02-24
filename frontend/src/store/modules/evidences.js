import moment from '@divante-adventure/work-moment';
import uploader from '../../util/uploader';

export default {
  namespaced: true,
  state: {
    evidences: [],
  },
  getters: {
    canUseEvidences: (state, getters, rootState) => ['CLC LUMP SUM', 'CLC HOURLY', 'B2B LUMP SUM', 'B2B HOURLY']
        .includes(rootState.Employees.loggedEmployee.contract.name),
    canUseOvertime: (state, getters, rootState) => ['CoE'].includes(rootState.Employees.loggedEmployee.contract.name),
    hoursPerMonth: (state, getters, rootState) => month => {
      if (!month) {
        month = getters.bestDefaultMonth;
      }
      const freeDaysIso = rootState.FreeDays.freeDays
          .map(timestamp => moment.unix(timestamp).isoDate());
      const monthMoment = moment(`${month}-15`, 'YYYY-MM-DD');
      const daysInMonth = Array.from(monthMoment.rangeOf('month').by('day'));
      let workingDays = 0;
      daysInMonth.forEach(day => {
            const isWeekend = [6, 7].indexOf(day.isoWeekday()) !== -1;
            const isIsoFreeDay = freeDaysIso.indexOf(moment(day).isoDate()) !== -1;
            if (!isWeekend && !isIsoFreeDay) {
              workingDays++;
            }
          });
      return workingDays * 8;
    },
    bestDefaultMonth: state => {
      const timestamps = state.evidences
          .map(evidence => moment().year(evidence.year).month(evidence.month).unix())
          .sort((a, b) => b - a);
      let timestamp = -1;
      if (timestamps.length > 0) {
        [timestamp] = timestamps;
      } else {
        let currentDay = moment();
        if (currentDay.date() < 15) {
          currentDay = currentDay.subtract({ month: 1 });
        }
        timestamp = currentDay.unix();
      }

      return moment.unix(timestamp).format('YYYY-MM');
    },
  },
  mutations: {
    setEvidences(state, evidences) {
      state.evidences = evidences;
    },
  },
  actions: {
    async loadMyEvidences(context) {
      try {
        const evidences = await context.rootState.apiClient.evidences.getMyEvidences();
        context.commit('setEvidences', evidences);
        return evidences;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async sendEvidence(context, data) {
      try {
        if (data.invoices !== undefined) {
          const { token } = context.rootState.Authorization;
          const url = `${window.ADVENTURE_BACKEND_URL}/api/evidence/invoice`;
          const promises = data.invoices.map(invoice => uploader(invoice, url, token));
          const responses = await Promise.all(promises);
          data.invoices = responses.map(string => JSON.parse(string).id);
        }
        return context.rootState.apiClient.evidences.generate(data);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async changeOvertimeStatus(context, { evidenceId, status }) {
      try {
        return await context.rootState.apiClient.evidences.update(evidenceId, { status });
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadEmployeeEvidence(context, employeeId) {
      try {
        return await context.rootState.apiClient.evidences.getUserEvidences(employeeId);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
