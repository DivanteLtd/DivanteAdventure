<template>
  <v-list-item>
    <v-list-item-action>
      <v-icon v-if="icon" color="primary">{{ icon }}</v-icon>
    </v-list-item-action>
    <v-list-item-content>
      <v-list-item-title v-if="title">{{ title }}</v-list-item-title>
      <v-list-item-subtitle v-if="subtitle">{{ subtitle }}</v-list-item-subtitle>
    </v-list-item-content>
  </v-list-item>
</template>

<script>
  export default {
    name: 'DashboardProfileRow',
    props: {
      icon: { type: String, required: false, default: null },
      title: { type: String, required: false, default: null },
      subtitle: { type: String, required: false, default: null },
    },
  };
</script>
