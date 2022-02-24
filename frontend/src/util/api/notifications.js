// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  get: () => restClient.get('notification'),
  unmark: id => restClient.post(`notification/${id}`),
  delete: id => restClient.delete(`notification/${id}`),
});
