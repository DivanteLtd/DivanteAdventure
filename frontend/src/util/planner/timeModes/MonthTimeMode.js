import AbstractTimeMode from './AbstractTimeMode';
import MonthDisplayControl from './displayControl/MonthDisplayControl';
import MonthEntryCalculator from './entryCalculator/MonthEntryCalculator';

export default class MonthTimeMode extends AbstractTimeMode {
  constructor() {
    super('month', new MonthDisplayControl(), new MonthEntryCalculator());
  }
}
