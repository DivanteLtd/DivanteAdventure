<template>
  <tr class="body-1">
    <td>
      <name-label :view-mode="viewMode.opposite" :element="element"/>
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn small text icon v-on="on" @click="editChild()" class="ma-0">
            <v-icon size="20">edit</v-icon>
          </v-btn>
        </template>
        <span>{{ $t(`Edit ${viewMode.opposite.value}`) }}</span>
      </v-tooltip>
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn small text icon v-on="on" class="ma-0" @click="addRange()">
            <v-icon>date_range</v-icon>
          </v-btn>
        </template>
        <span>{{ $t(`Add occupancy range to ${viewMode.opposite.value}`) }}</span>
      </v-tooltip>
      <v-tooltip v-if="timeMode.value === 'day'" bottom>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on"
                 class="ma-0"
                 @click="changeOvertime()"
                 :color="element.overtime ? '#008800' : '#880000'"
                 small text icon>
            <v-icon v-if="element.overtime">timer</v-icon>
            <v-icon v-else>timer_off</v-icon>
          </v-btn>
        </template>
        <span v-if="element.overtime">{{ $t('Overtime on') }}</span>
        <span v-else>{{ $t('Overtime off') }}</span>
      </v-tooltip>
    </td>
    <td @click="addSummaryByMode"
        :title="summaryByModeTooltip">
      {{ summaryByMode }}
    </td>
    <secondary-cell v-for="(field, index) in columns"
                    :key="index"
                    :field="field"
                    :element="element"
                    :parent-element="parentElement"/>
  </tr>
</template>

<script>
  import NameLabel from './NameLabel';
  import SecondaryCell from './SecondaryCell';
  import { EventBus, eventNames } from '../../../eventbus';
  import { allModesMixin, currentDateMixin, isoFreeDaysMixin } from '../../../mixins/PlannerMixins';
  import moment from '@divante-adventure/work-moment';
  import ViewMode from '../../../util/planner/ViewMode';
  import { mapState } from 'vuex';

  const SECONDS_IN_8H = 28800;

  export default {
    name: 'SecondaryRow',
    components: { SecondaryCell, NameLabel },
    mixins: [ allModesMixin, currentDateMixin, isoFreeDaysMixin ],
    props: {
      element: { type: Object, required: true },
      parentElement: { type: Object, required: true },
    },
    data() {
      return {
        showEvent: eventNames.addEditDeleteChild,
        currentMonth: moment(this.currentDate).format('YYYY'),
        currentYear: parseInt(moment(this.currentDate).format('MM')),
        ViewMode,
      };
    },
    computed: {
      ...mapState({
        currentDate: state => state.Planner.Time.currentDate,
        pairings: state => state.Planner.pairings,
        allEmployees: state => state.Planner.employees,
        allProjects: state => state.Planner.projects,
      }),
      columns() {
        const currentDate = moment(this.currentDate);
        const employee = this.allEmployees.find(employee => employee.id === this.element.employeeId);
        const project = this.allProjects.find(project => project.id === this.element.projectId);
        return this.timeMode.entryCalculator.createEntries(currentDate, employee, project, this.$store, this.element);
      },
      summaryByMode() {
        const workingSecondsInView = this.columns
          .filter(c => !c.freeDay)
          .filter(c => c.cssClass.search('disabled') === -1)
          .map(item => item.secondsPerDay)
          .reduce((a, b) => a + b, 0);
        const workingDays = this.columns.filter(c => !c.freeDay).length;
        const availableSecondsInView = (this.timeMode.getSecondsInView(this.currentDate, this.isoFreeDaysMixin));
        const leaveDaysSeconds = this.timeMode.value === 'day'
          ? availableSecondsInView - (workingDays * SECONDS_IN_8H) : 0;
        const summary = this.dataMode.formatSeconds(workingSecondsInView, availableSecondsInView - leaveDaysSeconds);
        if (this.dataMode.value === 'worktime') {
          return parseFloat(
            summary / (this.viewMode === ViewMode.employee ? this.parentElement.worktime / SECONDS_IN_8H : 1)
          ).toFixed(2);
        } else {
          return summary;
        }
      },
      summaryByModeTooltip() {
        return this.timeMode.value === 'day' ? this.$t(`Add jobTimeValue range to ${this.viewMode.opposite.value}`) : '';
      },
    },
    methods: {
      editChild() {
        const data = {
          element: this.element,
          parentElement: this.parentElement,
        };
        EventBus.$emit(eventNames.addEditDeleteChild, data);
      },
      changeOvertime() {
        const employeeId = this.element.employeeId;
        const projectId = this.element.projectId;
        const filtered = this.pairings.filter(pair => pair.employeeId === employeeId && pair.projectId === projectId);
        const pairObject = filtered[0];
        if (pairObject.overtime === true) {
          EventBus.$emit(eventNames.disallowOvertime, pairObject.id);
        } else {
          EventBus.$emit(eventNames.allowOvertime, pairObject.id);
        }
      },
      addRange() {
        const pairings = this.element;
        const eventData = { pairings };
        EventBus.$emit(eventNames.createOccupancyRange, eventData);
      },
      addSummaryByMode() {
        const field = { entry: this.columns[0].entry };
        if (this.timeMode.value !== 'day' || !this.timeMode.displayControl.isAddingCellsAllowed(field)) {
          return;
        }
        const data = {
          startDate: this.columns[0].entry,
          timeMode: this.timeMode,
          mainElementType: this.viewMode,
          businessDays: this.columns.filter(c => !c.isWeekend).length,
          endDate: this.columns[this.columns.length - 1].entry.endOf(this.timeMode.displayControl.getColumnRange()),
          primaryElement: this.parentElement,
          connectedElement: this.element,
          summaryByMode: parseFloat(this.summaryByMode),
        };
        const employee = this.allEmployees.filter(employee => employee.id === this.element.employeeId)[0];
        const getEmployeeWorkingTime = parseFloat(ViewMode.employee.getWorktime(employee, this.dataMode));
        data.availableWorkingTime = data.businessDays * getEmployeeWorkingTime;
        EventBus.$emit(eventNames.summaryByMode, data);
      },
    },
    mounted() {
      EventBus.$on(eventNames.allowOvertime, data => this.$store.dispatch('Planner/allowOvertime', data));
      EventBus.$on(eventNames.disallowOvertime, data => this.$store.dispatch('Planner/disallowOvertime', data));
    },
    i18n: {
      messages: {
        pl: {
          'Add occupancy range to project': 'Dodaj zakres zajętości do projektu',
          'Add occupancy range to employee': 'Dodaj zakres zajętości do osoby',
          'Add jobTimeValue range to project': 'Dodaj bilans zajętości do projektu',
          'Add jobTimeValue range to employee': 'Dodaj bilans zajętości do osoby',
          'Edit project': 'Edytuj projekt',
          'Edit employee': 'Edytuj osobę',
          'Overtime on': 'Nadgodziny włączone',
          'Overtime off': 'Nadgodziny wyłączone',
        },
      },
    },
  };
</script>

<style lang="scss" scoped>
  @import "../../../scss/settings";
  @import "../../../scss/columns";
  @include setupColumnWidth;

  tr {
    background-color: $secondaryRowBackgroundColor;
  }

  a {
    text-decoration: none;
    color: black;
  }
</style>
