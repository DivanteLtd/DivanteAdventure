import { getSuggestedLanguage, setLanguage } from '../../i18n/i18n';
import moment from '@divante-adventure/work-moment';

export default {
  namespaced: true,
  state: {
    employees: [],
    employee: {},
    leaderStructures: [],
    departments: [],
    contracts: [],
    positions: [],
    permissions: [],
    pairings: [],
    payload: {},
    loggedEmployee: {},
    hardware: {},
    firstHiredDate: [],
    employeeWorkLocations: [],
    allEmployeesWorkLocations: [],
    employeesTodayWorkLocations: [],
    showAskForSlack: false,
    financialContracts: [],
  },
  getters: {
    getManagersAcceptingLeaveRequests: state => state.employees.filter(employee => employee.manager),
    redirectToUserEdit: state => !state.loggedEmployee.hasOwnProperty('name')
        || !state.loggedEmployee.hasOwnProperty('lastName')
        || !state.loggedEmployee.hasOwnProperty('email')
        || !state.loggedEmployee.hasOwnProperty('privatePhone')
        || !state.loggedEmployee.hasOwnProperty('contract')
        || !state.loggedEmployee.hasOwnProperty('gender')
        || !state.loggedEmployee.hasOwnProperty('hiredAt')
        || !state.loggedEmployee.hasOwnProperty('dateOfBirth')
        || !state.loggedEmployee.hasOwnProperty('emergencyFirstName')
        || !state.loggedEmployee.hasOwnProperty('emergencyLastName')
        || !state.loggedEmployee.hasOwnProperty('emergencyPhone')
        || !state.loggedEmployee.hasOwnProperty('street')
        || !state.loggedEmployee.hasOwnProperty('city')
        || !state.loggedEmployee.hasOwnProperty('postalCode')
        || !state.loggedEmployee.hasOwnProperty('country')
        || !state.loggedEmployee.hasOwnProperty('dataUpdate')
        || moment(state.loggedEmployee.dataUpdate) < moment().subtract(6, 'months')
        || !state.loggedEmployee.hasSetPin
        || state.loggedEmployee.workMode === 0,
    redirectToGdpr: state => state.loggedEmployee.agreementsRequired,
  },
  mutations: {
    setEmployees(state, employees) {
      state.employees = employees;
    },
    setEmployee(state, employee) {
      state.employee = employee;
    },
    setLeaderStructures(state, leaderStructures) {
      state.leaderStructures = leaderStructures;
    },
    setHardware(state, hardware) {
      state.hardware = hardware;
    },
    setLoggedEmployee(state, employee) {
      state.loggedEmployee = employee;
    },
    setDepartments(state, departments) {
      state.departments = departments;
    },
    setContracts(state, contracts) {
      state.contracts = contracts;
    },
    setPositions(state, positions) {
      state.positions = positions;
    },
    setPermissions(state, permissions) {
      state.permissions = permissions;
    },
    setPairings(state, pairings) {
      state.pairings = pairings;
    },
    deleteEmployeeByIndex(state, index) {
      state.employees.splice(index, 1);
    },
    setFirstHiredDate(state, firstHiredDate) {
      state.firstHiredDate = firstHiredDate;
    },
    updateEmployees(state, employee) {
      state.employees = [
        ...state.employees.filter(element => element.id !== employee.id),
        employee,
      ];
    },
    showAskForSlack(state) {
      state.showAskForSlack = true;
    },
    setEmployeeWorkLocations(state, employeeWorkLocations) {
      state.employeeWorkLocations = employeeWorkLocations;
    },
    setAllEmployeesWorkLocations(state, allEmployeesWorkLocations) {
      state.allEmployeesWorkLocations = allEmployeesWorkLocations;
    },
    setEmployeesTodayWorkLocations(state, employeesTodayWorkLocations) {
      state.employeesTodayWorkLocations = employeesTodayWorkLocations;
    },
    updateLeaderStructure(state, data) {
      const index = state.leaderStructures.findIndex(val => val.leader.id === data.leaderId);
      const leaderStructure = state.leaderStructures.filter(val => val.leader.id === data.leaderId)[0];
      leaderStructure.structures = leaderStructure.structures.filter(val => val.id !== data.padawanId);
      const filteredStructure = state.leaderStructures.filter(val => val.leader.id !== data.leaderId);
      if (!leaderStructure.structures.length) {
        state.leaderStructures = filteredStructure;
      } else {
        filteredStructure.splice(index, 0, leaderStructure);
        state.leaderStructures = filteredStructure;
      }
    },
    setFinancialContracts(state, contracts) {
      state.financialContracts = contracts;
    },
  },
  actions: {
    async loadEmployees(context) {
      try {
        const employees = await context.rootState.apiClient.employee.getWithDetails();
        context.commit('setEmployees', employees);
        return employees;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadLeaderStructures(context) {
      try {
        const structures = await context.rootState.apiClient.employee.getLeaderStructures();
        context.commit('setLeaderStructures', structures);
        return structures;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deletePadawan(context, data) {
      try {
        await context.rootState.apiClient.employee.deleteLeaderStructure(data);
        context.commit('updateLeaderStructure', data);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadHardware(context, id) {
      try {
        if (id === undefined) {
          id = await context.rootState.Authorization.payload.employeeId;
        }
        const hardware = await context.rootState.apiClient.employee.getHardwareList(id);
        context.commit('setHardware', hardware);
        return hardware;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadEmployee(context, id) {
      try {
        if (id === undefined) {
          id = await context.rootState.Authorization.payload.employeeId;
        }
        return context.dispatch('loadEmployeeMeta', { id, setter: 'setEmployee' });
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadEmployeeWorkLocations(context) {
      try {
        const employeeWorkLocations = await context.rootState.apiClient.employee.getEmployeeWorkLocations();
        context.commit('setEmployeeWorkLocations', employeeWorkLocations);
        return employeeWorkLocations;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadAllEmployeesWorkLocations(context) {
      try {
        const allEmployeesWorkLocations = await context.rootState.apiClient.employee.getAllEmployeesWorkLocations();
        context.commit('setAllEmployeesWorkLocations', allEmployeesWorkLocations);
        return allEmployeesWorkLocations;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadEmployeesTodayWorkLocations(context) {
      try {
        const employeesTodayWorkLocations = await context.rootState.apiClient.employee.getEmployeesTodayWorkLocations();
        context.commit('setEmployeesTodayWorkLocations', employeesTodayWorkLocations);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadLoggedEmployee(context) {
      try {
        const id = await context.rootState.Authorization.payload.employeeId;
        const loggedEmployee = await context.dispatch('loadEmployeeMeta', { id, setter: 'setLoggedEmployee' });
        if (loggedEmployee.employee.language === undefined) {
          const data = {
            id: loggedEmployee.employee.id,
            language: getSuggestedLanguage(),
          };
          await context.rootState.apiClient.employee.update(data.id, data);
        } else if (getSuggestedLanguage() !== loggedEmployee.employee.language) {
          setLanguage(loggedEmployee.employee.language);
        }
        if (loggedEmployee.askForSlack) {
          await context.commit('showAskForSlack');
        }
        return loggedEmployee;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadEmployeeMeta(context, { id, setter }) {
      try {
        if (id === undefined) {
          id = await context.rootState.Authorization.payload.employeeId;
        }
        context.commit(setter, {});
        const response = await context.rootState.apiClient.employee.getById(id);
        context.commit(setter, response.employee);
        context.commit('setDepartments', response.departments);
        context.commit('setPositions', response.positions);
        context.commit('setContracts', response.contracts);
        context.commit('setPermissions', response.roles);
        return response;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadPairings(context) { // pairs of employee - projects
      try {
        const pairings = await context.rootState.apiClient.employeeProject.get();
        context.commit('setPairings', pairings);
        return pairings;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async saveEmployee(context, data) {
      if (data.id === undefined) {
        data.id = context.rootState.Authorization.payload.employeeId;
      }
      try {
        const newEmployee = await context.rootState.apiClient.employee.update(data.id, data);
        context.commit('setEmployee', newEmployee);
        context.commit('updateEmployees', newEmployee);
        if (context.state.loggedEmployee.id === newEmployee.id) {
          context.commit('setLoggedEmployee', newEmployee);
        }
        return newEmployee;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async saveWorkLocation(context, data) {
      try {
        await context.rootState.apiClient.employee.saveWorkLocation(data);
        return await context.dispatch('loadEmployeeWorkLocations');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async addPairings(context, data) {
      try {
        await context.rootState.apiClient.employeeProject.assign(data);
        return await context.dispatch('loadPairings');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async editPairings(context, data) {
      try {
        await context.rootState.apiClient.employeeProject.update(data.id, data);
        return await context.dispatch('loadPairings');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deletePairings(context, id) {
      try {
        await context.rootState.apiClient.employeeProject.unassign(id);
        return await context.dispatch('loadPairings');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async assignToTribe(context, data) {
      try {
        const response = await context.rootState.apiClient.employee.assignToTribe(data.idEmployee, data.idTribe);
        context.commit('setEmployees', response);
        return response;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deleteFromTribe(context, data) {
      try {
        const response = await context.rootState.apiClient.employee.unAssignToTribe(data.idEmployee, data.idTribe);
        context.commit('setEmployees', response);
        return response;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deleteEmployee(context, id) {
      try {
        const result = await context.rootState.apiClient.employee.delete(id);
        const index = context.state.employees.findIndex(employee => employee.id === id);
        if (index !== -1) {
          context.commit('deleteEmployeeByIndex', index);
        }
        return result;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async verifyPin(context, pin) {
      try {
        return await context.rootState.apiClient.employee.verifyPin(pin);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async unlockUser(context, userId) {
      try {
        return await context.rootState.apiClient.employee.unlock(userId);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async getFirstHiredDate(context) {
      try {
        const firstHiredDate = await context.rootState.apiClient.employee.getFirstHiredDate();
        context.commit('setFirstHiredDate', firstHiredDate);
        return firstHiredDate;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async hideSlackDialog(context) {
      return context.rootState.apiClient.employee.hideSlackDialog();
    },
    async getContracts(context, employeeId) {
      try {
        const contracts = await context.rootState.apiClient.employee.getContracts(employeeId);
        context.commit('setFinancialContracts', contracts);
        return contracts;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async addContract(context, data) {
      await context.rootState.apiClient.employee.addContract(data.employee_id, data);
    },
    async editContract(context, data) {
      await context.rootState.apiClient.employee.editContract(data.id, data);
    },
    async deleteContract(context, id) {
      await context.rootState.apiClient.employee.deleteContract(id);
    },
    async getCSV(context) {
      try {
        const { token } = await context.rootState.apiClient.employee.getCSVEmployeesList();
        const redirect = `${window.ADVENTURE_BACKEND_URL}/download/employee/list/${token}`;
        window.location.replace(redirect);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
