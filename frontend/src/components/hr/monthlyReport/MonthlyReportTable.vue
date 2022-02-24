<template>
  <v-data-table mobile-breakpoint="0"
                :items-per-page="5" :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                :no-results-text="$t('No results found.')"
                :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                :loading="loading"
                :headers="headers"
                :items="rows"
                disable-pagination
                hide-default-footer>
    <template v-slot:item="{ item }">
      <monthly-report-row :month="item" :employees="allEmployeesWithData"/>
    </template>
  </v-data-table>
</template>

<script>
  import MonthlyReportRow from './MonthlyReportRow';
  import moment from '@divante-adventure/work-moment';

  const STATUS_POTENTIAL = 0;
  const STATUS_ACCEPTED = 1;

  export default {
    name: 'MonthlyReportTable',
    components: { MonthlyReportRow },
    props: {
      employees: { type: Array, required: true },
      potentialEmployees: { type: Array, required: true },
      loading: { type: Boolean, default: false },
      year: { type: Number, required: true },
    },
    data() {
      return {
        headers: [
          this.$t('Month'),
          this.$t('People count on end of month'),
          this.$t('Change with previous month'),
          this.$t('Incoming people count'),
          this.$t('Leaving people count'),
        ].map(text => ({ text, align: 'center', sortable: false })),
      };
    },
    computed: {
      rows() {
        const months = [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 ];
        return months.map(month => ({
          firstDay: moment(`${month}-${this.year}`, 'M-YYYY').startOf('month'),
          lastDay: moment(`${month}-${this.year}`, 'M-YYYY').endOf('month'),
          label: this.$t(`date.months`)[month - 1],
        }));
      },
      allEmployeesWithData() {
        return [ ...this.employeesWithData, ...this.potentialEmployeesWithData ];
      },
      employeesWithData() {
        return this.employees.map(employee => ({
          name: employee.name,
          lastName: employee.lastName,
          hiredAt: moment(employee.hiredAt, 'YYYY-MM-DD'),
          hiredTo: typeof employee.hiredTo === 'undefined' ? undefined : moment(employee.hiredTo, 'YYYY-MM-DD'),
          status: STATUS_ACCEPTED,
        }));
      },
      potentialEmployeesWithData() {
        return this.potentialEmployees
          .filter(employee => {
            return employee.status === STATUS_POTENTIAL
              || (employee.status === STATUS_ACCEPTED && !employee.hasOwnProperty('joinedEmployee'));
          }).map(employee => ({
            name: employee.name,
            lastName: employee.lastName,
            hiredAt: typeof employee.hireDate === 'undefined' ? undefined : moment(employee.hireDate, 'DD-MM-YYYY'),
            hiredTo: undefined,
            status: employee.status,
          }));
      },
    },
    i18n: {
      messages: {
        pl: {
          'Loading data...': 'Wczytywanie...',
          'No data available.': 'Brak danych.',
          'No results found.': 'Nie znaleziono.',

          'Month': 'Miesiąc',
          'People count on end of month': 'Liczba osób na koniec miesiąca',
          'Change with previous month': 'Zmiana względem poprzedniego miesiąca',
          'Incoming people count': 'Liczba dołączonych osób',
          'Leaving people count': 'Liczba osób, które odeszły',
        },
      },
    },
  };
</script>
