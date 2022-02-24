<template>
  <v-timeline dense align-top style="overflow: auto">
    <!-- Request created -->
    <v-timeline-item icon="timelapse" color="yellow">
      <strong>{{ $t('Request created') }}</strong>
      <div class="caption mb-2">{{ formatDate(request.createdAt) }}</div>
      <employee-chip :employee="request.employee"/>
    </v-timeline-item>
    <!-- Resigned before acceptation -->
    <v-timeline-item v-if="resignedWithoutAcceptance" icon="highlight_off" color="red">
      <strong>{{ $t('Resigned') }}</strong>
      <div class="caption mb-2">{{ formatDate(request.updatedAt) }}</div>
      <employee-chip :employee="request.employee"/>
    </v-timeline-item>
    <!-- Request rejected -->
    <v-timeline-item v-if="rejected" icon="highlight_off" color="red">
      <strong>{{ $t('Rejected') }}</strong>
      <div class="caption mb-2">{{ formatDate(request.updatedAt) }}</div>
      <employee-chip :employee="request.manager"/>
    </v-timeline-item>
    <!-- Request accepted -->
    <v-timeline-item v-if="accepted" icon="check_circle" color="green">
      <strong>{{ $t('Accepted') }}</strong>
      <div class="caption mb-2">{{ formatDate(request.acceptedAt) }}</div>
      <employee-chip :employee="request.manager"/>
    </v-timeline-item>
    <!-- Request rejected after acceptance -->
    <v-timeline-item v-if="rejectedAfterAcceptance" icon="highlight_off" color="red">
      <strong>{{ $t('Rejected') }}</strong>
      <div class="caption mb-2">{{ formatDate(request.updatedAt) }}</div>
      <employee-chip :employee="request.manager"/>
    </v-timeline-item>
    <!-- Requested resignation -->
    <v-timeline-item v-if="pendingResignation || resignationAccepted" icon="timelapse" color="yellow">
      <strong>{{ $t('Requested resignation') }}</strong>
      <div class="caption mb-2"><span v-if="!resignationAccepted">{{ formatDate(request.updatedAt) }}</span></div>
      <employee-chip :employee="request.employee"/>
    </v-timeline-item>
    <!-- Resignation accepted -->
    <v-timeline-item v-if="resignationAccepted" icon="check_circle" color="red">
      <strong>{{ $t('Resignation accepted') }}</strong>
      <div class="caption mb-2">{{ formatDate(request.updatedAt) }}</div>
      <employee-chip :employee="request.manager"/>
    </v-timeline-item>
    <!-- Request deleted -->
    <v-timeline-item v-if="deleted" icon="delete" color="#BBBBBB">
      <strong>{{ $t('Deleted by administrator') }}</strong>
      <div class="caption mb-2">{{ formatDate(request.updatedAt) }}</div>
    </v-timeline-item>
  </v-timeline>
</template>

<script>
  import EmployeeChip from '../../utils/EmployeeChip';
  import { leaveRequestsStatuses as statuses } from '../../../util/freeDays';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'RequestTimeline',
    components: { EmployeeChip },
    props: {
      request: { type: Object, required: true },
    },
    computed: {
      resignedWithoutAcceptance() {
        return typeof(this.request.acceptedAt) === 'undefined'
          && this.request.status === statuses.resigned
          && !this.request.hidden;
      },
      rejected() {
        return typeof(this.request.acceptedAt) === 'undefined' && this.request.status === statuses.rejected;
      },
      rejectedAfterAcceptance() {
        return typeof(this.request.acceptedAt) !== 'undefined' && this.request.status === statuses.rejected;
      },
      accepted() {
        return typeof(this.request.acceptedAt) !== 'undefined';
      },
      pendingResignation() {
        return this.request.status === statuses.pendingResignation;
      },
      resignationAccepted() {
        return typeof(this.request.acceptedAt) !== 'undefined'
          && this.request.status === statuses.resigned
          && !this.request.hidden;
      },
      deleted() {
        return this.request.status === statuses.resigned && this.request.hidden;
      },
    },
    methods: {
      formatDate(date) {
        return moment(date).format(this.$t('date_format'));
      },
    },
    i18n: { messages: {
      pl: {
        'Request created': 'Utworzono wniosek',
        'Resigned': 'Zrezygnowano',
        'Rejected': 'Odrzucono',
        'Accepted': 'Zaakceptowano',
        'Requested resignation': 'Złożono wniosek o rezygnację',
        'Resignation accepted': 'Rezygnacja zaakceptowana',
        'Deleted by administrator': 'Usunięto przez administratora',
      },
      en: {
        date_format: 'D MMM YYYY HH:mm:ss',
      },
    } },
  };
</script>
