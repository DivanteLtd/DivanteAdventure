<template>
  <v-list-item class="pa-2 evidence-row" @click="openEvidenceWindow">
    <v-list-item-content>
      <v-list-item-title :class="{'text-xs-center': $vuetify.breakpoint.xs}">
        {{ month }}
      </v-list-item-title>
      <v-list-item-subtitle :class="{'text-xs-center': $vuetify.breakpoint.xs}">
        <span v-if="overtimeHours === 0">
          {{ $t('working-hours', [workingHours]) }}
        </span>
        <span v-else>
          {{ $t('working-hours-overtime', [workingHours, overtimeHours]) }}
        </span>
      </v-list-item-subtitle>
    </v-list-item-content>
    <v-list-item-action>
      <evidence-status-chip :evidence="evidence"/>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import EvidenceStatusChip from '../../evidences/EvidenceStatusChip';
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'EvidenceDataRow',
    components: { EvidenceStatusChip },
    props: {
      evidence: { type: Object, required: true },
    },
    computed: {
      month() {
        return moment(`${this.evidence.year}-${this.evidence.month}-15`).format('MMMM YYYY');
      },
      workingHours() {
        return Math.round(this.evidence.workingHours);
      },
      overtimeHours() {
        return this.evidence.overtimeEntries.map(entry => entry.hours).reduce((a, b) => (a * 1 + b * 1), 0);
      },
    },
    methods: {
      openEvidenceWindow() {
        EventBus.$emit(eventNames.showEvidenceWindow, this.evidence);
      },
    },
    i18n: {
      messages: {
        pl: {
          'working-hours': '{0} godzin pracujących',
          'working-hours-overtime': '{0} godzin pracujących + {1} godzin dodatkowych',
        },
        en: {
          'working-hours': '{0} working hours',
          'working-hours-overtime': '{0} working hours + {1} additional hours',
        },
      },
    },
  };
</script>
<style scoped>
  .evidence-row{
    justify-content: center;
  }
</style>
