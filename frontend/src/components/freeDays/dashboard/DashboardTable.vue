<template>
  <v-data-table mobile-breakpoint="0"
                :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                :items="entriesByEmployee"
                :headers="headers"
                :search="search"
                :custom-filter="filter"
                :no-results-text="$t('No results found.')"
                hide-default-header
                @update:page="moveToTop">
    <template v-slot:header="{ props: { headers } }">
      <thead>
        <tr>
          <th>{{ $t('Person') }}</th>
          <th v-for="(item, i) in headers"
              class="text-center"
              :class="item.cssClass"
              :key="i">
            {{ item.text }}
          </th>
        </tr>
      </thead>
    </template>
    <template v-slot:item="{ item, headers }">
      <tr>
        <td class="pa-0"><employee-chip :employee="item.employee" class="ma-0" last-name-first/></td>
        <dashboard-cell v-for="(header, i) in headers" :key="i" :header="header"
                        :location="filterEmployeesWorkLocations(item.employee.id, header.day)"
                        :items="item.freeDays"/>
      </tr>
    </template>
    <template slot="pageText" slot-scope="props">
      {{ $t('page-text', props) }}
    </template>
  </v-data-table>
</template>

<script>
  import { mapState } from 'vuex';
  import moment from '@divante-adventure/work-moment';
  import DashboardCell from './DashboardCell';
  import EmployeeChip from '../../utils/EmployeeChip';

  export default {
    name: 'DashboardTable',
    components: { EmployeeChip, DashboardCell },
    props: {
      loading: { type: Boolean, required: true },
      search: { type: String, default: '' },
    },
    data() { return {
      pagination: {
        rowsPerPage: 10,
      },
    };},
    computed: {
      ...mapState({
        currentMonth: state => state.FreeDays.dashboardDate,
        statutoryFreeDays: state => state.FreeDays.statutoryFreeDays,
        entriesByEmployee: state => state.FreeDays.dashboardDays,
        employeesWorkLocations: state => state.Employees.allEmployeesWorkLocations,
      }),
      statutoryFreeDaysIso() {
        return this.statutoryFreeDays.map(timestamp => moment.unix(timestamp).format('YYYYDDD'));
      },
      headers() {
        const headers = [];
        const lastDay = moment(this.currentMonth).endOf('month').date();
        for(let i = 1; i <= lastDay; i++) {
          const day = moment(this.currentMonth).date(i);
          if (day.day() === 0 || day.day() === 6 || this.statutoryFreeDaysIso.includes(day.format('YYYYDDD'))) {
            const cssClass = (day.day() === 0 || day.day() === 6) ? 'weekend' : 'free';
            headers.push({ text: '', sortable: false, day, free: true, cssClass: `pa-0 ma-0 ${cssClass}` });
          }
          else {
            const text = day.format(this.$t('header_date_format'));
            if (day.format('DD-MM-YYYY') === moment().format('DD-MM-YYYY')) {
              headers.push({ text, sortable: false, day, free: false, cssClass: 'pa-0 ma-0 today-column' });
            } else {
              headers.push({ text, sortable: false, day, free: false, cssClass: 'pa-0 ma-0 day-column' });
            }
          }
        }
        return headers;
      },
    },
    methods: {
      filterEmployeesWorkLocations(employeeId, day) {
        const workLocationType = this.employeesWorkLocations
          .find(val => val.employeeId === employeeId
            && moment(val.date).isSame(moment(day), 'day'));
        return workLocationType ? workLocationType.type : 0;
      },
      filter(value, search, item) {
        const searchLower = search.toLowerCase().split(/[ ,.;]+/);
        const entryPartA = `${item.employee.name} ${item.employee.lastName}`;
        const entryPartB = `${item.employee.email} ${(item.employee.tribe || {}).name}`;
        const entryPartC = `${(item.employee.position || {}).name}`;
        const entryPartD = `${(item.employee.contract || {}).name} ${(item.employee.level || {}).name}`;
        const entry = `${entryPartA} ${entryPartB} ${entryPartC} ${entryPartD}`.toLowerCase();
        return searchLower.map(key => entry.includes(key)).reduce((a, b) => a && b, true);
      },
      moveToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      },
    },
    i18n: { messages: {
      pl: {
        'Person': 'Osoba',
        'No data available': 'Brak dostępnych danych',
        'All': 'Wszystkie',
        'Rows per page:': 'Wierszy na stronę',
        'Loading data...': 'Wczytywanie...',
        'No data available.': 'Brak danych.',
        'No results found.': 'Nie znaleziono.',
        'page-text': 'Osoby {pageStart}-{pageStop} z {itemsLength}',
      },
      en: {
        'header_date_format': 'dd DD',
        'page-text': 'People {pageStart}-{pageStop} of {itemsLength}',
      },
    } },
  };
</script>
<style scoped>
  .employee-column {
    min-width: 150px;
  }
  .day-column {
    min-width: 48px;
  }
  .today-column {
    background-color: cornflowerblue;
    min-width: 48px;
  }
  th, td {
    border-left: 1px solid rgba(0, 0, 0, 0.12);
  }
  th:first-child, td:first-child {
    border-left: none;
  }
  th.free {
    background-color: pink;
    max-width: 10px !important;
  }
  th.weekend {
    background-color: grey;
    max-width: 10px !important;
  }
</style>
