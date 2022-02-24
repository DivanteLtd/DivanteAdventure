<template>
  <v-row no-gutters wrap class="mobile-alert">
    <v-card class="pa-4 ma-3">
      <p v-if="plural" class="pa-2 ma-0">{{ $t('mobile_unavailable_plural', {name: alertName}) }}</p>
      <p v-else class="pa-2 ma-0">{{ $t('mobile_unavailable', {name: alertName}) }}</p>
    </v-card>
  </v-row>
</template>

<script>
  export default {
    name: 'MobileAlert',
    props: {
      alertName: { type: String, required: true },
      plural: { type: Boolean, required: true },
    },
    i18n: { messages: {
      pl: {
        mobile_unavailable: 'Przepraszamy, {name} jest dostępny tylko w wersji desktopowej.',
        mobile_unavailable_plural: 'Przepraszamy, {name} są dostępne tylko w wersji desktopowej.',
      },
      en: {
        mobile_unavailable: 'Sorry, {name} is available only on desktop version.',
        mobile_unavailable_plural: 'Sorry, {name} are available only on desktop version.',
      },
    } },
  };
</script>

<style scoped>
    .mobile-alert{
      display: flex;
      align-items: center;
      text-align: center;
      justify-content: center;
    }
    p{
      font-size: large;
      font-weight: 500;
    }
</style>
