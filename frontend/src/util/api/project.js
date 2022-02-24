// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  get: () => restClient.get('projects'),
  getByQuery: (query = '') => restClient.get(`projects?query=${query}`),
  getWithDetails: () => restClient.get('projects/details'),
  new: data => restClient.post('projects', data),
  update: (id, data) => restClient.patch(`projects/${id}`, data),
  downloadHistory: id => restClient.get(`project/history/${id}/download`), // TODO rename controller to "projects/"
  delete: id => restClient.delete(`projects/${id}`),
  hide: id => restClient.patch(`projects/hide/${id}`), // TODO move ID before "hide"
  addCriterium: (id, data) => restClient.post(`projects/${id}/criterium`, data),
  deleteCriterium: (projectId, criteriumId) => restClient.delete(`projects/${projectId}/criterium/${criteriumId}`),
  sendEmail: id => restClient.get(`projects/sendEmail/${id}`), // TODO change "get" to "post", move ID before "sendEmail"
  disconnectFromSlack: id => restClient.post(`projects/${id}/disconnectSlack`),
});
