<template>
  <v-progress-linear height="6" indeterminate color="blue" :active="displayLoadingBar" class="ma-0 mb-2"/>
</template>

<script>
  export default {
    name: 'LoadingIndicator',
    computed: {
      displayLoadingBar() {
        return this.$store.state.Planner.loadingBarRequestsCount !== 0;
      },
    },
  };
</script>
