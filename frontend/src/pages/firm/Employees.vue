<template>
  <employee-list-card id="page-employees-list" :loading="loading" :employees="employees"/>
</template>

<script>
  import EmployeeListCard from '../../components/employees/EmployeeListCard';
  import { EventBus, eventNames } from '../../eventbus';

  export default {
    name: 'Employees',
    components: { EmployeeListCard },
    data() {
      return {
        loading: false,
        employees: [],
      };
    },
    methods: {
      async loadData() {
        this.loading = true;
        this.employees = await this.$store.dispatch('Employees/loadEmployees');
        await this.$store.dispatch('Employees/loadEmployeesTodayWorkLocations');
        const paramsId = this.$route.params.id;
        const id = typeof(paramsId) === 'undefined' ? undefined : parseInt(paramsId);
        const filteredEmployee = this.employees.filter(employee => employee.id === id);
        this.loading = false;
        if (filteredEmployee.length === 1) {
          EventBus.$emit(eventNames.showEmployeeWindow, filteredEmployee[0]);
        }
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
    beforeRouteUpdate(to, from, next) {
      next();
      const paramsId = this.$route.params.id;
      const id = typeof(paramsId) === 'undefined' ? undefined : parseInt(paramsId);
      const filteredEmployee = this.employees.filter(employee => employee.id === id);
      if (filteredEmployee.length === 1) {
        EventBus.$emit(eventNames.showEmployeeWindow, filteredEmployee[0]);
      }
    },
    mounted() {
      EventBus.$on(eventNames.deleteEmployeeAfter, this.loadData);
      EventBus.$on(eventNames.employeeEdited, this.loadData);
    },
  };
</script>
