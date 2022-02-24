<template>
  <v-dialog v-model="dialogVisible" width="900">
    <v-card id="dialog-checklist-details">
      <v-app-bar color="transparent" class="headline" flat >
        <span :class="{'label-name-xs': $vuetify.breakpoint.xs}" >
          {{ label }}
        </span>
        <v-spacer/>
        <v-btn icon @click="dialogVisible = false"><v-icon>close</v-icon></v-btn>
      </v-app-bar>
      <v-progress-linear height="6" v-if="loading" indeterminate/>
      <v-divider v-else/>
      <checklist-details-window-content v-if="!loading" :employee-id="employeeId" :checklist="checklist"/>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import { getSuggestedLanguage } from '../../i18n/i18n';
  import { mapState } from 'vuex';
  import ChecklistDetailsWindowContent from '../../components/checklist/detailsDialog/ChecklistDetailsWindowContent';

  export default {
    name: 'ChecklistDetailsWindow',
    components: { ChecklistDetailsWindowContent },
    data() {
      return {
        dialogVisible: false,
        loading: false,
        employeeId: 0,
      };
    },
    computed: {
      ...mapState({
        /** @type {ApiClient|Object} */
        apiClient: state => state.apiClient,
        /** @type {Checklist|Object} */
        checklistDetails: state => state.Checklist.checklistDetails,
      }),
      checklist() {
        return this.checklistDetails ? this.checklistDetails : {};
      },
      label() {
        const answer = this.getAnswerInLanguage;
        const shortedAnswer = answer.replace(/^(.{34}[^\s]*).*/, '$1');
        return answer.length === shortedAnswer.length ? answer : `${shortedAnswer}...`;
      },
      getAnswerInLanguage() {
        const lang = getSuggestedLanguage();
        switch (lang) {
          case 'en': return this.checklist.nameEn || '';
          case 'pl': return this.checklist.namePl || '';
          default: return '';
        }
      },
    },
    methods: {
      async show(id, employeeId) {
        if(employeeId) {
          this.employeeId = employeeId;
        }
        if (this.dialogVisible) {
          return;
        }
        this.dialogVisible = true;
        await this.reload(id);
      },
      async reload(id) {
        this.loading = true;
        await this.$store.dispatch('Checklist/getChecklistDetails', id);
        this.loading = false;
      },
    },
    mounted() {
      EventBus.$on(eventNames.showChecklistDetails, this.show);
      EventBus.$on(eventNames.escapePressed, () => { this.dialogVisible = false; });
    },
  };
</script>
<style scoped>
.label-name-xs{
  font-size: small;
  font-weight: bold;
}
</style>
