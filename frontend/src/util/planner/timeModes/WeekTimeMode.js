import moment from '@divante-adventure/work-moment';
import AbstractTimeMode from './AbstractTimeMode';
import WeekDisplayControl from './displayControl/WeekDisplayControl';
import WeekEntryCalculator from './entryCalculator/WeekEntryCalculator';

export default class WeekTimeMode extends AbstractTimeMode {
  constructor() {
    super('week', new WeekDisplayControl(), new WeekEntryCalculator());
  }

  /**
   * @inheritDoc
   */
  copyField(source,
            destination,
            store,
            employeeId,
            projectId,
            destEmployeeId = undefined,
            destProjectId = undefined,
            element) {
    source = moment(source);
    destination = moment(destination);
    if (typeof(destEmployeeId) === 'undefined') {
      destEmployeeId = employeeId;
    }
    if (typeof(destProjectId) === 'undefined') {
      destProjectId = projectId;
    }
    if (source.isoDate() === destination.isoDate()) {
      return;
    }
    const eventEntries = [];
    const existingEntries = store.state.Planner.entries
        .filter(entry => entry.employeeId === employeeId && entry.projectId === projectId);
    const freeDays = store.state.Planner.Time.isoFreeDays;
    const destWeekStart = moment(destination).startOf('isoweek');
    for (let sourceDay of source.rangeOf('isoweek').by('day')) {
      sourceDay = moment(sourceDay);
      if (sourceDay.isFreeDay()) {
        continue;
      }
      const destDay = moment(destWeekStart).isoWeekday(sourceDay.isoWeekday());
      if (destDay.isFreeDay(freeDays)) {
        continue;
      }
      const leaveDays = store.state.Planner.leaveDays;
      const availability = leaveDays
          .filter(val => val.date === destDay.format('YYYY-MM-DD')
              && val.employeeId === element.employeeId
              && val.notAccepted === false);
      if(availability.length > 0) {continue;}

      let secondsPerDay = 0;
      const isoSourceDay = sourceDay.isoDate();
      const timeAlreadyUsed = store.state.Planner.entries
          .filter(entry => {
            const timestamp = moment.unix(entry.timestamp).isoDate();
            return entry.employeeId === employeeId && timestamp === destDay.format('YYYYDDDD') && entry.projectId !== destProjectId;
          })
          .map(entry => entry.secondsPerDay)
          .reduce((a, b) => a + b, 0);
      if (freeDays.indexOf(isoSourceDay) === -1) {
        let expectedTime = existingEntries
            .filter(entry => {
              const timestamp = moment.unix(entry.timestamp).isoDate();
              return timestamp === isoSourceDay;
            }).map(entry => entry.secondsPerDay);
        expectedTime = expectedTime.length === 1 ? expectedTime[0] : 0;
        const maxOccupancy = element.jobTimeValue - timeAlreadyUsed > 0 ? element.jobTimeValue - timeAlreadyUsed : 0;
        secondsPerDay = maxOccupancy > expectedTime ? expectedTime : maxOccupancy;
      }
      let dateInPeriod = false;
      element.dateFrom.forEach((value, idx) => {
          if (destDay >= moment(`${value}`, 'MM-YYYY').startOf('month')
              && destDay <= moment(`${element.dateTo[idx]}`, 'MM-YYYY').endOf('month')) {
            dateInPeriod = true;
            }
        });
      if (dateInPeriod === false) {
        secondsPerDay = 0;
      }
      eventEntries.push({
        timestamp: destDay.unix(),
        employeeId: destEmployeeId,
        projectId: destProjectId,
        secondsPerDay,
      });
    }
    store.dispatch('Planner/saveEntries', eventEntries);
  }
}
