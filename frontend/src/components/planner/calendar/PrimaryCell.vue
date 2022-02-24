<template>
  <td :class="field.cssClass"
      @click="elementClicked"
      @dragenter="dragOver"
      @mouseenter="(e) => updateTooltip(true)"
      @mouseleave="(e) => updateTooltip(false)">
    <span>
      {{ showValue }}
      <planner-tooltip :css-class="field.cssClass"
                       :time-mode="timeMode"
                       :display-tooltip="displayTooltip"
      />
    </span>
  </td>
</template>

<script>
  import { allModesMixin, isoFreeDaysMixin } from '../../../mixins/PlannerMixins';
  import { mapState } from 'vuex';
  import ViewMode from '../../../util/planner/ViewMode';
  import { eventNames, EventBus } from '../../../eventbus';
  import PlannerTooltip from './PlannerTooltip';

  const SECONDS_IN_8H = 28800;

  export default {
    name: 'PrimaryCell',
    components: { PlannerTooltip },
    mixins: [ allModesMixin, isoFreeDaysMixin ],
    props: {
      element: { type: Object, required: true },
      field: { type: Object, required: true },
    },
    data() {
      return {
        displayTooltip: false,
      };
    },
    computed: {
      ...mapState({
        allEmployees: state => state.Planner.employees,
      }),
      showValue() {
        const secondsInColumn = this.timeMode.getSecondsInColumn(this.field.entry, this.isoFreeDays);
        let cellValue = this.dataMode.formatSeconds(this.field.secondsPerDay, secondsInColumn);
        if (this.dataMode.value === 'worktime') {
          const divider = this.viewMode === ViewMode.employee ? this.element.worktime / SECONDS_IN_8H : 1;
          cellValue = (cellValue / divider).toFixed(2);
        }
        return this.field.freeDay ? '' : cellValue;
      },
    },
    methods: {
      updateTooltip(isMouseOn) {
        if (!isMouseOn) {
          this.displayTooltip = false;
        }
        else {
          this.displayTooltip = true;
        }
      },
      dragOver(e) {
        const field = this.field;
        if (!this.timeMode.displayControl.isCopyingFieldAllowed(field)) {
          return;
        }
        e.preventDefault();
        this.$store.commit('Planner/Drag/setEnd', { field });
      },
      elementClicked() {
        if (this.field.freeDay === true || !this.timeMode.displayControl.isAddingCellsAllowed(this.field)) {
          return;
        }
        const data = {
          date: this.field.entry,
          timeMode: this.timeMode,
          mainElementType: this.viewMode,
          mainElement: this.element,
          getEmployeeWorkingTime: [],
        };
        const formattedDate = this.field.entry.format('YYYYDDDD');
        data.connectedElements = this.element.children.map(child => {
          const employee = {
            id: child.employeeId,
          };
          const project = {
            id: child.projectId,
          };
          const { entryCalculator } = this.timeMode;
          const allEntries = entryCalculator.createEntries(this.field.entry, employee, project, this.$store);
          child.secondsPerDay = allEntries
            .filter(entry => entry.entry.format('YYYYDDDD') === formattedDate)
            .map(entry => entry.secondsPerDay)
            .reduce((a, b) => a + b, 0);
          return child;
        });
        const projectViewEmployees = [];
        data.connectedElements.forEach(val => {
          projectViewEmployees.push(this.allEmployees.filter(employee => employee.id === val.employeeId)[0]);
        });
        const employees = this.viewMode === ViewMode.employee ? [this.element] : projectViewEmployees;
        for (const employee of employees) {
          data.getEmployeeWorkingTime.push(parseFloat(ViewMode.employee.getWorktime(employee, this.dataMode)));
        }
        EventBus.$emit(eventNames.plannerEntryClicked, data);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Holiday': 'Dzień ustawowo wolny od pracy',
          'Sick leave': 'Zwolnienie lekarskie',
          'Pending sick leave': 'Oczekujące zwolnienie lekarskie',
          'Day off': 'Dzień wolny',
          'Pending day off request': 'Oczekujący wniosek urlopowy',
          'Today': 'Dzisiaj',
          'This week': 'Obecny tydzień',
          'This month': 'Obecny miesiąc',
        },
      },
    },
  };
</script>

<style lang="scss" scoped>
  @import "../../../scss/cells";
</style>
