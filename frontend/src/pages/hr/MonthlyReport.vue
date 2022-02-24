<template>
  <monthly-report-card id="page-hr-monthly-report"
                       :loading="loading"
                       :potential-employees="potentialEmployees"
                       :employees="employees"/>
</template>

<script>
  import MonthlyReportCard from '../../components/hr/monthlyReport/MonthlyReportCard';

  export default {
    name: 'MonthlyReport',
    components: { MonthlyReportCard },
    data() {
      return {
        loading: false,
        potentialEmployees: [],
        employees: [],
      };
    },
    methods: {
      async loadData() {
        this.loading = true;
        const [ potentialEmployees, employees ] = await Promise.all([
          this.$store.dispatch('Hr/loadPotentialEmployees'),
          this.$store.dispatch('Employees/loadEmployees'),
        ]);
        this.potentialEmployees = potentialEmployees;
        this.employees = employees;
        this.loading = false;
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
  };
</script>
