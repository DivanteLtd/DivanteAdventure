<template>
  <v-card flat>
    <v-card-text>
      <v-list dense>
        <info-tile :title="$t('Month:')">
          {{ month }} {{ fromNow }}
        </info-tile>
        <info-tile v-if="notCoE" :title="$t('Working hours:')">
          {{ parseFloat(evidence.workingHours).toFixed(2) }}
        </info-tile>
        <info-tile v-if="hourlyContract" :title="$t('Unavailable hours:')">
          {{ Math.round(evidence.unavailabilityHours) }}
        </info-tile>
        <info-tile v-if="!hourlyContract && notCoE" :title="$t('Paid free hours:')">
          {{ Math.round(evidence.paidFreeHours) }}
        </info-tile>
        <info-tile v-if="!hourlyContract && notCoE" :title="$t('Unpaid free hours:')">
          {{ Math.round(evidence.unpaidFreeHours) }}
        </info-tile>
        <info-tile v-if="!hourlyContract && notCoE" :title="$t('Sick leave hours:')">
          {{ Math.round(evidence.sickLeaveHours) }}
        </info-tile>
      </v-list>
    </v-card-text>
  </v-card>
</template>

<script>
  import InfoTile from '../../freeDays/info/InfoTile';
  import moment from '@divante-adventure/work-moment';
  import { contractsName } from '../../../util/contracts';

  export default {
    name: 'EvidenceInfo',
    components: { InfoTile },
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
      hourlyContract() {
        if (this.evidence.employee.contract !== undefined) {
          return this.evidence.employee.contract.id === Number(contractsName.B2B_HOURLY.id)
            || this.evidence.employee.contract.id === Number(contractsName.CLC_HOURLY.id);
        }
        return 0;
      },
      month() {
        return moment(`${this.evidence.year}-${this.evidence.month}-01`).format('MMMM YYYY');
      },
      fromNow() {
        if (this.month === moment().format('MMMM YYYY')) {
          return '';
        }
        else {
          const fromNow = moment(`${this.evidence.year}-${this.evidence.month}-01`).fromNow();
          return `(${fromNow})`;
        }
      },
    },
    i18n: { messages: {
      pl: {
        'Month:': 'Miesiąc:',
        'Unavailable hours:': 'Godziny niedostępności:',
        'Working hours:': 'Godziny wykonywanej pracy:',
        'Paid free hours:': 'Płatne godziny wolne:',
        'Unpaid free hours:': 'Bezpłatne godziny wolne:',
        'Sick leave hours:': 'Godziny zwolnienia chorobowego:',
      },
    } },
  };
</script>
