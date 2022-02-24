<template>
  <v-card flat>
    <v-card-title><h5>{{ $t('Unavailability days') }}</h5></v-card-title>
    <v-divider/>
    <v-list dense>
      <info-tile :title="$t('Used unavailability days:')">
        {{ usedUnavailabilityDaysCount }}
      </info-tile>
    </v-list>
  </v-card>
</template>

<script>
  import { countUsedLeaveDaysInPeriod, leaveDaysTypes } from '../../../util/freeDays';
  import InfoTile from './InfoTile';

  export default {
    name: 'UnavailabilityDaysInfo',
    components: { InfoTile },
    props: {
      period: { type: Object, required: true },
    },
    computed: {
      usedUnavailabilityDaysCount() {
        return countUsedLeaveDaysInPeriod(this.period, [leaveDaysTypes.unavailability]);
      },
    },
    i18n: { messages: {
      pl: {
        'Unavailability days': 'Dni niedostępności',
        'Used unavailability days:': 'Wykorzystane dni niedostępności:',
      },
    } },
  };
</script>
