<template>
  <v-card-text :class="{'pa-0': $vuetify.breakpoint.xs}">
    <v-row no-gutters wrap>
      <v-col cols="12" md="7">
        <users-view :checklist="checklist"/>
      </v-col>
      <v-col cols="12" md="5">
        <checklist-timeline :checklist="checklist"/>
      </v-col>
      <v-col cols="12" md="12" style="border-top: 0px solid rgba(0, 0, 0, 0.12);">
        <questions-list :questions="checklist.tasks" :employee-id="employeeId" :checklist="checklist"/>
      </v-col>
    </v-row>
  </v-card-text>
</template>

<script>
  import UsersView from './UsersView';
  import ChecklistTimeline from './ChecklistTimeline';
  import QuestionsList from './QuestionsList';

  export default {
    name: 'ChecklistDetailsWindowContent',
    components: { QuestionsList, ChecklistTimeline, UsersView },
    props: {
      /** @type {Checklist} */
      checklist: { type: Object, required: false, default: () => ({}) },
      employeeId: { type: Number, required: true },
    },
  };
</script>
