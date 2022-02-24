<template>
  <dashboard-card :title="$t('Checklists')" v-if="checklists.length > 0">
    <v-card-text :class="{'pa-2': $vuetify.breakpoint.xs}">
      <checklist-table :checklists="checklists" reduced/>
    </v-card-text>
    <v-divider/>
    <v-card-actions v-if="checklists.length > 1" style="justify-content: center;">
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" icon @click="goToChecklist"><v-icon>more_horiz</v-icon></v-btn>
        </template>
        <span>{{ $t('Open checklist page') }}</span>
      </v-tooltip>
    </v-card-actions>
  </dashboard-card>
</template>

<script>
  import DashboardCard from './DashboardCard';
  import ChecklistTable from '../checklist/checklistsView/ChecklistsTable';

  export default {
    name: 'DashboardChecklistView',
    components: { ChecklistTable, DashboardCard },
    props: {
      checklists: { type: Array, required: true },
    },
    methods: {
      goToChecklist() {
        this.$router.push('/myChecklists');
      },
    },
    i18n: {
      messages: {
        pl: {
          'Open checklist page': 'Otwórz widok checklist',
          'Checklists': 'Checklisty',
        },
      },
    },
  };
</script>
