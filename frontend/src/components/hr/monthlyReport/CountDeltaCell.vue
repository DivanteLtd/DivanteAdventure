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
    name: 'CountDeltaCell',
    props: {
      firstDay: { type: Object, required: true },
      lastDay: { type: Object, required: true },
      acceptedEmployees: { type: Array, required: true },
      potentialEmployees: { type: Array, required: true },
    },
    computed: {
      acceptedEmployeesCount() {
        const newPeople = this.acceptedEmployees
          .filter(employee => {
            if (typeof employee.hiredAt === 'undefined') {
              return false;
            }
            return employee.hiredAt.isSameOrBefore(this.lastDay) && employee.hiredAt.isSameOrAfter(this.firstDay);
          }).length;
        const leavingPeople = this.acceptedEmployees
          .filter(employee => {
            if (typeof employee.hiredTo === 'undefined') {
              return false;
            }
            return employee.hiredTo.isSameOrBefore(this.lastDay) && employee.hiredTo.isSameOrAfter(this.firstDay);
          }).length;
        return newPeople - leavingPeople;
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
          'count-accepted': 'Zmiana liczby osób o {0} względem ostatniego miesiąca',
          'count-accepted-plus-potential': 'Zmiana liczby osób o {0} względem ostatniego miesiąca + {1} potencjalnych nowych osób',
        },
        en: {
          'count-accepted': 'Change in the number of people by {0} in relation to the last month',
          'count-accepted-plus-potential': 'Change in the number of people by {0} in relation to the last month + {1} potential new people',
        },
      },
    },
  };
</script>
