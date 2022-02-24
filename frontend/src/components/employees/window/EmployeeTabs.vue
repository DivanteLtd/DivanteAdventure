<template>
  <v-card class="elevation-0">
    <v-app-bar color="transparent elevation-0" flat dense>
      <v-tabs class="elevation-0" v-model="selectedTab" centered flat>
        <v-tab>{{ $t('Data') }}</v-tab>
        <v-tab>{{ $t('In firm') }}</v-tab>
        <v-tab v-if="isSuperAdmin">{{ $t('Contracts') }}</v-tab>
        <v-tab v-if="employeeProjects.length > 0">{{ $t('Projects') }}</v-tab>
        <v-tab v-if="showEvidencesTab && evidences.length > 0">{{ $t('Evidences') }}</v-tab>
        <v-tab v-if="showHardwareTab">{{ $t('Hardware') }}</v-tab>
        <v-tab v-if="showChecklistsTab && checklists.length > 0">{{ $t('Checklists') }}</v-tab>
        <v-tab v-if="showFeedbackTab">{{ $t('Feedback') }}</v-tab>
      </v-tabs>
    </v-app-bar>
    <v-divider/>
    <v-card-text :class="{'pa-0': $vuetify.breakpoint.xs}">
      <v-tabs-items v-model="selectedTab">
        <v-tab-item>
          <personal-data-tab :employee="employee" :editable="editable"/>
        </v-tab-item>
        <v-tab-item>
          <firm-data-tab :employee="employee" :editable="editable"/>
        </v-tab-item>
        <v-tab-item v-if="isSuperAdmin">
          <contracts-tab :employee="employee"/>
        </v-tab-item>
        <v-tab-item v-if="employeeProjects.length > 0">
          <window-project-list :projects="employeeProjects"/>
        </v-tab-item>
        <v-tab-item v-if="showEvidencesTab && evidences.length > 0">
          <evidence-data-tab :evidences="evidences"/>
        </v-tab-item>
        <v-tab-item v-if="showHardwareTab">
          <hardware-data-tab :loading="loading"
                             :hardware-list="hardwareList"
                             :employee="employee"
                             :current-user="currentUser"/>
        </v-tab-item>
        <v-tab-item v-if="showChecklistsTab && checklists.length > 0">
          <checklist-data-tab :employee-id="employee.id" :checklists="checklists"/>
        </v-tab-item>
        <v-tab-item v-if="showFeedbackTab">
          <feedback-data-tab :planned="plannedFeedbacks"
                             :loading="loadingFeedback"
                             :feedback="feedback"
                             :employee="employee"
                             :current-user="currentUser"
                             @reload="getFeedback"/>
        </v-tab-item>
      </v-tabs-items>
    </v-card-text>
  </v-card>
</template>

<script>
  import PersonalDataTab from './PersonalDataTab';
  import FirmDataTab from './FirmDataTab';
  import { mapGetters, mapState } from 'vuex';
  import WindowProjectList from '../../utils/WindowProjectList';
  import EvidenceDataTab from './EvidenceDataTab';
  import HardwareDataTab from './HardwareDataTab';
  import ChecklistDataTab from './ChecklistDataTab';
  import FeedbackDataTab from './FeedbackDataTab';
  import ContractsTab from './ContractsTab';

  export default {
    name: 'EmployeeTabs',
    components: {
      ContractsTab,
      ChecklistDataTab,
      HardwareDataTab,
      EvidenceDataTab,
      WindowProjectList,
      FirmDataTab,
      FeedbackDataTab,
      PersonalDataTab,
    },
    props: {
      employee: { type: Object, required: true },
      editable: { type: Boolean, default: false },
      loading: { type: Boolean, default: false },
      showEvidencesTab: { type: Boolean, default: false },
      showChecklistsTab: { type: Boolean, default: false },
      evidences: { type: Array, default: () => ([]) },
      checklists: { type: Array, default: () => ([]) },
    },
    data() {
      return {
        selectedTab: 0,
        loadingFeedback: false,
      };
    },
    computed: {
      ...mapState({
        currentUser: state => state.Employees.loggedEmployee,
        projects: state => state.Projects.projects,
        pairings: state => state.Employees.pairings,
        hardwareList: state => state.Employees.hardware,
        tribes: state => state.Tribes.tribes,
        feedback: state => state.Feedback.feedback,
        plannedFeedbacks: state => state.Feedback.planned,
      }),
      ...mapGetters({
        isHelpdesk: 'Authorization/isHelpdesk',
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
      showHardwareTab() {
        return (this.isHelpdesk || this.currentUser.id === this.employee.id) && this.hardwareList.length;
      },
      showFeedbackTab() {
        const { tribeResponsible, employee, currentUser } = this;
        const leaders = employee.leaders ? employee.leaders.map(val => val.id) : [];
        if (currentUser.id === employee.id || leaders.includes(currentUser.id) || tribeResponsible) {
          this.getFeedback();
          return true;
        } else {
          return false;
        }
      },
      tribeResponsible() {
        if(typeof this.employee.tribe !== 'undefined') {
          const userTribe = this.tribes.find(val => val.id === this.employee.tribe.id);
          return userTribe && userTribe.responsible
            ? userTribe.responsible.some(val => val.id === this.currentUser.id) : false;
        } else {
          return false;
        }
      },
      employeeProjects() {
        const projectIds = this.pairings
          .filter(pairing => pairing.employeeId === this.employee.id)
          .map(pairing => pairing.projectId);
        return this.projects.filter(project => projectIds.includes(project.id));
      },
    },
    methods: {
      async getFeedback() {
        this.loadingFeedback = true;
        await Promise.all([
          this.$store.dispatch('Feedback/loadFeedback', this.employee.id),
          this.$store.dispatch('Feedback/loadPlannedFeedbacks', this.employee.id),
        ]);
        this.loadingFeedback = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Data': 'Dane',
          'In firm': 'W firmie',
          'Projects': 'Projekty',
          'Evidences': 'Ewidencje',
          'Hardware': 'Sprzęt',
          'Checklists': 'Checklisty',
          'Feedback': 'Feedback',
          'Contracts': 'Umowy',
        },
      },
    },
  };
</script>
