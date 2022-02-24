<template>
  <work-location :work-location-type="DELEGATION_TYPE"
                 icon="emoji_transportation"
                 :all-managers="allManagers"
                 :employee="employee"/>
</template>

<script>
  import WorkLocation from './WorkLocation';
  import { mapGetters, mapState } from 'vuex';

  export default {
    name: 'DelegationMenu',
    components: { WorkLocation },
    data() {
      return {
        DELEGATION_TYPE: 2,
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
