<template>
  <v-data-table :headers="headers" :items="records" :loading="loading" class="elevation-1 pt-1">
    <template v-slot:item="{ item }">
      <planner-stats-table-row :item="item"/>
    </template>
  </v-data-table>
</template>

<script>
  import PlannerStatsTableRow from './PlannerStatsTableRow';

  export default {
    name: 'PlannerStatsTable',
    components: { PlannerStatsTableRow },
    props: {
      records: { type: Array, default: () => ([]) },
      loading: { type: Boolean, required: true },
    },
    data() {
      return {
        headers: [
          {
            text: this.$t('Full name'),
            value: 'fullName',
          },
          {
            text: this.$t('Possible work hours in month'),
            value: 'workHours',
          },
          {
            text: this.$t('Planed work hours in month'),
            value: 'plannedHours',
          },
          {
            text: this.$t('Billable work hours in month'),
            value: 'billableHours',
          },
          {
            text: this.$t('No billable work hours in month'),
            value: 'noBillableHours',
          },
          {
            text: `${this.$t('Billable/Planed')} (%)`,
            value: 'ratio',
          },
        ],
      };
    },
    i18n: {
      messages: {
        pl: {
          'Full name': 'Imię i nazwisko',
          'Possible work hours in month': 'Możliwe godziny w danym miesiącu',
          'Planed work hours in month': 'Zaplanowane godziny w danym miesiącu',
          'Billable work hours in month': 'Bilowalne godziny w danym miesiącu',
          'No billable work hours in month': 'Nie bilowalne godziny w danym miesiacu',
        },
      },
    },
  };
</script>
