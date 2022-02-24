<template>
  <v-container grid-list-xl fluid>
    <v-card>
      <v-app-bar color="transparent" flat dens>
        <v-spacer/>
        <v-tabs v-model="selectedTab" centered>
          <v-tab>{{ $t('Rotation in time') }}</v-tab>
          <v-tab>{{ $t('Current statistics') }}</v-tab>
        </v-tabs>
        <v-spacer/>
      </v-app-bar>
      <v-divider/>
      <v-card-text :class="{'pa-0': $vuetify.breakpoint.xs}">
        <v-tabs-items v-model="selectedTab" touchless>
          <v-tab-item>
            <hr-table :loading="loadingRotation" :statistics="stats"/>
          </v-tab-item>
          <v-tab-item>
            <current-stats-table :loading="loadingStats" :employees="employees" :stats="currentStats"/>
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>
    </v-card>
    <year-dialog/>
    <month-dialog/>
  </v-container>
</template>

<script>
  import { mapState } from 'vuex';
  import HrTable from '../../components/hr/rotation/hrTable/HrTable';
  import CurrentStatsTable from '../../components/hr/currentStats/CurrentStatsTable';
  import YearDialog from '../../components/hr/rotation/hrTable/YearDialog';
  import MonthDialog from '../../components/hr/rotation/hrTable/MonthDialog';

  export default {
    name: 'RotationCard',
    components: { MonthDialog, YearDialog, CurrentStatsTable, HrTable },
    data() {
      return {
        selectedTab: 0,
        stats: [],
        currentStats: {},
        loadingRotation: false,
        loadingStats: false,
      };
    },
    computed: {
      ...mapState({
        employees: state => state.Employees.employees,
      }),
    },
    methods: {
      loadData() {
        this.stats = [];
        this.loadingRotation = true;
        this.loadingStats = true;
        this.$store.dispatch('Stats/loadRotationForWholeTime').then(result => {
          this.stats = result;
          this.loadingRotation = false;
        });
        this.$store.dispatch('Stats/loadActualStats').then(result => {
          this.currentStats = result;
          this.loadingStats = false;
        });
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
    i18n: {
      messages: {
        pl: {
          'Rotation in time': 'Przegląd w czasie',
          'Current statistics': 'Stan obecny',
        },
      },
    },
  };
</script>
