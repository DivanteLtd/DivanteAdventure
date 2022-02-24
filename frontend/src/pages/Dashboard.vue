<template>
  <div id="page-dashboard">
    <dashboard-page :profile-loaded="loaded"
                    :projects-loaded="projectsLoaded"
                    :pairings-loaded="pairingsLoaded"
                    :periods-loaded="periodsLoaded"
                    :news-loaded="newsLoaded"
                    :links-loaded="linksLoaded"
                    :checklists-loaded="checklistsLoaded"
                    :requests-loaded="requestsLoaded"
                    :hardware-agreements-loaded="hardwareAgreementsLoaded"/>
  </div>
</template>

<script>
  import DashboardPage from '../components/dashboard/DashboardPage';
  import { mapGetters, mapState } from 'vuex';
  import { EventBus, eventNames } from '../eventbus';

  export default {
    name: 'Dashboard',
    components: { DashboardPage },
    data() { return {
      loaded: false,
      projectsLoaded: false,
      pairingsLoaded: false,
      periodsLoaded: false,
      newsLoaded: false,
      linksLoaded: false,
      checklistsLoaded: false,
      requestsLoaded: false,
      hardwareAgreementsLoaded: false,
    };},
    computed: {
      ...mapGetters({
        canAcceptRequests: 'Authorization/isManager',
      }),
      ...mapState({
        employee: state => state.Employees.loggedEmployee,
      }),
    },
    methods: {
      async loadData() {
        const checklistId = typeof(this.$route.params.checklistId) === 'undefined' ? undefined : parseInt(this.$route.params.checklistId);
        if(typeof(checklistId) !== 'undefined') {
          const checklist = await this.$store.dispatch('Checklist/getChecklistDetails', checklistId);
          const taskId = typeof(this.$route.params.taskId) === 'undefined' ? undefined : parseInt(this.$route.params.taskId);
          const task = checklist.tasks.filter(task => task.id === taskId);
          if(task.length === 1) {
            EventBus.$emit(eventNames.showQuestionUpdate, checklist, task[0]);
          }
        }
        this.loaded = false;
        this.projectsLoaded = false;
        this.pairingsLoaded = false;
        this.periodsLoaded = false;
        this.newsLoaded = false;
        this.linksLoaded = false;
        this.checklistsLoaded = false;
        this.requestsLoaded = false;
        this.hardwareAgreementsLoaded = false;
        const promises = [
          this.$store.dispatch('Projects/loadProjects').then(() => {
            this.projectsLoaded = true;
          }),
          this.$store.dispatch('Employees/loadPairings').then(() => {
            this.pairingsLoaded = true;
          }),
          this.$store.dispatch('FreeDays/loadMyPeriods').then(() => {
            this.periodsLoaded = true;
          }),
          this.$store.dispatch('News/loadNews').then(() => {
            this.newsLoaded = true;
          }),
          this.$store.dispatch('Links/loadLinks').then(() => {
            this.linksLoaded = true;
          }),
          this.$store.dispatch('Checklist/loadMyChecklists').then(() => {
            this.checklistsLoaded = true;
          }),
          this.$store.dispatch('Hardware/loadHardwareForSigning').then(() => {
            this.hardwareAgreementsLoaded = true;
          }),
        ];
        promises.push(this.$store.dispatch('Notifications/loadNotifications'));
        if (this.canAcceptRequests) {
          promises.push(this.$store.dispatch('Requests/loadRequests').then(() => {
            this.requestsLoaded = true;
          }));
        }
        await Promise.all(promises);
        this.loaded = true;
      },
      reloadRequests() {
        this.$store.dispatch('FreeDays/loadMyPeriods');
        this.$store.dispatch('Requests/loadRequests');
      },
    },
    mounted() {
      EventBus.$on(eventNames.createNewLeaveRequestAfter, this.reloadRequests);
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
  };
</script>
