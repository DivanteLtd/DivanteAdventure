import { EventSourcePolyfill } from 'event-source-polyfill';

export default class Mercure {
  constructor(requestResult) {
    this._isMercure = requestResult.hasOwnProperty('@mercure')
        && requestResult.hasOwnProperty('@id')
        && requestResult.hasOwnProperty('@token')
        && requestResult.hasOwnProperty('@result');

    if (this._isMercure) {
      this._id = requestResult['@id'];
      this._token = requestResult['@token'];
      this._mercure = requestResult['@mercure'];
      this._result = requestResult['@result'];
      this._callbacks = {};
    } else {
      this._result = requestResult;
    }
  }

  /** @returns {boolean} */
  isMercure() {
    return this._isMercure;
  }

  getResult() {
    return this._result;
  }

  openConnection() {
    if (this.isMercure()) {
      const mercureUrl = new URL(this._mercure);
      mercureUrl.searchParams.append('topic', this._id);
      const auth = `Bearer ${this._token}`;
      const headers = { Authorization: auth };
      const source = new EventSourcePolyfill(mercureUrl, { headers });
      source.addEventListener('message', data => this._handle(data));
    }
  }

  /**
   * @callback handleActionCallback
   * @param {object} data
   * @param {string} actionName
   */

  /**
   * @param {string} actionName
   * @param {handleActionCallback} callback
   */
  onAction(actionName, callback) {
    this._callbacks[actionName] = callback;
  }

  _handle({ data }) {
    const { '@data': result, '@action': action } = JSON.parse(data);
    if (this._callbacks.hasOwnProperty(action)) {
      this._callbacks[action](result);
    } else {
      throw new Error(`Unrecognized action ${action} from Mercure`);
    }
  }
}
