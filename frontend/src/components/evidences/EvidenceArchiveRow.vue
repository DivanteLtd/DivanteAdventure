<template>
  <tr @click="openEvidenceWindow">
    <td>{{ evidence.id }}</td>
    <td>{{ month }}</td>
    <td><evidence-status-chip :evidence="evidence"/></td>
    <td v-if="evidence.employee.contract.id !== Number(contractsName.CoE.id)">{{ workingHours }}</td>
    <td>{{ overtimeHours }}</td>
    <td>{{ creationTime }}</td>
  </tr>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import EvidenceStatusChip from './EvidenceStatusChip';
  import { EventBus, eventNames } from '../../eventbus';
  import { contractsName } from '../../util/contracts';

  export default {
    name: 'EvidenceArchiveRow',
    components: { EvidenceStatusChip },
    props: {
      evidence: { type: Object, required: true },
    },
    data() {
      return {
        contractsName,
      };
    },
    computed: {
      creationTime() {
        if (this.evidence.createdAt) {
          return moment.unix(this.evidence.createdAt).format('D MMMM YYYY');
        }
        return '';
      },
      month() {
        return moment(`${this.evidence.year}-${this.evidence.month}-15`).format('MMMM YYYY');
      },
      workingHours() {
        return parseFloat(this.evidence.workingHours).toFixed(2);
      },
      overtimeHours() {
        return this.evidence.overtimeEntries.map(entry => entry.hours).reduce((a, b) => (a * 1 + b * 1), 0);
      },
    },
    methods: {
      openEvidenceWindow() {
        EventBus.$emit(eventNames.showEvidenceWindow, this.evidence);
      },
    },
  };
</script>

<style scoped>
  td {
    text-align: center;
  }
  tr {
    cursor: pointer;
  }
</style>
