import moment from '@divante-adventure/work-moment';

/**
 * @typedef {Object} Borders
 * @property {moment.Moment} beginning
 * @property {moment.Moment} ending
 */

/**
 * @typedef {Object} HeaderDeclaration
 * @property {string} text - displayed text
 * @property {string|undefined} exportText
 * @property {string} value
 * @property {string} align
 * @property {boolean} freeDay
 * @property {boolean} sortable
 * @property {moment.Moment} beginning
 */

export default class AbstractDisplayControl {
  constructor() {
    if (this.constructor === AbstractDisplayControl) {
      throw new TypeError('Abstract class AbstractTimeMode cannot be instantiated.');
    }
  }

  /**
   * Accepts some moment and returns borders of events (from-to) for given time mode, for example if time mode
   * should download whole month and date="21.11.2018", that method should return object with beginning="01.11.2018"
   * and ending="30.11.2018".
   * @param {moment.Moment} date
   * @return {Borders}
   */
  getBorders(date) {
    return {
      beginning: moment(date.startOf(this.getViewRange())),
      ending: moment(date.endOf(this.getViewRange())),
    };
  }

  /**
   * Returns range of days displayed in whole page, i.e. if you're using month view, it will return "year".
   * @return {string}
   */
  getViewRange() {
    throw new TypeError('You need to create method getViewRange when extending class AbstractDisplayControl.');
  }

  /**
   * Returns range of days displayed in one column, i.e. if you're using month view, it will return "month".
   * @return {string}
   */
  getColumnRange() {
    throw new TypeError('You need to create method getColumnRange when extending class AbstractDisplayControl.');
  }

  /**
   * Returns true if copying field (including dragging) is allowed for this time mode.
   * @param {PlannerEntry} field
   * @return {boolean}
   */
  // eslint-disable-next-line
  isCopyingFieldAllowed(field) {
    throw new TypeError('You need to create method isCopyingFieldAllowed when extending class AbstractDisplayControl.');
  }

  /**
   * Returns true if deleting field is allowed for this time mode.
   * @param {PlannerEntry} field
   * @return {boolean}
   */
  // eslint-disable-next-line
  isDeletingFieldAllowed(field) {
    throw new TypeError('You need to create method isDeletingFieldAllowed when extending class AbstractDisplayControl.');
  }

  /**
   * Returns true if adding cells via double click is allowed for this time mode.
   * @param {PlannerEntry} field
   * @returns {boolean}
   */
  isAddingCellsAllowed(field) {
    const momentEntry = field.entry;
    const currentMoment = moment();
    return currentMoment.isBefore(momentEntry) || moment(currentMoment).subtract(1, 'months').isSameOrBefore(momentEntry, 'day');
  }

  /**
   * @param {moment.Moment} date
   * @return {string|i18nObject}
   */
  // eslint-disable-next-line
  createDateLabel(date) {
    throw new TypeError('You need to create method createDateLabel when extending class AbstractDisplayControl.');
  }

  /**
   * Method returns time shift done during pressing "left" or "right" button. For example if current mode displays
   * three months, then pressing "left" or "right" button should move current time 3 months (back or forward). The
   * return value should then be {months: 3}.
   *
   * Returned object must be acceptable by moment.js "add" and "subtract" methods.
   * @link https://momentjs.com/docs/#/manipulating/add/
   * @return {Object}
   */
  getTimeShift() {
    throw new TypeError('You need to create method getTimeShift when extending class AbstractDisplayControl.');
  }

  /**
   * Returns array of headers used for Vuetify data table. If time mode displays whole month and date="21.11.2018",
   * that method should return headers from 01.11.2018 to 30.11.2018.
   * @param {moment.Moment} date
   * @param {String[]} isoFreeDays
   * @return {HeaderDeclaration[]}
   */
  // eslint-disable-next-line
  createHeaders(date, isoFreeDays) {
    throw new TypeError('You need to create method createHeaders when extending class AbstractDisplayControl.');
  }
}
