<template>
  <page-card :title="$t('Monthly report')" class="monthly-report-card">
    <template>
      <v-row no-gutters class="subheading pa-2">
        <v-btn @click="previousMonth" icon>
          <v-icon>chevron_left</v-icon>
        </v-btn>
        <span>
          {{ year }}
        </span>
        <v-btn @click="nextMonth" icon>
          <v-icon>chevron_right</v-icon>
        </v-btn>
      </v-row>
      <monthly-report-table :employees="employees"
                            :potential-employees="potentialEmployees"
                            :loading="loading"
                            :year="year"/>
    </template>
  </page-card>
</template>

<script>
  import PageCard from '../../utils/PageCard';
  import moment from '@divante-adventure/work-moment';
  import MonthlyReportTable from './MonthlyReportTable';

  export default {
    name: 'MonthlyReportCard',
    components: { MonthlyReportTable, PageCard },
    props: {
      employees: { type: Array, required: true },
      potentialEmployees: { type: Array, required: true },
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        year: moment().year(),
      };
    },
    methods: {
      previousMonth() {
        this.year--;
      },
      nextMonth() {
        this.year++;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Monthly report': 'Raport miesięczny',
        },
      },
    },
  };
</script>
<style scoped>
  .subheading{
    display: flex;
    justify-content: center;
    align-items: center;
  }
</style>
<style>
  .monthly-report-card h5{
    margin-top: 0 !important;
    margin-bottom: 0 !important;
  }
</style>
