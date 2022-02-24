<template>
  <v-menu id="component-app-notifications"
          v-if="notifications.length > 0"
          origin="center center"
          :nudge-bottom="10"
          transition="scale-transition"
          :close-on-content-click="false"
          offset-y>
    <template v-slot:activator="{ on }">
      <v-btn class="mr-2" icon large text v-on="on">
        <v-badge :value="!!unreadNotifications.length" color="red" left overlap>
          <template v-slot:badge>
            <span class="notifications-icon">{{ unreadNotifications.length }}</span>
          </template>
          <v-icon>notification_important</v-icon>
        </v-badge>
      </v-btn>
    </template>
    <v-list class="notifications">
      <notification-row v-for="(notification, index) in notifications" :key="index" :notification="notification"/>
    </v-list>
  </v-menu>
</template>

<script>
  import { mapState } from 'vuex';
  import NotificationRow from './NotificationRow';

  export default {
    name: 'NotificationsMenu',
    components: { NotificationRow },
    computed: {
      ...mapState({
        notifications: state => state.Notifications.notifications,
      }),
      unreadNotifications() {
        return this.notifications.filter(notifications => notifications.bold);
      },
    },
  };
</script>
<style scoped>
  .notifications {
    max-height: 400px;
    overflow: auto;
    padding: 8px !important;
  }
</style>
