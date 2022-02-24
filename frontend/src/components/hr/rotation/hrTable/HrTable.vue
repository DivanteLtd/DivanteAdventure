<template>
  <v-data-table mobile-breakpoint="0"
                :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                :no-results-text="$t('No results found.')"
                :items="statistics"
                :loading="loading"
                :headers="headers"
                disable-pagination
                hide-default-footer>
    <template v-slot:item="{ item }">
      <hr-table-row
        :entry="item"
        :label-converter="entryLabelConverter"
        :clickable="clickable"
        @click="clickedOnRow"/>
    </template>
  </v-data-table>
</template>

<script>
  import HrTableRow from './HrTableRow';
  import { EventBus, eventNames } from '../../../../eventbus';

  export default {
    name: 'HrTable',
    components: { HrTableRow },
    props: {
      loading: { type: Boolean, required: true },
      statistics: { type: Array, required: true },
      year: { type: Boolean, default: false },
      month: { type: Boolean, default: false },
    },
    data() {
      return {
        headers: [
          {
            text: '',
            align: 'center',
            sortable: false,
          }, {
            text: this.$t('Join company'),
            align: 'center',
            sortable: false,
          }, {
            text: this.$t('Left company'),
            align: 'center',
            sortable: false,
          }, {
            text: this.$t('Balance'),
            align: 'center',
            sortable: false,
          }, {
            text: this.$t('Average work time'),
            align: 'center',
            sortable: false,
          }, {
            text: this.$t('Women (%)'),
            align: 'center',
            sortable: false,
          }, {
            text: this.$t('Men (%)'),
            align: 'center',
            sortable: false,
          }, {
            text: this.$t('Terminated by company'),
            align: 'center',
            sortable: false,
          }, {
            text: this.$t('Terminated by person'),
            align: 'center',
            sortable: false,
          }, {
            text: this.$t('PRI %'),
            align: 'center',
            sortable: false,
          },
        ],
      };
    },
    computed: {
      entryLabelConverter() {
        if (this.month) {
          return this.entryLabelForMonth;
        } else if (this.year) {
          return this.entryLabelForYear;
        } else {
          return this.entryLabelForTime;
        }
      },
      clickable() {
        return !this.month;
      },
    },
    methods: {
      entryLabelForTime(entry) {
        return entry.year;
      },
      entryLabelForYear(entry) {
        return this.$t('month')[entry.month - 1];
      },
      entryLabelForMonth(entry) {
        return entry.tribe;
      },
      clickedOnRow(entry) {
        if (!this.year && !this.month) {
          EventBus.$emit(eventNames.hrStatsShowYear, { year: entry.year });
        } else if (this.year) {
          EventBus.$emit(eventNames.hrStatsShowMonth, { year: entry.year, month: entry.month });
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Loading data...': 'Wczytywanie...',
          'No data available.': 'Brak danych.',
          'No results found.': 'Nie znaleziono.',
          'Rows per page:': 'Wierszy na stronę:',
          'All': 'Wszystkie',

          'Year': 'Rok',
          'Month': 'Miesiąc',
          'Tribe': 'Praktyka',
          'Join company': 'Dołączyło do firmy',
          'Left company': 'Odeszło z firmy',
          'Balance': 'Bilans',
          'Average work time': 'Staż pracy',
          'Women (%)': 'Kobiety (%)',
          'Men (%)': 'Mężczyźni (%)',
          'PRI %': 'PRI (%)',
          'Terminated by company': 'Wypowiedzenie przez firmę',
          'Terminated by person': 'Wypowiedzenie przez osobę',

          'month': [
            'Styczeń',
            'Luty',
            'Marzec',
            'Kwiecień',
            'Maj',
            'Czerwiec',
            'Lipiec',
            'Sierpień',
            'Wrzesień',
            'Październik',
            'Listopad',
            'Grudzień',
          ],
        },
        en: {
          month: [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
          ],
          Tribe: 'Practice',
        },
      },
    },
  };
</script>
