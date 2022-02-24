// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  getCurrentConfiguration: () => restClient.get('config'),
  getContentConfiguration: () => restClient.get('config/content'),
  getConfigHistory: key => restClient.get(`config/${key}`),
  updateConfig: (key, value) => restClient.post('config', { entries: { [key]: value } }),
});
