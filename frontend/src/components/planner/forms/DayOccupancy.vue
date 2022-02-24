<template>
  <dialog-form :show-event="showEvent" :title="$t('Occupancy')" max-width="400" @show="show" @save="save">
    <v-col md="4">
      {{ $t('Date') }}
    </v-col>
    <v-col md="8">
      {{ displayDate }}
    </v-col>
    <v-col md="4">
      {{ viewMode === ViewMode.project ? $t('Project') : $t('Employee') }}
    </v-col>
    <v-col md="8">
      {{ viewMode.createElementLabel(primaryElement) }}
    </v-col>
    <v-col md="12">
      <v-divider></v-divider>
    </v-col>
    <v-col md="4">
      <b>{{ viewMode === ViewMode.project ? $t('Employee') : $t('Project') }}</b>
    </v-col>
    <v-col md="8">
      <b>{{ $t('Hours') }}</b>
    </v-col>
    <v-col md="12" v-for="(item, index) in connectedElements" :key="index">
      <v-row no-gutters wrap class="pa-0">
        <v-col md="4" class="mt-2 pa-0">
          {{ viewMode.opposite.createElementLabel(item) }}
        </v-col>
        <v-col md="8" class="ma-0 pa-0">
          <v-autocomplete v-if="timeMode.value !== 'day'"
                          v-model="item.time"
                          class="ma-0 pa-0"
                          :items="allowedSummaryByMode(index)"
                          @change="checkTime"
                          item-text="Description"
                          item-value="entry"
                          :no-data-text="$t('No data available')"
                          :rules="[rules.valueInLimit(index), validateTime]"
                          required/>
          <v-text-field v-if="timeMode.value === 'day'"
                        ref="time"
                        :disabled="checkAvailability(item)"
                        v-model="item.time"
                        @change="checkTime"
                        @input="checkTime"
                        @click="setLastElement(index)"
                        class="ma-0 pa-0"
                        :rules="[rules.correctTimeFormat, rules.valueInLimit(index), rules.integersOnly, validateTime]"
                        required autofocus/>
        </v-col>
      </v-row>
    </v-col>
    <v-alert
      v-if="maxHoursError"
      dense
      outlined
      type="error"
    >
      {{ $t('total_values_error', [maxTimePerDay]) }}
    </v-alert>
  </dialog-form>
</template>

