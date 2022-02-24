<template>
  <work-location :work-location-type="REMOTELY_TYPE"
                 :all-managers="allManagers"
                 icon="home"
                 v-if="employee.workMode !== WORK_REMOTELY"
                 :employee="employee"/>
</template>

<script>
  import WorkLocation from './WorkLocation';
  import { mapGetters, mapState } from 'vuex';

  const WORK_REMOTELY = 2;
  const REMOTELY_TYPE = 1;

  export default {
    name: 'RemotelyMenu',
    components: { WorkLocation },
    data() {
      return {
        WORK_REMOTELY,
        REMOTELY_TYPE,
      };
    },
    computed: {
      ...mapGetters({
        allManagers: 'Employees/getManagersAcceptingLeaveRequests',
      }),
      ...mapState({
        employee: state => state.Employees.employee,
      }),
    },
  };
</script>
