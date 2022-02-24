<template>
  <v-list-item @click="setStatus">
    <v-list-item-action>
      <v-icon :color="status.color">{{ status.icon }}</v-icon>
    </v-list-item-action>
    <v-list-item-content>
      <v-list-item-title>{{ label }}</v-list-item-title>
    </v-list-item-content>
  </v-list-item>
</template>

<script>
  import { mapState } from 'vuex';
  import { getSuggestedLanguage } from '../../../i18n/i18n';

  export default {
    name: 'TaskDetailsStatusSwitchRow',
    props: {
      status: { type: Object, required: true },
      checklistId: { type: Number, required: true },
      taskId: { type: Number, required: true },
      statusId: { type: Number, required: true },
      employeeId: { type: Number, required: true },
    },
    computed: {
      ...mapState({
        apiClient: state => state.apiClient,
      }),
      label() {
        switch (getSuggestedLanguage()) {
          case 'pl': return this.status.label_pl;
          case 'en': return this.status.label_en;
          default: return 'N/A';
        }
      },
    },
    methods: {
      async setStatus() {
        this.$emit('lock');
        const statusDone = 2;
        await this.apiClient.checklist.updateStatus(this.checklistId, this.taskId, this.statusId);
        await this.$store.dispatch('Checklist/getChecklistDetails', this.checklistId);
        if(this.statusId === statusDone) {
          await this.$store.dispatch('Checklist/getAllChecklists');
        }
        if(this.employeeId) {
          await this.$store.dispatch('Checklist/loadEmployeeChecklists', this.employeeId);
        }
        await this.$store.dispatch('Checklist/loadMyChecklists');
        this.$emit('unlock');
      },
    },
  };
</script>
