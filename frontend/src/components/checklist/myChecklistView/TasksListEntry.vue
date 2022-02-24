<template>
  <v-list class="task-list-entry">
    <task-details v-if="showDetails" :dialog-visible.sync="showDetails" :task="task" :checklist="checklist"
                  :employee-id="employeeId" :class="{'pt-3': $vuetify.breakpoint.xs}"/>
    <v-list-item class="pl-0" @click="showTaskDetails">
      <v-list-item-action style="min-width: 25px">
        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-icon v-on="on" :color="status.color">{{ status.icon }}</v-icon>
          </template>
          {{ statusName }}
        </v-tooltip>
      </v-list-item-action>
      <v-list-item-action-text >
        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <div v-on="on">
              {{ name }}
            </div>
          </template>
          {{ description }}
        </v-tooltip>
      </v-list-item-action-text>
    </v-list-item>
  </v-list>
</template>

<script>
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import TaskDetails from './TaskDetails';

  export default {
    name: 'TasksListEntry',
    components: { TaskDetails },
    props: {
      task: { type: Object, required: true },
      checklist: { type: Object, required: true },
      employeeId: { type: Number, required: true },
    },
    data() {
      return {
        showDetails: false,
      };
    },
    computed: {
      name() {
        switch (getSuggestedLanguage()) {
          case 'en': return this.task.nameEn;
          case 'pl': return this.task.namePl;
          default: return '';
        }
      },
      description() {
        switch (getSuggestedLanguage()) {
          case 'en': return this.task.descriptionEn;
          case 'pl': return this.task.descriptionPl;
          default: return '';
        }
      },
      status() {
        return this.task.possibleStatuses[this.task.status];
      },
      statusName() {
        switch (getSuggestedLanguage()) {
          case 'en': return this.status.label_en;
          case 'pl': return this.status.label_pl;
          default: return '';
        }
      },
    },
    methods: {
      showTaskDetails() {
        this.showDetails = true;
      },
    },
  };
</script>
<style>
  .task-list-entry .v-list__tile{
    height: unset;
  }
</style>
