<template>
  <v-container id="page-stats" grid-list-xl fluid class="page-wrapper">
    <template v-if="$vuetify.breakpoint.mdAndUp">
      <planner-stats/>
    </template>
    <template v-else >
      <mobile-alert alert-name="planner stats" plural/>
    </template>
  </v-container>
</template>

<script>
  import PlannerStats from '../../components/planner/PlannerStats';
  import MobileAlert from '../../components/utils/MobileAlert';

  export default {
    name: 'Stats',
    components: { PlannerStats, MobileAlert },
  };
</script>
<style scoped>
  .page-wrapper{
    display: flex;
  }
</style>
