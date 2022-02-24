<template>
  <v-app-bar id="component-app-toolbar" class="px-2" color="primary" fixed dark app>
    <v-app-bar-nav-icon @click.stop="handleDrawerToggle"></v-app-bar-nav-icon>
    <template v-if="$vuetify.breakpoint.smAndUp">
      <search-field/>
    </template>
    <v-spacer></v-spacer>
    <delegation-menu/>
    <remotely-menu/>
    <language-menu/>
    <notifications-menu/>
    <push-notification-menu/>
    <app-menu/>
  </v-app-bar>
</template>
<script>
  import AppMenu from './menu/AppMenu';
  import NotificationsMenu from './menu/NotificationsMenu';
  import RemotelyMenu from './menu/RemotelyMenu';
  import DelegationMenu from './menu/DelegationMenu';
  import LanguageMenu from './menu/LanguageMenu';
  import SearchField from './globalSearch/SearchField';
  import PushNotificationMenu from './menu/PushNotificationMenu';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'AppToolbar',
    components: {
      PushNotificationMenu,
      SearchField,
      LanguageMenu,
      AppMenu,
      NotificationsMenu,
      RemotelyMenu,
      DelegationMenu,
    },
    methods: {
      async loadData() {
        await this.$store.dispatch('Employees/loadEmployee');
        await this.$store.dispatch('Employees/loadEmployees');
        await this.$store.dispatch('Employees/loadEmployeeWorkLocations');
        await this.$store.dispatch('FreeDays/loadConfirmedLeaveDaysByEmployee');
        const end = moment().endOf('year').add({ year: 1 });
        await this.$store.dispatch('FreeDays/loadStatutoryFreeDays', { start: moment(), end });
      },
      handleDrawerToggle() {
        window.getApp.$emit('APP_DRAWER_TOGGLED');
      },
    },
    mounted() {
      if (this.$store.getters['Authorization/isLoggedIn']) {
        this.loadData();
      }
    },
  };
</script>
