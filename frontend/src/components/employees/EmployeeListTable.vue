<template>
  <v-data-table mobile-breakpoint="0"
                :items-per-page="5"
                :no-data-text="loading ? this.$t('Loading data...') : this.$t('No data available.')"
                :no-results-text="$t('No results found.')"
                :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                :items="employees"
                :loading="loading"
                :headers="headers"
                :search="search"
                :custom-filter="filter"
                :custom-sort="customSort"
                @update:page="moveToTop"
                calculate-widths
                must-sort>
    <template v-slot:item="{ item }">
      <employee-list-row :item="item"
                         :location="filterEmployeesWorkLocations(item.id)"/>
      <template slot="pageText" slot-scope="props">
        {{ $t('page-text', props) }}
      </template>
    </template>
  </v-data-table>
</template>

<script>
  import { mapGetters, mapState } from 'vuex';
  import EmployeeListRow from './EmployeeListRow';

  export default {
    name: 'EmployeeListTable',
    components: { EmployeeListRow },
    props: {
      loading: { type: Boolean, required: true },
      search: { type: String, default: '' },
      employees: { type: Array, default: () => ([]) },
    },
    data() { return {
      unfilteredHeaders: [{
        align: 'center',
        value: 'photo',
        sortable: false,
        width: '0',
      }, {
        align: 'center',
        value: 'availableToday',
        sortable: false,
        width: '0',
      }, {
        text: this.$t('Person'),
        align: 'center',
        value: 'lastName',
        sortable: true,
      }, {
        text: this.$t('Contract type'),
        align: 'center',
        value: 'contract',
        sortable: true,
        role: 'ROLE_SUPER_ADMIN',
      }, {
        text: this.$t('Tribe and position'),
        align: 'center',
        value: 'tribe',
        width: 250,
        sortable: true,
      }, {
        text: this.$t('Phone number'),
        align: 'center',
        value: 'phone',
        sortable: false,
      }, {
        text: this.$t('Licence plate'),
        align: 'center',
        value: 'licencePlate',
        sortable: false,
      }, {
        align: 'center',
        sortable: false,
        value: 'actions',
        role: 'ROLE_TRIBE_MASTER',
      }],
    };},
    computed: {
      ...mapGetters({
        isAuthorized: 'Authorization/isAuthorized',
      }),
      ...mapState({
        employeesTodayWorkLocations: state => state.Employees.employeesTodayWorkLocations,
      }),
      headers() {
        return this.unfilteredHeaders.filter(header => (header.hasOwnProperty('role') ? this.isAuthorized(header.role) : true));
      },
    },
    methods: {
      filterEmployeesWorkLocations(employeeId) {
        const employeeWorkLocation = this.employeesTodayWorkLocations.find(val => {
          return val.employeeId === employeeId;
        });
        return employeeWorkLocation ? employeeWorkLocation.type : 0;
      },
      filter(value, search, item) {
        const searchLower = search.toLowerCase().split(/[ ,.;]+/);
        const entryPartA = `${item.name} ${item.lastName}`;
        const entryPartB = `${item.email} ${(item.tribe || {}).name}`;
        const entryPartC = `${(item.position || {}).name} ${item.city} ${(item.level || {}).name}`;
        const entryPartD = this.$t(`${(item.contract || {}).name}`);
        const entryPartE = `${item.phone}`;
        const entryPartF = `${item.licencePlate}`;
        const entry = `${entryPartA} ${entryPartB} ${entryPartC} ${entryPartD} ${entryPartE} ${entryPartF}`.replace(/\s/g, '').toLowerCase();
        return searchLower.map(key => entry.includes(key)).reduce((a, b) => a && b, true);
      },
      customSort(items, index, isDesc) {
        let aPerson = ``;
        let bPerson = ``;
        items.sort((a, b) => {
          if (index[0] === 'lastName') {
            aPerson = `${a.lastName} ${a.name}`;
            bPerson = `${b.lastName} ${b.name}`;
            if(!isDesc[0]) {
              return aPerson.localeCompare(bPerson);
            } else {
              return bPerson.localeCompare(aPerson);
            }
          }
          else if (index[0] === 'contract' || index[0] === 'tribe') {
            aPerson = `${(a[index] || {}).name}`;
            bPerson = `${(b[index] || {}).name}`;
          }
          else{
            aPerson = `${a[index]}`;
            bPerson = `${b[index]}`;
          }
          if(!isDesc[0]) {
            return aPerson.localeCompare(bPerson);
          } else {
            return bPerson.localeCompare(aPerson);
          }
        });
        return items;
      },
      moveToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      },
    },
    i18n: { messages: {
      pl: {
        'CoE': 'UoP',
        'CLC LUMP SUM': 'UCP RYCZAŁT',
        'CLC HOURLY': 'UCP GODZINOWE',
        'B2B LUMP SUM': 'B2B RYCZAŁT',
        'B2B HOURLY': 'B2B GODZINOWE',
        'Loading data...': 'Wczytywanie...',
        'No data available.': 'Brak danych.',
        'No results found.': 'Nie znaleziono.',
        'Rows per page:': 'Wierszy na stronę:',
        'All': 'Wszystkie',
        'Person': 'Osoba',
        'Tribe and position': 'Plemię i stanowisko',
        'Contract type': 'Forma współpracy',
        'Phone number': 'Numer telefonu',
        'Licence plate': 'Numer rejestracyjny pojazdu',
        'page-text': 'Osoby {pageStart}-{pageStop} z {itemsLength}',
      },
      en: {
        'page-text': 'People {pageStart}-{pageStop} of {itemsLength}',
      },
    } },
  };
</script>
