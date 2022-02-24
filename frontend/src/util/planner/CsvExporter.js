import moment from '@divante-adventure/work-moment';
import ViewMode from './ViewMode';
import DataMode from './DataMode';
import { reportHeaders } from '../../i18n/i18n';

export default class CsvExporter {
  static exportTableToCsv(context) {
    const viewMode = context.state.viewMode;
    let listStructure = context.getters.listStructure;
    if (viewMode === ViewMode.employee) {
      const currentDate = moment(this.currentDate);
      listStructure = context.getters.listStructure
          .filter(element => moment(element.hiredTo) > moment(`${currentDate.format('YYYY-MM-DD')}`, 'YYYY-MM-DD'));
    }
    const list = listStructure.map(element => {
      const employee = viewMode === ViewMode.employee ? element : false;
      const project = viewMode === ViewMode.project ? element : false;
      const tableEntries = CsvExporter.createTableEntries(employee, project, context);
      const exportEntries = tableEntries.map(entry => CsvExporter.getTextEntry(entry, context));
      exportEntries.unshift(CsvExporter.getWorkedTime(element, tableEntries, context));
      if (viewMode === ViewMode.employee) {
        exportEntries.unshift(CsvExporter.getAvailableWorkingTime(employee, context));
        exportEntries.unshift(viewMode.getWorktime(element, DataMode.hours, '%1').replace('h', ''));
      }
      exportEntries.unshift(viewMode.createElementLabel(element));
      return exportEntries;
    });
    list.unshift(CsvExporter.getTableHeaders(context));
    CsvExporter.exportToCsv(list, 'raport.csv');
  }

  static getAvailableWorkingTime(employee, context) {
    const currentDate = moment(context.state.Time.currentDate);
    const timeMode = context.state.Time.timeMode;
    const viewMode = context.state.viewMode;
    const isoFreeDays = context.state.Time.isoFreeDays;

    const workingDays = currentDate.workingDaysCount(timeMode.displayControl.getViewRange(), isoFreeDays);
    const workingHours = workingDays * parseFloat(viewMode.getWorktime(employee, DataMode.hours));
    return `${workingHours}`;
  }

  static getWorkedTime(element, tableEntries, context) {
    const currentDate = moment(context.state.Time.currentDate);
    const businessDays = tableEntries.filter(c => !c.isWeekend).length;
    const workingDays = tableEntries.filter(c => c.cssClass === '').length;
    const leaveDaysHours = (businessDays - workingDays) * 8;
    const timeMode = context.state.Time.timeMode;
    const viewMode = context.state.viewMode;
    const entries = context.state.entries;
    const leaveDays = context.state.leaveDays;
    const isoFreeDays = context.state.Time.isoFreeDays;

    const workingHours = timeMode
        .getWorkingHours(currentDate, element, viewMode, entries, leaveDays, isoFreeDays) + leaveDaysHours;
    return `${workingHours}`;
  }

  static createTableEntries(employee, project, context) {
    const currentDate = moment(context.state.Time.currentDate);
    const timeMode = context.state.Time.timeMode;
    const entries = context.state.entries;
    const leaveDays = context.state.leaveDays;
    const isoFreeDays = context.state.Time.isoFreeDays;
    return timeMode.entryCalculator
        .calculateEntriesForReport(currentDate, employee, project, entries, leaveDays, isoFreeDays);
  }

  static getTextEntry(entry, context) {
    const timeMode = context.state.Time.timeMode;
    const isoFreeDays = context.state.Time.isoFreeDays;
    const limit = timeMode.getSecondsInColumn(entry.entry, isoFreeDays);
    return DataMode.hours.formatSeconds(entry.secondsPerDay, limit).replace('h', '');
  }

  static getTableHeaders(context) {
    const timeHeaders = context
      .getters['Time/headersByView']
      .map(item => (item.hasOwnProperty('exportText') ? item.exportText : item.text));
    const viewMode = context.state.viewMode;
    return [
      viewMode === ViewMode.employee ? reportHeaders('person') : reportHeaders('project'),
    ]
      .concat(viewMode === ViewMode.employee ? [
        reportHeaders('work time'), reportHeaders('available time'),
      ] : [])
      .concat([reportHeaders('planned')])
      .concat(timeHeaders);
  }

  static exportToCsv(rows, fileName) {
    const csvContent = rows.map(CsvExporter.processRow).join('');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    if (navigator.msSaveBlob) {
      navigator.msSaveBlob(blob, fileName);
    }
    else {
      const link = document.createElement('a');
      const url = URL.createObjectURL(blob);
      link.setAttribute('href', url);
      link.setAttribute('download', fileName);
      link.style.visibility = 'hidden';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  }

  static processRow(row) {
    return `${row
      .map(cell => (cell === null ? '' : cell.toString()))
      .map(cell => cell.replace(/"/g, '""'))
      .map(cell => (cell.search(/[",\n]/g) >= 0 ? `"${cell}"` : cell))
      .join(',')}\n`;
  }
}
