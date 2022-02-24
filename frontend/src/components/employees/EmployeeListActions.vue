<template>
  <div class="icons">
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';

  export default {
    name: 'EmployeeListActions',
    components: { },
    props: {
      employee: { type: Object, required: true },
    },
    data() { return {
      deleteUser: false,
    };},
    computed: {
      ...mapGetters({
        isManager: 'Authorization/isManager',
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
      employeeProps() {
        return this.employee;
      },
      daysOffMessage() {
        switch (this.employee.contract.name) {
          case 'CoE': return this.$t('Leave days');
          case 'CLC LUMP SUM':
          case 'B2B LUMP SUM': return this.$t('Free days');
          default: return this.$t('Unavailability days');
        }
      },
    },
    methods: {
      goToEmployeeDaysOff() {
        const url = `/free-days/${this.employee.id}`;
        this.$router.push(url);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Leave days': 'Dni urlopowe',
          'Free days': 'Dni wolne',
          'Unavailability days': 'Dni niedostępności',
          'Delete': 'Usuń',
        },
      },
    },
  };
</script>
<style scoped>
  icons {
    justify-content: flex-end;
    display: flex;
  }
</style>
