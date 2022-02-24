<template>
  <v-container id="page-positions" grid-list-xl fluid>
    <positions-container :loading="loading"/>
  </v-container>
</template>

<script>
  import PositionsContainer from '../components/positions/PositionsContainer';

  export default {
    name: 'Positions',
    components: { PositionsContainer },
    data() {
      return {
        loading: false,
      };
    },
    methods: {
      async loadData() {
        this.loading = true;
        await Promise.all([
          this.$store.dispatch('Positions/loadPositions'),
          this.$store.dispatch('Positions/loadLevels'),
          this.$store.dispatch('Positions/loadStrategies'),
        ]);
        this.loading = false;
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
  };
</script>
