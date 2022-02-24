// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @typedef {Object} PinSet
 * @property {boolean} hasSetPin
 */

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  get: () => restClient.get('employees'),
  getByQuery: (query = '') => restClient.get(`employees?query=${query}`),
  getWithDetails: () => restClient.get('employees/details'),
  getHardwareList: id => restClient.get(`employees/hardware/${id}`),
  getById: id => restClient.get(`employees/id/${id}`),
  getByName: name => restClient.get(`employees/name/${name}`),
  getByLastName: name => restClient.get(`employees/lastName/${name}`),
  update: (id, data) => restClient.patch(`employees/${id}`, data),
  saveWorkLocation: data => restClient.post('employees/workLocation', data),
  getEmployeeWorkLocations: () => restClient.get(`employees/workLocation`),
  getAllEmployeesWorkLocations: () => restClient.get(`employees/workLocation/all`),
  getEmployeesTodayWorkLocations: () => restClient.get(`employees/workLocation/today`),
  // eslint-disable-next-line camelcase
  assignToTribe: (user_id, tribe_id) => restClient.post(`employees/assign/tribe/${user_id}`, { tribe_id }),
  // eslint-disable-next-line camelcase
  unAssignToTribe: (user_id, tribe_id) => restClient.post(`employees/unassign/tribe/${user_id}`, { tribe_id }),
  delete: id => restClient.delete(`employees/${id}`),
  /** @returns {Promise<PinSet>} */
  hasSetPin: () => restClient.get('employees/isPinSet'),
  verifyPin: pin => restClient.post('employees/verifyPin', { pin }),
  unlock: id => restClient.post(`employees/unlock/${id}`),
  getEmployeesEndedWork: () => restClient.get('employees/endedWork'),
  getFirstHiredDate: () => restClient.get('employees/firstHiredDate'),
  createEmployeesEndedWork: data => restClient.post('employees/endedWork', data),
  updateEmployeesEndedWork: (id, data) => restClient.patch(`employees/endedWork/${id}`, data),
  deleteEmployeesEndedWork: id => restClient.delete(`employees/endedWork/${id}`),
  hideSlackDialog: () => restClient.post('employees/hideSlack'),
  addLeaderStructure: structure => restClient.post('employees/leaderStructure', structure),
  getLeaderStructures: () => restClient.get('employees/leaderStructures'),
  deleteLeaderStructure: data => restClient.post('employees/deletePadawan', data),
  addContract: (id, data) => restClient.post(`employees/addContract/${id}`, data),
  editContract: (id, data) => restClient.post(`employees/editContract/${id}`, data),
  deleteContract: id => restClient.delete(`employees/deleteContract/${id}`),
  getContracts: id => restClient.get(`employees/getContracts/${id}`),
  getCSVEmployeesList: () => restClient.get('employees/getCSV'),
});
