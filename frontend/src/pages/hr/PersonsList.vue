<template>
  <v-container id="page-hr-persons-list" grid-list-xl fluid>
    <persons-list-card :loading="loading"
                       :employees="employees"
                       :potential-employees="potentialEmployees"
                       :end-cooperation-employees="endCooperationEmployees"/>
  </v-container>
</template>

<script>
  import PersonsListCard from '../../components/hr/personsList/PersonsListCard';
  import { EventBus, eventNames } from '../../eventbus';
  import { mapState } from 'vuex';

  export default {
    name: 'PersonsList',
    components: { PersonsListCard },
    data() {
      return {
        loading: false,
      };
    },
    computed: {
      ...mapState({
        employees: state => state.Employees.employees,
        potentialEmployees: state => state.Hr.potentialEmployees,
        endCooperationEmployees: state => state.Hr.endCooperationEmployees,
      }),
    },
    methods: {
      async loadData() {
        this.loading = true;
        await Promise.all([
          this.$store.dispatch('Hr/loadPotentialEmployees'),
          this.$store.dispatch('Employees/loadEmployees'),
          this.$store.dispatch('Hr/loadEndCooperationEmployees'),
        ]);
        this.loading = false;
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
    mounted() {
      EventBus.$on(eventNames.hrPersonListReload, this.loadData);
    },
  };
</script>
