<template>
  <v-list v-if="checklist.finishedAt">
    <template v-for="(question, index) in questions">
      <v-divider :key="index + 'divider'"/>
      <questions-list-row :key="index + 'row'" :question="question"/>
    </template>
  </v-list>
  <v-list v-else>
    <template v-for="(question, index) in questions">
      <v-divider :key="index + 'divider'"/>
      <tasks-list-entry :key="index + 'row'" :task="question" :employee-id="employeeId" :checklist="checklist"/>
    </template>
  </v-list>
</template>

<script>
  import QuestionsListRow from './QuestionsListRow';
  import TasksListEntry from '../myChecklistView/TasksListEntry';

  export default {
    name: 'QuestionsList',
    components: { TasksListEntry, QuestionsListRow },
    props: {
      questions: { type: Array, required: false, default: () => ([]) },
      checklist: { type: Object, required: false, default: () => ({}) },
      employeeId: { type: Number, required: true },
    },
  };
</script>
