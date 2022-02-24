<template>
  <v-dialog v-model="visible" id="loading-dialog" width="300px">
    <v-card color="primary" dark>
      <v-card-text>
        {{ $t('Generating report') }}
        <v-progress-linear height="6" indeterminate color="white"/>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    name: 'LoadingDialog',
    props: {
      visible: { type: Boolean, default: false },
    },
    i18n: {
      messages: {
        pl: { 'Generating report': 'Generowanie raportu' },
      },
    },
  };
</script>
