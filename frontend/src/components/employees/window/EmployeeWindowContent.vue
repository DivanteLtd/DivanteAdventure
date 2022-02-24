<template>
  <v-card-text :class="{'pa-0': $vuetify.breakpoint.xs}">
    <employee-window-details @close="$emit('close')" :loading="loading" :employee="employee" :editable="editable"/>
    <employee-tabs :employee="employee"
                   :editable="false"
                   :loading="loading"
                   :show-checklists-tab="showChecklistsTab"
                   :checklists="checklists"
                   :show-evidences-tab="showEvidencesTab"
                   :evidences="evidences"/>
  </v-card-text>
</template>

<script>
  import EmployeeWindowDetails from './EmployeeWindowDetails';
  import EmployeeTabs from './EmployeeTabs';

  export default {
    name: 'EmployeeWindowContent',
    components: { EmployeeTabs, EmployeeWindowDetails },
    props: {
      employee: { type: Object, required: true },
      editable: { type: Boolean, default: false },
      loading: { type: Boolean, default: false },
      showChecklistsTab: { type: Boolean, default: false },
      showEvidencesTab: { type: Boolean, default: false },
      evidences: { type: Array, default: () => ([]) },
      checklists: { type: Array, default: () => ([]) },
    },
  };
</script>
