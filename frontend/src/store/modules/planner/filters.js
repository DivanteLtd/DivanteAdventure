import DataMode from '../../../util/planner/DataMode';
import ViewMode from '../../../util/planner/ViewMode';
import DataStorage from '@divante/data-storage';

/**
 * @typedef {Object} BackendFilter
 * @property {string} id
 * @property {function} apply
 * @property {function} canBeUsed
 * @property {string|i18nObject|function():(string|i18nObject)} label
 */
/**
 * @typedef {Object} SelectedFilter
 * @property {BackendFilter} filter
 * @property {string} enteredText
 */
export default {
  namespaced: true,
  state: {
    query: null,
    /** @type {BackendFilter[]} */
    filters: [],
    /** @type {SelectedFilter[]} */
    selectedFilters: [],
    /** @type {BackendFilter[]} */
    plannerFilters: [{
      id: 'planner/nameFilter',
      label: () => enteredText => ({ i18n: 'filter_by_name', params: { name: enteredText } }),
      apply: modes => (element, enteredText) => modes.view
          .createElementLabel(element).toLowerCase().includes(enteredText.toLowerCase()),
      canBeUsed: () => enteredText => enteredText.length > 0,
    }, {
      id: 'planner/summaryLesserThan',
      label: () => enteredText => ({ i18n: 'filter_by_summaryLesserThan', params: { value: enteredText } }),
      apply: modes => (element, enteredText) => {
        const entries = modes.rootState.Planner.entries;
        const leaveDays = modes.rootState.Planner.leaveDays;
        const isoFreeDays = modes.rootState.Planner.Time.isoFreeDays;
        const currentDate = modes.rootState.Planner.Time.currentDate;
        const hours = modes.time.getWorkingHours(currentDate, element, modes.view, entries, leaveDays, isoFreeDays);
        return enteredText.length > 0 && !isNaN(enteredText) && enteredText > 0 && hours < enteredText;
      },
      canBeUsed: modes => enteredText => {
        return modes.view === ViewMode.employee && enteredText.length > 0 && !isNaN(enteredText) && enteredText > 0;
      },
    }, {
      id: 'planner/summaryGreaterThan',
      label: () => enteredText => ({ i18n: 'filter_by_summaryGreaterThan', params: { value: enteredText } }),
      apply: modes => (element, enteredText) => {
        const entries = modes.rootState.Planner.entries;
        const leaveDays = modes.rootState.Planner.leaveDays;
        const isoFreeDays = modes.rootState.Planner.Time.isoFreeDays;
        const currentDate = modes.rootState.Planner.Time.currentDate;
        const hours = modes.time.getWorkingHours(currentDate, element, modes.view, entries, leaveDays, isoFreeDays);
        return enteredText.length > 0 && !isNaN(enteredText) && enteredText >= 0 && hours > enteredText;
      },
      canBeUsed: modes => enteredText => {
        return modes.view === ViewMode.employee && enteredText.length > 0 && !isNaN(enteredText) && enteredText >= 0;
      },
    }, {
      id: 'planner/assignedWorktimeLesserThan',
      label: () => enteredText => ({ i18n: 'filter_by_assignedWorktimeLesserThan', params: { value: enteredText } }),
      apply: modes => (element, enteredText) => {
        const entries = modes.rootState.Planner.entries;
        const leaveDays = modes.rootState.Planner.leaveDays;
        const isoFreeDays = modes.rootState.Planner.Time.isoFreeDays;
        const currentDate = modes.rootState.Planner.Time.currentDate;
        const workingSeconds = modes.time.entryCalculator
            .getWorkingSeconds(currentDate, element, modes.view, entries, leaveDays, isoFreeDays);
        const allSeconds = modes.time.getSecondsInView(currentDate, isoFreeDays);
        const worktime = DataMode.worktime.formatSeconds(workingSeconds, allSeconds);
        return enteredText.length > 0 && !isNaN(enteredText) && enteredText > 0 && worktime < enteredText;
      },
      canBeUsed: modes => enteredText => {
        return modes.view === ViewMode.employee && enteredText.length > 0 && !isNaN(enteredText) && enteredText > 0;
      },
    }, {
      id: 'planner/assignedWorktimeGreaterThan',
      label: () => enteredText => ({ i18n: 'filter_by_assignedWorktimeGreaterThan', params: { value: enteredText } }),
      apply: modes => (element, enteredText) => {
        const entries = modes.rootState.Planner.entries;
        const leaveDays = modes.rootState.Planner.leaveDays;
        const isoFreeDays = modes.rootState.Planner.Time.isoFreeDays;
        const currentDate = modes.rootState.Planner.Time.currentDate;
        const workingSeconds = modes.time.entryCalculator
            .getWorkingSeconds(currentDate, element, modes.view, entries, leaveDays, isoFreeDays);
        const allSeconds = modes.time.getSecondsInView(currentDate, isoFreeDays);
        const worktime = DataMode.worktime.formatSeconds(workingSeconds, allSeconds);
        return enteredText.length > 0 && !isNaN(enteredText) && enteredText > 0 && worktime > enteredText;
      },
      canBeUsed: modes => enteredText => {
        return modes.view === ViewMode.employee && enteredText.length > 0 && !isNaN(enteredText) && enteredText > 0;
      },
    }, {
      id: 'planner/worktimeLesserThan',
      label: () => enteredText => ({ i18n: 'filter_by_worktimeLesserThan', params: { value: enteredText } }),
      apply: () => (element, enteredText) => {
        const worktime = DataMode.worktime.formatSeconds(element.worktime);
        return enteredText.length > 0 && !isNaN(enteredText) && enteredText > 0 && worktime < enteredText;
      },
      canBeUsed: modes => enteredText => {
        return modes.view === ViewMode.employee && enteredText.length > 0 && !isNaN(enteredText) && enteredText > 0;
      },
    }, {
      id: 'planner/worktimeGreaterThan',
      label: () => enteredText => ({ i18n: 'filter_by_worktimeGreaterThan', params: { value: enteredText } }),
      apply: () => (element, enteredText) => {
        const worktime = DataMode.worktime.formatSeconds(element.worktime);
        return enteredText.length > 0 && !isNaN(enteredText) && enteredText > 0 && worktime > enteredText;
      },
      canBeUsed: modes => enteredText => {
        return modes.view === ViewMode.employee && enteredText.length > 0 && !isNaN(enteredText) && enteredText > 0;
      },
    }, {
      id: 'planner/assignmentPercentageGreaterThan',
      label: () => enteredText => ({ i18n: 'filter_by_assignmentPercentageGreaterThan', params: { value: enteredText } }),
      apply: modes => (element, enteredText) => {
        const entries = modes.rootState.Planner.entries;
        const leaveDays = modes.rootState.Planner.leaveDays;
        const isoFreeDays = modes.rootState.Planner.Time.isoFreeDays;
        const currentDate = modes.rootState.Planner.Time.currentDate;
        const workingSeconds = modes.time.entryCalculator
            .getWorkingSeconds(currentDate, element, modes.view, entries, leaveDays, isoFreeDays);
        const allSeconds = modes.time.getSecondsInView(currentDate, isoFreeDays);
        const assignedWorktime = DataMode.worktime.formatSeconds(workingSeconds, allSeconds);
        const wholeWorktime = DataMode.worktime.formatSeconds(element.worktime);
        const percentage = (assignedWorktime * 100) / wholeWorktime;
        return enteredText.length > 0 && !isNaN(enteredText) && enteredText > 0 && percentage > enteredText;
      },
      canBeUsed: modes => enteredText => {
        return modes.view === ViewMode.employee && enteredText.length > 0 && !isNaN(enteredText) && enteredText > 0;
      },
    }, {
      id: 'planner/assignmentPercentageLesserThan',
      label: () => enteredText => ({ i18n: 'filter_by_assignmentPercentageLesserThan', params: { value: enteredText } }),
      apply: modes => (element, enteredText) => {
        const entries = modes.rootState.Planner.entries;
        const leaveDays = modes.rootState.Planner.leaveDays;
        const isoFreeDays = modes.rootState.Planner.Time.isoFreeDays;
        const currentDate = modes.rootState.Planner.Time.currentDate;
        const workingSeconds = modes.time.entryCalculator
            .getWorkingSeconds(currentDate, element, modes.view, entries, leaveDays, isoFreeDays);
        const allSeconds = modes.time.getSecondsInView(currentDate, isoFreeDays);
        const assignedWorktime = DataMode.worktime.formatSeconds(workingSeconds, allSeconds);
        const wholeWorktime = DataMode.worktime.formatSeconds(element.worktime);
        const percentage = (assignedWorktime * 100) / wholeWorktime;
        return enteredText.length > 0 && !isNaN(enteredText) && enteredText > 0 && percentage < enteredText;
      },
      canBeUsed: modes => enteredText => {
        return modes.view === ViewMode.employee && enteredText.length > 0 && !isNaN(enteredText) && enteredText > 0;
      },
    },
    ],
  },
  getters: {
    getFilters: (state, getters, rootState) => {
      const view = rootState.Planner.viewMode;
      const data = rootState.Planner.dataMode;
      const time = rootState.Planner.Time.timeMode;
      const modes = { view, data, time, state, getters, rootState };
      const retFilters = [];

      for (const backendFilter of state.filters) {
        retFilters.unshift({
          id: backendFilter.id,
          label: backendFilter.label,
          apply: backendFilter.apply,
          canBeUsed: backendFilter.canBeUsed,
        });
      }

      for (const plannerFilter of state.plannerFilters) {
        retFilters.unshift({
          id: plannerFilter.id,
          label: plannerFilter.label(modes),
          apply: plannerFilter.apply(modes),
          canBeUsed: plannerFilter.canBeUsed(modes),
          default: plannerFilter.id === 'planner/nameFilter',
        });
      }
      return retFilters;
    },
    getQuery(state) {
      if (typeof state.query === 'string') {
        return state.query;
      } else {
        return '';
      }
    },
  },
  mutations: {
    setFilters(state, mutationParam) {
      state.filters = mutationParam.filters.map(filter => {
        filter.apply = element => {
          const filters = element.hasOwnProperty('filters') ? element.filters : [];
          if (Array.isArray(filters)) {
            return filters.indexOf(filter.id) !== -1;
          } else if (typeof filters === 'object') {
            return Object.values(filters).indexOf(filter.id) !== -1;
          } else {
            return false;
          }
        };
        filter.canBeUsed = enteredText => enteredText === null
            || enteredText.length === 0
            || filter.label.toLowerCase().includes(enteredText.toLowerCase());
        return filter;
      });
    },
    loadSelectedFiltersFromStorage(state, stateFilters) {
      const storage = new DataStorage();
      const storageFilters = storage.getObjectValue('filters', {});
      state.selectedFilters = [];
      const newStorageFilters = {};
      stateFilters.forEach(filter => {
        if (storageFilters.hasOwnProperty(filter.id)) {
          state.selectedFilters.push({
            filter,
            enteredText: storageFilters[filter.id],
          });
          newStorageFilters[filter.id] = storageFilters[filter.id];
        }
      });
      storage.setObjectValue('filters', newStorageFilters);
    },
    /**
     * @param state
     * @param {SelectedFilter} filter
     */
    addFilter(state, filter) {
      const storage = new DataStorage();
      state.selectedFilters.push(filter);
      const filters = storage.getObjectValue('filters', {});
      filters[filter.filter.id] = filter.enteredText;
      storage.setObjectValue('filters', filters);
    },
    /**
     * @param state
     * @param {SelectedFilter} filter
     */
    removeFilter(state, filter) {
      const storage = new DataStorage();
      state.selectedFilters = state.selectedFilters.filter(f => f !== filter);
      const filters = storage.getObjectValue('filters', {});
      delete filters[filter.filter.id];
      storage.setObjectValue('filters', filters);
    },
    clearFilters(state) {
      state.selectedFilters = [];
      new DataStorage().setObjectValue('filters', {});
    },
    setQueryFromStorage(state, storage) {
      const query = storage.getObjectValue('query', {});
      state.query = query;
    },
    setQuery(state, query) {
      const storage = new DataStorage();
      storage.setObjectValue('query', query);
      state.query = query;
    },
  },
  actions: {
    loadFilters(context) {
      context.commit('Planner/requestLoadingBar', {}, { root: true });
      context.rootState.apiClient.filter.get().then(response => {
        const mutationParam = {
          filters: response,
          viewMode: context.rootState.Planner.viewMode,
          dataMode: context.rootState.Planner.dataMode,
        };
        context.commit('setFilters', mutationParam);
        context.commit('loadSelectedFiltersFromStorage', context.getters.getFilters);
      }).finally(() => {
        context.commit('Planner/releaseLoadingBar', {}, { root: true });
      });
    },
    filterByQuery({ dispatch, rootGetters }, query) {
      if (rootGetters['Planner/currentViewMode'].value !== 'project') {
        dispatch('Planner/loadEmployeesByQuery', query, { root: true });
      } else {
        dispatch('Planner/loadProjectsByQuery', query, { root: true });
      }
    },
    loadQuery(context) {
      const storage = new DataStorage();
      context.commit('setQueryFromStorage', storage);
    },
  },

};
