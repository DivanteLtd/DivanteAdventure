import AbstractDataMode from './AbstractDataMode';

export default class HoursDataMode extends AbstractDataMode {
  constructor() {
    super('hours');
  }

  /**
   * @inheritDoc
   */
  // eslint-disable-next-line
  formatSeconds(seconds, fullTime = 8 * 60 * 60) {
    const secondsInHour = 60 * 60;
    const hours = Math.round(seconds * 10 / secondsInHour) / 10;
    return `${hours}h`;
  }
}
