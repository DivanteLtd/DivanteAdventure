import AbstractDisplayControl from './AbstractDisplayControl';
import moment from '@divante-adventure/work-moment';
import { getSuggestedLanguage, getSplittedMonth } from '../../../../i18n/i18n';

export default class MonthDisplayControl extends AbstractDisplayControl {
  /**
   * @inheritDoc
   */
  getViewRange() {
    return 'year';
  }

  /**
   * @inheritDoc
   */
  getColumnRange() {
    return 'month';
  }

  /**
   * @inheritDoc
   */
  isCopyingFieldAllowed() {
    return false;
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
    return date.format('YYYY');
  }

  /**
   * @inheritDoc
   */
  getTimeShift() {
    return { year: 1 };
  }

  /**
   * @inheritDoc
   */
  // eslint-disable-next-line
  createHeaders(date, freeDays) {
    const yearStart = moment(date).startOf('year');
    const headers = [];
    for (let i = 0; i < 12; i++) {
      const month = moment(yearStart).add({ months: i }).startOf('month').locale(getSuggestedLanguage());
      const text = getSplittedMonth(month.month()).replace(/\./g, '\u00ad');
      headers.push({
        text,
        exportText: month.format('MM.YYYY'),
        value: `year${text}`,
        align: 'center',
        sortable: false,
        freeDay: false,
        beginning: moment(month),
      });
    }
    return headers;
  }
}
