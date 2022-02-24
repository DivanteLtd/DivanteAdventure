import AbstractEntryCalculator from './AbstractEntryCalculator';
import moment from '@divante-adventure/work-moment';

const SECONDS_IN_8H = 28800;

export default class DayEntryCalculator extends AbstractEntryCalculator {
  /**
   * @inheritDoc
   */
  // eslint-disable-next-line no-unused-vars
  createEntries(date, employee, project, vuexStore, element) {
    const entries = vuexStore.state.Planner.entries;
    const leaveDays = vuexStore.state.Planner.leaveDays;
    const isoFreeDays = vuexStore.state.Planner.Time.isoFreeDays;
    return this.calculateEntries(date, employee, project, entries, leaveDays, isoFreeDays);
  }

  /**
   * @inheritDoc
   */
  calculateEntriesForReport(currentDate, employee, project, entries, leaveDays, isoFreeDays) {
    const days = [];
    const employeeId = employee ? employee.id : -1;
    const projectId = project ? project.id : -1;
    for (let date of currentDate.rangeOfMonth().by('day')) {
      date = moment(date);
      const freeDays = leaveDays
          .filter(day => day.notAccepted === false && day.employeeId === employeeId && day.date === date.format('YYYY-MM-DD'));

      let cssClass = leaveDays
          .map(day => {
            if (day.employeeId === employeeId && day.date === date.format('YYYY-MM-DD')) {
              return day.type + (day.notAccepted ? '-planned' : '');
            }
            else {
              return '';
            }
          })
          .reduce((a, b) => `${a} ${b}`, '')
          .trim();

      const isFreeDay = date.isFreeDay(isoFreeDays);
      if (isFreeDay) {
        cssClass = `${cssClass} freeDay`.trim();
      }
      const entrySum = this.filterEntriesAndSumSeconds(employeeId, projectId, entries, date.rangeOfDay());
      let secondsPerDay = 0;
      if (!isFreeDay) {
        secondsPerDay = freeDays.length > 0 ? SECONDS_IN_8H : entrySum;
      }
      days.push({
        isWeekend: date.isFreeDay(isoFreeDays),
        entry: moment(date),
        secondsPerDay,
        freeDay: isFreeDay || freeDays.length > 0,
        cssClass,
      });
    }
    return days;
  }

  /**
   * @inheritDoc
   */
  // eslint-disable-next-line no-unused-vars
  calculateEntries(currentDate, employee, project, entries, leaveDays, isoFreeDays, element) {
    const days = [];
    const employeeId = employee ? employee.id : -1;
    const projectId = project ? project.id : -1;
    let projectEndDate = null;
    if (typeof project.endedAt !== 'undefined') {
      projectEndDate = moment(project.endedAt, 'YYYY-MM-DD');
    }
    for (let date of currentDate.rangeOfMonth().by('day')) {
      date = moment(date);
      const freeDays = leaveDays
          .filter(day => day.notAccepted === false && day.employeeId === employeeId && day.date === date.format('YYYY-MM-DD'));

      let cssClass = leaveDays
          .map(day => {
            if (day.employeeId === employeeId && day.date === date.format('YYYY-MM-DD')) {
              return day.type + (day.notAccepted ? '-planned' : '');
            }
            else {
              return '';
            }
          })
          .reduce((a, b) => `${a} ${b}`, '')
          .trim();
      if (date.format('YYYY-MM-DD') === moment().format('YYYY-MM-DD') && cssClass === '') {
        cssClass = 'today';
      }
      const isFreeDay = date.isFreeDay(isoFreeDays);
      const isWeekend = date.day() === 0 || date.day() === 6;
      if (isWeekend) {
        cssClass = `${cssClass} weekend`.trim();
      } else if (isFreeDay) {
        cssClass = `${cssClass} freeDay`.trim();
      }
      if (projectEndDate !== null && projectEndDate.isValid()) {
        if(date.isSameOrAfter(projectEndDate.startOf('day'))) {
          cssClass = `${cssClass} disabled`.trim();
        }
      }
      if (!!employee.children && employee.children.length > 0) {
        const deleted = employee.children.reduce((total, x) => (x.deleted ? total + 1 : total), 0);
        if (deleted === employee.children.length && date.isSameOrAfter(this.getFirstDate(employee.children))) {
          cssClass = `${cssClass} disabled`.trim();
        }
      }
      const disable = cssClass.search('disabled') !== -1;
      const entrySum = this.filterEntriesAndSumSeconds(employeeId, projectId, entries, date.rangeOfDay());
      days.push({
        isWeekend: date.isFreeDay(isoFreeDays),
        entry: moment(date),
        secondsPerDay: (isFreeDay || freeDays.length > 0) ? 0 : entrySum,
        freeDay: disable || isFreeDay || freeDays.length > 0,
        cssClass,
      });
    }
    return days;
  }
}
