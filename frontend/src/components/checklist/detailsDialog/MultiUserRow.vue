<template>
  <v-list-item class="multi-user-row-flex-top">
    <div class="mr-1">{{ label }}</div>
    <div class="my-2 employee-chip">
      <employee-chip v-for="(employee, index) in employees" :key="index" :employee="employee" class="ma-1"/>
    </div>
  </v-list-item>
</template>

<script>
  import EmployeeChip from '../../utils/EmployeeChip';

  export default {
    name: 'SingleUserRow',
    components: { EmployeeChip },
    props: {
      label: { type: String, required: true },
      employees: { type: Array, required: true },
    },
  };
</script>

<style>
  .multi-user-row-flex-top .v-list__tile {
    display: flex;
    flex-flow: row wrap;
    justify-content: space-between;
    width: 100%;
  }
  @media only screen and (max-width: 600px) {
    .multi-user-row-flex-top{
      flex-direction: column;
      min-height: unset;
    }
    .multi-user-row-flex-top .employee-chip{
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
  }
</style>
