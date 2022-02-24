export default {
    namespaced: true,
    state: {
        checklistTemplates: [],
        checklists: [],
        myChecklists: [],
        employeeChecklists: [],
        checklistTemplateDetails: {},
        checklistDetails: {},
        checklistTemplateQuestions: [],
    },
    mutations: {
        setChecklistTemplates(state, checklistTemplates) {
            state.checklistTemplates = checklistTemplates;
        },
        setChecklists(state, checklists) {
            state.checklists = checklists;
        },
        setMyChecklists(state, checklists) {
            state.myChecklists = checklists;
        },
        setEmployeeChecklists(state, checklists) {
            state.employeeChecklists = checklists;
        },
        setChecklistTemplateQuestions(state, checklistTemplateQuestions) {
            state.checklistTemplateQuestions = checklistTemplateQuestions;
        },
        setChecklistTemplateDetails(state, checklistTemplateDetails) {
            state.checklistTemplateDetails = checklistTemplateDetails;
        },
        setChecklistDetails(state, checklistDetails) {
            state.checklistDetails = checklistDetails;
        },
    },
    actions: {
        async loadChecklistTemplates(context) {
            try {
                const checklistTemplates = await context.rootState.apiClient.checklist.getChecklistTemplates();
                context.commit('setChecklistTemplates', checklistTemplates);
                return checklistTemplates;
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async getQuestionsFromTemplate(context, data) {
            try {
                const apiClient = context.rootState.apiClient;
                const checklistTemplateQuestions = await apiClient.checklist.getQuestionsFromTemplate(data.id);
                context.commit('setChecklistTemplateQuestions', checklistTemplateQuestions);
                return checklistTemplateQuestions;
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async loadChecklistTemplateDetails(context, data) {
            try {
                const apiClient = context.rootState.apiClient;
                const checklistTemplateDetails = await apiClient.checklist.getChecklistDetails(data.id);
                context.commit('setChecklistTemplateDetails', checklistTemplateDetails);
                return checklistTemplateDetails;
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async createChecklistTemplate(context, data) {
            try {
                const apiClient = context.rootState.apiClient;
                const checklistTemplatesBefore = await context.dispatch('loadChecklistTemplates');
                await apiClient.checklist.createChecklistTemplate(data);
                const checklistTemplatesAfter = await context.dispatch('loadChecklistTemplates');
                return checklistTemplatesAfter.filter(
                    ({ id: id1 }) => !checklistTemplatesBefore.some(({ id: id2 }) => id2 === id1)
                )[0];
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async addQuestionToTemplate(context, data) {
            try {
                const apiClient = context.rootState.apiClient;
                await apiClient.checklist.addQuestionToTemplate(data.id, data);
                return context.dispatch('getQuestionsFromTemplate', data);
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async updateQuestionInTemplate(context, data) {
            try {
                const apiClient = context.rootState.apiClient;
                await apiClient.checklist.updateQuestionInTemplate(data.templateId, data.id, data);
                data.id = data.templateId;
                return context.dispatch('getQuestionsFromTemplate', data);
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async deleteQuestionFromTemplate(context, data) {
            try {
                const apiClient = context.rootState.apiClient;
                await apiClient.checklist.deleteQuestionFromTemplate(data.templateId, data.id);
                data.id = data.templateId;
                return context.dispatch('getQuestionsFromTemplate', data);
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async deleteChecklistTemplate(context, data) {
            try {
                const apiClient = context.rootState.apiClient;
                await apiClient.checklist.deleteChecklistTemplate(data.id);
                return context.dispatch('loadChecklistTemplates');
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async deleteChecklist(context, id) {
            try {
                const apiClient = context.rootState.apiClient;
                await apiClient.checklist.deleteChecklist(id);
                return context.dispatch('loadMyChecklists');
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async updateChecklistTemplate(context, data) {
            try {
                const apiClient = context.rootState.apiClient;
                await apiClient.checklist.updateChecklistTemplate(data.id, data);
                return context.dispatch('loadChecklistTemplates');
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async loadMyChecklists(context) {
            try {
                const apiClient = context.rootState.apiClient;
                const checklists = await apiClient.checklist.getMyChecklists();
                context.commit('setMyChecklists', checklists);
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async loadEmployeeChecklists(context, data) {
            try {
                const apiClient = context.rootState.apiClient;
                const checklists = await apiClient.checklist.getUserChecklists(data);
                context.commit('setEmployeeChecklists', checklists);
                return checklists;
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async getAllChecklists(context) {
            try {
                const apiClient = context.rootState.apiClient;
                const checklists = await apiClient.checklist.getAllChecklists();
                context.commit('setChecklists', checklists);
                return checklists;
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async getChecklistDetails(context, id) {
            try {
                const apiClient = context.rootState.apiClient;
                const checklist = await apiClient.checklist.getChecklistDetails(id);
                context.commit('setChecklistDetails', checklist);
                return checklist;
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async updateStatus(context, data) {
            try {
                const apiClient = context.rootState.apiClient;
                await apiClient.checklist.updateStatus(data.checklistId, data.questionId, data.status);
                return context.dispatch('loadChecklistTemplates');
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
    },
};
