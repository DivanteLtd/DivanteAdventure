import moment from '@divante-adventure/work-moment';

export default {
  namespaced: true,
  state: {
    companyStats: [],
    years: [],
    tribes: [],
    employees: [],
    year: new Date().getFullYear(),
    selectedTribes: [],
  },
  getters: {
    employees: state => state.employees,
    year: state => state.year,
    companyStats: state => state.companyStats,
    years: state => state.years,
    tribesByYear: state => year => state.tribes.filter(tribe => tribe.year <= year),
    selectedTribes: state => state.selectedTribes,
    prepareCompanyStatsForChart: state => {
      const rows = state.companyStats.map((year, nr) => {
        const monthName = moment().month(nr).format('MMMM');
        return [
          monthName,
          year.possibleSeconds / 3600,
          year.planedSeconds / 3600,
          year.billableSeconds / 3600,
          year.noBillableSeconds / 3600,
        ];
      });
      return rows;
    },
  },
  mutations: {
    setCompanyState(state, stats) {
      state.companyStats = stats;
    },
    setYears(state, years) {
      state.years = years;
    },
    setTribes(state, tribes) {
      state.tribes = tribes;
    },
    setEmployees(state, employees) {
      state.employees = employees;
    },
    setYear(state, year) {
      state.year = year;
    },
    setSelectedTribes(state, tribes) {
      state.selectedTribes = tribes;
    },
  },
  actions: {
    resetEmployeesStats(context) {
      context.commit('setEmployees', []);
    },
    async loadYears(context) {
      try {
        const years = await context.rootState.apiClient.statistics.getYears();
        return context.commit('setYears', years);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadStatsByYearAndTribes(context, obj) {
      try {
        const stats = await context.rootState.apiClient.statistics.getCompanyStatsByYearAndTribes(obj.year, obj.tribes);
        return context.commit('setCompanyState', stats);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadTribes(context) {
      try {
        const tribes = await context.rootState.apiClient.statistics.getTribes();
        context.commit('setSelectedTribes', tribes);
        return context.commit('setTribes', tribes);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadEmployeesByDateAndTribes(context, obj) {
      try {
        const employees = await context.rootState.apiClient.statistics.getEmployeesByDateAndTribes(
          obj.date, obj.tribes
        );
        return context.commit('setEmployees', employees);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
