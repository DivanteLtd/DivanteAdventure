// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  get: () => restClient.get('statistic/rotate'),
  getGeneral: () => restClient.get('statistic/rotate/general'),
  getRotation: () => restClient.get('statistics/rotation'),
  getRotationForYear: year => restClient.get(`statistics/rotation/${year}`),
  getRotationForMonth: (year, month) => restClient.get(`statistics/rotation/${year}/${month}`),
  getActualStatistics: () => restClient.get('statistics/actual'),
  getCompanyStatsByYearAndTribes: (year, tribes) => {
    return restClient.get(`statistics/planner/getByYear/${year}/${JSON.stringify(tribes)}`);
  },
  getEmployeesByDateAndTribes: (date, tribes) => {
    return restClient.get(`statistics/planner/getEmployeesByDateAndTribes/${date}/${JSON.stringify(tribes)}`);
  },
  getYears: () => restClient.get(`statistics/planner/yearsofactivity`),
  getTribes: () => restClient.get(`statistics/planner/tribes`),
});
