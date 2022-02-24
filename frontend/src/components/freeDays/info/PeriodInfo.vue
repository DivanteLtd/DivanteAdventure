<template>
  <v-container fluid :class="{'pa-4': $vuetify.breakpoint.smAndUp, 'pa-0': $vuetify.breakpoint.xs}">
    <!-- CLC LUMP SUM, B2B LUMP SUM-->
    <v-row no-gutters wrap v-if="freeDaysAllowed">
      <v-col cols="12" md="4">
        <period-general-info :period="period"/>
      </v-col>
      <v-col cols="12" md="4">
        <free-days-info :period="period"/>
      </v-col>
      <v-col cols="12" md="4">
        <sick-free-days-info :period="period"/>
      </v-col>
    </v-row>
    <!-- CLC HOURLY, B2B HOURLY-->
    <v-row no-gutters wrap v-else-if="unavailabilityDaysOnly">
      <v-col cols="12" md="6">
        <period-general-info :period="period"/>
      </v-col>
      <v-col cols="12" md="6">
        <unavailability-days-info :period="period"/>
      </v-col>
    </v-row>
    <!-- CoE -->
    <v-row no-gutters wrap v-else-if="leaveDaysAllowed">
      <v-col cols="12" md="3">
        <period-general-info :period="period"/>
      </v-col>
      <v-col cols="12" md="3">
        <leave-days-info :period="period"/>
      </v-col>
      <v-col cols="12" md="3">
        <other-leaves-info :period="period"/>
      </v-col>
      <v-col cols="12" md="3">
        <sick-leaves-info :period="period"/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import PeriodGeneralInfo from './PeriodGeneralInfo';
  import FreeDaysInfo from './FreeDaysInfo';
  import SickLeavesInfo from './SickLeavesInfo';
  import { canUseFreeDays, canUseLeaveDays, canUseUnavailabilityDays } from '../../../util/freeDays';
  import LeaveDaysInfo from './LeaveDaysInfo';
  import OtherLeavesInfo from './OtherLeavesInfo';
  import SickFreeDaysInfo from './SickFreeDaysInfo';
  import UnavailabilityDaysInfo from './UnavailabilityDaysInfo';

  export default {
    name: 'PeriodInfo',
    components: {
      UnavailabilityDaysInfo,
      SickFreeDaysInfo,
      OtherLeavesInfo,
      LeaveDaysInfo,
      SickLeavesInfo,
      FreeDaysInfo,
      PeriodGeneralInfo,
    },
    props: {
      period: { type: Object, required: true },
    },
    computed: {
      freeDaysAllowed() {
        return canUseFreeDays(this.period.employee);
      },
      leaveDaysAllowed() {
        return canUseLeaveDays(this.period.employee);
      },
      unavailabilityDaysOnly() {
        return canUseUnavailabilityDays(this.period.employee);
      },
    },
  };
</script>
