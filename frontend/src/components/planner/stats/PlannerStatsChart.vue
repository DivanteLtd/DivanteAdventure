<template>
  <v-container min-height="400" class="d-flex justify-center align-center" v-if="loading">
    <v-progress-circular indeterminate color="primary" />
  </v-container>
  <GChart
    v-else
    type="ComboChart"
    class="py-3 g-chart"
    :data="[...legend, ...dataForChart]"
    :options="chartOptions"
    :events="{select: clickInBar, error: muteError}"
    ref="gChart"
  />
</template>

<script>
  import { GChart } from 'vue-google-charts';
  import { mapActions } from 'vuex';

  export default {
    name: 'PlannerStatsChart',
    components: { GChart },
    props: {
      dataForChart: { type: Array, required: true },
      tribes: { type: Array, required: true },
      year: { type: Number, required: true },
      loading: { type: Boolean, required: true },
    },
    data() {
      return {
        chartOptions: {
          loading: false,
          seriesType: 'bars',
          height: 400,
          chartArea: {
            width: '80%',
            height: '75%',
            top: 25,
          },
          hAxis: {
            title: this.$t('Months'),
          },
          vAxis: {
            title: this.$t('Hours'),
          },
          legend: {
            is3D: true,
            position: 'bottom',
            textStyle: { fontSize: 12 },
          },
        },
        legend: [[
          this.$t('Month'),
          this.$t('Possible'),
          this.$t('Planned'),
          this.$t('Billable hours'),
          this.$t('No billable hours'),
        ]],
      };
    },
    methods: {
      ...mapActions(
        'PlannerStats',
        [
          'loadEmployeesByDateAndTribes',
        ]
      ),
      async clickInBar() {
        const table = this.$refs.gChart.chartObject;
        const selection = table.getSelection();
        const columnNumber = selection[0].row;
        const date = `${this.year}-${columnNumber + 1}`;
        const tribes = this.tribes.map(tribe => tribe.id);
        this.$emit('update:loading', true);
        await this.loadEmployeesByDateAndTribes({ date, tribes });
        this.$emit('update:loading', false);
      },
      muteError() {
        const google = window.google;
        google.visualization.errors.removeAll(this.$refs.gChart.chartObject.getContainer());
      },
    },
    i18n: {
      messages: {
        pl: {
          'Year': 'Rok',
          'Month': 'Miesiąc',
          'Months': 'Miesiące',
          'Hours': 'Godziny',
          'Tribes': 'Praktyki',
          'Possible': 'Ilość możliwych godzin w miesiącu',
          'Planned': 'Ilość zaplanowanych godzin w miesiącu',
          'Billable hours': 'Ilość billowalych godzin w miesiącu',
          'No billable hours': 'Ilość nie billowalnych godzin w miesiącu',

        },
        en: {
          'Planned': 'Number of planned hourd in month',
          'Possible': 'Number of possible hours in a month',
          'Billable hours': 'Number of billable hour in a month',
          'No billable hours': 'Number of no billable hour in a mont',
          'Tribes': 'Praktyki',
        },
      },
    },
  };
</script>
<style scoped>
  .g-chart{
    cursor: pointer;
  }
</style>
