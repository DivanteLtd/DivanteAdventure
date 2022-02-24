<template>
  <v-combobox :items="sortedEmployees"
              v-model="employee"
              :label="label"
              :prepend-icon="prependIcon"
              :item-text="getItemText"
              chips dense :disabled="isEdit">
    <template slot="selection" slot-scope="data">
      <employee-chip v-if="typeof(data.item) === 'object'" :employee="data.item"/>
    </template>
  </v-combobox>
</template>

<script>
  import EmployeeChip from './EmployeeChip';

  export default {
    name: 'EmployeeChooser',
    components: { EmployeeChip },
    props: {
      employees: { type: Array, required: true },
      value: { type: Object, default: () => null },
      label: { type: String, default: null },
      prependIcon: { type: String, default: null },
      isEdit: { type: Boolean, required: false },
    },
    computed: {
      sortedEmployees() {
        return this.employees.slice(0, this.employees.length).sort((a, b) => {
          const textA = this.getItemText(a);
          const textB = this.getItemText(b);
          return textA.localeCompare(textB);
        });
      },
      employee: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
    methods: {
      getItemText(item) {
        return item.lastName ? `${item.lastName} ${item.name}` : item.name;
      },
    },
  };
</script>
