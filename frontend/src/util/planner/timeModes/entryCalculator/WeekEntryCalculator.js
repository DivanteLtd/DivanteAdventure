import AbstractEntryCalculator from './AbstractEntryCalculator';
import moment from '@divante-adventure/work-moment';

export default class WeekEntryCalculator extends AbstractEntryCalculator {
  /**
   * @inheritDoc
   */
  calculateEntries(currentDate, employee, project, entries, leaveDays, isoFreeDays, element) {
    const days = [];
    const employeeId = employee ? employee.id : -1;
    const projectId = project ? project.id : -1;
    const projects = (employee && employee.children) ? [...employee.children] : [];
    let projectEndDate = null;
    if (typeof project.endedAt !== 'undefined') {
      projectEndDate = moment(project.endedAt, 'YYYY-MM-DD');
    }
    for (const week of moment(currentDate).rangeOfQuarter().by('week')) {
      if (typeof element !== 'undefined') {
        const endWeek = moment(week.endOf('isoweek'));
        const startWeek = moment(week.startOf('isoweek'));
        const currentRange = moment.range(startWeek, endWeek);
        const startDate = [];
        const endDate = [];
        element.dateFrom.forEach((val, idx) => {
          startDate.push(moment(`${val}`, 'MM-YYYY').startOf('month'));
          endDate.push(moment(`${element.dateTo[idx]}`, 'MM-YYYY').endOf('month'));
        });
        let disabledWeek = startDate
            .map((val, idx) => !moment.range(val, endDate[idx]).overlaps(currentRange))
            .reduce((a, b) => a && b, true);

        if (projectEndDate !== null && projectEndDate.isValid()) {
          disabledWeek = moment(week).isSameOrAfter(projectEndDate.startOf('isoweek'));
        }
        if (disabledWeek) {
          days.push({
            entry: moment(week),
            secondsPerDay: 0,
            freeDay: false,
            cssClass: 'disabled',
          });
          continue;
        }
      }
      const employeeLeaveDays = leaveDays
          .filter(day => day.employeeId === employeeId && week.rangeOf('isoweek').contains(moment(day.date)));
      const freeDays = employeeLeaveDays.filter(day => day.notAccepted === false);
      let cssClass = '';
      if (week.rangeOf('isoweek').contains(moment())) {
        cssClass = 'today';
      }
      const entrySum = this.filterEntriesAndSumSeconds(employeeId, projectId, entries, week.rangeOf('isoweek'), freeDays);
      if (projects.length > 0) {
        const deleted = projects.reduce((total, x) => (x.deleted ? total + 1 : total), 0);
        if (deleted === projects.length && moment(week).isSameOrAfter(moment(this.getFirstDate(projects)).startOf('isoweek'))) {
          cssClass = `${cssClass} disabled`.trim();
        }
      }

      days.push({
        entry: moment(week),
        secondsPerDay: entrySum,
        freeDay: false,
        cssClass,
      });
    }
    return days;
  }
}
