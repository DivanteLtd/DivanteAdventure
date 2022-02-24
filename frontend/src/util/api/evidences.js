// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  generate: data => restClient.post('evidence/generate', data),
  getMyEvidences: () => restClient.get('evidence'),
  getUserEvidences: id => restClient.get(`evidence/user/${id}`),
  getMyRequests: () => restClient.get(`evidence/requests`),
  update: (id, data) => restClient.post(`evidence/${id}`, data),
});
