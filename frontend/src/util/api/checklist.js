// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';
/**
 * @typedef {Object} EmployeeChecklistData
 * @property {number} id
 * @property {string} name
 * @property {string} lastName
 * @property {string} photo
 */
/**
 * @typedef {Object} AbstractChecklistEntry
 * @property {number} id
 * @property {number} type
 * @property {string} namePl
 * @property {string} nameEn
 * @property {EmployeeChecklistData} subject
 * @property {?EmployeeChecklistData} owner
 * @property {?number} finishedAt
 */
/**
 * @typedef {AbstractChecklistEntry} Checklist
 * @property {number} startedAt
 * @property {ChecklistQuestion[]} tasks
 */
/**
 * @typedef {AbstractChecklistEntry} ChecklistListEntry
 * @property {number} tasksFinishedCount
 * @property {number} tasksAllCount
 */
/**
 * @typedef {Object} ChecklistQuestion
 * @property {number} id
 * @property {string} namePl
 * @property {string} nameEn
 * @property {string} descriptionPl
 * @property {json} descriptionEn
 * @property {Array} possibleStatuses
 * @property {string} status
 * @property {?Object} checklist
 * @property {?EmployeeChecklistData} responsible
 * @property {?number} checkedAt
 */
/**
 * @typedef {Object} ChecklistTemplateQuestion
 * @property {number} id
 * @property {string} namePl
 * @property {string} nameEn
 * @property {string} descriptionPl
 * @property {json} descriptionEn
 * @property {Array} possibleStatuses
 * @property {?Object} checklist
 * @property {?EmployeeChecklistData} responsible
 * @property {?number} checkedAt
 */
/**
 * @typedef {Object} ChecklistTemplate
 * @property {number} id
 * @property {number} type
 * @property {string} namePl
 * @property {string} nameEn
 * @property {ChecklistTemplateQuestion[]} tasks
 */
/**
 * @param {RestClient} restClient
 */
export default restClient => ({
    /**
     * @returns {Promise<ChecklistTemplate[]>}
     */
    getChecklistTemplates: () => restClient.get('template/checklist'),
    /**
     * @param {ChecklistTemplate} data
     * @returns {Promise}
     */
    createChecklistTemplate: data => restClient.post('template/checklist', data),
    /**
     * @param {number} id
     * @returns {Promise}
     */
    deleteChecklistTemplate: id => restClient.delete(`template/checklist/${id}`),
    /**
     * @param {number} id
     * @returns {Promise}
     */
    deleteChecklist: id => restClient.delete(`checklist/${id}`),
    /**
     * @param {number} templateId
     * @param {number} subjectId
     * @param {number} ownerId
     * @param {boolean} hidden
     * @param {string} dueDate
     * @returns {Promise}
     */
    assignTemplate: (templateId, subjectId, ownerId, hidden, dueDate) => restClient.post(`template/checklist/${templateId}/apply`, { ownerId, subjectId, hidden, dueDate }),
    /**
     * @param {number} id
     * @param {ChecklistTemplate} data
     * @returns {Promise}
     */
    updateChecklistTemplate: (id, data) => restClient.patch(`template/checklist/${id}`, data),
    /**
     * @returns {Promise<Checklist[]>}
     */
    getAllChecklists: () => restClient.get('checklist'),
    /**
     * @param {number} id
     * @returns {Promise<Checklist>}
     */
    getChecklistDetails: id => restClient.get(`checklist/${id}`),
    /**
     * @param {number} id
     * @returns {Promise<ChecklistListEntry[]>}
     */
    getUserChecklists: id => restClient.get(`employees/${id}/checklists`),
    /**
     * @param {number} checklistId
     * @param {ChecklistTemplateQuestion} data
     * @returns {Promise}
     */
    addQuestionToTemplate: (checklistId, data) => restClient.post(`question/checklist/${checklistId}`, data),
    /**
     * @param {number} checklistId
     * @param {number} questionId
     * @param {ChecklistTemplateQuestion} data
     * @returns {Promise}
     */
    updateQuestionInTemplate: (checklistId, questionId, data) => restClient.patch(`question/checklist/${checklistId}/${questionId}`, data),
    /**
     * @param {number} checklistId
     * @param {number} questionId
     * @returns {Promise}
     */
    deleteQuestionFromTemplate: (checklistId, questionId) => restClient.delete(`question/checklist/${checklistId}/${questionId}`),
    /**
     * @param {number} checklistId
     * @returns {Promise<ChecklistTemplateQuestion[]>}
     */
    getQuestionsFromTemplate: checklistId => restClient.get(`question/checklist/${checklistId}`),
    /**
     * @returns {Promise<Checklist[]>}
     */
    getMyChecklists: () => restClient.get(`checklist/mine`),
    /**
     * @param {number} checklistId
     * @param {number} questionId
     * @returns {Promise}
     */
    pingQuestion: (checklistId, questionId) => restClient.post(`checklist/${checklistId}/${questionId}/ping`),
    /**
     * @param {number} checklistId
     * @param {number} questionId
     * @param {number} status
     * @returns {Promise}
     */
    updateStatus: (checklistId, questionId, status) => restClient.post(`checklist/${checklistId}/${questionId}`, { status }),
});
