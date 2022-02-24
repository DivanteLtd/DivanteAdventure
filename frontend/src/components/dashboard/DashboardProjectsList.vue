<template>
  <dashboard-card :title="$t('Projects')" :loading="!loaded">
    <v-list class="pa-0" dense>
      <v-list-item v-for="(project, index) of projects" :key="index" @click="showProject(project)">
        <v-list-item-content>
          <v-list-item-title>{{ project.name }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </dashboard-card>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import DashboardCard from './DashboardCard';

  export default {
    name: 'DashboardProjectsList',
    components: { DashboardCard },
    props: {
      projects: { type: Array, default: () => ([]) },
      loaded: { type: Boolean, default: false },
    },
    methods: {
      showProject(project) {
        EventBus.$emit(eventNames.showProjectWindow, project);
      },
    },
    i18n: { messages: {
      pl: {
        Projects: 'Projekty',
      },
    },
    },
  };
</script>
