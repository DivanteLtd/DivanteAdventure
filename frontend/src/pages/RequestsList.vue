<template>
  <requests-view id="page-requests-list" :loading="loading" :reload-function="loadData"/>
</template>

<script>
  import RequestsView from '../components/requests/RequestsView';

  export default {
    name: 'RequestsList',
    components: { RequestsView },
    data() { return {
      loading: false,
    };},
    methods: {
      async loadData() {
        this.loading = true;
        await this.$store.dispatch('Requests/loadRequests');
        this.loading = false;
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
  };
</script>
