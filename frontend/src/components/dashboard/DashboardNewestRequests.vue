<template>
  <dashboard-card :title="$t('Your newest requests')" :loading="!loaded">
    <request-table :period="fakePeriod" reduced/>
  </dashboard-card>
</template>

<script>
  import { mapState } from 'vuex';
  import RequestTable from '../freeDays/requests/RequestTable';
  import DashboardCard from './DashboardCard';
  import { leaveRequestsStatuses } from '../../util/freeDays';

  export default {
    name: 'DashboardNewestRequests',
    components: { DashboardCard, RequestTable },
    props: {
      loaded: { type: Boolean, default: false },
    },
    computed: {
      ...mapState({
        periods: state => state.FreeDays.myPeriods,
      }),
      requests() {
        return this.periods
          .map(period => period.requests)
          .reduce((a, b) => a.concat(b), [])
          .sort((a, b) => b.createdAt.localeCompare(a.createdAt))
          .slice(0, 3);
      },
      fakePeriod() {
        return { requests: this.requests.filter(request => request.status !== leaveRequestsStatuses.planned) };
      },
    },
    i18n: { messages: {
      pl: {
        'Your newest requests': 'Twoje najnowsze wnioski',
      },
    },
    },
  };
</script>
