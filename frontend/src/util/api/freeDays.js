// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';
import moment from '@divante-adventure/work-moment';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  get: () => restClient.get('freeDays'),
  getByStartAndEndTimestamp: (start, end) => {
    const startYear = moment.unix(start).year() - 1;
    const endYear = moment.unix(end).year() + 1;
    return restClient.get(`freeDays?year_gte=${startYear}&year_lt=${endYear}`)
        .then(days => days.map(day => moment(day).unix()));
  },
  getByStartAndEndYear: (start, end) => restClient.get(`freeDays?year_gte=${start}&year_lte=${end}`),
  getEntries: () => restClient.get('freeDays/entries'),
  switchEntry: id => restClient.post(`freeDays/entries/${id}/switch`),
  deleteEntry: id => restClient.delete(`freeDays/entries/${id}`),
  createEntry: ({ date, name, repeating }) => restClient.post('freeDays/entries', { date, name, repeating }),
});