<script>
  import DialogForm from './DialogForm';
  import moment from '@divante-adventure/work-moment';
  import { allModesMixin } from '../../../mixins/PlannerMixins';
  import { eventNames } from '../../../eventbus';
  import ViewMode from '../../../util/planner/ViewMode';
  import DataMode from '../../../util/planner/DataMode';
  import { mapState } from 'vuex';

  const SECONDS_IN_HOUR = 3600;
  const MAX_HOURS_IN_DAY = 16;

  export default {
    name: 'DayOccupancy',
    components: { DialogForm },
    mixins: [allModesMixin],
    data() {
      return {
        shortDateFormat: 'DD.MM.YYYY',
        maxHoursPerDay: 16,
        showEvent: eventNames.plannerEntryClicked,
        connectedElements: [],
        date: moment(),
        primaryElement: {},
        jobTimeValue: [],
        validDates: [],
        validateTime: true,
        maxHoursError: false,
        allEntries: 0,
        maxTimePerDay: 0,
        focus: false,
        lastElement: {},
        rules: {
          integersOnly: value => {
            // == instead of ===, because value and parseInt(value) have different types
            // eslint-disable-next-line eqeqeq
            return (value == parseInt(value))
              || this.$t('Integers only');
          },
          correctTimeFormat: value => {
            return (!isNaN(value) && parseInt(value) >= 0)
              || this.$t('Invalid value');
          },
          valueInLimit: index => value => {
            let maxHoursPerDay = 0;
            if (this.timeMode.value === 'day') {
              const overtime = this.primaryElement.children.filter(v => v.overtime === true);
              if (this.connectedElements.length === 1) {
                if (this.allEntries === undefined) {
                  this.allEntries = 0;
                }
                maxHoursPerDay = this.maxHoursPerDay - this.allEntries;
                if (this.connectedElements[0].overtime === false) {
                  maxHoursPerDay = this.jobTimeValue[0] - this.allEntries;
                }
                maxHoursPerDay += overtime.length * 8;
                this.validateTime = (!isNaN(value) && (parseInt(value) <= maxHoursPerDay || parseInt(value) === 0));
                return this.validateTime ? true : this.$t('Value is too big');
              } else if (this.viewMode === ViewMode.employee) {
                const workTime = this.primaryElement.worktime / SECONDS_IN_HOUR;
                if (overtime.length > 0) {
                  maxHoursPerDay = MAX_HOURS_IN_DAY;
                } else {
                  maxHoursPerDay = workTime;
                }
                const sumHours = this.primaryElement.children.reduce((a, b) => Number(a.time) + Number(b.time));
                this.validateTime = (!isNaN(value) && (sumHours <= maxHoursPerDay));
                if (this.validateTime === false) {
                  return this.$t('Value is too big');
                }
                return true;
              } else {
                this.validateTime = (!isNaN(value)
                  && (parseInt(value) <= this.jobTimeValue[index] || parseInt(value) === 0));
                return this.validateTime ? true : this.$t('Value is too big');
              }
            } else {
              const availableEntries = this.allowedSummaryByMode(index);
              const entry = availableEntries.filter(val => val.entry === value);
              if (entry.length === 0) {
                return this.$t('Invalid value');
              }
              if (this.viewMode === ViewMode.employee) {
                return (!isNaN(value) && (parseInt(value) <= this.maxTimePerDay || parseInt(value) === 0))
                  || this.$t('Value is too big');
              } else {
                const jobTimeValue = this.jobTimeValue[index] * SECONDS_IN_HOUR;
                return (!isNaN(value) && (parseInt(value) <= jobTimeValue || parseInt(value) === 0))
                  || this.$t('Value is too big');
              }
            }
          },
        },
        ViewMode,
      };
    },
    computed: {
      ...mapState({
        freeDays: state => state.Planner.Time.isoFreeDays,
        leaveDays: state => state.Planner.leaveDays,
        allPairings: state => state.Planner.pairings,
        allProjects: state => state.Planner.projects,
      }),
      displayDate() {
        return this.date.format(this.shortDateFormat);
      },
    },
    methods: {
      checkProject(item) {
        const project = this.allProjects.find(val => val.id === item.projectId);
        if (project.delete) {
          const endedAt = moment(project.endedAt, 'YYYY-MM-DD');
          return !this.date.isSameOrAfter(endedAt);
        }
        return true;
      },
      checkAvailability(item) {
        return this.leaveDays
          .filter(val => val.date === this.date.format('YYYY-MM-DD')
            && val.employeeId === item.employeeId
            && val.notAccepted === false).length > 0;
      },
      setLastElement(lastElement) {
        this.lastElement = this.$refs.time[lastElement];
      },
      checkTime() {
        this.maxHoursError = false;
        if (typeof this.$refs.time !== 'undefined') {
          this.$refs.time.forEach(val => {
            if (this.lastElement !== val) {
              val.resetValidation();
            }
          });
        }
        this.validateTime = true;
        if (this.connectedElements.length > 0 && this.timeMode.value === 'day') {
          this.validateTime = (this.connectedElements.time - this.allEntries) <= this.jobTimeValue[0];
        } else if (this.connectedElements.length > 1 && this.maxTimePerDay > 0
          && (this.connectedElements.reduce((acc, obj) => acc + obj.time, 0) > this.maxTimePerDay)) {
          this.validateTime = false;
          this.maxHoursError = true;
        }
        return this.validateTime ? true : this.$t('Value is too big');
      },
      allowedSummaryByMode(index) {
        let dates = this.date.workingDays(this.timeMode.displayControl.getColumnRange(), this.freeDays);
        if (this.timeMode.value === 'week') {
          const weekTimeDates = [];
          dates.forEach(val => {
            this.connectedElements[index].dateFrom.forEach((value, idx) => {
              if (val >= moment(`${value}`, 'MM-YYYY').startOf('month')
                && val <= moment(`${this.connectedElements[index].dateTo[idx]}`, 'MM-YYYY').endOf('month')) {
                weekTimeDates.push(val);
              }
            });
          });
          dates = weekTimeDates;
        }
        const countOfDates = dates.length;
        const allowedEntry = [];
        const jobTimeValueEntry = this.viewMode === this.ViewMode.employee
          ? this.jobTimeValue[0]
          : this.jobTimeValue[index];
        for (let i = 0; i <= jobTimeValueEntry; i++) {
          allowedEntry.push(countOfDates * i);
        }
        this.maxTimePerDay = allowedEntry[allowedEntry.length - 1];
        return allowedEntry.map(entry => {
          const Description = this.$t('hours_per_day', [entry, entry / allowedEntry[1]]);
          return Object.assign({}, { entry }, { Description });
        });
      },
      save() {
        if (this.timeMode.value === 'week' || this.timeMode.value === 'month') {
          if (this.connectedElements.length > 1 && this.viewMode === ViewMode.employee) {
            this.validDates.forEach((val, idx) => {
              this.connectedElements[idx].secondsPerDay = this.connectedElements[idx].time * 60 * 60;
              this.connectedElements[idx].secondsPerDay /= val.length;
              const actionEntries = [];
              for (const key in val) {
                if (!val.hasOwnProperty(key)) {
                  continue;
                }
                const isLeaveDay = this.leaveDays.filter(leaveDay => leaveDay.date === val[key].format('YYYY-MM-DD')
                  && leaveDay.employeeId === this.connectedElements[idx].employeeId
                  && leaveDay.notAccepted === false);
                if (isLeaveDay.length > 0) {
                  continue;
                }
                const eventDate = val[key];
                actionEntries.push({
                  date: moment(eventDate),
                  primary: this.primaryElement,
                  type: this.viewMode,
                  elements: [this.connectedElements[idx]],
                });
              }
              this.$store.dispatch('Planner/prepareEntries', actionEntries);
            });
          } else {
            this.validDates.forEach((validDate, index) => {
              const tmpTime = this.connectedElements[index].time;
              const actionEntries = [];
              validDate.forEach(date => {
                const isLeaveDay = this.leaveDays.filter(leaveDay => leaveDay.date === date.format('YYYY-MM-DD')
                  && leaveDay.employeeId === this.connectedElements[index].employeeId
                  && leaveDay.notAccepted === false);
                if (isLeaveDay.length > 0) {
                  return;
                }
                this.connectedElements[index].time = tmpTime;
                let timeModeFlag = false;
                if (this.timeMode.value === 'week') {
                  timeModeFlag = true;
                }
                const collectSecondsPerDay = [];
                const employee = {
                  id: this.viewMode === ViewMode.employee
                    ? this.primaryElement.id
                    : this.connectedElements[index].employeeId,
                };
                const projectsOfEmployee = this.allPairings.filter(val => val.employeeId === employee.id
                  && val.projectId !== this.connectedElements[index].projectId);
                projectsOfEmployee.forEach((value, idx) => {
                  const project = {
                    id: projectsOfEmployee[idx].projectId,
                  };

                  this.$store.commit('Planner/Time/setTimeMode', 'day');
                  const allEntries = this.timeMode.entryCalculator.createEntries(date, employee, project, this.$store);
                  allEntries.forEach(val => {
                    if (String(val.entry.format('YYYYDDDD')) === String(date.format('YYYYDDDD'))) {
                      collectSecondsPerDay.push(val.secondsPerDay);
                    }
                  });
                });
                if (timeModeFlag) {
                  this.$store.commit('Planner/Time/setTimeMode', 'week');
                } else {
                  this.$store.commit('Planner/Time/setTimeMode', 'month');
                }
                let maxOccupancy = this.jobTimeValue[0] * SECONDS_IN_HOUR;
                if (collectSecondsPerDay.length > 0) {
                  maxOccupancy -= collectSecondsPerDay.reduce((a, b) => a + b, 0);
                }
                maxOccupancy = maxOccupancy > 0 ? maxOccupancy : 0;
                const hoursPerDay = this.connectedElements[index].time / this.validDates[index].length;
                const expectedTime = hoursPerDay * SECONDS_IN_HOUR;
                const secondsPerDay = maxOccupancy >= expectedTime ? expectedTime : maxOccupancy;
                const assignValues = {
                  dateFrom: this.connectedElements[index].dateFrom,
                  dateTo: this.connectedElements[index].dateTo,
                  employeeId: this.connectedElements[index].employeeId,
                  employeeLastName: this.connectedElements[index].employeeLastName,
                  employeeName: this.connectedElements[index].employeeName,
                  id: this.connectedElements[index].id,
                  overtime: this.connectedElements[index].overtime,
                  projectId: this.connectedElements[index].projectId,
                  projectName: this.connectedElements[index].projectName,
                  secondsPerDay,
                };
                actionEntries.push({
                  date: moment(date),
                  primary: this.primaryElement,
                  type: this.viewMode,
                  elements: [assignValues],
                });
              });
              this.$store.dispatch('Planner/prepareEntries', actionEntries);
            });
          }
        } else {
          let actionEntries = [];
          this.connectedElements.forEach(val => {
            val.secondsPerDay = val.time * SECONDS_IN_HOUR;
          });
          actionEntries = [{
            date: moment(this.date),
            primary: this.primaryElement,
            type: this.viewMode,
            elements: this.connectedElements,
          }];
          this.$store.dispatch('Planner/prepareEntries', actionEntries);
        }
        this.connectedElements = [];
      },
      show(data) {
        this.jobTimeValue = [];
        this.allEntries = data.allEntries;
        this.validDates = [];
        this.connectedElements = [];
        this.date = data.date;
        if (this.dataMode === DataMode.worktime) {
          data.getEmployeeWorkingTime.forEach(val => {
            this.jobTimeValue.push(val * 8);
          });
        } else {
          this.jobTimeValue = data.getEmployeeWorkingTime;
        }
        this.primaryElement = data.mainElement;
        const defaultValue = data.connectedElements.length > 1 ? 0 : data.getEmployeeWorkingTime[0] * SECONDS_IN_HOUR;
        if (this.timeMode.value === 'week' || this.timeMode.value === 'month') {
          const jobTimeValueIdx = [];
          data.connectedElements.forEach((element, idxElement) => {
            const dates = this.date.workingDays(this.timeMode.displayControl.getColumnRange(), this.freeDays);
            element.dateFrom.forEach((value, idx) => {
              if ((dates[0] >= moment(`${value}`, 'MM-YYYY').startOf('month')
                && dates[0] <= moment(`${element.dateTo[idx]}`, 'MM-YYYY').endOf('month'))
                || (dates.slice(-1).pop() >= moment(`${value}`, 'MM-YYYY').startOf('month')
                && dates.slice(-1).pop() <= moment(`${element.dateTo[idx]}`, 'MM-YYYY').endOf('month'))) {
                this.connectedElements.push(element);
                jobTimeValueIdx.push(idxElement);
              }
            });
          });
          const tmp = [];
          if (this.viewMode !== this.ViewMode.employee) {
            this.jobTimeValue.forEach((val, idx) => {
              [...new Set(jobTimeValueIdx)].forEach(value => {
                if (idx === value) {
                  tmp.push(val);
                }
              });
            });
            this.jobTimeValue = tmp;
          }
        } else {
          this.connectedElements = data.connectedElements;
        }
        this.connectedElements.map(element => {
          let timestamp = element.secondsPerDay;
          if (typeof (timestamp) === 'undefined' || timestamp === 0) {
            timestamp = defaultValue;
          }
          element.time = Math.round(timestamp / SECONDS_IN_HOUR);
          return element;
        });
        if (this.timeMode.value === 'week' || this.timeMode.value === 'month') {
          const dates = this.date.workingDays(this.timeMode.displayControl.getColumnRange(), this.freeDays);
          this.connectedElements.forEach(element => {
            const weekTimeDates = [];
            dates.forEach(val => {
              element.dateFrom.forEach((value, idx) => {
                if (val >= moment(`${value}`, 'MM-YYYY').startOf('month')
                  && val <= moment(`${element.dateTo[idx]}`, 'MM-YYYY').endOf('month')) {
                  weekTimeDates.push(val);
                }
              });
            });
            this.validDates.push(weekTimeDates);
          });
        }
        this.checkTime();
      },
    },
    i18n: {
      messages: {
        pl: {
          'Cancel': 'Anuluj',
          'Date': 'Data',
          'Employee': 'Osoba',
          'Hours': 'Godziny',
          'hours_per_day': '{0} ({1} h dziennie)',
          'total_values_error': 'Wartości łącznie nie mogą być wyższe niż {0} godzin',
          'Integers only': 'Tylko liczby całkowite',
          'Invalid value': 'Niepoprawna wartość',
          'No data available': 'Wartość nie jest dostępna',
          'Occupancy': 'Zajętość',
          'Project': 'Projekt',
          'Value is too big': 'Wartość jest zbyt duża',
        },
        en: {
          hours_per_day: '({0} ({1} h a day)',
          total_values_error: 'Total values cannot be higher than {0} hours',
        },
      },
    },
  };
</script>
