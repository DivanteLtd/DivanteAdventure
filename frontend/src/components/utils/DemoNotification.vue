<template>
  <v-alert tile type="warning" style="width: 100%; margin: 0;">
    <strong>{{ $t('message-1') }}</strong>
    <span>{{ $t('message-2') }}</span>
    <strong>{{ $t('message-3') }} {{ timer }}</strong>
  </v-alert>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import demoTimer from '../../util/demoTimer';

  export default {
    name: 'DemoNotification',
    data() {
      return {
        now: moment(),
        interval: null,
      };
    },
    computed: {
      timer() {
        return demoTimer(this.now);
      },
    },
    created() {
      this.interval = setInterval(() => {
        this.now = moment();
      }, 490);
    },
    beforeDestroy() {
      clearInterval(this.interval);
    },
    i18n: {
      messages: {
        pl: {
          'message-1': 'To jest instancja demo Adventure - Open HRM.',
          'message-2': 'Możesz wykonywać tutaj dowolne czynności oraz zmieniać swoje uprawnienia.',
          'message-3': 'Dane instancji demo zostaną wyczyszczone za: ',
        },
        en: {
          'message-1': 'This is a demo instance of Adventure - Open HRM.',
          'message-2': 'You can do whatever you want here and change your permissions.',
          'message-3': 'Instance data will be cleaned in: ',
        },
      },
    },
  };
</script>
