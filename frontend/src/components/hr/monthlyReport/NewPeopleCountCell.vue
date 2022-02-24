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
    name: 'NewPeopleCountCell',
    props: {
      firstDay: { type: Object, required: true },
      lastDay: { type: Object, required: true },
      acceptedEmployees: { type: Array, required: true },
      potentialEmployees: { type: Array, required: true },
    },
    computed: {
      acceptedEmployeesCount() {
        return this.acceptedEmployees
          .filter(employee => {
            if (typeof employee.hiredAt === 'undefined') {
              return false;
            }
            return employee.hiredAt.isSameOrBefore(this.lastDay) && employee.hiredAt.isSameOrAfter(this.firstDay);
          }).length;
      },
      potentialEmployeesCount() {
        return this.potentialEmployees
          .filter(employee => {
            if (typeof employee.hiredAt === 'undefined') {
              return false;
            }
            return employee.hiredAt.isSameOrAfter(this.firstDay) && employee.hiredAt.isSameOrBefore(this.lastDay);
          }).length;
      },
      text() {
        const accepted = this.acceptedEmployeesCount;
        const potential = this.potentialEmployeesCount;
        if (potential === 0) {
          return `${accepted}`;
        } else {
          return `${accepted} (+ ${potential})`;
        }
      },
      tooltip() {
        const accepted = this.acceptedEmployeesCount;
        const potential = this.potentialEmployeesCount;
        if (potential === 0) {
          return this.$t('count-accepted', [ accepted ]);
        } else {
          return this.$t('count-accepted-plus-potential', [ accepted, potential ]);
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'count-accepted': '{0} nowych osób',
          'count-accepted-plus-potential': '{0} nowych osób + {1} osób potencjalnych',
        },
        en: {
          'count-accepted': '{0} new people',
          'count-accepted-plus-potential': '{0} new people + {1} potential people',
        },
      },
    },
  };
</script>
