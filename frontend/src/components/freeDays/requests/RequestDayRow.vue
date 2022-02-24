<template>
  <tr class="request-day-row" :class="dayCanceled ? 'canceled' : ''">
    <td :class="resignClass">{{ date }}</td>
    <td :class="resignClass" class="pa-0">
      <v-col class="type-row">
        <v-icon class="pr-1">
          {{ icon }}
        </v-icon>
        {{ getDayNameByType(day.type) }}
      </v-col>
    </td>
    <td :class="resignClass" v-if="isLeaveCare()">{{ day.hours }}</td>
    <td :class="resignClass" v-if="resignMode && !day.deleted && day.status !== leaveDaysStatuses.resigned">
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" icon @click="updateDay"><v-icon>delete</v-icon></v-btn>
        </template>
        {{ $t('Delete') }}
      </v-tooltip>
    </td>
    <td v-if="resignMode && day.deleted && day.status !== leaveDaysStatuses.resigned">
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" icon @click="updateDay"><v-icon>replay</v-icon></v-btn>
        </template>
        {{ $t('Return') }}
      </v-tooltip>
    </td>
  </tr>
</template>

<script>
  import { getDayNameByType, leaveDaysStatuses, leaveDaysTypes } from '../../../util/freeDays';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'RequestDayRow',
    props: {
      day: { type: Object, required: true },
      resignMode: { type: Boolean, required: true },
    },
    data() {
      return {
        getDayNameByType,
        leaveDaysStatuses,
      };
    },
    computed: {
      resignClass() {
        return this.day.deleted || this.day.status === leaveDaysStatuses.pendingResignation
          || this.day.status === leaveDaysStatuses.resigned ? 'canceled' : '';
      },
      date() {
        return moment(this.day.date).format(this.$t('date_format'));
      },
      dayCanceled() {
        return this.day.status === leaveDaysStatuses.canceled;
      },
      icon() {
        switch(this.day.type) {
          case leaveDaysTypes.freePaid: return 'weekend';
          case leaveDaysTypes.freeUnpaid: return 'weekend';
          case leaveDaysTypes.leavePaid: return 'weekend';
          case leaveDaysTypes.leaveUnpaid: return 'weekend';
          case leaveDaysTypes.leaveRequest: return 'warning';
          case leaveDaysTypes.leaveOccasional: return 'local_bar';
          case leaveDaysTypes.leaveCare: return 'child_friendly';
          case leaveDaysTypes.sickLeavePaid: return 'airline_seat_individual_suite';
          case leaveDaysTypes.sickLeaveUnpaid: return 'airline_seat_individual_suite';
          case leaveDaysTypes.overtime: return 'access_time';
          case leaveDaysTypes.additionalHours: return 'access_time';
          case leaveDaysTypes.unavailability: return 'weekend';
          default: return '';
        }
      },
    },
    methods: {
      updateDay() {
        this.$emit('update', this.day);
      },
      isLeaveCare() {
        return this.day.type === leaveDaysTypes.leaveCare;
      },
    },
    i18n: {
      messages: {
        pl: {
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
          'Delete': 'Usuń',
          'Return': 'Cofnij',
        },
        en: {
          date_format: 'DD.MM.YYYY',
        },
      },
    },
  };
</script>

<style scoped>
  tr.canceled {
    text-decoration: black line-through;
  }
  td.canceled {
    text-decoration: black line-through;
  }
  td {
    text-align: center;
  }
  .type-row{
    display: flex;
    align-items: center;
    justify-content: center;
    height: auto;
  }
</style>

<style>
  .request-day-row td{
    padding: 0 8px !important;
  }
</style>
