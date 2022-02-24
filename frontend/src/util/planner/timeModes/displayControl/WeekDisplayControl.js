import AbstractDisplayControl from './AbstractDisplayControl';
import moment from '@divante-adventure/work-moment';

export default class WeekDisplayControl extends AbstractDisplayControl {
  /**
   * @inheritDoc
   */
  getViewRange() {
    return 'quarter';
  }

  /**
   * @inheritDoc
   */
  getColumnRange() {
    return 'week';
  }

  /**
   * @inheritDoc
   */
  isCopyingFieldAllowed(field) {
    const momentEntry = field.entry;
    const currentMoment = moment();
    return currentMoment.isBefore(momentEntry) || moment(currentMoment).subtract(1, 'months').isSameOrBefore(momentEntry, 'day');
  }

  /**
   * @inheritDoc
   */
  isDeletingFieldAllowed() {
    return false;
  }

  /**
   * @inheritDoc
   */
  createDateLabel(date) {
    const quarter = date.quarter();
    const quarterRoman = (() => { switch(date.quarter()) {
      case 1: return 'I';
      case 2: return 'II';
      case 3: return 'III';
      case 4: return 'IV';
      default: return '';
    }})();
    const year = date.format('YYYY');
    return { i18n: 'nth-quarter', params: { quarter, quarterRoman, year } };
  }

  /**
   * @inheritDoc
   */
  getTimeShift() {
    return { months: 3 };
  }

  /**
   * @inheritDoc
   */
  // eslint-disable-next-line
  createHeaders(date, freeDays) {
    const headers = [];
    for (let week of moment(date).rangeOfQuarter().by('week')) {
      week = moment(week.startOf('isoweek'));
      const weekEnd = moment(week).endOf('isoweek');
      const text = `${week.format('DD.MM')} - ${weekEnd.format('DD.MM')}`;
      const exportText = `${week.format('DD.MM.YYYY')} - ${weekEnd.format('DD.MM.YYYY')}`;
      headers.push({
        text,
        exportText,
        value: `week${text}`,
        align: 'center',
        sortable: false,
        freeDay: false,
        beginning: moment(week),
      });
    }
    return headers;
  }
}
