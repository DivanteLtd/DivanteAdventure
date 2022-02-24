<template>
  <tr>
    <th></th>
    <th> {{ $t('Bilance') }}</th>
    <header-cell v-for="(column, index) in columns" :key="index" :column="column"/>
  </tr>
</template>

<script>
  import HeaderCell from './HeaderCell';

  export default {
    name: 'HeaderDateRow',
    components: { HeaderCell },
    props: {
      items: { type: Array, required: true },
    },
    computed: {
      columns() {
        return this.items.slice(2).filter(val => val.value !== 'summary');
      },
    },
    i18n: {
      messages: {
        pl: {
          Bilance: 'Bilans',
        },
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
