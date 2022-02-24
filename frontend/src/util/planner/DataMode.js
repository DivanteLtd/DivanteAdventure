import HoursDataMode from './dataModes/HoursDataMode';
import WorkTimeDataMode from './dataModes/WorkTimeDataMode';

/** @type {Object.<string, AbstractDataMode>} */
export const DataMode = {
  hours: new HoursDataMode(),
  worktime: new WorkTimeDataMode(),
};

export function getDataModeByValue(value) {
  for (const key in DataMode) {
    if (DataMode.hasOwnProperty(key)) {
      const obj = DataMode[key];
      if (obj.value === value) {
        return obj;
      }
    }
  }
  throw new TypeError(`DataMode with value '${value}' not found.`);
}

export function createDataModeFilterEntries() {
  const entries = [];
  for (const key in DataMode) {
    if (DataMode.hasOwnProperty(key)) {
      const obj = DataMode[key];
      entries.push({
        value: obj.value,
        label: obj.getFilterLabel(),
      });
    }
  }
  return entries;
}

export default DataMode;
