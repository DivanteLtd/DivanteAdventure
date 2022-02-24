<template>
  <v-list-item id="slack-status-switcher">
    <v-list-item-action class="slack__img">
      <v-avatar class="slack__img" tile>
        <v-img src="/static/slack_logo.png" alt="Slack"/>
      </v-avatar>
    </v-list-item-action>
    <v-list-item-content>
      <v-list-item-title>
        {{ slackStatusText }}
      </v-list-item-title>
      <v-list-item-subtitle>
        {{ $t('Slack status') }}
      </v-list-item-subtitle>
    </v-list-item-content>
    <v-list-item-action style="justify-content: flex-end">
      <v-btn v-if="showConnectButton" :disabled="isDemo"
             color="primary" :loading="loading" @click="connect" small>
        {{ $t('Connect') }}
      </v-btn>
      <v-btn v-if="showDisconnectButton" color="error" :loading="loading" @click="disconnect" small>
        {{ $t('Disconnect') }}
      </v-btn>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
  export default {
    name: 'SlackStatusSwitcher',
    props: {
      slackStatus: { type: Number, default: 0 },
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        status: {
          NOT_ASKED: 0,
          ASKED: 1,
          WAITING_FOR_AUTHORIZATION: 2,
          AUTHORIZED: 3,
        },
      };
    },
    computed: {
      isDemo() {
        return window.ADVENTURE_DEMO_ENABLED;
      },
      slackStatusText() {
        if (window.ADVENTURE_DEMO_ENABLED) {
          return this.$t('Slack connection is not available in demo mode');
        }
        switch(this.slackStatus) {
          case this.status.NOT_ASKED: return this.$t('Not connected with Slack');
          case this.status.ASKED: return this.$t('Not connected with Slack');
          case this.status.WAITING_FOR_AUTHORIZATION: return this.$t('Not connected with Slack');
          case this.status.AUTHORIZED: return this.$t('Connected with Slack');
          default: return this.$t('Unknown');
        }
      },
      showConnectButton() {
        return this.slackStatus === this.status.NOT_ASKED
          || this.slackStatus === this.status.ASKED
          || this.slackStatus === this.status.WAITING_FOR_AUTHORIZATION;
      },
      showDisconnectButton() {
        return this.slackStatus === this.status.AUTHORIZED;
      },
    },
    methods: {
      connect() {
        this.$emit('connect');
      },
      disconnect() {
        this.$emit('disconnect');
      },
    },
    i18n: {
      messages: {
        pl: {
          'Slack status': 'Status Slacka',
          'Not connected with Slack': 'Nie połączono ze Slackiem',
          'Connected with Slack': 'Połączono ze Slackiem',
          'Connect': 'Połącz',
          'Disconnect': 'Odłącz',
          'Unknown': 'Nieznany',
          'Slack connection is not available in demo mode': 'Podłączenie slacka nie jest dostępne w wersji demo',
        },
      },
    },
  };
</script>
<style scoped>
  .slack__img{
    height: 24px !important;
    width: 24px !important;
    min-width: 0 !important;
  }
</style>
