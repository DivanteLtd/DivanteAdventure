<template>
  <page-card id="checklist-templates-card" :title="$t('Checklist templates')">
    <checklist-templates-table :loading="loading" :checklist-templates="checklistTemplates" :search="search"/>
    <template slot="options">
      <v-row no-gutters class="d-flex align-center">
        <v-text-field v-model="search"
                      append-icon="search"
                      :label="$t('search-label')"
                      single-line hide-details/>
        <checklist-templates-list-menu/>
      </v-row>
    </template>
  </page-card>
</template>

<script>
  import ChecklistTemplatesTable from '../checklistTemplatesTable/Table';
  import ChecklistTemplatesListMenu from './ListMenu';
  import PageCard from '../../../utils/PageCard';

  export default {
    name: 'ChecklistTemplatesCard',
    components: { ChecklistTemplatesTable, ChecklistTemplatesListMenu, PageCard },
    props: {
      loading: { type: Boolean, required: true },
      checklistTemplates: { type: Array, default: () => ([]) },
    },
    data() {
      return {
        search: '',
      };
    },
    i18n: {
      messages: {
        pl: {
          'Checklist templates': 'Szablony checklist',
          'Search': 'Szukaj',
          'search-label': 'Szukaj po nazwie...',
        },
        en: {
          'search-label': 'Search by name...',
        },
      },
    },
  };
</script>
