<template>
  <v-dialog v-model="dialogVisible" width="800">
    <v-card id="dialog-project-details">
      <v-app-bar color="transparent" class="headline" flat>
        <span :class="{'project-name-xs': $vuetify.breakpoint.xs}">{{ item.name }}</span>
        <v-spacer/>
        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" :disabled="!isDeleteButtonVisible" icon @click="deleteProject">
              <v-icon>delete</v-icon>
            </v-btn>
          </template>
          {{ $t('Delete') }}
        </v-tooltip>
        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" :disabled="!isEditButtonVisible" icon @click="editProject">
              <v-icon>edit</v-icon>
            </v-btn>
          </template>
          {{ $t('Edit') }}
        </v-tooltip>
        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" @click="dialogVisible = false" icon>
              <v-icon>close</v-icon>
            </v-btn>
          </template>
          {{ $t('Close') }}
        </v-tooltip>
      </v-app-bar>
      <v-divider/>
      <project-window-content :project="item" :loading="loading"/>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import { mapState } from 'vuex';
  import ProjectWindowContent from '../../components/projects/window/ProjectWindowContent';

  export default {
    name: 'ProjectWindow',
    components: { ProjectWindowContent },
    data() {
      return {
        dialogVisible: false,
        item: {},
        loading: false,
      };
    },
    computed: {
      ...mapState({
        employeeProjects: state => state.Employees.pairings,
        allEmployees: state => state.Employees.employees,
      }),
      canEdit() {
        return this.$store.getters['Authorization/isManager'];
      },
      allEmployeesInProject() {
        return this.employeeProjects
          .filter(pair => typeof pair.projectId !== 'undefined' && pair.projectId === this.item.id);
      },
      employeesInProject() {
        const employees = [];
        this.employeeProjects.forEach(pair => {
          if (typeof pair.projectId !== 'undefined' && pair.projectId === this.item.id) {
            const employee = this.allEmployees.filter(employee => employee.id === pair.employeeId);
            if (employee.length > 0) {
              employee[0].project = this.item.name;
              employee[0].projectId = this.item.id;
              employee[0].projectWindow = 1;
              employees.push(employee[0]);
            }
          }
        });
        return employees;
      },
      isDeleteButtonVisible() {
        return this.canEdit && this.item.visibility && !this.loading;
      },
      isEditButtonVisible() {
        return this.canEdit && this.item.visibility;
      },
    },
    watch: {
      dialogVisible() {
        if (this.dialogVisible) {
          window.history.pushState({}, null, `/#/firm/projects/${this.item.id}`);
        } else {
          window.history.pushState({}, null, `/#/firm/projects/`);
        }
      },
    },
    methods: {
      async reload() {
        this.loading = true;
        try {
          await Promise.all([
            this.$store.dispatch('Employees/loadPairings'),
            this.$store.dispatch('Criteria/loadCriteria'),
            this.$store.dispatch('Employees/loadEmployees'),
            this.$store.dispatch('Projects/loadIntegrationProjects'),
          ]);
          const allProjects = this.$store.state.Projects.projects;
          const filtered = allProjects.filter(project => project.id === this.item.id);
          if (filtered.length === 1) {
            this.item = filtered[0];
          }
        } finally {
          this.loading = false;
        }
      },
      async show(item) {
        if (this.dialogVisible) {
          return;
        }
        this.dialogVisible = true;
        this.item = item;
        await this.reload();
      },
      editProject() {
        EventBus.$emit(eventNames.projectForm, this.item);
      },
      deleteProject() {
        const data = {
          id: this.item.id,
          employeesLength: this.allEmployeesInProject.length,
        };
        if (data.employeesLength > this.employeesInProject.length) {
          data.notVisiblePerson = true;
        }
        EventBus.$emit(eventNames.deleteProjectForm, data);
      },
    },
    mounted() {
      EventBus.$on(eventNames.showProjectWindow, this.show);
      EventBus.$on(eventNames.refreshProjectWindow, this.reload);
      EventBus.$on(eventNames.escapePressed, () => { this.dialogVisible = false; });
    },
    i18n: {
      messages: {
        pl: {
          Close: 'Zamknij',
          Edit: 'Edytuj',
          Delete: 'Usuń',
        },
      },
    },
  };
</script>
<style scoped>
  .project-name-xs{
    font-size: medium;
    line-height: initial;
  }
</style>
