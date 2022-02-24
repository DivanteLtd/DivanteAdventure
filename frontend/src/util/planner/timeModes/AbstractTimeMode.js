import AbstractFilterMode from '../AbstractFilterMode';
import moment from '@divante-adventure/work-moment';

/**
 * @class AbstractTimeMode
 * @property {AbstractDisplayControl} displayControl
 * @property {AbstractEntryCalculator} entryCalculator
 */
export default class AbstractTimeMode extends AbstractFilterMode {
  /**
   * AbstractTimeMode constructor
   * @param {string} value
   * @param {AbstractDisplayControl} displayControl
   * @param {AbstractEntryCalculator} entryCalculator
   */
  constructor(value, displayControl, entryCalculator) {
    super(value);
    this.displayControl = displayControl;
    this.entryCalculator = entryCalculator;
    if (this.constructor === AbstractTimeMode) {
      throw new TypeError('Abstract class AbstractTimeMode cannot be instantiated.');
    }
  }

  /**
   * Copy field from source date to destination date for given employee and project.
   * @param {moment.Moment} source
   * @param {moment.Moment} destination
   * @param {Vuex.Store} store
   * @param employeeId
   * @param projectId
   * @param destEmployeeId
   * @param destProjectId
   * @param element
   */
  // eslint-disable-next-line
  copyField(source, destination, store, employeeId, projectId, destEmployeeId = undefined, destProjectId = undefined, element) {
    throw new TypeError("You either tried to run 'copyField' method on time mode that returns 'false' on 'isCopyingFieldAllowed' or didn't override 'copyField' method in time mode which allows copying.");
  }

  /**
   * Calculates working seconds for given column.
   * @param {moment.Moment} time
   * @param {Vuex.Store} isoFreeDays
   * @return {number}
   */
  getSecondsInColumn(time, isoFreeDays) {
    return moment(time).workingSeconds(this.displayControl.getColumnRange(), isoFreeDays);
  }

  /**
   * Calculates working seconds for all displayed columns.
   * @param {moment.Moment} time
   * @param {string[]} isoFreeDays
   * @return {number}
   */
  getSecondsInView(time, isoFreeDays) {
    return moment(time).workingSeconds(this.displayControl.getViewRange(), isoFreeDays);
  }

  /**
   * Calculates working days for all displayed columns.
   * @param {moment.Moment} time
   * @param {Vuex.Store} store
   * @return {number}
   */
  getWorkingDaysInView(time, store) {
    return moment(time).workingDaysCount(this.displayControl.getViewRange(), store.state.Planner.Time.isoFreeDays);
  }

  /**
   * Calculates sum of working hours for current time mode.
   * @param {moment.Moment} currentDate
   * @param {Employee|Project} entity
   * @param {Vuex.Store} vuexStore
   * @return {number}
   */
  getWorkingHoursFromStore(currentDate, entity, vuexStore, element) {
    const workingSeconds = this.getWorkingSecondsFromStore(currentDate, entity, vuexStore, element);
    return Math.round((workingSeconds * 10) / 3600) / 10;
  }

  /**
   * Calculates sum of working seconds for current time mode.
   * @param {moment.Moment} currentDate
   * @param {Employee|Project} entity
   * @param {Vuex.Store} vuexStore
   * @return {number}
   */
  getWorkingSecondsFromStore(currentDate, entity, vuexStore, element) {
    const viewMode = vuexStore.state.Planner.viewMode;
    const entries = vuexStore.state.Planner.entries;
    const leaveDays = vuexStore.state.Planner.leaveDays;
    const isoFreeDays = vuexStore.state.Planner.Time.isoFreeDays;
    return this.entryCalculator
        .getWorkingSeconds(currentDate, entity, viewMode, entries, leaveDays, isoFreeDays, element);
  }


  /**
   * Calculates sum of working hours for current time mode.
   * @param {moment.Moment} currentDate
   * @param {Employee|Project} entity
   * @param {AbstractViewMode} viewMode
   * @param {BackendEntry[]} entries
   * @param {Object[]} leaveDays
   * @param {String[]} isoFreeDays
   * @return {number}
   */
  getWorkingHours(currentDate, entity, viewMode, entries, leaveDays, isoFreeDays, element) {
    const workingSeconds = this.entryCalculator
        .getWorkingSeconds(currentDate, entity, viewMode, entries, leaveDays, isoFreeDays, element);
    return Math.round((workingSeconds * 10) / 3600) / 10;
  }
}
