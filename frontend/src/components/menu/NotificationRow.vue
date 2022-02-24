<template>
  <v-list-item>
    <v-list-item-action class="notification-delete ma-0">
      <v-btn :loading="loadingDelete" :title="$t('Delete')" text @click="deleteNotification(notification.id)">
        <v-icon>delete</v-icon>
      </v-btn>
    </v-list-item-action>
    <v-list-item-content class="notification-title pa-0">
      <v-btn :title="$t('Go to see')" text class="text-none font-weight-regular"
             @click="goToNotificationSubject(notification.path, notification.id)">
        <v-list-item-title v-if="notification.bold" class="font-weight-bold">
          {{ $t(`notifications.${notification.description}`) + ' ' + notification.subject }}
        </v-list-item-title>
        <v-list-item-title v-else>
          {{ $t(`notifications.${notification.description}`) + ' ' + notification.subject }}
        </v-list-item-title>
      </v-btn>
    </v-list-item-content>
  </v-list-item>
</template>

<script>
  export default {
    name: 'NotificationRow',
    props: {
      notification: { type: Object, required: true },
    },
    data() {
      return {
        loadingDelete: false,
      };
    },
    methods: {
      async deleteNotification(id) {
        this.loadingDelete = true;
        await this.$store.dispatch('Notifications/delete', id);
        this.loadingDelete = false;
      },
      goToNotificationSubject(url, id) {
        this.$router.push(url);
        this.$store.dispatch('Notifications/unmark', id);
      },
    },
    i18n: { messages: {
      pl: {
        'Delete': 'Usuń',
        'Go to see': 'Przejdź aby zobaczyć',
      },
    },
    },
  };
</script>

<style>
  .notification-delete .v-btn{
    padding: 0 !important;
    min-width: 50px !important;
  }
  .notification-title .v-btn{
    padding: 0 !important;
    width: 100%;
  }
  .notification-title .v-btn__content{
    width: inherit;
    text-align: left;
  }
  .notifications .v-list-item{
    padding: 8px !important;
  }
  .notification-title .v-list-item__title{
    white-space: initial;
    padding: 4px;
    font-size: small;
  }
</style>
