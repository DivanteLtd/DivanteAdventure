<template>
  <tr class="subheading">
    <td>
      <name-label :view-mode="viewMode" :element="element" primary/>
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn small text icon v-on="on" @click="addChild()" class="ma-0">
            <v-icon size="20">add</v-icon>
          </v-btn>
        </template>
        <span>{{ $t(`Add new ${viewMode.opposite.value}`) }}</span>
      </v-tooltip>
      <v-tooltip bottom :disabled="!expandable">
        <template v-slot:activator="{ on }">
          <v-btn small text icon @click="switchVisibility" :disabled="!expandable" depressed class="ma-0"
                 v-on="on">
            <v-icon size="20" v-if="!expanded && expandable">arrow_drop_down</v-icon>
            <v-icon size="20" v-if="expanded && expandable">arrow_drop_up</v-icon>
            <v-icon size="20" class="hidden-icon" v-if="!expandable">arrow_drop_up</v-icon>
          </v-btn>
        </template>
        <span>{{ $t(`${expanded ? 'Hide' : 'Show'} ${viewMode.opposite.value}s`) }}</span>
      </v-tooltip>
    </td>
    <td :class="timeValueColor">
      {{ summaryByMode }}
    </td>
    <primary-cell v-for="(field, index) in columns"
                  :key="index"
                  :field="field"
                  :element="element"/>
  </tr>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import { allModesMixin, currentDateMixin, isoFreeDaysMixin } from '../../../mixins/PlannerMixins';
  import moment from '@divante-adventure/work-moment';
  import ViewMode from '../../../util/planner/ViewMode';
  import DataMode from '../../../util/planner/DataMode';
  import NameLabel from './NameLabel';
  import PrimaryCell from './PrimaryCell';

  const HOURS_IN_DAY = 8;
  const SECONDS_IN_HOUR = 3600;
  const SECONDS_IN_8H = 28800;

  export default {
    name: 'PrimaryRow',
    components: { PrimaryCell, NameLabel },
    mixins: [ allModesMixin, currentDateMixin, isoFreeDaysMixin ],
    props: {
      element: { type: Object, required: true },
      expandable: { type: Boolean, default: false },
      expanded: { type: Boolean, default: false },
    },
    computed: {
      workingHoursFromStore() {
        const currentDate = moment(this.currentDate);
        return this.timeMode.getWorkingHoursFromStore(currentDate, this.element, this.$store);
      },
      availableWorkingTime() {
        const time = moment(this.currentDate);
        const workingDays = this.timeMode.getWorkingDaysInView(time, this.$store);
        return workingDays * parseFloat(this.viewMode.getWorktime(this.element, this.dataMode));
      },
      columns() {
        const currentDate = moment(this.currentDate);
        const employee = this.viewMode === ViewMode.employee ? this.element : false;
        const project = this.viewMode === ViewMode.project ? this.element : false;
        return this.timeMode.entryCalculator.createEntries(currentDate, employee, project, this.$store);
      },
      summaryByMode() {
        const businessDays = this.columns.filter(c => !c.isWeekend).length;
        const workingDays = this.columns.filter(c => !c.freeDay).length;
        const leaveDaysHours = (businessDays - workingDays) * HOURS_IN_DAY;
        const workingHours = this.workingHoursFromStore + leaveDaysHours;

        if (this.dataMode === DataMode.worktime) {
          const allSeconds = this.timeMode.getSecondsInView(this.currentDate, this.isoFreeDays)
            * (this.viewMode === ViewMode.employee ? this.element.worktime / SECONDS_IN_8H : 1);
          return ((workingHours * SECONDS_IN_HOUR) / allSeconds).toFixed(2);
        }
        else if (this.viewMode === ViewMode.employee) {
          return `${workingHours}\u00a0h / ${this.availableWorkingTime}\u00a0h`;
        }
        else {
          return `${workingHours}\u00a0h`;
        }
      },
      timeValueColor() {
        if (this.viewMode === ViewMode.project) {
          return '';
        }
        const dayOff = this.columns.filter(c => c.cssClass === 'day-off');
        let dayOffSum = 0;
        if (this.element.worktime) {
          dayOffSum = dayOff.length * this.element.worktime / SECONDS_IN_HOUR;
        }
        const workingHours = this.workingHoursFromStore + dayOffSum;
        let availableWorkingTime = this.availableWorkingTime;
        if (this.dataMode === DataMode.worktime) {
          availableWorkingTime *= HOURS_IN_DAY;
        }
        if (workingHours > availableWorkingTime) {
          return 'red--text';
        } else if (workingHours < availableWorkingTime) {
          return 'orange--text';
        } else {
          return 'teal--text';
        }
      },

    },
    methods: {
      addChild() {
        const data = {
          element: this.element,
        };
        EventBus.$emit(eventNames.addEditDeleteChild, data);
      },
      switchVisibility() {
        if (this.element.children.length > 0) {
          this.$emit('switched-visibility');
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Add new project': 'Dodaj nowy projekt',
          'Add new employee': 'Dodaj nową osobę',
          'Show projects': 'Pokaż projekty',
          'Show employees': 'Pokaż osoby',
          'Hide projects': 'Ukryj projekty',
          'Hide employees': 'Ukryj osoby',
        },
      },
    },
  };
</script>

<style lang="scss" scoped>
  @import "../../../scss/settings";
  @import "../../../scss/columns";
  @include setupColumnWidth;

  .hidden-icon {
    opacity: 0;
  }

  a {
    text-decoration: none;
    color: black;
  }
</style>
