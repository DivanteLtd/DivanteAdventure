// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

const ROOT = 'level';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  get: () => restClient.get(ROOT),
  create: data => restClient.post(ROOT, data),
  update: (id, data) => restClient.patch(`${ROOT}/${id}`, data),
  delete: id => restClient.delete(`${ROOT}/${id}`),
});
