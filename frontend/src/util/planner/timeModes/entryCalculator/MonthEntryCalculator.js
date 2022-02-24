import AbstractEntryCalculator from './AbstractEntryCalculator';
import moment from '@divante-adventure/work-moment';
import { getSuggestedLanguage } from '../../../../i18n/i18n';

export default class MonthEntryCalculator extends AbstractEntryCalculator {
  /**
   * @inheritDoc
   */
  calculateEntries(currentDate, employee, project, entries, leaveDays, isoFreeDays, element) {
    const days = [];
    const employeeId = employee ? employee.id : -1;
    const projectId = project ? project.id : -1;
    let projectEndDate = null;
    const projects = (employee && employee.children) ? [...employee.children] : []; if (typeof project.endedAt !== 'undefined') {
      projectEndDate = moment(project.endedAt, 'YYYY-MM-DD');
    }

    for (let month of currentDate.rangeOfYear().by('month')) {
      const rangeOfMonth = moment(month).rangeOfMonth();
      if (typeof element !== 'undefined') {
        const startDate = [];
        const endDate = [];
        element.dateFrom.forEach((val, idx) => {
          startDate.push(moment(`${val}`, 'MM-YYYY').startOf('month'));

          endDate.push(moment(`${element.dateTo[idx]}`, 'MM-YYYY').endOf('month'));
        });

        let disabledMonth = startDate
            .map((val, idx) => !moment.range(val, endDate[idx]).overlaps(rangeOfMonth))
            .reduce((a, b) => a && b, true);
        if (projectEndDate !== null && projectEndDate.isValid()) {
            disabledMonth = moment(month).isSameOrAfter(projectEndDate.startOf('month'));
        }
        if (disabledMonth) {
          days.push({
            entry: moment(month),
            secondsPerDay: 0,
            freeDay: false,
            cssClass: 'disabled',
          });
          continue;
        }
      }
      month = month.startOf('month').locale(getSuggestedLanguage());
      const employeeLeaveDays = leaveDays
          .filter(day => day.employeeId === employeeId && rangeOfMonth.contains(moment(day.date)));
      const freeDays = employeeLeaveDays.filter(day => day.notAccepted === false);
      const entrySum = this.filterEntriesAndSumSeconds(employeeId, projectId, entries, rangeOfMonth, freeDays);
      let cssClass = '';
      if (moment(month).format('YYYY-MM') === moment().format('YYYY-MM')) {
        cssClass = 'today';
      }
      if (projects.length > 0) {
        const deleted = projects.reduce((total, x) => (x.deleted ? total + 1 : total), 0);
        if (deleted === projects.length && moment(month).isSameOrAfter(moment(this.getFirstDate(projects)).startOf('month'))) {
          cssClass = `${cssClass} disabled`.trim();
        }
      }
      days.push({
        entry: moment(month),
        secondsPerDay: entrySum,
        freeDay: false,
        cssClass,
      });
    }
    return days;
  }
}
