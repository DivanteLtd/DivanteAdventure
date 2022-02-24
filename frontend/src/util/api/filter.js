// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  get: () => restClient.get('filters'),
  getById: id => restClient.get(`filters?id=${id}`),
  getByLabel: name => restClient.get(`filters?label=${name}`),
});
