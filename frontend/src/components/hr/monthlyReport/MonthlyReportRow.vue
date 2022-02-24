<template>
  <tr>
    <td class="text-center font-weight-bold">
      {{ month.label }}
    </td>
    <people-count-in-month-cell :first-day="firstDay"
                                :last-day="lastDay"
                                :accepted-employees="acceptedEmployees"
                                :potential-employees="potentialEmployees"/>
    <count-delta-cell :first-day="firstDay"
                      :last-day="lastDay"
                      :accepted-employees="acceptedEmployees"
                      :potential-employees="potentialEmployees"/>
    <new-people-count-cell :first-day="firstDay"
                           :last-day="lastDay"
                           :accepted-employees="acceptedEmployees"
                           :potential-employees="potentialEmployees"/>
    <leaving-people-cell :first-day="firstDay"
                         :last-day="lastDay"
                         :employees="acceptedEmployees"/>
  </tr>
</template>

<script>
  import PeopleCountInMonthCell from './PeopleCountInMonthCell';
  import NewPeopleCountCell from './NewPeopleCountCell';
  import LeavingPeopleCell from './LeavingPeopleCell';
  import CountDeltaCell from './CountDeltaCell';

  const STATUS_POTENTIAL = 0;
  const STATUS_ACCEPTED = 1;

  export default {
    name: 'MonthlyReportRow',
    components: { CountDeltaCell, LeavingPeopleCell, NewPeopleCountCell, PeopleCountInMonthCell },
    props: {
      month: { type: Object, required: true },
      employees: { type: Array, required: true },
    },
    computed: {
      firstDay() {
        return this.month.firstDay;
      },
      lastDay() {
        return this.month.lastDay;
      },
      acceptedEmployees() {
        return this.employees.filter(employee => employee.status === STATUS_ACCEPTED);
      },
      potentialEmployees() {
        return this.employees.filter(employee => employee.status === STATUS_POTENTIAL);
      },
    },
  };
</script>
