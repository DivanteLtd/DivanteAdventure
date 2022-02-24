<template>
  <request-row @show="showRequestWindow"
               :request="request"
               chip-color="#32847B"
               chip-icon="access_time"
               :chip-text="$t('Overtime')">
    <template slot="status"><evidence-status-chip :evidence="request"/></template>
    <template slot="date">{{ date }}</template>
  </request-row>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import EvidenceStatusChip from '../evidences/EvidenceStatusChip';
  import { EventBus, eventNames } from '../../eventbus';
  import RequestRow from './RequestRow';

  export default {
    name: 'OvertimeRequestRow',
    components: { RequestRow, EvidenceStatusChip },
    props: {
      request: { type: Object, required: true },
      awaiting: { type: Boolean, default: false },
    },
    computed: {
      date() {
        if (this.request.createdAt) {
          return moment.unix(this.request.createdAt).format(this.$t('date_format'));
        }
        return '';
      },
    },
    methods: {
      showRequestWindow() {
        if (this.awaiting) {
          EventBus.$emit(eventNames.showEvidenceWindowForAcceptance, this.request);
        }
        else {
          EventBus.$emit(eventNames.showEvidenceWindow, this.request);
        }
      },
    },
    i18n: { messages: {
      pl: {
        Overtime: 'Dodatkowe godziny',
        date_format: 'D MMMM YYYY r. HH:mm:ss',
      },
      en: {
        date_format: 'DD-MM-YYYY HH:mm:ss',
      },
    } },
  };
</script>
