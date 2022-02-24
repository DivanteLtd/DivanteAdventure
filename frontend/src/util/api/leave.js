// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  get: () => restClient.get('leave'),
  getConfirmedLeaveDays: () => restClient.get('leave/confirmed'),
  getByStartAndEnd: (start, end) => restClient.get(`leave?start=${start}&end=${end}`),
  getDashboardByStartAndEnd: (start, end) => restClient.get(`leave/summary?start=${start}&end=${end}`),
});
