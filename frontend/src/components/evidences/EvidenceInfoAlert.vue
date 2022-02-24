<template>
  <v-alert :value="true" type="info" class="mt-2">
    <h1 class="headline">{{ $t('title') }}</h1>
    <div v-html="$t('message')" class="evidence-alert__message">
      {{ $t('message') }}
    </div>
  </v-alert>
</template>
<script>
  export default {
    name: 'EvidenceInfoAlert',
    i18n: { messages: {
      pl: {
        title: 'Ewidencje',
        message: 'Ewidencja godzin powinna być wysłana ostatniego dnia miesiąca lub najpóźniej pierwszego dnia roboczego miesiąca następnego.<br/>Wszystkie osoby współpracujące na podstawie umowy cywilnoprawnej lub B2B są zobligowane do wysyłania ewidencji w terminie za pośrednictwiem poniższego formularza.',
      },
      en: {
        title: 'Evidences',
        message: 'Evidences should be sent on the last day of the month or on the first working day of the following month.<br/> All persons cooperating on the basis of civil law contract or B2B are obliged to send the evidence on time through the form below.',
      },
    } },
  };
</script>
<style scoped>
  @media only screen and (max-width: 600px) {
    .evidence-alert__message{
      font-size: small;
    }
  }
</style>
