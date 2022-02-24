<template>
  <th :class="columnClass"
      @dragenter="preventDefault; dragOver()">
    <span class="content" v-show="!isHidden">{{ column.text }}</span>
  </th>
</template>

<script>
  import { timeModeMixin } from '../../../mixins/PlannerMixins';
  import moment from '@divante-adventure/work-moment';
  import TimeMode from '../../../util/planner/TimeMode';

  export default {
    name: 'HeaderCell',
    mixins: [ timeModeMixin ],
    props: {
      column: { type: Object, required: true },
    },
    computed: {
      columnClass() {
        const day = this.column.beginning;
        if (day) {
          return 'data-column';
        }
        if (this.timeMode.value !== TimeMode.day.value && day.format('YYYY-MM') !== moment().format('YYYY-MM')) {
          return 'data-column';
        }
        if (this.timeMode.value === TimeMode.month.value && day.format('YYYY-MM') === moment().format('YYYY-MM')) {
          return 'data-column today';
        }
        if (this.timeMode.value === TimeMode.week.value && day.rangeOf('isoweek').contains(moment())) {
          return 'data-column today';
        }
        if (day.day() === 0 || day.day() === 6) {
          return 'data-column weekend';
        }
        if (this.column.freeDay) {
          return 'data-column freeDay';
        }
        if (day.format('YYYY-MM-DD') === moment().format('YYYY-MM-DD')) {
          return 'data-column today';
        }
        return 'data-column';
      },
      isHidden() {
        if (this.timeMode.value === 'week' || this.timeMode.value === 'month') {
          return false;
        }
        const day = this.column.beginning;
        if (day.day() === 0 || day.day() === 6) {
          return true;
        }
        if (this.column.freeDay) {
          return true;
        }
        return false;
      },
    },
    methods: {
      dragOver() {
        const field = { entry: this.column.beginning };
        if (!this.timeMode.displayControl.isCopyingFieldAllowed(field)) {
          return;
        }
        this.$store.commit('Planner/Drag/setEnd', { field });
      },
      preventDefault(e) {
        e.preventDefault();
      },
    },
  };
</script>

<style lang="scss" scoped>
  @import "../../../scss/settings";
  @import "../../../scss/columns";

  @include setupColumnWidth;

  tr th.data-column {
    max-width: 20px;
    text-align: center;
    border-left: $tableBorderStyle;
    padding: 0 0.5em !important;
    white-space: nowrap;
  }
  tr th.freeDay {
    background-color: $freeDayColor;

    .content {
      display: none;
    }
  }
  tr th.weekend {
    background-color: $weekendDayColor;

    .content {
      display: none;
    }
  }
  tr th.today {
    background-color: $todayDayColor;
  }
</style>
