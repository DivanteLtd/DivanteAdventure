<template>
  <v-card-text :class="{'pa-2': $vuetify.breakpoint.xs}">
    <project-window-details :project="project"/>
    <project-window-tabs :project="project" :loading="loading"/>
  </v-card-text>
</template>

<script>
  import ProjectWindowDetails from './ProjectWindowDetails';
  import ProjectWindowTabs from './ProjectWindowTabs';

  export default {
    name: 'ProjectWindowContent',
    components: { ProjectWindowTabs, ProjectWindowDetails },
    props: {
      project: { type: Object, required: true },
      loading: { type: Boolean, default: false },
    },
  };
</script>
