<template>
  <v-container grid-list-xl fluid>
    <v-card>
      <v-app-bar color="transparent" flat :height="checkIfMobile">
        <v-row no-gutters wrap class="align-center">
          <v-spacer/>
          <v-col cols="12" sm="8" md="6" :class="{'pb-2': $vuetify.breakpoint.xs}">
            <v-tabs v-model="selectedTab" centered>
              <v-tab>{{ $t('active-projects', [activeProjects.length]) }}</v-tab>
              <v-tab>{{ $t('archived-projects', [archivedProjects.length]) }}</v-tab>
            </v-tabs>
          </v-col>
          <v-col cols="12" sm="4" md="3" lg="3">
            <v-row no-gutters wrap class="align-center">
              <v-text-field v-model="search" append-icon="search" :label="$t('Search')" single-line hide-details/>
              <project-more-menu/>
            </v-row>
          </v-col>
        </v-row>
      </v-app-bar>
      <v-divider/>
      <v-card-text class="pa-0">
        <v-tabs-items v-model="selectedTab" touchless>
          <v-tab-item>
            <project-list-table :loading="loading" :projects="activeProjects"/>
          </v-tab-item>
          <v-tab-item>
            <project-list-table id="tab-archived-projects" :loading="loading" :projects="archivedProjects"/>
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script>
  import ProjectMoreMenu from './ProjectMoreMenu';
  import ProjectListTable from './ProjectListTable';

  export default {
    name: 'ProjectTabs',
    components: { ProjectListTable, ProjectMoreMenu },
    props: {
      loading: { type: Boolean, default: false },
      projects: { type: Array, required: true },
    },
    data() {
      return {
        selectedTab: 0,
        search: '',
      };
    },
    computed: {
      checkIfMobile() {
        return this.$vuetify.breakpoint.xs ? 120 : 48;
      },
      nameFilteredProjects() {
        const searchLower = this.search.replace(/\s/g, '').toLowerCase();
        return this.projects.filter(project => project.name.replace(/\s/g, '').toLowerCase().includes(searchLower));
      },
      activeProjects() {
        return this.nameFilteredProjects.filter(project => project.visibility);
      },
      archivedProjects() {
        return this.nameFilteredProjects.filter(project => !project.visibility);
      },
    },
    i18n: {
      messages: {
        pl: {
          'active-projects': 'Aktywne projekty ({0})',
          'archived-projects': 'Zarchiwizowane projekty ({0})',
          'Search': 'Szukaj',
        },
        en: {
          'active-projects': 'Active projects ({0})',
          'archived-projects': 'Archived projects ({0})',
        },
      },
    },
  };
</script>
<style>
  .v-slide-group__prev {
    display: none !important;
  }
</style>
