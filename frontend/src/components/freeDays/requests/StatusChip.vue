<template>
  <v-chip :color="color" outlined>
    <v-icon class="mr-1">{{ icon }}</v-icon>
    {{ label }}
  </v-chip>
</template>

<script>
  import { leaveDaysTypes, leaveRequestsStatuses } from '../../../util/freeDays';

  export default {
    name: 'StatusChip',
    props: {
      request: { type: Object, required: true },
    },
    computed: {
      label() {
        if (this.request.hidden && this.request.status === leaveRequestsStatuses.resigned) {
          return this.$t('Deleted');
        }
        if (this.notRequiringAcceptance) {
          return this.$t('Not requiring actions');
        }
        switch(this.request.status) {
          case leaveRequestsStatuses.pending: return this.$t('Awaiting decision');
          case leaveRequestsStatuses.accepted: return this.$t('Request accepted');
          case leaveRequestsStatuses.rejected: return this.$t('Request rejected');
          case leaveRequestsStatuses.pendingResignation: return this.$t('Pending resignation');
          case leaveRequestsStatuses.resigned: return this.$t('Resigned');
          case leaveRequestsStatuses.planned: return this.$t('Planned');
          default: return 'N/A';
        }
      },
      color() {
        if ((this.request.hidden && this.request.status === leaveRequestsStatuses.resigned)
          || this.notRequiringAcceptance) {
          return '#BBBBBB';
        }
        switch(this.request.status) {
          case leaveRequestsStatuses.pending: return '#0000FF';
          case leaveRequestsStatuses.accepted: return '#00AA00';
          case leaveRequestsStatuses.rejected: return '#FF0000';
          case leaveRequestsStatuses.pendingResignation: return '#AAAA00';
          case leaveRequestsStatuses.resigned: return '#FF0000';
          case leaveRequestsStatuses.planned: return '#0000FF';
          default: return null;
        }
      },
      icon() {
        if (this.request.hidden && this.request.status === leaveRequestsStatuses.resigned) {
          return 'delete';
        }
        if (this.notRequiringAcceptance) {
          return 'check_circle';
        }
        switch(this.request.status) {
          case leaveRequestsStatuses.pending: return 'timelapse';
          case leaveRequestsStatuses.accepted: return 'check_circle';
          case leaveRequestsStatuses.rejected: return 'highlight_off';
          case leaveRequestsStatuses.pendingResignation: return 'timelapse';
          case leaveRequestsStatuses.resigned: return 'check_circle';
          case leaveRequestsStatuses.planned: return 'timelapse';
          default: return null;
        }
      },
      notRequiringAcceptance() {
        const days = this.request.days || [{ type: -1 }];
        if (days.length > 0) {
          const type = days[0].type;
          return type === leaveDaysTypes.overtime || type === leaveDaysTypes.additionalHours;
        }
        return false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Awaiting decision': 'Oczekuje na akceptację',
          'Request accepted': 'Wniosek zaakceptowany',
          'Request rejected': 'Wniosek odrzucony',
          'Pending resignation': 'Oczekuje na rezygnację',
          'Resigned': 'Zrezygnowano',
          'Planned': 'Zaplanowano',
          'Deleted': 'Usunięto',
          'Not requiring actions': 'Nie wymaga czynności',
        },
      },
    },
  };
</script>
