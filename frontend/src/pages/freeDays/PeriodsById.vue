<template>
  <period-view-container id="page-period-view-by-id" :loading="loading" :period-update-function="loadData"/>
</template>

<script>
  import PeriodViewContainer from '../../components/freeDays/PeriodViewContainer';

  export default {
    name: 'PeriodsById',
    components: { PeriodViewContainer },
    data() {
      return {
        loading: false,
      };
    },
    methods: {
      loadData() {
        const loadId = this.$route.params.id;
        this.loading = true;
        this.$store.dispatch('FreeDays/loadPeriodsByEmployee', loadId).finally(() => {
          this.loading = false;
        });
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
  };
</script>
