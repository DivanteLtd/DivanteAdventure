<template>
  <tribe-stats-card id="page-hr-tribe-stats" :loading="loading" />
</template>

<script>
  import TribeStatsCard from '../../components/hr/tribeStats/TribeStatsCard';
  import { mapActions } from 'vuex';

  export default {
    name: 'TribeStats',
    components: { TribeStatsCard },
    data() {
      return {
        loading: false,
      };
    },
    methods: {
      ...mapActions('Stats', [
        'load',
      ]),
      async loadData() {
        this.loading = true;
        await this.load();
        this.loading = false;
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
  };
</script>

<style scoped>

</style>
