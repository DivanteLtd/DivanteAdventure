<template>
  <v-container id="checklists" class="pa-0">
    <checklist-templates-card :loading="loading" :checklist-templates="checklistTemplates"/>
  </v-container>
</template>

<script>
  import ChecklistTemplatesCard from '../../components/hr/checklistTemplates/checklistTemplatesView/Card';
  import { mapState } from 'vuex';

  export default {
    name: 'ChecklistTemplates',
    components: { ChecklistTemplatesCard },
    data() {
      return {
        loading: false,
      };
    },
    computed: {
      ...mapState({
        checklistTemplates: state => state.Checklist.checklistTemplates,
      }),
    },
    methods: {
      async loadData() {
        this.loading = true;
        await Promise.all([
          this.$store.dispatch('Checklist/loadChecklistTemplates'),
          this.$store.dispatch('Employees/loadEmployees'),
        ]);
        this.loading = false;
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
    mounted() {
      this.loadData();
    },
  };
</script>
