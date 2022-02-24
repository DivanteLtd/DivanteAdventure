import ProjectViewMode from './viewModes/ProjectViewMode';
import EmployeeViewMode from './viewModes/EmployeeViewMode';

/** @type {Object.<string, AbstractViewMode>} */
const ViewMode = {
  project: new ProjectViewMode(),
  employee: new EmployeeViewMode(),
};
ViewMode.project.opposite = ViewMode.employee;
ViewMode.employee.opposite = ViewMode.project;
export default ViewMode;

export function getViewModeByValue(value) {
  for (const key in ViewMode) {
    if (ViewMode.hasOwnProperty(key)) {
      const obj = ViewMode[key];
      if (obj.value === value) {
        return obj;
      }
    }
  }
  throw new TypeError(`ViewMode with value '${value}' not found.`);
}

export function createViewModeFilterEntries() {
  const entries = [];
  for (const key in ViewMode) {
    if (ViewMode.hasOwnProperty(key)) {
      const obj = ViewMode[key];
      entries.push({
        value: obj.value,
        label: obj.getFilterLabel(),
      });
    }
  }
  return entries;
}
