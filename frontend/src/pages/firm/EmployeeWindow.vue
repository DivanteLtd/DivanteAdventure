<template>
  <v-dialog v-model="dialogVisible" width="1200">
    <v-card id="dialog-employee-details">
      <v-app-bar color="transparent" class="headline" flat >
        <span :class="{'employee-name-xs': $vuetify.breakpoint.xs}" >
          {{ employee.name }} {{ employee.lastName }}
        </span>
        <v-spacer/>
        <v-btn icon @click="dialogVisible = false"><v-icon>close</v-icon></v-btn>
      </v-app-bar>
      <v-progress-linear height="6" indeterminate v-if="loading"/>
      <v-divider v-else/>
      <employee-window-content :employee="employee"
                               :editable="canEdit"
                               :loading="loading"
                               @close="dialogVisible = false"
                               :show-checklists-tab="showChecklistsTab"
                               :checklists="employeeChecklists"
                               :show-evidences-tab="showEvidencesTab"
                               :evidences="evidences"/>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import EmployeeWindowContent from '../../components/employees/window/EmployeeWindowContent';
  import { mapState, mapGetters } from 'vuex';

  export default {
    name: 'EmployeeWindow',
    components: { EmployeeWindowContent },
    data() {
      return {
        dialogVisible: false,
        employee: {
          id: -1,
        },
        evidences: [],
        checklists: [],
        loading: false,
      };
    },
    computed: {
      ...mapState({
        currentUser: state => state.Employees.loggedEmployee,
        employeeChecklists: state => state.Checklist.employeeChecklists,
      }),
      ...mapGetters({
        canEditOtherUser: 'Authorization/isTribeMaster',
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
      canEdit() {
        return this.canEditOtherUser || this.currentUser.id === this.employee.id;
      },
      showEvidencesTab() {
        const contract = this.employee.contract || {};
        return this.isSuperAdmin && (contract.name === 'CLC LUMP SUM' || contract.name === 'CLC HOURLY'
          || contract.name === 'B2B LUMP SUM' || contract.name === 'B2B HOURLY');
      },
      showChecklistsTab() {
        return this.isSuperAdmin;
      },
    },
    watch: {
      dialogVisible() {
        const { path } = this.$route;
        if (this.dialogVisible && path !== '/dashboard') {
          window.history.pushState({}, null, `/#/firm/employees/${this.employee.id}`);
        } else if (!this.dialogVisible && path.length > 0) {
          window.history.pushState({}, null, `/#${path}`);
        } else if (!this.dialogVisible) {
          window.history.pushState({}, null, `/#/firm/employees`);
        }
      },
    },
    methods: {
      show(employee) {
        if (!this.dialogVisible) {
          this.employee = employee;
          this.dialogVisible = true;
          this.reload();
        }
      },
      async reload() {
        this.evidences = [];
        this.checklists = [];
        this.loading = true;
        const data = await this.$store.dispatch('Employees/loadEmployee', this.employee.id);
        await Promise.all([
          this.$store.dispatch('Employees/loadPairings'),
          this.$store.dispatch('Employees/loadEmployees'),
          this.$store.dispatch('Projects/loadProjects'),
          this.$store.dispatch('Tribes/loadTribes'),
          this.$store.dispatch('Employees/loadHardware', this.employee.id),
          this.$store.dispatch('ContractsType/load'),
          this.$store.dispatch('Employees/getContracts', this.employee.id),
        ]);
        this.employee = data.employee;
        if (this.showEvidencesTab) {
          this.evidences = await this.$store.dispatch('Evidences/loadEmployeeEvidence', this.employee.id);
        }
        if (this.showChecklistsTab) {
          this.$store.dispatch('Checklist/loadEmployeeChecklists', this.employee.id);
        }
        this.loading = false;
      },
      async escapeAndRedirect(path) {
        const url = `/free-days/${this.employee.id}`;
        if (path === 'firm/employees') {
          await this.$router.push(url);
        } else {
          await this.$router.push(url);
          window.location.reload();
        }
      },
    },
    mounted() {
      EventBus.$on(eventNames.showEmployeeWindow, this.show);
      EventBus.$on(eventNames.employeeEdited, this.reload);
      EventBus.$on(eventNames.redirect, this.escapeAndRedirect);
    },
  };
</script>
<style scoped>
  .employee-name-xs{
    font-size: large;
    line-height: initial;
  }
</style>
