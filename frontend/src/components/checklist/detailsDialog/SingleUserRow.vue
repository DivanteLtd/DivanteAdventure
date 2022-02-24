<template>
  <v-list-item class="single-user-row-flex-top">
    <div class="mr-1">{{ label }}</div>
    <employee-chip :employee="employee" class="ma-1"/>
  </v-list-item>
</template>

<script>
  import EmployeeChip from '../../utils/EmployeeChip';

  export default {
    name: 'SingleUserRow',
    components: { EmployeeChip },
    props: {
      label: { type: String, required: true },
      employee: { type: Object, required: true },
    },
  };
</script>

<style>
  .single-user-row-flex-top .v-list__tile {
    display: flex;
    flex-flow: row wrap;
    justify-content: space-between;
    width: 100%;
  }
  @media only screen and (max-width: 600px) {
    .single-user-row-flex-top{
      flex-direction: column;
      min-height: unset;
      padding-bottom: 8px;
    }
  }
</style>
