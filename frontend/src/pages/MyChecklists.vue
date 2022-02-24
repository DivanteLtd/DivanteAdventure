<template>
  <v-container id="my-checklists" class="pa-0">
    <checklist-card :loading="loading" :checklists="checklists" @loading="handleLoading"/>
  </v-container>
</template>

<script>
  import { mapState } from 'vuex';
  import ChecklistCard from '../components/checklist/checklistsView/ChecklistsCard';

  export default {
    name: 'ChecklistTemplates',
    components: { ChecklistCard },
    data() {
      return {
        loading: false,
      };
    },
    computed: {
      ...mapState({
        checklists: state => state.Checklist.myChecklists,
      }),
    },
    methods: {
      async loadData() {
        this.loading = true;
        await Promise.all([
          this.$store.dispatch('Checklist/loadMyChecklists'),
        ]);
        this.loading = false;
      },
      handleLoading() {
        this.loading = !this.loading;
      },
    },
    mounted() {
      this.loadData();
    },
  };
</script>
