<template>
  <v-list-item @click="click">
    <v-list-item-action>
      <v-icon color="#7F3E30">weekend</v-icon>
    </v-list-item-action>
    <v-list-item-content>
      <v-list-item-title>{{ label }}</v-list-item-title>
      <v-list-item-subtitle>{{ request.employee.name }} {{ request.employee.lastName }}</v-list-item-subtitle>
    </v-list-item-content>
  </v-list-item>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import { leaveRequestsStatuses } from '../../util/freeDays';

  export default {
    name: 'DashboardLeaveRequest',
    props: {
      request: { type: Object, required: true, default: () => ({}) },
    },
    computed: {
      label() {
        const id = this.request.id;
        if (this.request.status === leaveRequestsStatuses.pendingResignation) {
          return this.$t('request_resign_id', { id });
        }
        else {
          return this.$t('request_id', { id });
        }
      },
    },
    methods: {
      click() {
        EventBus.$emit(eventNames.showRequestDetailsForAcceptance, this.request);
      },
    },
    i18n: { messages: {
      pl: {
        request_id: 'Wniosek o dni wolne #{id}',
        request_resign_id: 'Rezygnacja z wniosku #{id} o dni wolne',
      },
      en: {
        request_id: 'Leave request #{id}',
        request_resign_id: 'Resignation from leave request #{id}',
      },
    },
    },
  };
</script>
