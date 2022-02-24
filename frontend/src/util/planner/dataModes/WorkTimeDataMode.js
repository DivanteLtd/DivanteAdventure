import AbstractDataMode from './AbstractDataMode';

export default class WorkTimeDataMode extends AbstractDataMode {
  constructor() {
    super('worktime');
  }

  /**
   * @inheritDoc
   */
  formatSeconds(seconds, fullTime = 8 * 60 * 60) {
    if (fullTime === 0) {return (0).toFixed(2);}
    return (seconds / fullTime).toFixed(2);
  }
}
