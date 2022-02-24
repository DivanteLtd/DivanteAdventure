import AbstractViewMode from './AbstractViewMode';
import moment from '@divante-adventure/work-moment';

export default class EmployeeViewMode extends AbstractViewMode {
  constructor() {
    super('employee');
  }

  /**
   * @inheritDoc
   */
  createListStructure(employees, projects, pairings, filters, currentRange) {
    const hiredEmployees = employees
        .filter(employee => currentRange.start <= moment(employee.hiredTo) || !employee.hiredTo);
    return this.applyFilters(hiredEmployees, filters)
        .map(employee => {
          employee.children = pairings.filter(
              pair => {
                if (pair.employeeId !== employee.id) {
                  return false;
                }
                const startDate = [];
                const endDate = [];
                const datesFrom = pair.dateFrom || [];
                datesFrom.forEach((val, idx) => {
                  startDate.push(moment(`${val}`, 'MM-YYYY').startOf('month'));
                  endDate.push(moment(`${pair.dateTo[idx]}`, 'MM-YYYY').endOf('month'));
                });
                const disabledMonth = startDate
                    .map((val, idx) => !moment.range(val, endDate[idx]).overlaps(currentRange))
                    .reduce((a, b) => a && b, true);

                return !disabledMonth;
              },
          );
          return employee;
        }).toArray();
  }

  /**
   * @inheritDoc
   * @param {Employee} element
   */
  createElementLabel(element) {
    if (`${element.employeeName}` !== 'undefined') {
      return `${element.employeeName} ${element.employeeLastName}`;
    } else {
      return `${element.name} ${element.lastName}`;
    }
  }

  /**
   * @inheritDoc
   * @param {Project} element
   */
  createChildElementLabel(element) {
    return element.name || element.projectName;
  }

  /**
   * @inheritDoc
   * @return {Employee[]}
   */
  getElementsFromStore(store) {
    return store.state.Planner.employees;
  }

  /**
   * @inheritDoc
   */
  createUrl(store, id) {
    return `/firm/employees/${id}`;
  }

  /**
   * @inheritDoc
   */
  getWorktime(element, dataMode, format = '%1') {
    let worktime = 0;
    if (typeof(element.worktime) !== 'undefined') {
      worktime = element.worktime;
    }
    const formatted = dataMode.formatSeconds(worktime);
    return format.replace('%1', formatted);
  }
}
