<template>
  <div id="page-scheduler" class="page-wrapper">
    <template v-if="$vuetify.breakpoint.mdAndUp">
      <planner/>
    </template>
    <template v-else >
      <mobile-alert alert-name="planner" :plural="false"/>
    </template>
  </div>
</template>

<script>
  import Planner from '../../components/planner/Planner';
  import MobileAlert from '../../components/utils/MobileAlert';

  export default {
    name: 'Scheduler',
    components: { Planner, MobileAlert },
  };
</script>

<style scoped>
  .page-wrapper{
    display: flex;
  }
</style>
