<template>
  <v-data-table :items-per-page="15"
                :items="items"
                :headers="headers"
                item-key="id">
    <template v-slot:item="{ item }">
      <contracts-row :item="item" />
    </template>
  </v-data-table>
</template>

<script>
  import ContractsRow from './ContractsRow';

  export default {
    name: 'ContractsTable',
    components: { ContractsRow },
    props: {
      items: { type: Array, required: true },
    },
    data() {
      return {
        sortBy: 'endDate',
        sortDesc: false,
        headers: [{
          text: this.$t('Contract type'),
          align: 'center',
          value: 'contractType',
        }, {
          text: this.$t('Start contract date'),
          align: 'center',
          value: 'startDate',
        }, {
          text: this.$t('End contract date'),
          align: 'center',
          value: 'endDate',
          sortable: false,
        }, {
          text: this.$t('Notice period'),
          align: 'center',
          value: 'noticePeriod',
          sortable: false,
        }, {
          text: this.$t('Actions'),
          align: 'center',
          value: 'id',
          sortable: false,
        }],
      };
    },
    i18n: {
      messages: {
        pl: {
          'Contract type': 'Typ umowy',
          'Start contract date': 'Data początku obowiązywania umowy',
          'Assign contract date': 'Data podpisu umowy',
          'End contract date': 'Data końca umowy',
          'Notice period': 'Okres wypowiedzenia',
          'Actions': 'Akcje',
        },
      },
    },
  };
</script>
