export default {
  namespaced: true,
  state: {
    endCooperationEmployees: [],
    potentialEmployees: [],
  },
  mutations: {
    setEndCooperationEmployees(state, endCooperationEmployees) {
      state.endCooperationEmployees = endCooperationEmployees;
    },
    setPotentialEmployees(state, potentialEmployees) {
      state.potentialEmployees = potentialEmployees;
    },
    deletePotentialEmployees(state, id) {
      state.potentialEmployees = state.potentialEmployees.filter(val => val.id !== id);
    },
  },
  actions: {
    async loadPotentialEmployees(context) {
      try {
        const apiClient = context.rootState.apiClient;
        const potentialEmployees = await apiClient.potentialEmployees.get();
        context.commit('setPotentialEmployees', potentialEmployees);
        return potentialEmployees;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async createPotentialEmployee(context, data) {
      try {
        const apiClient = context.rootState.apiClient;
        await apiClient.potentialEmployees.create(data);
        return context.dispatch('loadPotentialEmployees');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async updatePotentialEmployee(context, data) {
      try {
        const apiClient = context.rootState.apiClient;
        await apiClient.potentialEmployees.update(data.id, data);
        return context.dispatch('loadPotentialEmployees');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deletePotentialEmployee(context, id) {
      try {
        await context.rootState.apiClient.potentialEmployees.delete(id);
        context.commit('deletePotentialEmployees', id);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadEndCooperationEmployees(context) {
      try {
        const apiClient = context.rootState.apiClient;
        const endCooperationEmployees = await apiClient.employee.getEmployeesEndedWork();
        context.commit('setEndCooperationEmployees', endCooperationEmployees);
        return endCooperationEmployees;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async createEmployeeToDismiss(context, data) {
      try {
        const apiClient = context.rootState.apiClient;
        await apiClient.employee.createEmployeesEndedWork(data);
        return context.dispatch('loadEndCooperationEmployees');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async updateEmployeeToDismiss(context, data) {
      try {
        const apiClient = context.rootState.apiClient;
        await apiClient.employee.updateEmployeesEndedWork(data.id, data);
        return context.dispatch('loadEndCooperationEmployees');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deleteEmployeeToDismiss(context, id) {
      try {
        const apiClient = context.rootState.apiClient;
        await apiClient.employee.deleteEmployeesEndedWork(id);
        return context.dispatch('loadEndCooperationEmployees');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
