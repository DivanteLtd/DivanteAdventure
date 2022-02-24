import Time from './time';
import Drag from './drag';
import Filters from './filters';
import ViewMode, { getViewModeByValue } from '../../../util/planner/ViewMode';
import DataMode, { getDataModeByValue } from '../../../util/planner/DataMode';
import moment from '@divante-adventure/work-moment';
import DataStorage from '@divante/data-storage';
import SavedFilters from './savedFilters';
import CsvExporter from '../../../util/planner/CsvExporter';

export default {
  namespaced: true,
  modules: {
    Time, Drag, Filters, SavedFilters,
  },
  state: {
    viewMode: ViewMode.employee,
    dataMode: DataMode.hours,
    employees: [],
    projects: [],
    pairings: [],
    entries: [],
    leaveDays: [],
    loadingBarRequestsCount: 0,
    cellContent: 0,
  },
  getters: {
    listStructure: state => {
      const { employees, projects, pairings } = state;
      const selectedFilters = state.Filters.selectedFilters;
      const currentRange = moment(state.Time.currentDate).rangeOf(state.Time.timeMode.displayControl.getViewRange());
      return state.viewMode.createListStructure(employees, projects, pairings, selectedFilters, currentRange);
    },
    currentViewMode(state) {
      return state.viewMode;
    },
  },
  mutations: {
    setCellContent(state, cellContent) {
      state.cellContent = cellContent;
    },
    setDataMode(state, mode) {
      state.dataMode = getDataModeByValue(mode);
      new DataStorage().setValue('dataMode', mode);
    },
    requestLoadingBar(state) {
      state.loadingBarRequestsCount++;
    },
    releaseLoadingBar(state) {
      state.loadingBarRequestsCount--;
    },
    setMode(state, mode) {
      state.viewMode = getViewModeByValue(mode);
      new DataStorage().setValue('viewMode', mode);
    },
    setEmployees(state, employees) {
      state.employees = employees;
    },
    setProjects(state, projects) {
      state.projects = projects;
    },
    setPairings(state, pairings) {
      state.pairings = pairings;
    },
    deletePairingByIndex(state, index) {
      state.pairings.splice(index, 1);
    },
    setLeaveDays(state, leaveDays) {
      state.leaveDays = leaveDays.map(leaveDay => {
        if (!leaveDay.hasOwnProperty('notAccepted')) {
          leaveDay.notAccepted = false;
        }
        return leaveDay;
      });
    },
    setEntries(state, entries) {
      const freeDays = state.Time.isoFreeDays;
      state.entries = entries
          .map(entry => {
            entry.timestamp = moment.unix(entry.timestamp).startOf('day').unix();
            return entry;
          })
          .filter(entry => moment.unix(entry.timestamp).isWorkingDay(freeDays));
    },
  },
  actions: {
    reloadAfterTimeChange(context) {
      context.dispatch('loadEntries');
      context.dispatch('loadLeaveDays');
      context.dispatch('Time/loadFreeDays');
      const { employees, projects, pairings } = context.state;
      const selectedFilters = context.state.Filters.selectedFilters;
      const currentDate = new Date(context.state.Time.currentDate);
      const currentRange = moment(currentDate).rangeOf(context.state.Time.timeMode.displayControl.getViewRange());
      context.state.viewMode.createListStructure(employees, projects, pairings, selectedFilters, currentRange);
    },
    exportToCsv(context) {
      CsvExporter.exportTableToCsv(context);
    },
    loadFromStorage(context) {
      const storage = new DataStorage();
      context.commit('setDataMode', storage.getValue('dataMode', 'hours'));
      context.commit('setMode', storage.getValue('viewMode', 'employee'));
      context.commit('Time/setTimeMode', storage.getValue('timeMode', 'week'));
      context.commit('SavedFilters/setSavedFilters', storage.getObjectValue('storedFilters', {}));
    },
    loadDataFromBackend(context, params = {}) {
      context.dispatch('loadEmployees', params.loadEmployees);
      context.dispatch('loadProjects', params.loadProjects);
      context.dispatch('loadPairings', params.loadPairings);
      context.dispatch('loadEntries', params.loadEntries);
      context.dispatch('Time/loadFreeDays', params.loadFreeDays);
      context.dispatch('loadLeaveDays', params.loadLeaveDays);
      context.dispatch('Filters/loadFilters', params.loadFilters);
    },
    loadEmployees(context) {
      context.commit('requestLoadingBar');
      context.rootState.apiClient.employee.get().then(response => {
        context.commit('setEmployees', response);
      }).finally(() => {
        context.commit('releaseLoadingBar');
      });
    },
    loadEmployeesByQuery(context, query) {
      context.commit('requestLoadingBar');
      context.rootState.apiClient.employee.getByQuery(query).then(response => {
        context.commit('setEmployees', response);
      }).finally(() => {
        context.commit('releaseLoadingBar');
      });
    },
    loadProjects(context) {
      context.commit('requestLoadingBar');
      context.rootState.apiClient.project.get().then(response => {
        context.commit('setProjects', response);
      }).finally(() => {
        context.commit('releaseLoadingBar');
      });
    },
    loadProjectsByQuery(context, query) {
      context.commit('requestLoadingBar');
      context.rootState.apiClient.project.getByQuery(query).then(response => {
        context.commit('setProjects', response);
      }).finally(() => {
        context.commit('releaseLoadingBar');
      });
    },
    loadPairings(context) {
      context.commit('requestLoadingBar');
      context.rootState.apiClient.employeeProject.get().then(response => {
        context.commit('setPairings', response);
      }).finally(() => {
        context.commit('releaseLoadingBar');
      });
    },
    createPairing(context, pair) {
      const request = {
        employeeId: pair.employee.id,
        employeeName: pair.employee.name,
        employeeLastName: pair.employee.lastName,
        projectId: pair.project.id,
        projectName: pair.project.name,
      };
      request.dateFrom = [];
      request.dateFrom.push(pair.dateFrom);
      request.dateTo = [];
      request.dateTo.push(pair.dateTo);
      const pairings = context.state.pairings.slice();
      context.commit('requestLoadingBar');
      context.rootState.apiClient.employeeProject.assign(request).then(response => {
        pairings.push(response);
        context.commit('setPairings', pairings);
      }).finally(() => {
        context.commit('releaseLoadingBar');
      });
    },
    addPairing(context, pair) {
      const request = {
        id: pair.id,
      };
      request.dateFrom = pair.dateFrom;
      request.dateTo = pair.dateTo;
      const pairings = context.state.pairings.slice();
      context.commit('requestLoadingBar');
      context.rootState.apiClient.employeeProject.update(request.id, request).then(response => {
        const index = pairings.findIndex(element => element.id === request.id);
        pairings[index] = response;
        context.commit('setPairings', pairings);
      }).finally(() => {
        context.commit('releaseLoadingBar');
      });
    },
    editPairing(context, pair) {
      const request = {
        id: pair.id,
        dateFrom: pair.dateFrom,
        dateTo: pair.dateTo,
      };
      const pairings = context.state.pairings.slice();
      context.commit('requestLoadingBar');
      context.rootState.apiClient.employeeProject.update(request.id, request).then(response => {
        const index = pairings.findIndex(element => element.id === request.id);
        pairings[index] = response;
        context.commit('setPairings', pairings);
      }).finally(() => {
        context.dispatch('loadEntries');
        context.commit('releaseLoadingBar');
      });
    },
    deletePairing(context, pair) {
      const pairings = context.state.pairings.slice();
      const index = pairings.findIndex(element => element.id === pair.id);
      if (typeof(index) !== 'undefined') { // if entry exists.
        context.commit('requestLoadingBar');
        context.rootState.apiClient.employeeProject.unassign(pair.id).then(() => {
          context.commit('deletePairingByIndex', index);
        }).finally(() => {
          context.dispatch('loadEntries');
          context.commit('releaseLoadingBar');
        });
      }
    },
    allowOvertime(context, id) {
      const pairings = context.state.pairings.slice();
      context.commit('requestLoadingBar');
      context.rootState.apiClient.employeeProject.allowOvertime(id).then(response => {
        const index = pairings.findIndex(x => x.id === response.id);
        pairings[index] = response;
        context.commit('setPairings', pairings);
      }).finally(() => {
        context.commit('releaseLoadingBar');
      });
    },
    disallowOvertime(context, id) {
      const pairings = context.state.pairings.slice();
      context.commit('requestLoadingBar');
      context.rootState.apiClient.employeeProject.disallowOvertime(id).then(response => {
        const index = pairings.findIndex(x => x.id === response.id);
        pairings[index] = response;
        context.commit('setPairings', pairings);
      }).finally(() => {
        context.commit('releaseLoadingBar');
      });
    },
    loadEntries(context, borders = undefined) {
      if (borders === undefined) {
        const currentDate = context.state.Time.currentDate;
        const timeMode = context.state.Time.timeMode;
        borders = timeMode.displayControl.getBorders(moment(currentDate));
      }
      const beginning = borders.beginning.unix();
      const ending = borders.ending.unix();
      context.commit('requestLoadingBar');
      context.rootState.apiClient.occupancy.getByStartAndEndTimestamp(beginning, ending).then(response => {
        context.commit('setEntries', response);
      }).finally(() => {
        context.commit('releaseLoadingBar');
      });
    },
    loadLeaveDays(context, borders = undefined) {
      if (borders === undefined) {
        const currentDate = context.state.Time.currentDate;
        const timeMode = context.state.Time.timeMode;
        borders = timeMode.displayControl.getBorders(moment(currentDate));
      }
      const beginning = borders.beginning.format('YYYY-MM-DD');
      const ending = borders.ending.format('YYYY-MM-DD');
      context.commit('requestLoadingBar');
      context.rootState.apiClient.leave.getByStartAndEnd(beginning, ending).then(response => {
        context.commit('setLeaveDays', response);
      }).finally(() => {
        context.commit('releaseLoadingBar');
      });
    },
    prepareEntries(context, actionData) {
      actionData = Array.isArray(actionData) ? actionData : [actionData];
      const entriesToCreate = [];
      for (const data of actionData) {
        const timestamp = data.date.unix();
        const primaryId = data.primary.id;
        const type = data.type;
        for (const element of data.elements) {
          entriesToCreate.push({
            timestamp,
            employeeId: type === ViewMode.employee ? primaryId : element.employeeId,
            projectId: type === ViewMode.employee ? element.projectId : primaryId,
            secondsPerDay: element.secondsPerDay,
          });
        }
      }
      context.dispatch('saveEntries', entriesToCreate);
    },
    prepareRangeEntries(context, data) {
      const freeDays = context.state.Time.isoFreeDays;
      const leaveDays = context.state.leaveDays
          .filter(leaveDay => leaveDay.employeeId === data.employeeId && leaveDay.notAccepted === false)
          .map(day => day.date);
      const freeAndLeaveDays = freeDays.concat(leaveDays);
      const currDate = moment(data.start).startOf('day');
      const lastDate = moment(data.end).startOf('day');
      const entriesToCreate = Array.from(moment.range(currDate, lastDate).by('day'))
          .filter(date => {
            const existedEntry = data.entries
                .filter(entry => {
                  const timestamp = moment.unix(entry.timestamp).isoDate();
                  return entry.employeeId === data.employeeId && timestamp === date.isoDate()
                      && entry.projectId !== data.projectId;
                })
                .map(entry => entry.secondsPerDay)
                .reduce((a, b) => a + b, 0);
            const maxEntries = data.workTime - existedEntry > 0 ? data.workTime - existedEntry : 0;
            date.occupancy = maxEntries < data.occupancy ? maxEntries : data.occupancy;
            return date.isWorkingDay(freeAndLeaveDays);
          })
          .map(entryDate => ({
            timestamp: entryDate.startOf('day').unix(),
            employeeId: data.employeeId,
            projectId: data.projectId,
            secondsPerDay: entryDate.occupancy,
          }));
      context.dispatch('saveEntries', entriesToCreate);
    },
    saveEntries(context, entriesToCreate) {
      context.commit('requestLoadingBar');
      return context.rootState.apiClient.occupancy
          .add({ multiple: true, data: entriesToCreate })
          .then(() => {
                context.commit('releaseLoadingBar');
                return context.dispatch('loadEntries');
              });
    },
    createReport(context, criteria) {
      context.rootState.apiClient.report.create(criteria)
        .then(response => {
          const redirect = `${window.ADVENTURE_BACKEND_URL}/download/planner/report/${response.token}`;
          window.location.replace(redirect);
        });
    },
  },
};
