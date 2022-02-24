// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  get: () => restClient.get('employeeProjects'),
  assign: data => restClient.post('employeeProjects', data),
  unassign: id => restClient.delete(`employeeProjects/${id}`),
  allowOvertime: id => restClient.patch(`employeeProjects/${id}`, { overtime: true }),
  disallowOvertime: id => restClient.patch(`employeeProjects/${id}`, { overtime: false }),
  update: (id, data) => restClient.patch(`employeeProjects/${id}`, data),
});
