<template>
  <v-container>
    <v-row no-gutters wrap>
      <v-col cols="12" md="6">
        <evidence-timeline :evidence="evidence"/>
      </v-col>
      <v-col cols="12" md="6">
        <evidence-info :evidence="evidence"/>
      </v-col>
      <v-col cols="12" md="12" v-if="showOvertime">
        <h1 class="title mb-2" style="text-align: center">{{ $t('Overtimes') }}</h1>
        <evidence-overtime-table :overtimes="evidence.overtimeEntries"/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import EvidenceTimeline from './EvidenceTimeline';
  import EvidenceInfo from './EvidenceInfo';
  import EvidenceOvertimeTable from './EvidenceOvertimeTable';

  export default {
    name: 'EvidenceDialogContent',
    components: { EvidenceOvertimeTable, EvidenceInfo, EvidenceTimeline },
    props: {
      evidence: { type: Object, default: () => ({}) },
    },
    computed: {
      showOvertime() {
        return this.evidence.overtimeEntries && this.evidence.overtimeEntries.length > 0;
      },
    },
    i18n: { messages: {
      pl: {
        Overtimes: 'Dodatkowe godziny',
      },
    } },
  };
</script>
