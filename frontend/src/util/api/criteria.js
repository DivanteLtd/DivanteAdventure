// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  list: () => restClient.get('criteria'),
  create: data => restClient.post('criteria', data),
  update: data => restClient.post(`criteria/${data.id}`, data),
  delete: id => restClient.delete(`criteria/${id}`),
});
