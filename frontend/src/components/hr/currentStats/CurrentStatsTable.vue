<template>
  <v-progress-linear height="6" v-if="loading" indeterminate/>
  <current-stats-view v-else :stats="stats"/>
</template>

<script>
  import CurrentStatsView from './CurrentStatsView';

  export default {
    name: 'CurrentStatsTable',
    components: { CurrentStatsView },
    props: {
      loading: { type: Boolean, required: true },
      employees: { type: Array, required: true },
      stats: { type: Object, required: true },
    },
  };
</script>
