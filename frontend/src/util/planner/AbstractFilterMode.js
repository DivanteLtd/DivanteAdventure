/**
 * @typedef {Object} i18nObject
 * @property {string} i18n
 * @property {object} params
 */

/**
 * @class AbstractFilterMode
 * @property {string} this.value
 */
export default class AbstractFilterMode {
  constructor(value) {
    if (this.constructor === AbstractFilterMode) {
      throw new TypeError('Abstract class AbstractFilterMode cannot be instantiated.');
    }
    this.value = value;
  }

  /**
   * Returns label to be used as representation of mode in filter buttons.
   * @return {string}
   */
  getFilterLabel() {
    return this.value.charAt(0).toUpperCase() + this.value.slice(1);
  }
}
