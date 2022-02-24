<template>
  <dashboard-card :title="$t('Awaiting requests')" :loading="!loaded">
    <v-list class="pa-0" two-line v-if="requests.length> 0">
      <template v-for="(request, index) of requests">
        <dashboard-request-in-progress v-if="request._inProgress" :key="index"/>
        <dashboard-overtime-request v-else-if="request._reqType === 'overtime'" :key="index" :request="request"/>
        <dashboard-leave-request v-else-if="request._reqType === 'leave'" :key="index" :request="request"/>
      </template>
    </v-list>
    <v-list class="pa-0" v-else dense>
      <v-list-item>
        <v-list-item-title>{{ $t("You don't have any pending requests.") }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </dashboard-card>
</template>

<script>
  import { mapGetters } from 'vuex';
  import DashboardOvertimeRequest from './DashboardOvertimeRequest';
  import DashboardLeaveRequest from './DashboardLeaveRequest';
  import { EventBus, eventNames } from '../../eventbus';
  import DashboardRequestInProgress from './DashboardRequestInProgress';
  import DashboardCard from './DashboardCard';

  export default {
    name: 'DashboardPendingRequests',
    components: { DashboardCard, DashboardRequestInProgress, DashboardLeaveRequest, DashboardOvertimeRequest },
    props: {
      loaded: { type: Boolean, default: false },
    },
    computed: {
      ...mapGetters({
        requests: 'Requests/getRequestsForDashboard',
      }),
    },
    methods: {
      updateRequest(data) {
        this.$store.commit('Requests/updateRequestStatus', data);
      },
      removeRequest(data) {
        this.$store.commit('Requests/removeRequest', data);
      },
    },
    mounted() {
      EventBus.$on(eventNames.requestStatusUpdateBefore, this.updateRequest);
      EventBus.$on(eventNames.requestStatusUpdate, this.removeRequest);
    },
    i18n: { messages: {
      pl: {
        'Awaiting requests': 'Oczekujące wnioski',
        "You don't have any pending requests.": 'Nie masz żadnych oczekujących wniosków.',
      },
    },
    },
  };
</script>
