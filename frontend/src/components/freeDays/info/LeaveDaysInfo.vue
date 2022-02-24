<template>
  <v-card flat>
    <v-card-title><h5>{{ $t('Leave days') }}</h5></v-card-title>
    <v-divider/>
    <v-list dense>
      <info-tile :title="$t('Used paid leave days:')">
        {{ usedLeaveDaysCount }} / {{ period.freeDays }}
      </info-tile>
      <info-tile :title="$t('Available leave days:')">
        {{ period.freeDays - usedLeaveDaysCount }}
      </info-tile>
      <info-tile :title="$t('Used unpaid leave days:')">
        {{ usedUnpaidLeaveDaysCount }}
      </info-tile>
    </v-list>
  </v-card>
</template>

<script>
  import { countUsedLeaveDaysInPeriod, leaveDaysTypes } from '../../../util/freeDays';
  import InfoTile from './InfoTile';


  export default {
    name: 'LeaveDaysInfo',
    components: { InfoTile },
    props: {
      period: { type: Object, required: true },
    },
    computed: {
      usedLeaveDaysCount() {
        return countUsedLeaveDaysInPeriod(this.period, [leaveDaysTypes.freePaid, leaveDaysTypes.leavePaid,
                                                        leaveDaysTypes.leaveRequest]);
      },
      usedUnpaidLeaveDaysCount() {
        return countUsedLeaveDaysInPeriod(this.period, [leaveDaysTypes.leaveUnpaid, leaveDaysTypes.freeUnpaid]);
      },
    },
    i18n: { messages: {
      pl: {
        'Leave days': 'Urlopy',
        'Used paid leave days:': 'Wykorzystane urlopy płatne:',
        'Available leave days:': 'Dostępne urlopy płatne:',
        'Used unpaid leave days:': 'Wykorzystane urlopy bezpłatne:',
      },
    } },
  };
</script>
