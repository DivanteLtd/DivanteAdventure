<template>
  <v-container>
    <v-row no-gutters wrap>
      <v-col cols="12">
        <evidence-info-alert/>
      </v-col>
      <v-col cols="12">
        <v-progress-linear height="6" indeterminate/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import EvidenceInfoAlert from './EvidenceInfoAlert';

  export default {
    name: 'NewEvidenceFormLoading',
    components: { EvidenceInfoAlert },
  };
</script>
