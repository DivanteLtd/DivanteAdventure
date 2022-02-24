// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  createNewPeriod: data => restClient.post('period', data),
  getMyPeriods: () => restClient.get('period'),
  getEmployeePeriods: id => restClient.get(`period/${id}`),
  updatePeriod: (id, data) => restClient.patch(`period/${id}`, data),
  deletePeriod: id => restClient.delete(`period/${id}`),
  getFreeDaysReport: () => restClient.get('period/report'),
});
