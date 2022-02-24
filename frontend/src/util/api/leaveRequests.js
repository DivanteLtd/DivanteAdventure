// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  createNewRequest: (periodId, data) => restClient.post(`leaveRequest/${periodId}`, data),
  getMyRequests: () => restClient.get('leaveRequest'),
  getEmployeeRequests: id => restClient.get(`leaveRequest/${id}`),
  getRequestsToMe: () => restClient.get('leaveRequest/acceptable'),
  updateRequest: (id, data) => restClient.patch(`leaveRequest/${id}`, data),
  updateRequestDay: (id, data) => restClient.patch(`leaveRequest/day/${id}`, data),
  deleteRequest: id => restClient.delete(`leaveRequest/${id}`),
  createDownloadToken: id => restClient.get(`sickleaveattachment/${id}/token`),
});
