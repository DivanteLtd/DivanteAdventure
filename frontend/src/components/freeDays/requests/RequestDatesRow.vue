<template>
  <tr class="hide-border"
      style="border-top: none !important; background: none; cursor: pointer;"
      @click="openRequestWindow">
    <td colspan="5" style="height: auto;" class="py-2">
      <span class="body-1">{{ getDayNameByType(request.days[0].type) }}</span>
      <v-tooltip top
                 :key="index"
                 v-for="(day, index) in request.days"
      >
        <template v-slot:activator="{ on }">
          <v-chip v-on="on"
                  :color="chipStatus(day, 'color')"
                  small
          >
            {{ day.date }}
          </v-chip>
        </template>
        {{ chipStatus(day, 'text') }}
      </v-tooltip>
    </td>
  </tr>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import moment from '@divante-adventure/work-moment';
  import { getDayNameByType, leaveDaysStatuses, leaveDaysTypes } from '../../../util/freeDays';

  export default {
    name: 'RequestDatesRow',
    props: {
      request: { type: Object, required: true },
      periodStartDate: { type: Object, required: false, default: null },
    },
    data() {
      return {
        getDayNameByType,
        moment,
      };
    },
    computed: {
      days() {
        return this.request.days.map(day => day.date).sort();
      },
    },
    methods: {
      chipStatus(day, value) {
        if (day.status === leaveDaysStatuses.pendingResignation && day.type === leaveDaysTypes.overtime) {
          return value === 'color' ? '#FF0000' : this.$t('active');
        } else if (day.status === leaveDaysStatuses.pendingResignation) {
          return value === 'color' ? '#AAAA00' : this.$t('pending resignation');
        } else if (day.status === leaveDaysStatuses.resigned) {
          return value === 'color' ? '#FF0000' : this.$t('resigned');
        } else {
          return value === 'color' ? '' : this.$t('active');
        }
      },
      openRequestWindow() {
        this.request.periodStartDate = this.periodStartDate;
        EventBus.$emit(eventNames.showRequestDetails, this.request);
      },
    },
    i18n: {
      messages: {
        pl: {
          'pending resignation': 'rezygnacja w toku',
          'resigned': 'zrezygnowano',
          'active': 'aktywny',
          'Paid free day': 'Płatny dzień wolny',
          'Unpaid free day': 'Bezpłatny dzień wolny',
          'Paid leave day': 'Urlop płatny',
          'Unpaid leave day': 'Urlop bezpłatny',
          'Leave day on request': 'Urlop na żądanie',
          'Leave day on occasion': 'Urlop okolicznościowy',
          'Care leave day': 'Dzień opieki nad dzieckiem',
          'Sick leave': 'Dzień chorobowy',
          'Unpaid sick leave': 'Bezpłatny dzień chorobowy',
          'Taking back overtime': 'Odbieranie nadgodzin',
          'Taking back additional hours': 'Odbieranie godzin dodatkowych',
          'Unavailability day': 'Dzień niedostępności',
        },
      },
    },
  };
</script>
