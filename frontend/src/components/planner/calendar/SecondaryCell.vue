<template>
  <td :class="(isActive || showContextMenu ? 'active' : '') + ' ' + field.cssClass"
      @dblclick="addCell(field.entry)"
      @click="checkField"
      @contextmenu="showMenu"
      @mouseenter="(e) => updateTooltip(true)"
      @mouseleave="(e) => updateTooltip(false)"
  >
    <div class="content" @dragstart="dragStart" @dragenter="dragOver" @dragend="dragEnd">
      <span ref="content">
        {{ secondsInColumn }}
      </span>
      <planner-tooltip :css-class="field.cssClass"
                       :time-mode="timeMode"
                       :display-tooltip="displayTooltip"
      />
    </div>
    <v-menu v-model="showContextMenu"
            :position-x="x"
            :position-y="y"
            absolute
    >
      <v-list>
        <v-list-item
          v-for="(item, i) in activeItems"
          :key="i"
          @click="item.onClick(field)"
        >
          <v-list-item-title>{{ item.title }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </td>
</template>

<script>
  import { eventNames, EventBus } from '../../../eventbus';
  import { allModesMixin, isoFreeDaysMixin } from '../../../mixins/PlannerMixins';
  import { mapState } from 'vuex';
  import moment from '@divante-adventure/work-moment';
  import ViewMode from '../../../util/planner/ViewMode';
  import PlannerTooltip from './PlannerTooltip';

  const SECONDS_IN_HOUR = 3600;
  export default {
    name: 'SecondaryCell',
    components: { PlannerTooltip },
    mixins: [ allModesMixin, isoFreeDaysMixin ],
    props: {
      element: { type: Object, required: true },
      field: { type: Object, required: true },
      parentElement: { type: Object, required: true },
    },
    data() {
      return {
        displayTooltip: false,
        isActiveHolder: false,
        items: [{
          title: this.$t('Copy'),
          onClick: () => this.dragStart(),
          active: () => this.timeMode.displayControl.isCopyingFieldAllowed(this.field),
        }, {
          title: this.$t('Paste'),
          onClick: () => this.pasteCell(),
          active: () => this.timeMode.displayControl.isCopyingFieldAllowed(this.field),
        }, {
          title: this.$t('Delete'),
          onClick: () => this.deleteCell(),
          active: () => this.timeMode.displayControl.isDeletingFieldAllowed(this.field),
        }, {
          title: this.$t('Add'),
          onClick: field => this.addCell(field.entry),
          active: () => this.timeMode.displayControl.isCopyingFieldAllowed(this.field),
        }],
        showContextMenu: false,
        x: 0,
        y: 0,
      };
    },
    computed: {
      ...mapState({
        cellContent: state => state.Planner.cellContent,
        selectionStart: state => state.Planner.Drag.startSelection,
        selectionEnd: state => state.Planner.Drag.endSelection,
        allEmployees: state => state.Planner.employees,
        allPairings: state => state.Planner.pairings,
        leaveDays: state => state.Planner.leaveDays,
      }),
      secondsInColumn() {
        if (this.field.freeDay || this.field.cssClass === 'disabled') {
          return '';
        } else if (this.dataMode.value === 'worktime') {
          return (this.dataMode.formatSeconds(this.field.secondsPerDay,
                                              this.timeMode.getSecondsInColumn(this.field.entry, this.isoFreeDays))
            / (this.viewMode === ViewMode.employee ? this.employeeWorkingTime : 1)).toFixed(2);
        } else {
          return this.dataMode.formatSeconds(this.field.secondsPerDay,
                                             this.timeMode.getSecondsInColumn(this.field.entry, this.isoFreeDays));
        }
      },
      activeItems() {
        return this.items.filter(i => i.active());
      },
      employeeWorkingTime() {
        const employeeObject = this.allEmployees.filter(employee => employee.id === this.element.employeeId)[0];
        return parseFloat(ViewMode.employee.getWorktime(employeeObject, this.dataMode));
      },
      isActive: {
        get() {
          if(this.isActiveHolder) {
            return true;
          }
          if (typeof(this.selectionStart.field) !== 'undefined' && typeof(this.selectionEnd.field) !== 'undefined') {
            const ends = [this.selectionStart.field.entry, this.selectionEnd.field.entry];
            const range = moment.range(moment.min(ends), moment.max(ends));
            return this.selectionStart.element === this.element
              && this.selectionStart.parent === this.parentElement
              && range.contains(this.field.entry);
          }
          return false;
        },
        set(data) {
          this.isActiveHolder = data;
        },
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
      addCell(date) {
        if (this.field.freeDay === true
          || this.field.cssClass === 'disabled'
          || !this.timeMode.displayControl.isAddingCellsAllowed(this.field)) {
          return;
        }
        const data = {
          date,
          timeMode: this.timeMode,
          mainElementType: this.viewMode,
          mainElement: this.parentElement,
          connectedElements: [this.element],
          getEmployeeWorkingTime: [this.employeeWorkingTime],
        };
        const collectSecondsPerDay = [];
        const employee = {
          id: this.element.employeeId,
        };
        const projectsOfEmployee = this.allPairings.filter(val => val.employeeId === employee.id);
        projectsOfEmployee.forEach((value, idx) => {
          const project = {
            id: projectsOfEmployee[idx].projectId,
          };
          const allEntries = this.timeMode.entryCalculator.createEntries(date, employee, project, this.$store);
          allEntries.forEach(val => {
            if (String(val.entry.format('YYYYDDDD')) === String(this.field.entry.format('YYYYDDDD'))) {
              collectSecondsPerDay.push(val.secondsPerDay);
            }
          });
        });
        const prepareEntries = collectSecondsPerDay.reduce((a, b) => a + b, 0) - this.field.secondsPerDay;
        data.allEntries = prepareEntries / SECONDS_IN_HOUR;
        data.connectedElements[0].secondsPerDay = this.field.secondsPerDay;
        EventBus.$emit(eventNames.plannerEntryClicked, data);
      },
      checkField(e) {
        if (this.field.freeDay === true || this.field.cssClass === 'disabled') {
          return;
        }
        EventBus.$emit(eventNames.secondaryCellClicked, e);
        this.isActiveHolder = true;
        const node = this.$refs.content;
        if (document.body.createTextRange) {
          const range = document.body.createTextRange();
          range.moveToElementText(node);
          range.select();
        } else if (window.getSelection) {
          const selection = window.getSelection();
          const range = document.createRange();
          range.selectNodeContents(node);
          selection.removeAllRanges();
          selection.addRange(range);
        }
      },
      deleteCell() {
        if (!this.timeMode.displayControl.isDeletingFieldAllowed(this.field)) {
          return;
        }
        if (this.isActiveHolder === true || this.showContextMenu === true) {
          if (this.timeMode.value === 'week') {
            for (const sourceDay of this.field.entry.rangeOf('isoweek').by('day')) {
              this.field.content = '0h';
              this.field.secondsPerDay = 0;
              this.element.secondsPerDay = 0;
              this.element.time = '0h';
              this.$store.dispatch('Planner/prepareEntries', {
                date: sourceDay,
                primary: this.parentElement,
                type: this.viewMode,
                elements: [this.element],
              });
            }
          } else if (this.timeMode.value === 'day') {
            this.field.content = '0h';
            this.field.secondsPerDay = 0;
            this.element.secondsPerDay = 0;
            this.element.time = '0h';
            this.$store.dispatch('Planner/prepareEntries', {
              date: this.field.entry,
              primary: this.parentElement,
              type: this.viewMode,
              elements: [this.element],
            });
          }
        }
      },
      disableFields() {
        this.isActiveHolder = false;
        this.showContextMenu = false;
      },
      dragEnd() {
        if (!this.timeMode.displayControl.isCopyingFieldAllowed(this.field) || this.field.cssClass === 'disabled') {
          return;
        }
        const employeeId = this.element.employeeId;
        const projectId = this.element.projectId;
        const ends = [this.selectionStart.field.entry, this.selectionEnd.field.entry];
        const range = moment.range(moment.min(ends), moment.max(ends));
        this.element.jobTimeValue = this.employeeWorkingTime * SECONDS_IN_HOUR;
        const employeeLeaveDays = this.leaveDays.filter(
          val => val.employeeId === employeeId && val.notAccepted === false
        );
        for (const day of range.by(this.timeMode.displayControl.getColumnRange())) {
          let tmp = true;
          employeeLeaveDays.forEach(val => {
            if (val.date === moment(day).format('YYYY-MM-DD')) {
              tmp = false;
            }
          });
          if (tmp) {
            this.timeMode.copyField(this.selectionStart.field.entry, day, this.$store, employeeId, projectId,
                                    undefined, undefined, this.element);
          }
        }
        this.$store.commit('Planner/Drag/setStart', {});
        this.$store.commit('Planner/Drag/setEnd', {});
      },
      dragOver(e) {
        if (!this.timeMode.displayControl.isCopyingFieldAllowed(this.field) || this.field.cssClass === 'disabled') {
          return;
        }
        e.preventDefault();
        const entry = {
          field: this.field,
          element: this.element,
          parent: this.parent,
        };
        this.$store.commit('Planner/Drag/setEnd', entry);
      },
      dragStart() {
        if (!this.timeMode.displayControl.isCopyingFieldAllowed(this.field) || this.field.cssClass === 'disabled') {
          return;
        }
        if (this.isActiveHolder === true || this.showContextMenu === true) {
          const entry = {
            field: this.field,
            element: this.element,
            parent: this.parentElement,
          };
          this.$store.commit('Planner/Drag/setStart', entry);
        }
      },
      pasteCell() {
        if (!this.timeMode.displayControl.isCopyingFieldAllowed(this.field) || this.field.cssClass === 'disabled') {
          return;
        }
        if (this.isActiveHolder === true || this.showContextMenu === true) {
          const sourceEmployeeId = this.selectionStart.element.employeeId;
          const sourceProjectId = this.selectionStart.element.projectId;
          const destEmployeeId = this.element.employeeId;
          const destProjectId = this.element.projectId;
          const day = this.field.entry;
          this.element.jobTimeValue = this.employeeWorkingTime * SECONDS_IN_HOUR;
          this.timeMode.copyField(this.selectionStart.field.entry, day, this.$store, sourceEmployeeId,
                                  sourceProjectId, destEmployeeId, destProjectId, this.element);
          this.isActiveHolder = false;
        }
      },
      showMenu(e) {
        if (this.field.freeDay === true || this.field.cssClass === 'disabled') {
          return;
        }
        if (!this.timeMode.displayControl.isCopyingFieldAllowed(this.field)) {
          return;
        }
        e.preventDefault();
        this.showContextMenu = false;
        this.x = e.clientX;
        this.y = e.clientY;
        this.$nextTick(() => {
          this.showContextMenu = true;
        });
        EventBus.$emit(eventNames.secondaryCellClicked, e);
      },
    },
    mounted() {
      EventBus.$on(eventNames.secondaryCellClicked, this.disableFields);
      EventBus.$on(eventNames.deletePressed, this.deleteCell);
      EventBus.$on(eventNames.ctrlC, this.dragStart);
      EventBus.$on(eventNames.ctrlV, this.pasteCell);
    },
    i18n: {
      messages: {
        pl: {
          'Copy': 'Kopiuj',
          'Paste': 'Wklej',
          'Delete': 'Usuń',
          'Add': 'Dodaj',
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
  td {
    padding: 0 !important;
  }

  td div.content {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    position: relative;
  }

  td div.content span {
    position: absolute;
    margin: auto;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    height: 18px;
  }

  // span::selection and span::-moz-selection must be in different blocks, otherwise it won't work!
  td div.content span::selection {
    background: rgba(0, 0, 0, 0);
  }

  td div.content span::-moz-selection {
    background: rgba(0, 0, 0, 0);
  }
</style>
