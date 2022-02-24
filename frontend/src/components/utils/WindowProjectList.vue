<template>
  <v-list two-line dense>
    <v-list-item v-for="(project, index) in projects" :key="index" @click="showProject(project)">
      <v-list-item-content>
        <v-list-item-title>{{ project.name }}</v-list-item-title>
        <v-list-item-subtitle>{{ project.description }}</v-list-item-subtitle>
      </v-list-item-content>
    </v-list-item>
  </v-list>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';

  export default {
    name: 'WindowProjectList',
    props: {
      projects: { type: Array, required: true },
    },
    methods: {
      showProject(project) {
        EventBus.$emit(eventNames.showProjectWindow, project);
      },
    },
  };
</script>
