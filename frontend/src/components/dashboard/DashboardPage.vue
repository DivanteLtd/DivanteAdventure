<template>
  <v-container :class="{'pa-2': $vuetify.breakpoint.sm, 'pa-3': $vuetify.breakpoint.mdAndUp}">
    <v-row no-gutters wrap class="mb-2">
      <v-col lg="4" sm="12" cols="12" :class="{'pa-2': $vuetify.breakpoint.smAndUp}">
        <hardware-agreements-view v-if="agreements.length > 0"
                                  :agreements="agreements"
                                  :loaded="hardwareAgreementsLoaded"/>
        <dashboard-profile-card :employee="employee" :loaded="profileLoaded"/>
        <dashboard-pending-requests v-if="canAcceptRequests" :loaded="requestsLoaded"/>
        <dashboard-projects-list :loaded="projectsLoaded && pairingsLoaded" :projects="projects"/>
      </v-col>
      <v-col lg="8" sm="12" cols="12" :class="{'pa-2': $vuetify.breakpoint.smAndUp}">
        <dashboard-checklist-view v-if="checklistsLoaded && checklists.length > 0" :checklists="checklists"/>
        <dashboard-posts :posts="posts" :loaded="newsLoaded"/>
        <dashboard-newest-requests :loaded="periodsLoaded"/>
        <dashboard-links :loaded="linksLoaded"/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import DashboardProfileCard from './DashboardProfileCard';
  import { mapState, mapGetters } from 'vuex';
  import DashboardProjectsList from './DashboardProjectsList';
  import DashboardPendingRequests from './DashboardPendingRequests';
  import DashboardNewestRequests from './DashboardNewestRequests';
  import DashboardPosts from './DashboardPosts';
  import DashboardLinks from './DashboardLinks';
  import DashboardChecklistView from './DashboardChecklistView';
  import HardwareAgreementsView from './HardwareAgreementsView';

  export default {
    name: 'DashboardPage',
    components: {
      HardwareAgreementsView,
      DashboardChecklistView,
      DashboardPosts,
      DashboardNewestRequests,
      DashboardPendingRequests,
      DashboardProjectsList,
      DashboardProfileCard,
      DashboardLinks,
    },
    props: {
      profileLoaded: { type: Boolean, default: false },
      projectsLoaded: { type: Boolean, default: false },
      pairingsLoaded: { type: Boolean, default: false },
      periodsLoaded: { type: Boolean, default: false },
      newsLoaded: { type: Boolean, default: false },
      linksLoaded: { type: Boolean, default: false },
      checklistsLoaded: { type: Boolean, default: false },
      requestsLoaded: { type: Boolean, default: false },
      hardwareAgreementsLoaded: { type: Boolean, default: false },
    },
    computed: {
      ...mapState({
        employee: state => state.Employees.loggedEmployee,
        employeeProjects: state => state.Employees.pairings,
        allProjects: state => state.Projects.projects,
        posts: state => state.News.news,
        checklists: state => state.Checklist.myChecklists,
        agreements: state => state.Hardware.agreementsToSign,
      }),
      ...mapGetters({
        canAcceptRequests: 'Authorization/isManager',
      }),
      projects() {
        const pairings = this.employeeProjects
          .filter(pair => pair.employeeId === this.employee.id)
          .map(pair => pair.projectId);
        return this.allProjects.filter(project => pairings.includes(project.id));
      },
    },
  };
</script>
