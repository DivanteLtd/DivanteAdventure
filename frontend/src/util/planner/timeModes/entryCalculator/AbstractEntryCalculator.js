import moment from '@divante-adventure/work-moment';
import ViewMode from '../../ViewMode';

/**
 * @typedef {Object} BackendEntry
 * @property {number} id
 * @property {number} employeeId
 * @property {number} projectId
 * @property {number} secondsPerDay
 * @property {number} timestamp
 */

/**
 * @typedef {Object} PlannerEntry
 * @property {moment.Moment} entry - beginning of selected field, i.e. if field is a single week, it should keep first day of week
 * @property {number} secondsPerDay
 * @property {boolean} freeDay
 * @property {string} cssClass - CSS class to use; default ''
 */

const SECONDS_PER_DAY = 28800;

export default class AbstractEntryCalculator {
  constructor() {
    if (this.constructor === AbstractEntryCalculator) {
      throw new TypeError('Abstract class AbstractEntryCalculator cannot be instantiated.');
    }
  }

  /**
   * Returns array of entries to display in selected row. If time mode displays whole month and date="21.11.2018",
   * that method should return entries from 01.11.2018 to 30.11.2018.
   * @param {moment.Moment} date
   * @param {Employee|boolean} employee if 'false', accept all employees
   * @param {Project|boolean} project if 'false', accept all projects
   * @param {Vuex.Store} vuexStore
   * @param {element} element is a employeeProjects pairings
   * @return {PlannerEntry[]}
   */
  createEntries(date, employee, project, vuexStore, element) {
    const entries = vuexStore.state.Planner.entries;
    const leaveDays = vuexStore.state.Planner.leaveDays;
    const isoFreeDays = vuexStore.state.Planner.Time.isoFreeDays;
    const viewMode = vuexStore.state.Planner.viewMode;

    const correctEmployeeMode = viewMode === ViewMode.employee && project !== false;
    const correctProjectMode = viewMode === ViewMode.project && employee !== false;
    if (correctEmployeeMode || correctProjectMode) {
      return this.calculateEntries(date, employee, project, entries, [], isoFreeDays, element);
    }
    else {
      return this.calculateEntries(date, employee, project, entries, leaveDays, isoFreeDays, element);
    }
  }

  /**
   * Returns array of entries to display in selected row. If time mode displays whole month and date =
   * "21.11.2018", that method should return entries from 01.11.2018 to 30.11.2018.
   * @param {moment.Moment} currentDate
   * @param {Employee|boolean} employee if 'false', accept all employees
   * @param {Project|boolean} project if 'false', accept all projects
   * @param {BackendEntry[]} entries
   * @param {Object[]} leaveDays
   * @param {String[]} isoFreeDays
   * @param {Pairing} element
   * @return {PlannerEntry[]}
   */
  // eslint-disable-next-line
  calculateEntries(currentDate, employee, project, entries, leaveDays, isoFreeDays, element) {
    throw new TypeError('You need to create method calculateEntries when extending class AbstractEntryCalculator.');
  }

  /**
   * Returns array of entries to display in report exported to CSV. If time mode displays whole month and date =
   * "21.11.2018", that method should return entries from 01.11.2018 to 30.11.2018.
   * @param {moment.Moment} currentDate
   * @param {Employee|boolean} employee if 'false', accept all employees
   * @param {Project|boolean} project if 'false', accept all projects
   * @param {BackendEntry[]} entries
   * @param {Object[]} leaveDays
   * @param {String[]} isoFreeDays
   * @return {PlannerEntry[]}
   */
  // eslint-disable-next-line
  calculateEntriesForReport(currentDate, employee, project, entries, leaveDays, isoFreeDays) {
    return this.calculateEntries(currentDate, employee, project, entries, leaveDays, isoFreeDays, undefined);
  }

  /**
   * @param {number} employeeId if -1, accept all employees
   * @param {number} projectId if -1, accept all projects
   * @param {BackendEntry[]} backendEntries
   * @param range
   * @param {Object[]} leaveDays
   * @return {number}
   */
  filterEntriesAndSumSeconds(employeeId, projectId, backendEntries, range, leaveDays = []) {
    const leaveDaysRanges = leaveDays.map(day => moment(moment(day.date)).rangeOfDay());
    let leaveDaysCount = leaveDaysRanges.length;
    let entriesSeconds = 0;
    backendEntries.forEach(entry => {
      if (!((projectId === -1 || entry.projectId === projectId)
          && (employeeId === -1 || entry.employeeId === employeeId)
          && range.contains(moment.unix(entry.timestamp)))) {
        return;
      }
      const correctRanges = leaveDaysRanges.filter(range => range.contains(moment.unix(entry.timestamp)));
      if (correctRanges.length > 0) {
        leaveDaysCount--;
        entriesSeconds += SECONDS_PER_DAY;
      }
      else {
        entriesSeconds += entry.secondsPerDay;
      }
    });
    const leaveDaysSeconds = leaveDaysCount * SECONDS_PER_DAY;
    return entriesSeconds + leaveDaysSeconds;
  }

  /**
   * Calculates sum of working seconds for current time mode.
   * @param {moment.Moment} currentDate
   * @param {Employee|Project} entity
   * @param {AbstractViewMode} viewMode
   * @param {BackendEntry[]} entries
   * @param {Object[]} leaveDays
   * @param {String[]} isoFreeDays
   * @param {Pairing} element
   * @return {number}
   */
  getWorkingSeconds(currentDate, entity, viewMode, entries, leaveDays, isoFreeDays, element) {
    const employee = viewMode === ViewMode.employee ? entity : false;
    const project = viewMode === ViewMode.project ? entity : false;
    return this.calculateEntries(currentDate, employee, project, entries, leaveDays, isoFreeDays, element)
        .map(item => item.secondsPerDay)
        .reduce((a, b) => a + b, 0);
  }

  getFirstDate(projects) {
    const firstDate = moment(projects[0].endedAt, 'YYYY-MM-DD');
    return projects.find(p => moment(p.endedAt, 'YYYY-MM-DD').isSameOrBefore(firstDate)).endedAt;
  }
}
