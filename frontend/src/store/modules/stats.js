import moment from '@divante-adventure/work-moment';

export default {
  namespaced: true,
  state: {
    stats: [],
    tribes: [],
    minDate: {},
  },
  getters: {
    minDate: state => {
      if (typeof state.minDate.format === 'undefined') {
        return moment();
      }
      return state.minDate;
    },
    getTribes: state => {
      return state.tribes;
    },
    getStats: state => {
      return state.stats;
    },
    getTribeCameStats: (state, getters) => (date, tribeName) => {
      const elements = getters.getStats.filter(s => s.date.isSame(date, 'month') && s.name === tribeName);
      if (elements.length === 0) {
        return 0;
      }
      return elements[0].came;
    },
    getTribeLeftStats: (state, getters) => (date, tribeName) => {
      const elements = getters.getStats.filter(s => s.date.isSame(date, 'month') && s.name === tribeName);
      if (elements.length === 0) {
        return 0;
      }
      return elements[0].left;
    },
    getTribeCurrentStats: (state, getters) => (date, tribeName) => {
      let sum = 0;
      getters.getStats.forEach(s => {
        if ((s.date.isSameOrBefore(date, 'month')) && s.name === tribeName) {
          sum += s.worked;
        }
      });
      return sum;
    },
    getTribeStatistics: (state, getters) => date => {
      return getters.getTribes.map(t => {
        return {
          name: t.name,
          came: getters.getTribeCameStats(moment(date, 'YYYY-MM'), t.name),
          left: getters.getTribeLeftStats(moment(date, 'YYYY-MM'), t.name),
          current: getters.getTribeCurrentStats(moment(date, 'YYYY-MM'), t.name),
        };
      });
    },
  },
  mutations: {
    setStats(state, stats) {
      state.stats = stats.map(s => {
        const date = moment(s.date, 'YYYY-MM');
        const obj = {};
        if (date.isValid()) {
          obj.date = date;
        }
        obj.name = s.tribeName;
        obj.came = s.numberOfEnter;
        obj.left = s.numberOfLeave;
        obj.worked = s.numberOfWork;
        return obj;
      }).filter(Boolean);
    },
    setTribes(state, tribes) {
      state.tribes = tribes.map(t => {
        return {
          name: t.name,
          came: 0,
          left: 0,
          worked: 0,
        };
      });
   },
    setMinDate(state, firstHiredDate) {
      state.minDate = moment(firstHiredDate[0].hiredAt);
    },
  },
  actions: {
    async load(context) {
      try {
        const stats = await context.rootState.apiClient.statistics.get();
        const tribes = await context.rootState.apiClient.tribes.get();
        const firstHiredDate = await context.rootState.apiClient.employee.getFirstHiredDate();
        context.commit('setStats', stats);
        context.commit('setTribes', tribes);
        context.commit('setMinDate', firstHiredDate);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadRotationForWholeTime(context) {
      return context.rootState.apiClient.statistics.getRotation();
    },
    async loadRotationForYear(context, { year }) {
      return context.rootState.apiClient.statistics.getRotationForYear(year);
    },
    async loadRotationForMonth(context, { year, month }) {
      return context.rootState.apiClient.statistics.getRotationForMonth(year, month);
    },
    async loadActualStats({ rootState }) {
      return rootState.apiClient.statistics.getActualStatistics();
    },
  },
};
