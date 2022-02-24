<template>
  <th :class="columnClass" @dragenter="preventDefault; dragOver()">
    <v-icon size="20"
            class="content"
            v-if="orderData.sortBy === column.value && !orderData.descending">
      arrow_drop_up
    </v-icon>
    <v-icon size="20"
            class="content"
            v-if="orderData.sortBy === column.value && orderData.descending">
      arrow_drop_down
    </v-icon>
  </th>
</template>

<script>
  import { timeModeMixin } from '../../../mixins/PlannerMixins';
  import TimeMode from '../../../util/planner/TimeMode';

  export default {
    name: 'SortCell',
    mixins: [ timeModeMixin ],
    props: {
      column: { type: Object, required: true },
      orderData: { type: Object, required: true },
    },
    computed: {
      columnClass() {
        if (this.timeMode.value !== TimeMode.day.value || !this.column.hasOwnProperty('beginning')) {
          return 'data-column';
        }
        const day = this.column.beginning;
        if (day.day() === 0 || day.day() === 6) {
          return 'data-column weekend';
        }
        if (this.column.freeDay) {
          return 'data-column freeDay';
        }
        return 'data-column';
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
    padding: 0 !important;
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
</style>
