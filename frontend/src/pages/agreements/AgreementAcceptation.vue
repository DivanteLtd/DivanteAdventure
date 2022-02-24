<template>
  <v-container id="page-agreement-acceptation" grid-list-xl fluid>
    <agreement-acceptation-container :loading="loading"/>
  </v-container>
</template>

<script>
  import AgreementAcceptationContainer from '../../components/agreements/AgreementAcceptationContainer';

  export default {
    name: 'AgreementAcceptation',
    components: { AgreementAcceptationContainer },
    data() {
      return {
        loading: false,
      };
    },
    methods: {
      async loadData() {
        this.loading = true;
        await Promise.all([
          this.$store.dispatch('Agreements/loadGDPRAcceptationList'),
          this.$store.dispatch('Agreements/loadMarketingAcceptationList'),
          this.$store.dispatch('Agreements/loadISOAcceptationList'),
          this.$store.dispatch('Agreements/loadAgreements'),
          this.$store.dispatch('Agreements/loadMarketingConsents'),
        ]);
        this.loading = false;
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
  };
</script>
