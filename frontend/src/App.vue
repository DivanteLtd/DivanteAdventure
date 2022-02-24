<template>
  <v-app class="application">
    <template v-if="loaded && checkPinStatus && showUserEdit && typeof loggedEmployee.id !== 'undefined'">
      <first-login-view/>
    </template>
    <template v-else-if="!$route.meta.public">
      <v-app id="inspire" class="app test">
        <app-drawer class="app--drawer"></app-drawer>
        <app-toolbar class="app--toolbar"></app-toolbar>

        <v-content>
          <div class="page-wrapper pb-12">
            <v-alert
              border="top"
              color="red lighten-2"
              dark
            >
              {{ $t('alert') }}
            </v-alert>
            <router-view></router-view>
          </div>
          <!-- App Footer -->
          <v-footer v-if="demoEnabled">
            <demo-notification/>
          </v-footer>
          <v-footer v-else absolute class="white app--footer pa-4">
            <span class="caption">
              Adventure Team &copy; {{ year }}
            </span>
          </v-footer>
        </v-content>
        <!-- Go to top -->
        <app-fab></app-fab>
      </v-app>
    </template>
    <template v-else>
      <transition>
        <keep-alive>
          <router-view></router-view>
        </keep-alive>
      </transition>
    </template>
    <v-snackbar :timeout="3000"
                :color="snackbar.color"
                v-model="snackbar.show"
                bottom right>
      {{ snackbar.text }}
      <v-btn dark text @click.native="hideSnackbar()" icon>
        <v-icon>close</v-icon>
      </v-btn>
    </v-snackbar>
    <dialog-container style="display: none"/>
  </v-app>
</template>
<script>
  import AppDrawer from './components/AppDrawer';
  import AppToolbar from './components/AppToolbar';
  import AppFab from './components/AppFab';
  import DialogContainer from './pages/DialogContainer';
  import { mapState, mapGetters } from 'vuex';
  import FirstLoginView from './components/employees/firstLogin/FirstLoginView';
  import DemoNotification from './components/utils/DemoNotification';

  export default {
    components: {
      DemoNotification,
      FirstLoginView,
      DialogContainer,
      AppDrawer,
      AppToolbar,
      AppFab,
    },
    data: () => ({
      demoEnabled: window.ADVENTURE_DEMO_ENABLED || false,
      expanded: true,
      rightDrawer: false,
      year: new Date().getFullYear(),
    }),
    computed: {
      ...mapState({
        snackbar: state => state.appSnackbar,
        pinEntered: state => state.pinEntered,
        loaded: state => state.loaded,
        loggedEmployee: state => state.Employees.loggedEmployee,
      }),
      ...mapGetters({
        showUserEdit: 'Employees/redirectToUserEdit',
        isLoggedIn: 'Authorization/isLoggedIn',
      }),
      checkPinStatus() {
        return this.loggedEmployee.hasSetPin === true ? this.pinEntered : this.isLoggedIn;
      },
    },
    methods: {
      hideSnackbar() {
        this.$store.commit('hideSnackbar');
      },
    },
    created() {
      window.getApp = this;
      this.$store.dispatch('init');
    },
    i18n: {
      messages: {
        pl: {
          alert: 'ALERT.',
        },
        en: {
          alert: 'ALERT',
        },
      },
    },
  };
</script>

<style scoped>
  .page-wrapper {
    min-height: calc(100vh - 64px - 50px - 81px);
  }
</style>
<style>
  .application {
    font-family: 'basier_circleregular', Fallback, sans-serif !important;
    font-size: 15px;
  }
  .v-card__text, .v-card__title {
    word-break: normal;
  }
  .headline,
  .title,
  .subheading {
    font-family: "basier_circleregular", sans-serif !important;
  }
  ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
    background-color: #fafafa;
  }
  ::-webkit-scrollbar-thumb {
    background-color: darkgrey;
  }
  #component-app-notifications .v-badge__badge{
      width: unset;
      min-width: 22px;
  }
  .v-dialog>.v-card>.v-card__subtitle, .v-dialog>.v-card>.v-card__text{
    padding: 24px 20px;
  }
  .v-slide-group__prev {
    display: none !important;
  }
  @media only screen and (max-width: 600px) {
    .container {
      padding: 8px;
    }
    .v-slide-group__prev{
      display: none !important;
    }
    #dialog-project-details .v-list__tile{
      padding: 0;
    }
    .v-dialog:not(.v-dialog--fullscreen){
      margin: 16px;
      max-height: 95%;
    }
    .v-list__tile__action, .v-list__tile__avatar{
      min-width: 32px;
    }
    .v-menu__content .v-select-list .v-list__tile{
      padding: 0 8px;
    }
    .search-item span{
      font-size: small;
    }
    .v-picker__body{
      width: 250px !important;
    }
    .v-expansion-panel-content__wrap{
      padding: 0;
    }
  }
  @media only screen and (min-width: 1264px) {
    .container {
      max-width: 3840px;
    }
  }
</style>
