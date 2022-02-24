// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
    listContractsType: () => restClient.get('dictionaries/contracts'),
    createContractType: data => restClient.post('dictionaries/contract', data),
    updateContractType: data => restClient.post(`dictionaries/contracts/${data.id}`, data),
    deleteContractType: id => restClient.delete(`dictionaries/contracts/${id}`),
});
