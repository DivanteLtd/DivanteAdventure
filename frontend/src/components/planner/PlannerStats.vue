<template>
  <page-card id="checklist-templates-card" :title="$t('Stats from year')">
    <div :class="{'px-5 pt-3': $vuetify.breakpoint.smAndUp}">
      <v-select
        v-model="year"
        :label="$t('Year')"
        :items="sortedYears"
        :loading="loading"
      ></v-select>
      <v-combobox
        v-model="selected"
        :items="tribesByYear"
        :label="$t('Tribes')"
        :loading="loading"
        @input="comboboxChange"
        item-text="name"
        item-value="id"
        multiple
        small-chips
      >
      </v-combobox>
      <planner-stats-chart
        :data-for-chart="getDataForChart"
        :tribes="selected" :year="year"
        :loading="loading"
        class="pb-10"
        @update:loading="(loading) => loadingData(loading)"
      />
    </div>
    <template v-if="employees.length">
      <v-divider></v-divider>
      <planner-stats-table :records="employees" :loading="loadingDataToTable || loading"/>
    </template>
  </page-card>
</template>

<script>
  import { mapActions, mapGetters, mapMutations } from 'vuex';
  import PlannerStatsTable from './stats/PlannerStatsTable';
  import PlannerStatsChart from './stats/PlannerStatsChart';
  import PageCard from '../utils/PageCard';

  export default {
    name: 'PlannerStats',
    components: { PlannerStatsTable, PlannerStatsChart, PageCard },
    data() {
      return {
        loading: true,
        loadingDataToTable: false,
        selected: [],
      };
    },
    computed: {
      ...mapGetters({
        employees: 'PlannerStats/employees',
      }),
      getDataForChart() {
        return this.$store.getters['PlannerStats/prepareCompanyStatsForChart'];
      },
      tribesByYear() {
        return this.$store.getters['PlannerStats/tribesByYear'](this.year);
      },
      sortedYears() {
        return this.years.sort((a, b) => b - a);
      },
      years: {
        get() {
          return this.$store.getters['PlannerStats/years'];
        },
      },
      year: {
        get() {
          return this.$store.getters['PlannerStats/year'];
        },
        set(value) {
          this.setYear(value);
        },
      },
    },
    watch: {
      year() {
        const tribes = this.selected.map(tribe => tribe.id);
        this.loadStatsByYearAndTribes({ year: this.year, tribes });
      },

    },
    methods: {
      ...mapActions(
        'PlannerStats',
        [
          'loadStatsByYearAndTribes',
          'loadYears',
          'loadTribes',
          'loadEmployeesByDateAndTribes',
          'resetEmployeesStats',
        ]
      ),
      ...mapMutations('PlannerStats',
                      [
                        'setYear',
                        'setSelectedTribes',
                      ]),

      comboboxChange() {
        const tribes = this.selected.map(tribe => tribe.id);
        this.loadStatsByYearAndTribes({ year: this.year, tribes });
      },
      loadingData(val) {
        this.loadingDataToTable = val;
      },
    },
    async mounted() {
      await this.resetEmployeesStats();
      await this.loadTribes();
      await this.loadYears();
      const tribes = this.selected.map(tribe => tribe.id);
      await this.loadStatsByYearAndTribes({ year: this.year, tribes });
      this.loading = false;
    },
    i18n: {
      messages: {
        pl: {
          'Stats from year': 'Statystyki z roku',
          'Year': 'Rok',
          'Months': 'Miesiące',
          'Hours': 'Godziny',
          'Tribes': 'Praktyki',
        },
        en: {
          Tribes: 'Praktyki',
        },
      },
    },
  };
</script>
