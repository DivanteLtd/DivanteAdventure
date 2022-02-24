<template>
  <v-container id="system-config"
               class="gdpr-table mb-8"
               :class="{'pa-0': $vuetify.breakpoint.xs}"
               grid-list-xl fluid>
    <config-page-card :loading="loading"/>
  </v-container>
</template>

<script>
  import ConfigPageCard from '../components/config/ConfigPageCard';

  export default {
    name: 'Configuration',
    components: { ConfigPageCard },
    data() {
      return {
        loading: false,
      };
    },
    methods: {
      async loadData() {
        this.loading = true;
        await Promise.all([
          this.$store.dispatch('Config/loadConfig'),
          this.$store.dispatch('Config/loadContentConfig'),
          this.$store.dispatch('Config/loadFreeDays'),
        ]);
        this.loading = false;
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
  };
</script>
