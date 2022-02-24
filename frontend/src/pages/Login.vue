<template>
  <v-app id="page-login" class="primary">
    <v-content>
      <v-container fluid fill-height>
        <v-row no-gutters class="d-flex justify-center align-center">
          <v-col cols="12" sm="8" md="4" lg="4">
            <v-card class="elevation-1 pa-4">
              <v-card-text>
                <div class="layout column align-center">
                  <v-img src="/static/img/dvnt_logo_black.png" alt="Adventure" width="120" height="120"/>
                  <h1 class="flex my-4 primary--text text-center">Adventure - Open HRM</h1>
                </div>
              </v-card-text>
              <v-card-actions>
                <v-btn block color="primary" @click="login" :loading="loading">
                  {{ $t('Login with Google') }}
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-content>
    <v-footer v-if="demoEnabled" absolute class="app--footer" style="margin: 0; padding: 0;">
      <demo-notification/>
    </v-footer>
  </v-app>
</template>

<script>
  import { EventBus, eventNames } from '../eventbus';
  import DemoNotification from '../components/utils/DemoNotification';

  export default {
    name: 'Login',
    components: { DemoNotification },
    data: () => ({
      demoEnabled: window.ADVENTURE_DEMO_ENABLED || false,
      loading: false,
    }),
    methods: {
      login() {
        this.loading = true;
        const redirect = `${window.ADVENTURE_BACKEND_URL}/connect/google`;
        window.location.replace(redirect);
      },
    },
    async beforeMount() {
      if (this.$route.name === 'Logout' && !this.$store.getters['Authorization/isLoggedIn']) {
        this.$router.push('/login');
      } else if (this.$route.name === 'Logout') {
        this.$store.commit('Authorization/logout');
        this.$store.commit('showSnackbar', {
          text: this.$t('Logged out successfully'),
          color: 'success',
        });
      } else if (this.$store.getters['Authorization/isLoggedIn'] && this.$store.state.pinEntered) {
        this.loading = true;
        this.$router.push('/dashboard');
      }
      else if (this.$route.query.hasOwnProperty('token') && this.$route.query.hasOwnProperty('refresh_token')) {
        this.loading = true;
        // eslint-disable-next-line camelcase
        const { token, refresh_token } = this.$route.query;
        const query = Object.assign({}, this.$route.query);
        delete query.token;
        delete query.refresh_token;
        this.$router.replace({ query });
        await this.$store.dispatch('Authorization/setToken', { token, refresh_token });
        if (this.$store.state.Employees.loggedEmployee.hasSetPin) {
          EventBus.$emit(eventNames.showPinWindow);
        } else {
          this.$router.push('/dashboard');
        }
      }
    },
    i18n: { messages: {
      pl: {
        'Login with Google': 'Zaloguj się przez Google',
        'Logged out successfully': 'Wylogowano pomyślnie',
      },
    } },
  };
</script>
<style scoped lang="css">
  #login {
    height: 50%;
    width: 100%;
    position: absolute;
    top: 0;
    left: 0;
    content: "";
    z-index: 0;
  }
</style>
