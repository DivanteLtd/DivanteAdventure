// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  getAll: () => restClient.get('news'),
  getById: id => restClient.get(`news/${id}`),
  getHtmlNewsById: id => restClient.get(`news/${id}`),
  add: data => restClient.post('news', data),
  update: (id, data) => restClient.patch(`news/${id}`, data),
  delete: id => restClient.delete(`news/${id}`),
});
