<template>
  <page-card :title="$t('Tribes - statistics')">
    <v-container class="pb-0" :class="{'pa-4': $vuetify.breakpoint.xs}">
      <tribe-stats-calendar/>
    </v-container>
    <tribe-stats-table :loading="loading"/>
  </page-card>
</template>

<script>
  import PageCard from '../../utils/PageCard';
  import TribeStatsCalendar from './TribeStatsCalendar';
  import TribeStatsTable from './TribesStatsTable';

  export default {
    name: 'TribeStatsCard',
    components: { PageCard, TribeStatsCalendar, TribeStatsTable },
    props: {
      loading: { type: Boolean, default: false },
    },
    i18n: {
      messages: {
        pl: {
          'Tribes - statistics': 'Praktyki - statystyki',
        },
        en: {
          'Tribes - statistics': 'Practice - statistics',
        },
      },
    },
  };
</script>
