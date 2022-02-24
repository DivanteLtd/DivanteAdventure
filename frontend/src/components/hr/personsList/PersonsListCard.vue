<template>
  <v-card>
    <v-app-bar color="transparent" class="pa-0" flat dense :height="checkIfMobile">
      <v-container fluid>
        <v-row wrap>
          <v-spacer class="hidden-sm-and-down"/>
          <v-col cols="12" md="9" lg="8" xl="8" class="px-0">
            <v-tabs v-model="selectedTab" centered>
              <v-tab>{{ $t('active-people', [ filteredEmployees.length ]) }}</v-tab>
              <v-tab>{{ $t('potential-people', [ filteredPotentialEmployees.length ]) }}</v-tab>
              <v-tab>{{ $t('end-cooperation-employees', [ filteredEndCooperationEmployees.length ]) }}</v-tab>
            </v-tabs>
          </v-col>
          <v-col cols="12" md="3" lg="2" xl="2" class="d-flex align-center pa-0">
            <v-text-field v-model="search" append-icon="search" :label="$t('Search')" single-line hide-details/>
            <persons-list-menu :employees="filteredEmployees"/>
          </v-col>
        </v-row>
      </v-container>
    </v-app-bar>
    <v-divider/>
    <v-card-text class="pa-0">
      <v-tabs-items v-model="selectedTab" touchless>
        <v-tab-item>
          <persons-list-table :loading="loading" :employees="filteredEmployees"/>
        </v-tab-item>
        <v-tab-item>
          <potential-people-table :loading="loading" :employees="filteredPotentialEmployees"/>
        </v-tab-item>
        <v-tab-item>
          <end-cooperation-employees-table :loading="loading" :employees="filteredEndCooperationEmployees"/>
        </v-tab-item>
      </v-tabs-items>
    </v-card-text>
  </v-card>
</template>

<script>
  import PersonsListTable from '../rotation/PersonsListTable';
  import PotentialPeopleTable from './PotentialPeopleTable';
  import PersonsListMenu from './PersonsListMenu';
  import EndCooperationEmployeesTable from './EndCooperationEmployeesTable';

  export default {
    name: 'PersonsListCard',
    components: { PersonsListMenu, PotentialPeopleTable, PersonsListTable, EndCooperationEmployeesTable },
    props: {
      loading: { type: Boolean, required: true },
      employees: { type: Array, default: () => ([]) },
      potentialEmployees: { type: Array, default: () => ([]) },
      endCooperationEmployees: { type: Array, default: () => ([]) },
    },
    data() {
      return {
        search: '',
        selectedTab: 0,
      };
    },
    computed: {
      checkIfMobile() {
        return this.$vuetify.breakpoint.smAndDown ? 120 : 48;
      },
      searchArray() {
        return this.search.toLowerCase().split(/[ ,.;]+/);
      },
      filteredEmployees() {
        return this.employees.filter(employee => this.filterFunction(employee));
      },
      filteredPotentialEmployees() {
        return this.potentialEmployees.filter(employee => this.filterFunction(employee));
      },
      filteredEndCooperationEmployees() {
        return this.endCooperationEmployees.filter(employee => this.filterEndCooperation(employee));
      },
    },
    methods: {
      filterFunction(employee) {
        const entryPartA = `${employee.name} ${employee.lastName} ${employee.hiredAt} ${employee.email}`;
        const entryPartB = `${employee.gender === 0 ? this.$t('Man') : this.$t('Woman')}`;
        const entryPartC = `${(employee.position || {}).name} ${employee.city} ${employee.dateOfBirth}`;
        const entryPartD = this.$t(`${(employee.contract || {}).name}`);
        const entryPartE = this.$t(`${(employee.contractType || {})}`);
        const entryPartF = `${(employee.level || {}).name} ${employee.hiredTo}`;
        const entry = `${entryPartA} ${entryPartB} ${entryPartC} ${entryPartD} ${entryPartE} ${entryPartF}`
          .toLowerCase();
        return this.searchArray.map(key => entry.includes(key)).reduce((a, b) => a && b, true);
      },
      filterEndCooperation(employee) {
        const entryPartA = `${employee.name} ${employee.lastName} ${employee.email}`;
        const entry = entryPartA.toLocaleLowerCase();
        return this.searchArray.map(key => entry.includes(key)).reduce((a, b) => a && b, true);
      },
    },
    i18n: {
      messages: {
        pl: {
          'CoE': 'UoP',
          'CLC LUMP SUM': 'UCP RYCZAŁT',
          'CLC HOURLY': 'UCP GODZINOWE',
          'B2B LUMP SUM': 'B2B RYCZAŁT',
          'B2B HOURLY': 'B2B GODZINOWE',
          'People': 'Osoby',
          'Search': 'Szukaj',
          'active-people': 'Aktywne osoby ({0})',
          'potential-people': 'Potencjalne osoby ({0})',
          'end-cooperation-employees': 'Zakończenie współpracy ({0})',
        },
        en: {
          'active-people': 'Active people ({0})',
          'potential-people': 'Potential people ({0})',
          'end-cooperation-employees': 'Ended cooperation ({0})',
        },
      },
    },
  };
</script>
