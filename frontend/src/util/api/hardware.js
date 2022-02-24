// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

const ROOT = 'hardware';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
    getEntries: () => restClient.get(`${ROOT}/agreement`),
    getAgreementsToSign: () => restClient.get(`${ROOT}/agreement/to_sign`),
    generateAgreement: data => restClient.post(`${ROOT}/agreement`, data),
    deleteEntry: id => restClient.delete(`${ROOT}/${id}`),
    signAgreement: (id, password) => restClient.post(`${ROOT}/${id}/sign`, { password }),
});
