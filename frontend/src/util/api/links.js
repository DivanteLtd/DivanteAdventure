// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  getAll: () => restClient.get('links'),
  getById: id => restClient.get(`links/${id}`),
  add: data => restClient.post('links', data),
  update: (id, data) => restClient.patch(`links/${id}`, data),
  delete: id => restClient.delete(`links/${id}`),
});
