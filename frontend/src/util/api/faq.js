// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

const ROOT = 'faq';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  getCategories: () => restClient.get(`${ROOT}/category`),
  getQuestions: () => restClient.get(`${ROOT}/question`),
  createCategory: data => restClient.post(`${ROOT}/category`, data),
  createQuestion: data => restClient.post(`${ROOT}/question`, data),
  updateCategory: (id, data) => restClient.patch(`${ROOT}/category/${id}`, data),
  updateQuestion: (id, data) => restClient.patch(`${ROOT}/question/${id}`, data),
  deleteCategory: id => restClient.delete(`${ROOT}/category/${id}`),
  deleteQuestion: id => restClient.delete(`${ROOT}/question/${id}`),
  sendFAQMessage: data => restClient.post(`${ROOT}/asked`, data),
  getAskedQuestions: () => restClient.get(`${ROOT}/asked`),
  confirmQuestion: id => restClient.post(`${ROOT}/asked/${id}/confirm`),
  rejectQuestion: (id, reason) => restClient.post(`${ROOT}/asked/${id}/reject`, { reason }),
});
