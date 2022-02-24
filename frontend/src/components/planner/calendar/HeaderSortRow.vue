<template>
  <tr class="ordering-line">
    <th @click="updateOrder({value: 'name', sortable: true})">
      <v-icon size="20" v-if="orderData.sortBy === 'name' && !orderData.descending">arrow_drop_up</v-icon>
      <v-icon size="20" v-if="orderData.sortBy === 'name' && orderData.descending">arrow_drop_down</v-icon>
    </th>
    <sort-cell v-for="(column, index) in columns"
               :key="index"
               :column="column"
               :order-data="orderData"
               @click="updateOrder(column)"/>
  </tr>
</template>

<script>
  import { viewModeMixin, timeModeMixin, currentDateMixin } from '../../../mixins/PlannerMixins';
  import SortCell from './SortCell';

  export default {
    name: 'HeaderSortRow',
    components: { SortCell },
    mixins: [ viewModeMixin, timeModeMixin, currentDateMixin ],
    props: {
      items: { type: Array, default: () => [] },
    },
    data() {
      return {
        orderData: {
          sortBy: 'name',
          descending: false,
        },
      };
    },
    computed: {
      columns() {
        return this.items.slice(1);
      },
    },
    watch: {
      timeMode() {
        this.resetOrder();
      },
      currentDate() {
        this.resetOrder();
      },
    },
    methods: {
      dragOver(column) {
        const field = { entry: column.beginning };
        if (!this.timeMode.displayControl.isCopyingFieldAllowed(field)) {
          return;
        }
        this.$store.commit('Planner/Drag/setEnd', { field });
      },
      resetOrder() {
        this.orderData.sortBy = 'name';
        this.orderData.descending = false;
        this.$emit('order', this.orderData);
      },
      updateOrder(column) {
        if (!column.sortable) {
          return;
        }
        const columnName = column.value;
        if (this.orderData.sortBy === columnName) {
          this.orderData.descending = !this.orderData.descending;
        }
        else {
          this.orderData.sortBy = columnName;
          this.orderData.descending = false;
        }
        this.$emit('order', this.orderData);
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

  tr th.filter-column {
    padding: 0 0 0 24px !important;
  }

  tr.ordering-line {
    border-bottom: $tableBorderStyle;
    height: 10px;

    th {
      text-align: center;
    }

    th.freeDay .content {
      display: none;
    }
  }
</style>
