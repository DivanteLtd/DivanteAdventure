<template>
  <v-timeline dense align-top class="evidence-timeline">
    <!-- Evidence created -->
    <v-timeline-item icon="timelapse" color="yellow">
      <v-card class="elevation-0">
        <v-card-text>
          <strong>{{ notCoE ? $t('Evidence created') : $t('Evidence overtime created') }}</strong>
          <div class="caption mb-2">{{ formatDate(evidence.createdAt) }}</div>
          <employee-chip :employee="evidence.employee"/>
        </v-card-text>
      </v-card>
    </v-timeline-item>
    <!-- Evidence sent -->
    <v-timeline-item color="green" small v-if="notCoE">
      <v-card class="elevation-0">
        <v-card-text>
          <strong>{{ $t('Evidence sent') }}</strong>
        </v-card-text>
      </v-card>
    </v-timeline-item>
    <!-- Overtime entry -->
    <v-timeline-item v-if="overtimeStatusData.show" :color="overtimeStatusData.color" :icon="overtimeStatusData.icon">
      <v-card class="elevation-0">
        <v-card-text>
          <strong>{{ overtimeStatusData.message }}</strong>
          <div class="caption mb-2">{{ formatDate(overtimeStatusData.time) }}</div>
          <employee-chip :employee="evidence.overtimeManager"/>
        </v-card-text>
      </v-card>
    </v-timeline-item>
    <!-- Overtime sent -->
    <v-timeline-item v-if="showOvertimeSent" color="green" small>
      <v-card class="elevation-0">
        <v-card-text>
          <strong>{{ $t('Overtime sent') }}</strong>
        </v-card-text>
      </v-card>
    </v-timeline-item>
  </v-timeline>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import { evidenceStatus, getColor, getIcon } from '../../../util/evidences';
  import { contractsName } from '../../../util/contracts';
  import EmployeeChip from '../../utils/EmployeeChip';

  export default {
    name: 'EvidenceTimeline',
    components: { EmployeeChip },
    props: {
      evidence: { type: Object, default: () => ({}) },
    },
    computed: {
      notCoE() {
        if (this.evidence.employee.contract !== undefined) {
          return this.evidence.employee.contract.id !== Number(contractsName.CoE.id);
        }
        return 0;
      },
      overtimeStatusData() {
        if (this.evidence.status === evidenceStatus.APPROVAL_NOT_REQUIRED) {
          return { show: false };
        }
        else {
          let message = '';
          let time = '';
          switch(this.evidence.status) {
            case evidenceStatus.AWAITS_APPROVAL:
              message = this.$t('Overtime awaits approval');
              break;
            case evidenceStatus.SENT:
              message = this.$t('Overtime approved');
              time = this.evidence.updatedAt;
              break;
            case evidenceStatus.NOT_APPROVED:
              message = this.$t('Overtime not approved');
              time = this.evidence.updatedAt;
              break;
            default:
              message = '';
              time = '';
          }
          return {
            show: true,
            message,
            icon: getIcon(this.evidence.status),
            color: getColor(this.evidence.status),
            time,
          };
        }
      },
      showOvertimeSent() {
        return this.evidence.status === evidenceStatus.SENT;
      },
    },
    methods: {
      formatDate(timestamp) {
        if (timestamp) {
          return moment.unix(timestamp).format(this.$t('date_format'));
        }
        return '';
      },
    },
    i18n: { messages: {
      pl: {
        'Evidence created': 'Utworzono ewidencję',
        'Evidence overtime created': 'Utworzono ewidencję nadgodzin',
        'Evidence sent': 'Ewidencja wysłana do administracji',
        'Overtime awaits approval': 'Dodatkowe godziny oczekują na akceptację',
        'Overtime approved': 'Dodatkowe godziny zaakceptowane',
        'Overtime not approved': 'Dodatkowe godziny odrzucone',
        'Overtime sent': 'Dodatkowe godziny wysłane do administracji',
      },
      en: {
        date_format: 'D MMM YYYY HH:mm:ss',
      },
    } },
  };
</script>
<style scoped>
  .evidence-timeline{
    overflow: auto;
  }
</style>
