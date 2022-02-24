import AbstractDisplayControl from './AbstractDisplayControl';
import moment from '@divante-adventure/work-moment';
import { getShortWeekday } from '../../../../i18n/i18n';

export default class DayDisplayControl extends AbstractDisplayControl {
  /**
   * @inheritDoc
   */
  getViewRange() {
    return 'month';
  }

  /**
   * @inheritDoc
   */
  getColumnRange() {
    return 'day';
  }

  /**
   * @inheritDoc
   */
  isCopyingFieldAllowed(field) {
    return this.isDeletingFieldAllowed(field);
  }

  /**
   * @inheritDoc
   */
  isDeletingFieldAllowed(field) {
    const momentEntry = field.entry;
    const currentMoment = moment();
    return currentMoment.isBefore(momentEntry) || moment(currentMoment).subtract(1, 'months').isSameOrBefore(momentEntry, 'day');
  }

  /**
   * @inheritDoc
   */
  createDateLabel(date) {
    return date.format('MMMM YYYY');
  }

  /**
   * @inheritDoc
   */
  getTimeShift() {
    return { months: 1 };
  }

  /**
   * @inheritDoc
   */
  createHeaders(argDate, freeDays) {
    const headers = [];
    const self = this;
    for (let date of argDate.rangeOfMonth().by('day')) {
      date = moment(date);
      headers.push({
        text: self.prepareTextForDate(date),
        exportText: date.format('DD.MM.YYYY'),
        value: `day${date.date()}`,
        align: 'center',
        sortable: false,
        freeDay: date.isFreeDay(freeDays),
        beginning: moment(date),
      });
    }
    return headers;
  }

  prepareTextForDate(date) {
    const day = date.format('DD');
    const weekday = getShortWeekday(date.day());
    return `${weekday} ${day}`;
  }
}
