<template>
  <v-dialog v-model="dialogVisible" width="600" id="dialog-slack" persistent>
    <v-card>
      <v-card-title class="title">{{ $t('Slack integration') }}</v-card-title>
      <v-divider/>
      <v-card-text>
        <div class="subheading">
          {{ $t('text-1') }}
        </div>
        <div class="caption">
          {{ $t('text-2') }}
        </div>
      </v-card-text>
      <v-card-actions>
        <v-spacer/>
        <v-btn color="primary" @click="yes" :loading="loadingYes" text>{{ $t('Yes') }}</v-btn>
        <v-btn color="error" @click="no" :loading="loadingNo" text>{{ $t('No') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { mapState } from 'vuex';

  export default {
    name: 'SlackDialog',
    data() {
      return {
        dialogVisible: false,
        dialogWasSeen: false,
        loadingNo: false,
        loadingYes: false,
      };
    },
    computed: {
      ...mapState({
        showSlackDialog: state => state.Employees.showAskForSlack,
        pinEntered: state => state.pinEntered,
        token: state => state.Authorization.token,
      }),
    },
    watch: {
      showSlackDialog() {
        if (this.showSlackDialog && this.pinEntered && !this.dialogWasSeen && !window.ADVENTURE_DEMO_ENABLED) {
          this.dialogVisible = true;
        }
      },
      pinEntered() {
        if (this.showSlackDialog && this.pinEntered && !this.dialogWasSeen) {
          this.dialogVisible = true;
        }
      },
    },
    methods: {
      yes() {
        this.loadingYes = true;
        const redirect = `${window.ADVENTURE_BACKEND_URL}/slack/redirectUser?token=${this.token}`;
        window.location.replace(redirect);
      },
      async no() {
        this.loadingNo = true;
        await this.$store.dispatch('Employees/hideSlackDialog');
        this.loadingNo = false;
        this.hide();
      },
      hide() {
        this.dialogVisible = false;
        this.dialogWasSeen = true;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Slack integration': 'Integracja ze Slackiem',
          'text-1': 'Czy chcesz dostawać powiadomienia z Adventure na Slacka?',
          'text-2': 'Jeśli zdecydujesz nie włączać tego teraz, możesz zawsze zrobić to w oknie edycji profilu.',
          'Yes': 'Tak',
          'No': 'Nie',
        },
        en: {
          'text-1': 'Do you want to get notifications from Adventure on Slack?',
          'text-2': 'If you decide not to do this now, you can always enable it in your profile.',
        },
      },
    },
  };
</script>
