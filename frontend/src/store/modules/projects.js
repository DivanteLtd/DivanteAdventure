export default {
  namespaced: true,
  state: {
    projects: [],
    gitlabProjects: [],
  },
  mutations: {
    setProjects(state, projects) {
      projects = projects.map(p => {
        p.options = {};
        return p;
      });
      state.projects = projects;
    },
    setGitlabProjects(state, projects) {
      state.gitlabProjects = projects;
    },
  },
  actions: {
    async loadProjects(context) {
      try {
        if (context.rootState.apiClient.project) {
          const list = await context.rootState.apiClient.project.getWithDetails();
          context.commit('setProjects', list);
          return list;
        }
        return [];
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async new(context, data) {
      try {
        await context.rootState.apiClient.project.new(data);
        return await context.dispatch('loadProjects');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async update(context, data) {
      try {
        await context.rootState.apiClient.project.update(data.id, data);
        return await context.dispatch('loadProjects');
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async delete(context, id) {
      try {
        const projects = await context.rootState.apiClient.project.delete(id);
        context.commit('setProjects', projects);
        return projects;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async hide(context, id) {
      try {
        const projects = await context.rootState.apiClient.project.hide(id);
        context.commit('setProjects', projects);
        return projects;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async downloadReport(context, id) {
      try {
        const { token } = await context.rootState.apiClient.project.downloadHistory(id);
        const redirect = `${window.ADVENTURE_BACKEND_URL}/download/report/${token}`;
        window.location.replace(redirect);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async unsetCriterion(context, data) {
      try {
        return await context.rootState.apiClient.project.deleteCriterium(data.projectId, data.criterionId);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async assignCriterion(context, data) {
      try {
        return await context.rootState.apiClient.project.addCriterium(data.projectId, data.data);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async sendEmail(context, data) {
      try {
        return await context.rootState.apiClient.project.sendEmail(data.id);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadIntegrationProjects(context) {
      try {
        const gitlab = await context.rootState.apiClient.integrations.getGitlabProjects();
        context.commit('setGitlabProjects', gitlab);
        return { gitlab };
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async disconnectFromSlack(context, id) {
      return context.rootState.apiClient.project.disconnectFromSlack(id);
    },
  },
};
