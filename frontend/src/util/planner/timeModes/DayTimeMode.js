import moment from '@divante-adventure/work-moment';
import AbstractTimeMode from './AbstractTimeMode';
import DayDisplayControl from './displayControl/DayDisplayControl';
import DayEntryCalculator from './entryCalculator/DayEntryCalculator';

const SECONDS_IN_16H = 57600;

export default class DayTimeMode extends AbstractTimeMode {
  constructor() {
    super('day', new DayDisplayControl(), new DayEntryCalculator());
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
    if (typeof(destEmployeeId) === 'undefined') {
      destEmployeeId = employeeId;
    }
    if (typeof(destProjectId) === 'undefined') {
      destProjectId = projectId;
    }
    const isoSource = source.isoDate();
    const isoDest = destination.isoDate();
    if (isoSource === isoDest) {
      return;
    }
    const freeDays = store.state.Planner.Time.isoFreeDays;
    if (freeDays.indexOf(isoSource) !== -1 || freeDays.indexOf(isoDest) !== -1) {
      return;
    }
    let entries = store.state.Planner.entries
        .filter(entry => {
          const timestamp = moment.unix(entry.timestamp).isoDate();
          return entry.employeeId === employeeId && entry.projectId === projectId && timestamp === isoSource;
        }).map(entry => entry.secondsPerDay);
    const existedEntry = store.state.Planner.entries
        .filter(entry => {
          const timestamp = moment.unix(entry.timestamp).isoDate();
          return entry.employeeId === employeeId && timestamp === isoDest && entry.projectId !== destProjectId;
        })
        .map(entry => entry.secondsPerDay)
        .reduce((a, b) => a + b, 0);
    if (entries.length === 0) {
      entries = [0];
    }
    if (entries.length !== 1) {
      return;
    }
    let maxEntries = 0;
    if (element.overtime === false) {
      maxEntries = element.jobTimeValue - existedEntry > 0 ? element.jobTimeValue - existedEntry : 0;
    } else {
      maxEntries = SECONDS_IN_16H - existedEntry > 0 ? SECONDS_IN_16H - existedEntry : 0;
    }
    const secondsPerDay = maxEntries > entries[0] ? entries[0] : maxEntries;
    const storeEntry = {
      timestamp: destination.unix(),
      employeeId: destEmployeeId,
      projectId: destProjectId,
      secondsPerDay,
    };
    const storeEntries = [storeEntry];
    store.dispatch('Planner/saveEntries', storeEntries);
  }
}
