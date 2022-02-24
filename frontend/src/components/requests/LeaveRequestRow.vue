<template>
  <request-row @show="showRequestWindow"
               :request="request"
               :chip-color="chipColor"
               :chip-icon="chipIcon"
               :chip-text="chipText">
    <template slot="status"><status-chip :request="request"/></template>
    <template slot="date">{{ createdAt }}</template>
  </request-row>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import StatusChip from '../freeDays/requests/StatusChip';
  import { EventBus, eventNames } from '../../eventbus';
  import RequestRow from './RequestRow';
  import { leaveDaysTypes } from '../../util/freeDays';

  export default {
    name: 'LeaveRequestRow',
    components: { RequestRow, StatusChip },
    props: {
      request: { type: Object, required: true },
      awaiting: { type: Boolean, default: false },
      planned: { type: Boolean, default: false },
    },
    computed: {
      chipColor() {
        if (this.requestType === leaveDaysTypes.overtime || this.requestType === leaveDaysTypes.additionalHours) {
          return '#324584';
        }
        return '#7F3E30';
      },
      chipText() {
        if (this.requestType === leaveDaysTypes.overtime) {
          return this.$t('Taking back overtime');
        } else if (this.requestType === leaveDaysTypes.additionalHours) {
          return this.$t('Taking back additional hours');
        } else if (this.request.employee.contract.name === 'CoE') {
          return this.$t('Leave request');
        } else if (this.request.employee.contract.name === 'B2B HOURLY'
          || this.request.employee.contract.name === 'CLC HOURLY') {
          return this.$t('Unavailability day request');
        }
        return this.$t('Free day request');
      },
      chipIcon() {
        if (this.requestType === leaveDaysTypes.overtime || this.requestType === leaveDaysTypes.additionalHours) {
          return 'access_time';
        }
        return 'pause_circle_outline';
      },
      createdAt() {
        return moment.unix(this.request._orderTimestamp).format(this.$t('date_format'));
      },
      requestType() {
        const days = this.request.days || [{ type: -1 }];
        return days[0].type;
      },
    },
    methods: {
      showRequestWindow() {
        if (this.awaiting) {
          EventBus.$emit(eventNames.showRequestDetailsForAcceptance, this.request);
        } else if (this.planned) {
          EventBus.$emit(eventNames.showRequestDetailsForAcceptance, this.request);
        } else {
          EventBus.$emit(eventNames.showRequestDetails, this.request);
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Leave request': 'Wniosek urlopowy',
          'Unavailability day request': 'Wniosek o dzień niedostępnośći',
          'Free day request': 'Wniosek o dzień wolny',
          'date_format': 'D MMMM YYYY r. HH:mm:ss',
          'Taking back overtime': 'Odbieranie nadgodzin',
          'Taking back additional hours': 'Odbieranie godzin dodatkowych',
        },
        en: {
          date_format: 'DD-MM-YYYY HH:mm:ss',
        },
      },
    },
  };
</script>
