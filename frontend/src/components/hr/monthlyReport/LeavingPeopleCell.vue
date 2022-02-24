<template>
  <td class="text-center">
    <v-tooltip left>
      <template v-slot:activator="{ on }">
        <span v-on="on">{{ text }}</span>
      </template>
      {{ tooltip }}
    </v-tooltip>
  </td>
</template>

<script>
  export default {
    name: 'LeavingPeopleCell',
    props: {
      firstDay: { type: Object, required: true },
      lastDay: { type: Object, required: true },
      employees: { type: Array, required: true },
    },
    computed: {
      employeesCount() {
        return this.employees
          .filter(employee => {
            if (typeof employee.hiredTo === 'undefined') {
              return false;
            }
            return employee.hiredTo.isSameOrBefore(this.lastDay) && employee.hiredTo.isSameOrAfter(this.firstDay);
          }).length;
      },
      potentialEmployeesCount() {
        return 0;
      },
      text() {
        const accepted = this.employeesCount;
        return `${accepted}`;
      },
      tooltip() {
        const accepted = this.employeesCount;
        return this.$t('count-accepted', [accepted]);
      },
    },
    i18n: {
      messages: {
        pl: {
          'count-accepted': '{0} osób odeszło',
        },
        en: {
          'count-accepted': '{0} people leaving',
        },
      },
    },
  };
</script>
