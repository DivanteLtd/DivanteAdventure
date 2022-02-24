<template>
  <div id="tribe-tabs">
    <v-progress-linear height="6" v-if="loading" indeterminate/>
    <v-card v-else class="elevation-0">
      <v-app-bar color="transparent" flat dense>
        <v-row no-gutters wrap>
          <v-spacer v-if="$vuetify.breakpoint.smAndUp"/>
          <v-col cols="10" sm="10">
            <v-tabs v-model="selectedTab" centered class="pa-0">
              <v-tab>{{ $t('people', [ employees.length ]) }}</v-tab>
              <v-tab v-if="!tribe.isVirtual">{{ $t('projects', [ projects.length ]) }}</v-tab>
              <v-tab>{{ $t('positions', [ positions.length ]) }}</v-tab>
            </v-tabs>
          </v-col>
          <v-col cols="1" sm="1" class="tribe-more-menu">
            <v-menu offset-y v-if="seeMoreActions || isHr">
              <template v-slot:activator="{ on }">
                <v-btn v-on="on" icon><v-icon>more_vert</v-icon></v-btn>
              </template>
              <v-list dense>
                <v-list-item v-if="isTribeMaster"
                             @click="EventBus.$emit(eventNames.showEmployeeAssignToTribeWindow, tribeWithPositions)">
                  {{ $t('Assign person') }}
                </v-list-item>
                <v-list-item v-if="seeMoreActions || isHr" @click="exportEmployees">
                  {{ $t('Export people') }}
                </v-list-item>
                <v-list-item v-if="isTribeMaster"
                             @click="EventBus.$emit(eventNames.showPositionAssignToTribeWindow, tribeWithPositions)">
                  {{ $t('Assign position') }}
                </v-list-item>
              </v-list>
            </v-menu>
          </v-col>
        </v-row>
      </v-app-bar>
      <v-divider/>
      <v-card-text :class="{'pa-0': $vuetify.breakpoint.xs}">
        <v-tabs-items v-model="selectedTab" touchless>
          <v-tab-item>
            <tribe-employee-list :loading="loading" :employees="employees"/>
          </v-tab-item>
          <v-tab-item v-if="!tribe.isVirtual">
            <window-project-list :projects="projects"/>
          </v-tab-item>
          <v-tab-item>
            <tribe-position-list :positions="positions" :positions-count="positionsCount"/>
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
  import TribeEmployeeList from './TribeEmployeeList';
  import { EventBus, eventNames } from '../../../eventbus';
  import TribePositionList from './TribePositionList';
  import WindowProjectList from '../../utils/WindowProjectList';
  import { mapGetters, mapState } from 'vuex';
  import exportList from '../../../util/exporter';
  import { getWorkMode } from '../../../util/employee';

  export default {
    name: 'TribeTabs',
    components: { WindowProjectList, TribePositionList, TribeEmployeeList },
    props: {
      loading: { type: Boolean, default: false },
      tribe: { type: Object, required: true },
      employees: { type: Array, default: () => ([]) },
      projects: { type: Array, default: () => ([]) },
      positions: { type: Array, default: () => ([]) },
      positionsCount: { type: Object, default: () => {} },
    },
    data() {
      return {
        EventBus,
        eventNames,
        selectedTab: 0,
      };
    },
    computed: {
      ...mapState({
        currentUser: state => state.Employees.loggedEmployee,
      }),
      ...mapGetters({
        isTribeMaster: 'Authorization/isTribeMaster',
        isSuperAdmin: 'Authorization/isSuperAdmin',
        isHr: 'Authorization/isHr',
      }),
      tribeWithPositions() {
        const tribe = this.tribe;
        tribe.employees = this.employees;
        tribe.positions = this.positions;
        return tribe;
      },
      seeMoreActions() {
        if (this.isSuperAdmin) {
          return true;
        }
        return this.isTribeMaster
          && this.currentUser.tribe
          && this.currentUser.tribe.id === this.tribe.id;
      },
    },
    methods: {
      exportEmployees() {
        const list = this.employees;
        const headers = [{
          label: this.$t('ID'),
          value: employee => employee.id,
        }, {
          label: this.$t('Name and lastName'),
          value: employee => `${employee.name} ${employee.lastName}`,
        }, {
          label: this.$t('Hired at'),
          value: employee => employee.hiredAt || this.$t('Empty'),
        }, {
          label: this.$t('E-mail address'),
          value: employee => employee.email || this.$t('Empty'),
        }, {
          label: this.$t('City'),
          value: employee => employee.city || this.$t('Empty'),
        }, {
          label: this.$t('Contract type'),
          value: employee => (employee.contract || {}).name || this.$t('Empty'),
        }, {
          label: this.$t('Position'),
          value: employee => (employee.position || {}).name || this.$t('Empty'),
        }, {
          label: this.$t('Work mode'),
          value: employee => this.$t(getWorkMode(employee.workMode)),
        }];
        exportList(list, headers, 'employees.csv');
      },
    },
    i18n: {
      messages: {
        pl: {
          'Assign person': 'Przypisz osobę',
          'Assign position': 'Przypisz stanowisko',
          'Export people': 'Wyeksportuj osoby',
          'ID': 'ID',
          'Name and lastName': 'Imię i nazwisko',
          'Hired at': 'Data zatrudnienia',
          'E-mail address': 'Adres e-mail',
          'City': 'Miasto',
          'Contract type': 'Forma współpracy',
          'Position': 'Pozycja',
          'Empty': 'Brak',
          'Yes': 'Tak',
          'No': 'Nie',
          'people': 'Osoby ({0})',
          'projects': 'Projekty ({0})',
          'positions': 'Stanowiska ({0})',
          'Work mode': 'Tryb pracy',
          'Work from office': 'Praca z biura',
          'Work remotely': 'Praca zdalna',
          'Work partial remotely': 'Praca cześciowo zdalna',
        },
        en: {
          people: 'People ({0})',
          projects: 'Projects ({0})',
          positions: 'Positions ({0})',
        },
      },
    },
  };
</script>
<style scoped>
  .tribe-more-menu{
    text-align: right;
  }
</style>
<style>
  #tribe-tabs .v-toolbar__content, .v-toolbar__extension{
    padding: 0 !important;
  }
</style>
