import request from 'request';

/**
 * @typedef {Object} RestClientConfigObject
 * @property {string} token
 * @property {string} language
 */

/**
 * @typedef {Object} RequestData
 * @property {string} url
 * @property {string} method
 * @property {Object|Array} [body]
 */

export default class RestClient {
  /**
   * @param {RestClientConfigObject} config
   */
  constructor(config) {
    this._token = config.token || '';
    this._lang = config.language || 'en';
    this._loaded = false;
  }

  setLoaded() {
    this._loaded = true;
  }

  /** @returns {boolean} */
  isLoaded() {
    return this._loaded;
  }

  /**
   * @param {string} resourceUrl
   * @returns {Promise}
   */
  get(resourceUrl) {
    return this._doRequest(resourceUrl, 'GET');
  }

  /**
   * @param {string} resourceUrl
   * @param {?Array|?Object} data
   * @returns {Promise}
   */
  post(resourceUrl, data = undefined) {
    return this._doRequest(resourceUrl, 'POST', data);
  }

  /**
   * @param {string} resourceUrl
   * @param {?Array|?Object} data
   * @returns {Promise}
   */
  put(resourceUrl, data = undefined) {
    return this._doRequest(resourceUrl, 'PUT', data);
  }

  /**
   * @param {string} resourceUrl
   * @param {?Array|?Object} data
   * @returns {Promise}
   */
  patch(resourceUrl, data = undefined) {
    return this._doRequest(resourceUrl, 'PATCH', data);
  }

  /**
   * @param {string} resourceUrl
   * @returns {Promise}
   */
  delete(resourceUrl) {
    return this._doRequest(resourceUrl, 'DELETE');
  }

  /**
   * @param {string} resourceUrl
   * @param {string} method
   * @param {?Array|?Object} data
   * @returns {Promise}
   * @private
   */
  _doRequest(resourceUrl, method, data = undefined) {
    const url = this._createUrl(resourceUrl);
    /** @type {RequestData} */
    const requestData = { url, method };
    if (typeof data !== 'undefined') {
      requestData.body = data;
    }
    return this._apiCall(requestData);
  }

  /**
   * @param {string} resourceUrl
   * @returns {string}
   * @private
   */
  _createUrl(resourceUrl) {
    let url = `${window.ADVENTURE_BACKEND_URL}/api`;
    if (resourceUrl !== '') {
      url = `${url}/${resourceUrl}`;
    }
    return url;
  }

  /**
   * @param {RequestData} requestData
   * @returns {Promise<any>}
   * @private
   */
  _apiCall(requestData) {
    return new Promise((resolve, reject) => {
      request({
        url: requestData.url,
        method: requestData.method,
        json: true,
        body: requestData.body,
        headers: {
          Authorization: `Bearer ${this._token}`,
          AdventureLanguage: this._lang,
        },
      }, (error, response, body) => {
        if (error) {
          reject(error);
        } else if (!this._httpCallSucceeded(response)) {
          const code = response.code || response.statusCode;
          if (code === 503) { // maintenance mode activated
            window.location.reload();
          }
          let errorMessage = `HTTP ERROR ${response.code}`;
          if(body && body.hasOwnProperty('message')) {
            const params = body.hasOwnProperty('parameters') ? body.parameters : {};
            errorMessage = this._errorString(body.message, params);
          }
          reject(errorMessage);
        } else {
          resolve(body);
        }
      });
    });
  }

  /**
   * @param {Object} response
   * @param {number} response.statusCode
   * @returns {boolean}
   * @private
   */
  _httpCallSucceeded(response) {
    return response.statusCode >= 200 && response.statusCode < 300;
  }

  /**
   * @param {string} message
   * @param {?Object|?Array} parameters
   * @returns {string}
   * @private
   */
  _errorString(message, parameters) {
    if (parameters === null) {
      return message;
    }
    if (parameters instanceof Array) {
      for (let i = 0; i < parameters.length; i++) {
        const parameterPlaceholder = `%${(i + 1).toString()}`;
        message = message.replace(parameterPlaceholder, parameters[i]);
      }
    } else if (parameters instanceof Object) {
      for (const key in parameters) {
        if (parameters.hasOwnProperty(key)) {
          const parameterPlaceholder = `%${key}`;
          message = message.replace(parameterPlaceholder, parameters[key]);
        }
      }
    }
    return message;
  }
}
