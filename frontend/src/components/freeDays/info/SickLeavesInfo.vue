<template>
  <v-card flat>
    <v-card-title><h5 class="invisible">.</h5></v-card-title>
    <v-divider/>
    <v-list dense>
      <info-tile :title="$t('Used sick leaves:')">
        {{ usedSickLeaveDays }}
      </info-tile>
    </v-list>
  </v-card>
</template>

<script>
  import { countUsedLeaveDaysInPeriod, leaveDaysTypes } from '../../../util/freeDays';
  import InfoTile from './InfoTile';

  export default {
    name: 'SickLeavesInfo',
    components: { InfoTile },
    props: {
      period: { type: Object, required: true },
    },
    computed: {
      usedSickLeaveDays() {
        return countUsedLeaveDaysInPeriod(this.period, [leaveDaysTypes.sickLeavePaid]);
      },
    },
    i18n: { messages: {
      pl: {
        'Used sick leaves:': 'Wykorzystane dni chorobowe:',
      },
    } },
  };
</script>
<style scoped>
  h5.invisible {
    color: rgba(0, 0, 0, 0);
  }
</style>
