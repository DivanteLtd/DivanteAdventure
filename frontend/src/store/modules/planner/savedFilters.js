import DataStorage from '@divante/data-storage';

export default {
  namespaced: true,
  state: {
    savedFilters: {},
    savedQuery: '',
  },
  getters: {
    savedFiltersAsArray: state => {
      const array = [];
      for (const name in state.savedFilters) {
        if (!state.savedFilters.hasOwnProperty(name)) {
          continue;
        }
        const value = state.savedFilters[name];
        array.push({
          text: name,
          value,
        });
      }
      return array;
    },
  },
  mutations: {
    setSavedFilters(state, filters) {
      state.savedFilters = filters;
    },
    setSavedQuery(state, query) {
      state.savedQuery = query;
    },
  },
  actions: {
    saveFilter(context, data) {
      const storageFilters = {};
      context.rootState.Planner.Filters.selectedFilters
          .forEach(filter => {
            storageFilters[filter.filter.id] = filter.enteredText;
          });
      const storage = new DataStorage();
      const storageData = {
        filters: storageFilters,
        viewMode: data.viewMode.value,
        dataMode: data.dataMode.value,
        timeMode: data.timeMode.value,
        query: storage.getObjectValue('query', ''),
      };
      const storedFilters = storage.getObjectValue('storedFilters', {});
      storedFilters[data.name] = storageData;
      storage.setObjectValue('storedFilters', storedFilters);
      context.commit('setSavedFilters', storedFilters);
    },
    saveQuery(context, query) {
      const storage = new DataStorage();
      storage.setObjectValue('query', query);
      context.commit('setSavedQuery', query);
    },
  },
};
