<template>
  <v-card class="mb-3">
    <v-row no-gutters column class="ma-0">
      <v-app-bar color="transparent" flat dense>
        <slot name="header">
          <v-toolbar-title class="subheading ft-200">
            {{ title }}
          </v-toolbar-title>
        </slot>
        <v-spacer/>
        <slot name="actions"/>
      </v-app-bar>
    </v-row>
    <v-divider/>
    <v-list v-if="loading">
      <v-list-item>
        <v-list-item-content>
          <v-progress-linear height="6" indeterminate/>
        </v-list-item-content>
      </v-list-item>
    </v-list>
    <slot v-else/>
  </v-card>
</template>

<script>
  export default {
    name: 'DashboardCard',
    props: {
      title: { type: String, default: '' },
      loading: { type: Boolean, default: false },
    },
  };
</script>

<style scoped>
  .ft-200 {
    font-weight: 200;
  }
</style>
