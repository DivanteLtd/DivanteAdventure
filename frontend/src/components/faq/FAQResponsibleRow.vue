<template>
  <tr>
    <td colspan="2">
      <v-col class="employee">
        <div v-for="(responsibleEmployee, key) in category.responsibles" :key="key">
          <employee-chip v-if="typeof(responsibleEmployee) === 'object'" :employee="responsibleEmployee"/>
        </div>
      </v-col>
    </td>
  </tr>
</template>

<script>
  import EmployeeChip from '../utils/EmployeeChip';

  export default {
    name: 'FAQResponsibleRow',
    components: { EmployeeChip },
    props: {
      category: { type: Object, required: true },
    },
  };
</script>
<style scoped>
  td {
    border: none !important;
  }
  .employee {
    width: 100%;
    display: inline-flex;
  }
</style>
