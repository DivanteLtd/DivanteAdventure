// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  getByStartAndEndTimestamp: (start, end) => restClient.get(`pairings?timestamp_gte=${start}&timestamp_lte=${end}`),
  add: data => restClient.post('pairings', data),
  update: (id, data) => restClient.patch(`pairings/${id}`, data),
  delete: id => restClient.delete(`pairings/${id}`),
});
