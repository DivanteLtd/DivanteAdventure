<template>
  <v-container id="feedbackPage" class="pa-0">
    <feedback-card :loading="loading" :feedback-received="feedbackReceived" :employee="employee"/>
  </v-container>
</template>

<script>
  import FeedbackCard from '../components/feedback/feedbackMenu/FeedbackCard';
  import { mapGetters, mapState } from 'vuex';

  export default {
    name: 'Feedback',
    components: { FeedbackCard },
    data() {
      return {
        loading: false,
        feedbackReceived: [],
      };
    },
    computed: {
      ...mapState({
        employee: state => state.Employees.loggedEmployee,
      }),
      ...mapGetters({
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
    },
    methods: {
      async loadData() {
        this.loading = true;
        await this.$store.dispatch('Feedback/getMyStructure', this.employee.id);
        await this.$store.dispatch('Feedback/getFeedbackProvided', this.employee.id);
        this.feedbackReceived = await this.$store.state.apiClient.feedback.getFeedback(this.employee.id);
        if (this.isSuperAdmin) {
          await this.$store.dispatch('Employees/loadEmployees');
          await this.$store.dispatch('Employees/loadLeaderStructures');
        }
        if (this.employee.techTribeLeader) {
          await this.$store.dispatch('Feedback/getTribeStructure', this.employee.id);
        }
        this.loading = false;
      },
    },
    mounted() {
      this.loadData();
    },
  };
</script>
