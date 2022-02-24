// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  get: () => restClient.get('attachment'),
  deleteAttachment: id => restClient.delete(`attachment/${id}`),
  deleteAgreement: id => restClient.delete(`agreement/${id}`),
  getById: id => restClient.get(`agreement/${id}`),
  getMarketingById: id => restClient.get(`agreement/marketing/${id}`),
  update: (id, data) => restClient.post(`employeeagreement/${id}`, data),
  getGDPRAcceptationList: () => restClient.get('employeeagreement/gdpr'),
  getMarketingAcceptationList: () => restClient.get('employeeagreement/marketing'),
  getISOAcceptationList: () => restClient.get('employeeagreement/iso'),
  editAgreement: (id, data) => restClient.patch(`agreement/${id}`, data),
  newAgreement: data => restClient.post('agreement', data),
  createDownloadToken: id => restClient.get(`attachment/${id}/token`),
});
