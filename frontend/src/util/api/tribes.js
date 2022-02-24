// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  get: () => restClient.get('tribe'),
  new: data => restClient.post('tribe', data),
  update: (id, data) => restClient.patch(`tribe/${id}`, data),
  delete: id => restClient.delete(`tribe/${id}`),
  addPosition: (tribeId, positionId) => restClient.post(`tribe/${tribeId}/position/${positionId}`),
  deletePosition: (tribeId, positionId) => restClient.delete(`tribe/${tribeId}/position/${positionId}`),
  disconnectFromSlack: id => restClient.post(`tribe/${id}/disconnectSlack`),
});
