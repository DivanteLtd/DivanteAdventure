import AbstractViewMode from './AbstractViewMode';
import moment from '@divante-adventure/work-moment';

export default class ProjectViewMode extends AbstractViewMode {
  constructor() {
    super('project');
  }

  /**
   * @inheritDoc
   */
  createListStructure(employees, projects, pairings, filters, currentRange) {
    return this.applyFilters(projects, filters)
        .map(project => {
          project.children = pairings
              .filter(pair => {
                if (pair.projectId !== project.id) {
                  return false;
                }
                const startDate = [];
                const endDate = [];
                const datesFrom = pair.dateFrom;
                datesFrom.forEach((val, idx) => {
                  startDate.push(moment(`${val}`, 'MM-YYYY').startOf('month'));
                  endDate.push(moment(`${pair.dateTo[idx]}`, 'MM-YYYY').endOf('month'));
                });
                const disabledMonth = startDate
                    .map((val, idx) => !moment.range(val, endDate[idx]).overlaps(currentRange))
                    .reduce((a, b) => a && b, true);

                return !disabledMonth;
              });
          return project;
        })
      .filter(p => (!moment(p.endedAt).isSameOrBefore(currentRange.start.format())))
      .toArray();
  }

  /**
   * @inheritDoc
   * @param {Project} element
   */
  createElementLabel(element) {
    return element.name || element.projectName;
  }

  /**
   * @inheritDoc
   * @param {Employee} element
   */
  createChildElementLabel(element) {
    if (`${element.employeeName}` !== 'undefined') {
      return `${element.employeeName} ${element.employeeLastName}`;
    } else {
      return `${element.name} ${element.lastName}`;
    }
  }

  /**
   * @inheritDoc
   * @return {Project[]}
   */
  getElementsFromStore(store) {
    return store.state.Planner.projects;
  }

  /**
   * @inheritDoc
   */
  createUrl(store, id) {
    return `/firm/projects/${id}`;
  }

  /**
   * @inheritDoc
   */
  // eslint-disable-next-line
  getWorktime(element, dataMode, format = '%1') {
    return '';
  }
}
