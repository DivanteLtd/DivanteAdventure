<template>
  <tr @click="showDialog" :class="clickable ? 'clickable' : ''">
    <td style="font-weight: bold;">
      {{ label }}
    </td>
    <td>
      {{ entry.joined }}
    </td>
    <td>
      {{ entry.left }}
    </td>
    <td>
      {{ entry.balance }}
    </td>
    <td>
      {{ entry.workTime.toFixed(2) }}
    </td>
    <td>
      {{ women }}
    </td>
    <td>
      {{ men }}
    </td>
    <td>
      {{ entry.terminatedByCompany }}
    </td>
    <td>
      {{ entry.terminatedByEmployee }}
    </td>
    <td>
      {{ entry.pri.toFixed(2) }}%
    </td>
  </tr>
</template>

<script>
  export default {
    name: 'HrTableRow',
    props: {
      entry: { type: Object, required: true },
      labelConverter: { type: Function, required: true },
      clickable: { type: Boolean, default: false },
    },
    computed: {
      label() {
        return this.labelConverter(this.entry);
      },
      women() {
        const all = this.entry.women + this.entry.men;
        const percentage = this.entry.women * 100 / all;
        return `${this.entry.women} (${percentage.toFixed(2)}%)`;
      },
      men() {
        const all = this.entry.women + this.entry.men;
        const percentage = this.entry.men * 100 / all;
        return `${this.entry.men} (${percentage.toFixed(2)}%)`;
      },
    },
    methods: {
      showDialog() {
        this.$emit('click', this.entry);
      },
    },
  };
</script>

<style scoped>
  td {
    text-align: center;
  }

  tr.clickable {
    cursor: pointer;
  }
</style>
