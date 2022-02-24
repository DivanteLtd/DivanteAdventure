import RestClient from './restClient';
import agreements from './api/agreements';
import faq from './api/faq';
import criteria from './api/criteria';
import employee from './api/employee';
import employeeProject from './api/employeeProject';
import evidences from './api/evidences';
import filter from './api/filter';
import freeDays from './api/freeDays';
import globalSearch from './api/globalSearch';
import integrations from './api/integrations';
import leave from './api/leave';
import feedback from './api/feedback';
import leavePeriods from './api/leavePeriods';
import leaveRequests from './api/leaveRequests';
import levels from './api/levels';
import links from './api/links';
import news from './api/news';
import notifications from './api/notifications';
import occupancy from './api/occupancy';
import positions from './api/positions';
import potentialEmployees from './api/potentialEmployees';
import project from './api/project';
import report from './api/report';
import statistics from './api/statistics';
import strategies from './api/strategies';
import tribes from './api/tribes';
import checklist from './api/checklist';
import hardware from './api/hardware';
import config from './api/config';
import dictionaries from './api/dictionaries';

export default class ApiClient {
  /**
   * @param {RestClientConfigObject} config
   */
  constructor(config) {
    this._client = new RestClient(config);
  }

  _init() {
    this.agreements = agreements(this._client);
    this.faq = faq(this._client);
    this.criteria = criteria(this._client);
    this.employee = employee(this._client);
    this.employeeProject = employeeProject(this._client);
    this.evidences = evidences(this._client);
    this.filter = filter(this._client);
    this.freeDays = freeDays(this._client);
    this.globalSearch = globalSearch(this._client);
    this.integrations = integrations(this._client);
    this.leave = leave(this._client);
    this.feedback = feedback(this._client);
    this.leavePeriods = leavePeriods(this._client);
    this.leaveRequests = leaveRequests(this._client);
    this.levels = levels(this._client);
    this.links = links(this._client);
    this.news = news(this._client);
    this.notifications = notifications(this._client);
    this.occupancy = occupancy(this._client);
    this.positions = positions(this._client);
    this.potentialEmployees = potentialEmployees(this._client);
    this.project = project(this._client);
    this.report = report(this._client);
    this.statistics = statistics(this._client);
    this.strategies = strategies(this._client);
    this.tribes = tribes(this._client);
    this.checklist = checklist(this._client);
    this.hardware = hardware(this._client);
    this.config = config(this._client);
    this.dictionaries = dictionaries(this._client);
  }

  setLoaded() {
    this._client.setLoaded();
    this._init();
  }

  /** @returns {boolean} */
  isLoaded() {
    return this._client.isLoaded();
  }
}
