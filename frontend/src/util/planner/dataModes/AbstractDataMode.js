import AbstractFilterMode from '../AbstractFilterMode';

export default class AbstractDataMode extends AbstractFilterMode {
  constructor(value) {
    super(value);
    if (this.constructor === AbstractDataMode) {
      throw new TypeError('Abstract class AbstractDataMode cannot be instantiated.');
    }
  }

  /**
   * Formatting seconds to correct data entry, for example if hour mode is selected, (4 * 60 * 60) seconds will be
   * formatted as "4h", while in worktime mode with full worktime = 8h it will be formatted as "0.5".
   * @param {number} seconds - seconds to be parsed.
   * @param {number} fullTime - number of seconds treated as a full work time. Default value is 8 hours.
   * @return {string}
   */
  // eslint-disable-next-line
  formatSeconds(seconds, fullTime = 8 * 60 * 60) {
    throw new TypeError('You need to create method formatSeconds when extending class AbstractDataMode.');
  }
}
