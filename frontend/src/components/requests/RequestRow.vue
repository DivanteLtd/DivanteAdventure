<template>
  <tr @click="showRequestWindow">
    <td>{{ request.id }}</td>
    <td>
      <v-chip :color="chipColor" :text-color="chipColor" outlined>
        <v-avatar class="mr-1"><v-icon>{{ chipIcon }}</v-icon></v-avatar>
        {{ chipText }}
      </v-chip>
    </td>
    <td><employee-chip :employee="request.employee"/></td>
    <td><slot name="status" :request="request"/></td>
    <td><slot name="date" :request="request"/></td>
  </tr>
</template>

<script>
  import EmployeeChip from '../utils/EmployeeChip';
  import { getHexContractColor } from '../../util/colors';

  export default {
    name: 'RequestRow',
    components: { EmployeeChip },
    props: {
      request: { type: Object, required: true },
      chipColor: { type: String, default: '#000000' },
      chipIcon: { type: String, default: 'home' },
      chipText: { type: String, default: 'Request' },
    },
    computed: {
      textColor() {
        if (!this.request._requiringAction) {
          return this.chipColor;
        } else {
          return getHexContractColor(this.chipColor);
        }
      },
    },
    methods: {
      showRequestWindow() {
        this.$emit('show');
      },
    },
  };
</script>
<style scoped>
  tr {
    cursor: pointer;
  }
  td {
    border-bottom: none;
  }
</style>
