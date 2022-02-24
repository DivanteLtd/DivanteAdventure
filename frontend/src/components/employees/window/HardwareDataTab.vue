<template>
  <v-list two-line dense>
    <v-list-item v-if="loading">
      <v-progress-linear height="6" indeterminate/>
    </v-list-item>
    <hardware-data-row v-else v-for="(assignment, index) in employeeHardware" :key="index" :assignment="assignment"/>
  </v-list>
</template>

<script>
  import HardwareDataRow from './HardwareDataRow';
  import { mapState } from 'vuex';

  export default {
    name: 'HardwareDataTab',
    components: { HardwareDataRow },
    props: {
      employee: { type: Object, required: true },
      loading: { type: Boolean, default: false },
      hardwareList: { type: Array, required: true },
      currentUser: { type: Object, required: true },
    },
    data() {
      return {
        employeeHardware: this.hardwareList,
      };
    },
    computed: {
      ...mapState({
        apiClient: state => state.apiClient,
      }),
    },
    watch: {
      employee() {
        this.reload();
      },
    },
    methods: {
      async reload() {
        this.employeeHardware = await this.apiClient.employee.getHardwareList(this.employee.id);
      },
    },
  };
</script>
