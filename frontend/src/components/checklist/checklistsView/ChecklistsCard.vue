<template>
  <page-card :title="$t('Checklists')">
    <checklist-table :loading="loading" :checklists="checklistsWithData" :search="search" @loading="$emit('loading')"/>
    <template slot="options">
      <v-row no-gutters >
        <v-text-field v-model="search"
                      append-icon="search"
                      :label="$t('search-label')"
                      single-line hide-details/>
      </v-row>
    </template>
  </page-card>
</template>

<script>
  import ChecklistTable from './ChecklistsTable';
  import PageCard from '../../utils/PageCard';

  export default {
    name: 'ChecklistCard',
    components: { ChecklistTable, PageCard },
    props: {
      loading: { type: Boolean, required: true },
      checklists: { type: Array, default: () => ([]) },
    },
    data() {
      return {
        search: '',
      };
    },
    computed: {
      checklistsWithData() {
        return this.checklists.map(checklist => {
          const tasksAllCount = Math.max(checklist.tasksAllCount, 1);
          const tasksFinishedCount = Math.min(Math.max(checklist.tasksFinishedCount, 0), tasksAllCount);
          const tasksFinishedPercent = tasksAllCount / tasksFinishedCount;
          return { ...checklist, tasksFinishedPercent, tasksAllCount, tasksFinishedCount };
        });
      },
    },
    i18n: {
      messages: {
        pl: {
          'Checklists': 'Checklisty',
          'Search': 'Szukaj',
          'search-label': 'Szukaj po nazwie, właścicielu, podmiocie...',
        },
        en: {
          'search-label': 'Search by name, owner, subject...',
        },
      },
    },
  };
</script>

<style scoped>

</style>
