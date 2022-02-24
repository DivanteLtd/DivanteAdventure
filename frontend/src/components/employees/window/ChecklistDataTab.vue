<template>
  <v-list two-line>
    <template v-for="(checklist, index) in checklists">
      <checklist-data-row :key="index + 'row'" :employee-id="employeeId" :checklist="checklist"/>
      <v-divider v-if="index + 1 < checklists.length" :key="index + 'divider'"/>
    </template>
  </v-list>
</template>

<script>
  import ChecklistDataRow from './ChecklistDataRow';

  export default {
    name: 'ChecklistDataTab',
    components: { ChecklistDataRow },
    props: {
      /** @type {ChecklistListEntry[]} checklists */
      checklists: { type: Array, default: () => ([]) },
      employeeId: { type: Number, required: true },
    },
  };
</script>
