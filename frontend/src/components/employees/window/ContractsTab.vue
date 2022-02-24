<template>
  <page-card>
    <contracts-table :items="contracts"/>
    <template slot="options">
      <contracts-more-menu :employee="employee"/>
    </template>
  </page-card>
</template>

<script>
  import PageCard from '../../utils/PageCard';
  import ContractsMoreMenu from './ContractsMoreMenu';
  import ContractsTable from './ContractsTable';
  import { mapState } from 'vuex';

  export default {
    name: 'ContractsTab',
    components: { ContractsTable, PageCard, ContractsMoreMenu },
    props: {
      employee: { type: Object, required: true },
    },
    computed: {
      ...mapState({
        contractsType: state => state.ContractsType.contractsType,
        contracts: state => state.Employees.financialContracts,
      }),
    },
  };
</script>
