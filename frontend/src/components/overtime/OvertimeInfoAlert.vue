<template>
  <v-alert :value="true" type="info">
    <h1 class="headline">{{ $t('title') }}</h1>
    <div v-html="$t('message')">
      {{ $t('message') }}
    </div>
  </v-alert>
</template>

<script>
  export default {
    name: 'EvidenceInfoAlert',
    i18n: { messages: {
      pl: {
        title: 'Ewidencja nadgodzin',
        message: 'Ewidencja godzin dodatkowych powinna być wysłana ostatniego dnia miesiąca lub najpóźniej pierwszego dnia roboczego miesiąca następnego.</br> Wszystkie osoby współpracujące na podstawie umowy o pracę, umowy cywilnoprawnej lub B2B są zobligowane do wysyłania ewidencji w terminie za pośrednictwem poniższego formularza.',
      },
      en: {
        title: 'Evidence overtime',
        message: 'Evidence of additional hours should be sent on the last day of the month or at the latest on the first business day of the following month.</br> All persons cooperating on the basis of an employment contract, civil law agreement or B2B are obliged to send records on time via the form below.',
      },
    } },
  };
</script>
