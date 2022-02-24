<template>
  <v-card-text :class="{'pa-2': $vuetify.breakpoint.xs}">
    <tribe-details-view :tribe="tribe"/>
    <tribe-tabs :tribe="tribe"
                :loading="loading"
                :employees="employeesInTribe"
                :projects="projectsInTribe"
                :positions="positionsInTribe"
                :positions-count="positionsCount"
    />
  </v-card-text>
</template>

<script>
  import TribeDetailsView from './TribeDetailsView';
  import TribeTabs from './TribeTabs';
  import { mapState } from 'vuex';

  export default {
    name: 'TribeWindowContent',
    components: { TribeTabs, TribeDetailsView },
    props: {
      tribe: { type: Object, required: true },
      loading: { type: Boolean, default: false },
    },
    computed: {
      ...mapState({
        allEmployees: state => state.Employees.employees,
        allProjects: state => state.Projects.projects,
        allTribes: state => state.Tribes.tribes,
      }),
      employeesInTribe() {
        return this.allEmployees.filter(employee => employee.tribe && employee.tribe.id === this.tribe.id);
      },
      projectsInTribe() {
        return this.allProjects.filter(project => project.tribes.find(val => val === this.tribe.id));
      },
      positionsInTribe() {
        const tribe = this.allTribes.find(tribe => tribe.id === this.tribe.id);
        return tribe ? tribe.positions : [];
      },
      positionsCount() {
        return this.employeesInTribe.filter(employee => employee.position).reduce(
          (result, item) => ({
            ...result,
            [item.position.name]: result[item.position.name] ? result[item.position.name] + 1 : 1,
          }),
          {},
        );
      },
    },
  };
</script>
