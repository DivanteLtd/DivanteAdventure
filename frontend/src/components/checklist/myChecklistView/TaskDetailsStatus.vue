<template>
  <v-list-item @click="showMenu" :class="{'task__tile': !canUpdateStatus}">
    <v-list-item-action
      style="min-width: 40px"
      class="d-flex justify-center"
      :class="{'mr-3': $vuetify.breakpoint.smAndUp, 'mr-2': $vuetify.breakpoint.xs}">
      <v-icon :color="status.color">{{ status.icon }}</v-icon>
    </v-list-item-action>
    <v-list-item-content>
      <v-list-item-title>{{ statusName }}</v-list-item-title>
      <v-list-item-subtitle>{{ $t('Status') }}</v-list-item-subtitle>
    </v-list-item-content>
    <v-list-item-action v-if="canUpdateStatus">
      <v-tooltip right>
        <template v-slot:activator="{ on }">
          <task-details-status-switch v-on="on" :task="task" :employee-id="employeeId" :checklist="checklist"/>
        </template>
        {{ $t('Update status') }}
      </v-tooltip>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import { mapState, mapGetters } from 'vuex';
  import TaskDetailsStatusSwitch from './TaskDetailsStatusSwitch';
  import { ChecklistType } from '../../../util/checklists';
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'TaskDetailsStatus',
    components: { TaskDetailsStatusSwitch },
    props: {
      task: { type: Object, required: true },
      checklist: { type: Object, required: true },
      employeeId: { type: Number, required: true },
    },
    computed: {
      ...mapState({
        currentUser: state => state.Employees.loggedEmployee,
        apiClient: state => state.apiClient,
      }),
      ...mapGetters({
        isHr: 'Authorization/isHr',
      }),
      canUpdateStatus() {
        if (this.isHr) {
          return true;
        } if (this.checklist.type === ChecklistType.distributed) {
          const responsible = this.task.responsible[0] || { id: -1 };
          return this.currentUser.id === responsible.id;
        } else {
          return this.checklist.owners && this.checklist.owners.some(owner => owner.id === this.currentUser.id);
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
      showMenu() {
        EventBus.$emit(eventNames.showTaskMenu);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Status': 'Status',
          'Update status': 'Zaktualizuj status',
        },
      },
    },
  };
</script>
<style>
  .task__tile a{
    cursor: default !important;
  }
</style>
