<template>
  <dashboard-view id="page-free-days-dashboard" :loading="loading" :update-function="loadData"/>
</template>

<script>
  import DashboardView from '../../components/freeDays/DashboardView';

  export default {
    name: 'Dashboard',
    components: { DashboardView },
    data() {
      return {
        loading: false,
      };
    },
    methods: {
      async loadData() {
        this.loading = true;
        await Promise.all([
          this.$store.dispatch('FreeDays/loadDashboardDays'),
          this.$store.dispatch('Employees/loadAllEmployeesWorkLocations'),
        ]);
        this.loading = false;
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
  };
</script>
