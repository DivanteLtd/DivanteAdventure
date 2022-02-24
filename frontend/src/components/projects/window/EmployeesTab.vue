<template>
  <div class="text-xs-center">
    <v-list>
      <employee-row v-for="(employee, index) in employeesInProject" :key="index" :employee="employee"/>
    </v-list>
  </div>
</template>

<script>
  import { mapState } from 'vuex';
  import EmployeeRow from './EmployeeRow';

  export default {
    name: 'EmployeesTab',
    components: { EmployeeRow },
    props: {
      project: { type: Object, required: true },
    },
    computed: {
      ...mapState({
        employeeProjects: state => state.Employees.pairings,
        allEmployees: state => state.Employees.employees,
      }),
      employeesInProject() {
        return this.employeeProjects
          .filter(pair => pair.projectId !== 'undefined' && pair.projectId === this.project.id)
          .map(pair => this.allEmployees
            .filter(employee => employee.id === pair.employeeId)
            .map(employee => ({ ...employee, pairingId: pair.id })))
          .reduce((a, b) => [...a, ...b], [])
          .sort((a, b) => `${a.lastName} ${a.name}`.localeCompare(`${b.lastName} ${b.name}`));
      },
    },
  };
</script>
