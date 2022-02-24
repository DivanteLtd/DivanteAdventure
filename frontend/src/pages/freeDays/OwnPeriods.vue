<template>
  <period-view-container id="page-period-view-own" :loading="loading" :period-update-function="loadData" class="pb-4"/>
</template>

<script>
  import PeriodViewContainer from '../../components/freeDays/PeriodViewContainer';

  export default {
    name: 'OwnPeriods',
    components: { PeriodViewContainer },
    data() {
      return {
        loading: false,
      };
    },
    methods: {
      loadData() {
        this.loading = true;
        this.$store.dispatch('FreeDays/loadMyPeriods').finally(() => {
          this.loading = false;
        });
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
  };
</script>
