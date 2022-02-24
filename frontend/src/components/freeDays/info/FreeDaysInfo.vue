<template>
  <v-card flat>
    <v-card-title><h5>{{ $t('Free days') }}</h5></v-card-title>
    <v-divider/>
    <v-list dense>
      <info-tile :title="$t('Used paid free days:')">
        {{ usedFreeDaysCount }} / {{ period.freeDays }}
      </info-tile>
      <info-tile :title="$t('Available paid free days:')">
        {{ period.freeDays - usedFreeDaysCount }}
      </info-tile>
      <info-tile :title="$t('Used unpaid free days:')">
        {{ usedUnpaidFreeDays }}
      </info-tile>
    </v-list>
  </v-card>
</template>

<script>
  import { countUsedLeaveDaysInPeriod, leaveDaysTypes } from '../../../util/freeDays';
  import InfoTile from './InfoTile';

  export default {
    name: 'FreeDaysInfo',
    components: { InfoTile },
    props: {
      period: { type: Object, required: true },
    },
    computed: {
      usedFreeDaysCount() {
        return countUsedLeaveDaysInPeriod(this.period, [leaveDaysTypes.freePaid, leaveDaysTypes.leavePaid]);
      },
      usedUnpaidFreeDays() {
        return countUsedLeaveDaysInPeriod(this.period, [leaveDaysTypes.freeUnpaid]);
      },
    },
    i18n: { messages: {
      pl: {
        'Free days': 'Dni wolne',
        'Used paid free days:': 'Wykorzystane płatne dni wolne:',
        'Available paid free days:': 'Dostępne płatne dni wolne:',
        'Used unpaid free days:': 'Wykorzystane bezpłatne dni wolne:',
      },
    } },
  };
</script>
