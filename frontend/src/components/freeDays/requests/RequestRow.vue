<template>
  <tr style="border-bottom: none !important; background: none;" @click="openRequestWindow">
    <td>
      {{ '#' + request.id }}
    </td>
    <td class="date">
      {{ createdAtDate }}
      <br/>
      {{ createdAtTime }}
    </td>
    <td>
      <employee-chip :employee="request.manager"/>
    </td>
    <td>
      <status-chip :request="request"/>
    </td>
    <td class="right">
      <request-actions :request="request" :disable-delete="reduced"/>
    </td>
  </tr>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import EmployeeChip from '../../utils/EmployeeChip';
  import StatusChip from './StatusChip';
  import RequestActions from './RequestActions';
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'RequestRow',
    components: { RequestActions, StatusChip, EmployeeChip },
    props: {
      request: { type: Object, required: true },
      periodStartDate: { type: Object, required: false, default: null },
      reduced: { type: Boolean, default: false },
    },
    computed: {
      createdAtDate() {
        return moment(this.request.createdAt).format('D MMMM YYYY');
      },
      createdAtTime() {
        return moment(this.request.createdAt).format('HH:mm:ss');
      },
    },
    methods: {
      openRequestWindow() {
        this.request.periodStartDate = this.periodStartDate;
        EventBus.$emit(eventNames.showRequestDetails, this.request);
      },
    },
  };
</script>
<style scoped>
  td {
    text-align: center;
    border-bottom: none !important;
  }
  td.date {
    min-width: 130px;
  }
  td.right {
    text-align: right;
  }
  tr {
    cursor: pointer;
  }
</style>
