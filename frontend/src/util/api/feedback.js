// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

const ROOT = 'feedback';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  getFeedback: id => restClient.get(`${ROOT}/${id}`),
  getFeedbackProvided: id => restClient.get(`${ROOT}/provided/${id}`),
  addFeedback: data => restClient.post(`${ROOT}`, data),
  updateFeedback: (id, data) => restClient.patch(`${ROOT}/${id}`, data),
  deleteFeedback: id => restClient.delete(`${ROOT}/${id}`),
  planFeedback: (employeeId, date) => restClient.post(`${ROOT}/planned`, { employeeId, date }),
  getMyStructure: id => restClient.get(`${ROOT}/structure/${id}`),
  getTribeStructure: id => restClient.get(`${ROOT}/tribe/structure/${id}`),
  getPlannedByEmployee: id => restClient.get(`${ROOT}/planned/employee/${id}`),
});
