<template>
  <v-container id="page-marketing" grid-list-xl fluid>
    <v-row no-gutters wrap class="ma-0 pa-0" :class="{'mb-3': $vuetify.breakpoint.smAndUp}">
      <v-col cols="12" class="pa-0 pb-3">
        <marketing-info-top/>
      </v-col>
      <v-col cols="12" class="pa-0 pb-3">
        <marketing-consents :loaded="!loading"/>
      </v-col>
      <v-col cols="12" class="pa-0 pb-3">
        <marketing-info-bottom/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import MarketingInfoTop from '../../components/agreements/Marketing/MarketingInfoTop';
  import MarketingInfoBottom from '../../components/agreements/Marketing/MarketingInfoBottom';
  import MarketingConsents from '../../components/agreements/Marketing/MarketingConsents';

  export default {
    name: 'Marketing',
    components: { MarketingInfoTop, MarketingInfoBottom, MarketingConsents },
    data() {
      return {
        loading: false,
      };
    },
    methods: {
      async loadData() {
        this.loading = true;
        await this.$store.dispatch('Agreements/loadMarketingConsents');
        await this.$store.dispatch('Config/loadContentConfig');
        this.loading = false;
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
  };
</script>
