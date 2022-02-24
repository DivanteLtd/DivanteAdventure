import moment from '@divante-adventure/work-moment';
import { getSuggestedLanguage } from '../../../i18n/i18n';
import TimeMode, { getTimeModeByValue } from '../../../util/planner/TimeMode';
import DataStorage from '@divante/data-storage';

export default {
  namespaced: true,
  state: {
    timeMode: TimeMode.week,
    currentDate: moment().valueOf(),
    freeDays: [],
    isoFreeDays: [],
  },
  getters: {
    currentDateLabel: state => {
      const date = moment(state.currentDate).locale(getSuggestedLanguage());
      return state.timeMode.displayControl.createDateLabel(date);
    },
    headersByView: state => {
      const currentDate = moment(state.currentDate);
      return state.timeMode.displayControl.createHeaders(currentDate, state.isoFreeDays);
    },
  },
  mutations: {
    setTimeMode(state, mode) {
      state.timeMode = getTimeModeByValue(mode);
      new DataStorage().setValue('timeMode', mode);
    },
    addToTime(state) {
      const currentDate = moment(state.currentDate);
      currentDate.add(state.timeMode.displayControl.getTimeShift());
      state.currentDate = currentDate.valueOf();
    },
    subtractFromTime(state) {
      const currentDate = moment(state.currentDate);
      currentDate.subtract(state.timeMode.displayControl.getTimeShift());
      state.currentDate = currentDate.valueOf();
    },
    setFreeDays(state, freeDays) {
      state.freeDays = freeDays;
      state.isoFreeDays = freeDays.map(day => moment.unix(day).isoDate());
    },
  },
  actions: {
    loadFreeDays(context, borders = undefined) {
      if (borders === undefined) {
        const currentDate = context.state.currentDate;
        const timeMode = context.state.timeMode;
        borders = timeMode.displayControl.getBorders(moment(currentDate));
      }
      const beginning = borders.beginning.unix();
      const ending = borders.ending.unix();
      context.commit('Planner/requestLoadingBar', {}, { root: true });
      context.rootState.apiClient.freeDays.getByStartAndEndTimestamp(beginning, ending).then(response => {
        context.commit('setFreeDays', response);
      }).finally(() => {
        context.commit('Planner/releaseLoadingBar', {}, { root: true });
      });
    },
  },
};
