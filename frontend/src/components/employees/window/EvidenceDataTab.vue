<template>
  <v-list two-line>
    <evidence-data-row v-for="(evidence, index) in sortedEvidences" :key="index" :evidence="evidence"/>
  </v-list>
</template>

<script>
  import EvidenceDataRow from './EvidenceDataRow';

  export default {
    name: 'EvidenceDataTab',
    components: { EvidenceDataRow },
    props: {
      evidences: { type: Array, default: () => ([]) },
    },
    computed: {
      sortedEvidences() {
        return this.evidences.sort((a, b) => (a.year === b.year ? b.month - a.month : b.year - a.year));
      },
    },
  };
</script>
