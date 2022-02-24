<template>
  <page-card :title="$t('Projects')">
    <project-list-table :loading="loading" :projects="filteredProjects"/>
    <template slot="options">
      <v-row no-gutters >
        <v-text-field v-model="search"
                      append-icon="search"
                      :label="$t('Search')"
                      single-line hide-details/>
        <project-more-menu/>
      </v-row>
    </template>
  </page-card>
</template>

<script>
  import PageCard from '../utils/PageCard';
  import ProjectListTable from './ProjectListTable';
  import ProjectMoreMenu from './ProjectMoreMenu';

  export default {
    name: 'ProjectListCard',
    components: { ProjectListTable, ProjectMoreMenu, PageCard },
    props: {
      loading: { type: Boolean, default: false },
      projects: { type: Array, required: true },
    },
    data() {
      return {
        search: '',
      };
    },
    computed: {
      filteredProjects() {
        const searchLower = this.search.toLowerCase();
        return this.projects.filter(
          project => project.visibility !== 1 && project.name.toLowerCase().includes(searchLower),
        );
      },
    },
    i18n: { messages: {
      pl: {
        Projects: 'Projekty',
        Search: 'Szukaj',
      },
    },
    },
  };
</script>
