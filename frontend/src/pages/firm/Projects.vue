<template>
  <div id="page-projects-list">
    <project-tabs v-if="canSeeArchivedProjects" :loading="loading" :projects="projects"/>
    <project-list-card v-else :loading="loading" :projects="projects"/>
  </div>
</template>

<script>
  import ProjectListCard from '../../components/projects/ProjectListCard';
  import { mapState, mapGetters } from 'vuex';
  import { EventBus, eventNames } from '../../eventbus';
  import ProjectTabs from '../../components/projects/ProjectTabs';

  export default {
    name: 'Projects',
    components: { ProjectTabs, ProjectListCard },
    data() {
      return {
        loading: false,
      };
    },
    computed: {
      ...mapState({
        projects: state => state.Projects.projects,
      }),
      ...mapGetters({
        canSeeArchivedProjects: 'Authorization/isManager',
      }),
    },
    methods: {
      async loadData() {
        this.loading = true;
        await this.$store.dispatch('Projects/loadProjects');
        this.loading = false;
        const paramsId = this.$route.params.id;
        const id = typeof(paramsId) === 'undefined' ? undefined : parseInt(paramsId);
        const filteredProject = this.projects.filter(project => project.id === id);
        if (filteredProject.length === 1) {
          EventBus.$emit(eventNames.showProjectWindow, filteredProject[0]);
        }
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
    beforeRouteUpdate(to, from, next) {
      next();
      const paramsId = this.$route.params.id;
      const id = typeof(paramsId) === 'undefined' ? undefined : parseInt(paramsId);
      const filteredProject = this.projects.filter(project => project.id === id);
      if (filteredProject.length === 1) {
        EventBus.$emit(eventNames.showProjectWindow, filteredProject[0]);
      }
    },
  };
</script>
