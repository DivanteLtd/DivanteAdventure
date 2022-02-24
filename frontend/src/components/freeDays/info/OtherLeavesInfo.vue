<template>
  <v-card flat>
    <v-card-title><h5>{{ $t('Other') }}</h5></v-card-title>
    <v-divider/>
    <v-list dense>
      <info-tile :title="$t('Used leaves on request:')">
        {{ usedLeavesOnRequest }} / 4
      </info-tile>
      <info-tile v-if="usedCareLeaveDays > 0" :title="$t('Used care leave hours:')">
        {{ usedCareLeaveDays }} / 16
      </info-tile>
      <info-tile :title="$t('Used occasional leave days:')">
        {{ usedOccasionalLeaveDays }}
      </info-tile>
    </v-list>
  </v-card>
</template>

<script>
  import { countUsedLeaveCareHoursInPeriod, countUsedLeaveDaysInPeriod, leaveDaysTypes } from '../../../util/freeDays';
  import InfoTile from './InfoTile';

  export default {
    name: 'OtherLeavesInfo',
    components: { InfoTile },
    props: {
      period: { type: Object, required: true },
    },
    computed: {
      usedLeavesOnRequest() {
        return countUsedLeaveDaysInPeriod(this.period, [leaveDaysTypes.leaveRequest]);
      },
      usedCareLeaveDays() {
        return countUsedLeaveCareHoursInPeriod(this.period, leaveDaysTypes.leaveCare);
      },
      usedOccasionalLeaveDays() {
        return countUsedLeaveDaysInPeriod(this.period, [leaveDaysTypes.leaveOccasional]);
      },
    },
    i18n: { messages: {
      pl: {
        'Other': 'Inne',
        'Used leaves on request:': 'Wykorzystane dni urlopu na żądanie:',
        'Used care leave hours:': 'Wykorzystane godziny opieki nad dzieckiem:',
        'Used occasional leave days:': 'Wykorzystane dni urlopu okolicznościowego:',

      },
    } },
  };
</script>
