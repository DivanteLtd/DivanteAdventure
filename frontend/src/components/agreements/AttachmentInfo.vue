<template>
  <v-alert :value="true" type="info">
    <div v-html="$t('message')">
      {{ $t('message') }}
    </div>
  </v-alert>
</template>

<script>
  export default {
    name: 'AttachmentInfo',
    i18n: {
      messages: {
        pl: {
          message: 'Dodając załącznik, proszę pamiętać o odpowiednim prefixie: <b>pl</b> lub <b>en</b>',
        },
        en: {
          message: 'If you add attachment, you must add prefix: <b>pl</b> or <b>en</b>',
        },
      },
    },
  };
</script>

<style scoped>

</style>
