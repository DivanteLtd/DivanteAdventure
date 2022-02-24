import DayTimeMode from './timeModes/DayTimeMode';
import WeekTimeMode from './timeModes/WeekTimeMode';
import MonthTimeMode from './timeModes/MonthTimeMode';

/** @type {Object.<string, AbstractTimeMode>} */
export const TimeMode = {
  day: new DayTimeMode(),
  week: new WeekTimeMode(),
  month: new MonthTimeMode(),
};

export default TimeMode;

export function getTimeModeByValue(value) {
  for (const key in TimeMode) {
    if (TimeMode.hasOwnProperty(key)) {
      const obj = TimeMode[key];
      if (obj.value === value) {
        return obj;
      }
    }
  }
  throw new TypeError(`TimeMode with value '${value}' not found.`);
}

export function createTimeModeFilterEntries() {
  const entries = [];
  for (const key in TimeMode) {
    if (TimeMode.hasOwnProperty(key)) {
      const obj = TimeMode[key];
      entries.push({
        value: obj.value,
        label: obj.getFilterLabel(),
      });
    }
  }
  return entries;
}
