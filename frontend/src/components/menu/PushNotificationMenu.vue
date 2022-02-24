<template>
  <v-menu id="component-app-push-notifications"
          origin="center center"
          :nudge-bottom="10"
          transition="scale-transition"
          :close-on-content-click="false"
          v-if="visible"
          offset-y>
    <template v-slot:activator="{ on }">
      <v-btn class="mr-2" icon large text v-on="on">
        <v-icon>sms_failed</v-icon>
      </v-btn>
    </template>
    <v-card>
      <v-card-text>
        <v-btn @click="requestNotifications" block>{{ $t('Enable push notifications') }}</v-btn>
      </v-card-text>
    </v-card>
  </v-menu>
</template>

<script>
  import { hasGrantedPermission, enableNotifications, PermissionState } from '../../util/NotificationApi';

  export default {
    name: 'PushNotificationMenu',
    data() {
      return {
        visible: !hasGrantedPermission(),
      };
    },
    methods: {
      async requestNotifications() {
        const result = await enableNotifications();
        if (result === PermissionState.GRANTED) {
          this.visible = false;
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Enable push notifications': 'Włącz powiadomienia push',
        },
      },
    },
  };
</script>
