<template>
  <v-alert :value="showWarning" type="warning" transition="slide-y-transition">
    <h1 class="headline">{{ $t('title') }}</h1>
    <div>
      {{ $t('message') }}
    </div>
  </v-alert>
</template>

<script>
  const SHOW_INFO_RATE = ['200%'];

  export default {
    name: 'Evidence200PercentInfo',
    props: {
      overtimeEntries: { type: Array, default: () => ([]) },
    },
    computed: {
      showWarning() {
        if (this.overtimeEntries.length === 0) {
          return false;
        }
        return this.overtimeEntries.some(entry => SHOW_INFO_RATE.includes(entry.rate));
      },
    },
    i18n: { messages: {
      pl: {
        title: 'Ostrzeżenie',
        message: 'Możesz użyć stawki 200% tylko w niedzielę na wyraźne polecenie przełożonego.',
      },
      en: {
        title: 'Warning',
        message: 'You can only set 200% rate on Sundays and only with direct acceptance of your supervisor.',
      },
    } },
  };
</script>
