<template>
  <v-menu v-model="menuVisible" left bottom offset-y :min-width="$vuetify.breakpoint.mdAndUp ? 720 : 0">
    <template v-slot:activator="{ on }">
      <v-btn v-on="on" :loading="loading" icon>
        <v-icon v-if="menuVisible">keyboard_arrow_up</v-icon>
        <v-icon v-else>keyboard_arrow_down</v-icon>
      </v-btn>
    </template>
    <v-list>
      <template v-for="(status, index) in task.possibleStatuses">
        <task-details-status-switch-row :key="index"
                                        :status="status"
                                        :status-id="index"
                                        :task-id="task.id"
                                        :checklist-id="checklist.id"
                                        :employee-id="employeeId"
                                        @lock="lock"
                                        @unlock="unlock"/>
      </template>
    </v-list>
  </v-menu>
</template>

<script>
  import TaskDetailsStatusSwitchRow from './TaskDetailsStatusSwitchRow';
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'TaskDetailsStatusSwitch',
    components: { TaskDetailsStatusSwitchRow },
    props: {
      task: { type: Object, required: true },
      checklist: { type: Object, required: true },
      employeeId: { type: Number, required: true },
    },
    data() {
      return {
        menuVisible: false,
        loading: false,
      };
    },
    methods: {
      show() {
        this.menuVisible = true;
      },
      lock() {
        this.menuVisible = false;
        this.loading = true;
      },
      unlock() {
        this.loading = false;
      },
    },
    mounted() {
      EventBus.$on(eventNames.showTaskMenu, this.show);
    },
  };
</script>
