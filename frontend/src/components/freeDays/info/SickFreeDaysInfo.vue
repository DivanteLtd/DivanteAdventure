<template>
  <v-card flat>
    <v-card-title><h5>{{ $t('Sick leave days') }}</h5></v-card-title>
    <v-divider/>
    <v-list dense>
      <info-tile :title="$t('Used paid sick leaves:')">
        {{ usedPaidSickLeaveDays }} / {{ period.sickLeaveDays }}
      </info-tile>
      <info-tile :title="$t('Available paid sick leaves:')">
        {{ period.sickLeaveDays - usedPaidSickLeaveDays }}
      </info-tile>
      <info-tile :title="$t('Used unpaid sick leaves:')">
        {{ usedUnpaidSickLeaveDays }}
      </info-tile>
    </v-list>
  </v-card>
</template>

<script>
  import { countUsedLeaveDaysInPeriod, leaveDaysTypes } from '../../../util/freeDays';
  import InfoTile from './InfoTile';

  export default {
    name: 'SickFreeDaysInfo',
    components: { InfoTile },
    props: {
      period: { type: Object, required: true },
    },
    computed: {
      usedPaidSickLeaveDays() {
        return countUsedLeaveDaysInPeriod(this.period, [leaveDaysTypes.sickLeavePaid]);
      },
      usedUnpaidSickLeaveDays() {
        return countUsedLeaveDaysInPeriod(this.period, [leaveDaysTypes.sickLeaveUnpaid]);
      },
    },
    i18n: { messages: {
      pl: {
        'Sick leave days': 'Dni chorobowe',
        'Used paid sick leaves:': 'Wykorzystane płatne dni chorobowe:',
        'Available paid sick leaves:': 'Dostępne płatne dni chorobowe:',
        'Used unpaid sick leaves:': 'Wykorzystane bezpłatne dni chorobowe:',
      },
    } },
  };
</script>
