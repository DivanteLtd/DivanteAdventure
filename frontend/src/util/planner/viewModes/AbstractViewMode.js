import AbstractFilterMode from '../AbstractFilterMode';
import Stream from '@divante/js-stream';

/**
 * @typedef {Object} Employee
 * @property {number} id
 * @property {string} name
 * @property {string} lastName
 * @property {number} worktime work time per day in seconds
 * @property {String[]} filters
 */
/**
 * @typedef {Object} Project
 * @property {number} id
 * @property {string} name
 */

/**
 * @typedef {Object} Pairing
 * @property {number} id
 * @property {number} projectId
 * @property {string} projectName
 * @property {number} employeeId
 * @property {string} employeeName
 * @property {string} employeeLastName
 * @property {boolean} overtime
 * @property {array} dateFrom
 * @property {array} dateTo
 */
/**
 * @typedef {Project|Employee} ListStructureElement
 * @property {Project[]|Employee[]} children
 */

export default class AbstractViewMode extends AbstractFilterMode {
  constructor(value) {
    super(value);
    if (this.constructor === AbstractViewMode) {
      throw new TypeError('Abstract class AbstractViewMode cannot be instantiated.');
    }
  }

  /**
   * Creates correct list structure to be displayed on left side of planner.
   * @param {Employee[]} employees
   * @param {Project[]} projects
   * @param {Pairing[]} pairings
   * @param {SelectedFilter[]} filters
   * @return {ListStructureElement[]}
   */
  // eslint-disable-next-line
  createListStructure(employees, projects, pairings, filters, currentRange) {
    throw new TypeError('You need to create method createListStructure when extending class AbstractViewMode.');
  }

  /**
   * Apply selected filters to elements
   * @param {Employee[]|Project[]} elements
   * @param {SelectedFilter[]} filters
   * @return {Stream}
   */
  applyFilters(elements, filters) {
    if (filters.length === 0) {
      return Stream(elements);
    }
    return Stream(elements).filter(element => {
      const filtersResults = filters.map(filter => filter.filter.apply(element, filter.enteredText));
      for(let i = 0; i < filtersResults.length; i++) {
        if (!filtersResults[i]) {
          return false;
        }
      }
      return true;
    });
  }

  /**
   * Creates label for primary element.
   * @param {Employee|Project} element
   * @return {string}
   */
  // eslint-disable-next-line
  createElementLabel(element) {
    throw new TypeError('You need to create method createElementLabel when extending class AbstractViewMode.');
  }

  /**
   * Returns array of primary elements from store.
   * @param {Vuex.Store} store
   * @return {Employee[]|Project[]}
   */
  // eslint-disable-next-line
  getElementsFromStore(store) {
    throw new TypeError('You need to create method getElementsFromStore when extending class AbstractViewMode.');
  }

  /**
   * Returns URL to employee/project for router
   * @param {Vuex.Store} store
   * @param {Number} id
   * @return {string}
   */
  // eslint-disable-next-line
  createUrl(store, id) {
    throw new TypeError('You need to create method createUrl when extending class AbstractViewMode.');
  }

  /**
   * Returns formatted worktime or empty string, if element doesn't support worktime. Formatted worktime will be inserted
   * into `format` argument in place of "%1".
   * @param {Employee|Project} element
   * @param {AbstractDataMode} dataMode
   * @param {string} format
   * @return {string}
   */
  // eslint-disable-next-line
  getWorktime(element, dataMode, format = '%1') {
    throw new TypeError('You need to create method getWorktime when extending class AbstractViewMode.');
  }
}
