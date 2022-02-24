<template>
  <v-container>
    <v-row no-gutters wrap class="mb-2">
      <v-col lg="4" sm="12" cols="12">
        <v-card>
          <v-card-title class="title">{{ $t('Slack administration notifications') }}</v-card-title>
          <v-divider/>
          <v-card-text>
            <v-list dense>
              <v-list-item>{{ $t('Notifications about errors and other problems') }}</v-list-item>
              <slack-status-switcher :loading="loading || slackLoading"
                                     :slack-status="slackStatus"
                                     @connect="connect"
                                     @disconnect="disconnect"/>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import SlackStatusSwitcher from '../utils/SlackStatusSwitcher';
  import { mapState } from 'vuex';

  const STATUS_KEY = 'slack.admin_status';
  export default {
    name: 'OtherSettings',
    components: { SlackStatusSwitcher },
    props: {
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        slackLoading: false,
      };
    },
    computed: {
      ...mapState({
        entries: state => state.Config.config,
        token: state => state.Authorization.token,
      }),
      slackStatus() {
        return parseInt(this.entries.filter(e => e.key === STATUS_KEY)[0].value || '0');
      },
    },
    methods: {
      async disconnect() {
        this.slackLoading = true;
        await this.$store.dispatch('Config/updateEntry', { key: STATUS_KEY, value: 0 });
        this.slackLoading = false;
      },
      async connect() {
        const redirect = `${window.ADVENTURE_BACKEND_URL}/slack/redirectUser?token=${this.token}&type=admin`;
        window.location.replace(redirect);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Slack administration notifications': 'Powiadomienia administracyjne na Slacka',
          'Notifications about errors and other problems': 'Powiadomienia o błędach i innych problemach',
        },
      },
    },
  };
</script>
