<template>
  <v-data-table mobile-breakpoint="0"
                :items-per-page="5"
                :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                :no-results-text="$t('No results found.')"
                :items="getTribeStatistics(date)"
                :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                :loading="loading"
                :headers="headers"
                class="tribe-stats-table"
                disable-pagination
                hide-default-footer>
    <template v-slot:item="{ item }">
      <tribe-stats-row :item="item"/>
    </template>
  </v-data-table>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import { mapGetters } from 'vuex';
  import TribeStatsRow from './TribeStatsRow';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'TribesStatsTable',
    components: { TribeStatsRow },
    props: {
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        data: [],
        date: moment().format('YYYY-MM'),
        headers: [
          {
            align: 'center',
            value: 'name',
            sortable: false,
            text: this.$t('Tribe name'),
          },
          {
            align: 'center',
            value: 'came',
            sortable: false,
            text: this.$t('Came'),
          },
          {
            align: 'center',
            value: 'left',
            sortable: false,
            text: this.$t('Left'),
          },
          {
            align: 'center',
            value: 'worked',
            sortable: false,
            text: this.$t('Number of members'),
          },
        ],
      };
    },
    computed: {
      ...mapGetters('Stats', {
        getTribeStatistics: 'getTribeStatistics',
      }),
    },
    methods: {
      changeDate(date) {
        this.date = date;
      },
    },
    mounted() {
      EventBus.$on(eventNames.selectDate, this.changeDate);
    },
    i18n: {
      messages: {
        pl: {
          'Tribe name': 'Praktyka/Dział',
          'Came': ' Przyszło',
          'Left': 'Odeszło',
          'Number of members': 'Liczba członków',
          'No data available.': 'Brak danych',
          'Loading data...': 'Ładowanie danych',
        },
        en: {
          'Tribe name': 'Practice name',
        },
      },
    },
  };
</script>
