<template>
  <v-select :items="displayLeaveDaysTypes"
            v-model="valueHolder"
            :placeholder="$t('Select day type')"
            class="mt-0">
    <template slot="item" slot-scope="item">
      <v-avatar><v-icon>{{ item.item.icon }}</v-icon></v-avatar>
      {{ item.item.text }}
    </template>
  </v-select>
</template>

<script>
  import {
    leaveDaysTypes,
    getAvailablePaidDays,
    getAvailableDaysOnRequest,
    getAvailableCareLeavesHours,
  } from '../../../util/freeDays';

  export default {
    name: 'DayTypeSelectLeaveDays',
    props: {
      value: { type: Number, default: -1 },
      childCare: { type: Number, default: 0 },
      period: { type: Object, default: () => ({}) },
    },
    data() { return {
      valueHolder: this.value,
      leaveTypes: [
        {
          value: leaveDaysTypes.leavePaid,
          text: this.$t('Paid leave day'),
          icon: 'weekend',
          validate: period => getAvailablePaidDays(period) > 0,
        }, {
          value: leaveDaysTypes.leaveUnpaid,
          text: this.$t('Unpaid leave day'),
          icon: 'weekend',
          validate: () => true,
        }, {
          value: leaveDaysTypes.leaveRequest,
          text: this.$t('Leave day on request'),
          icon: 'warning',
          validate: period => getAvailableDaysOnRequest(period) > 0,
        }, {
          value: leaveDaysTypes.leaveOccasional,
          text: this.$t('Leave day on occasion'),
          icon: 'local_bar',
          validate: () => true,
        }, {
          value: leaveDaysTypes.sickLeavePaid,
          text: this.$t('Sick leave'),
          icon: 'airline_seat_individual_suite',
          validate: () => true,
        }, {
          value: leaveDaysTypes.overtime,
          text: this.$t('Taking back overtime'),
          icon: 'access_time',
          validate: () => true,
        }, {
          value: leaveDaysTypes.leaveCare,
          text: this.$t('Care leave day'),
          icon: 'child_friendly',
          validate: (period, childCare) => childCare === 1 && getAvailableCareLeavesHours(period) > 0,
        },
      ],
    };},
    computed: {
      displayLeaveDaysTypes() {
        return this.leaveTypes.filter(type => type.validate(this.period, this.childCare));
      },
    },
    watch: {
      valueHolder() {
        this.$emit('input', this.valueHolder);
      },
      value() {
        if (this.value !== this.valueHolder) {
          this.valueHolder = this.value;
        }
      },
    },
    i18n: { messages: {
      pl: {
        'Select day type': 'Wybierz typ dnia',
        'Paid leave day': 'Urlop płatny',
        'Unpaid leave day': 'Urlop bezpłatny',
        'Leave day on request': 'Urlop na żądanie',
        'Leave day on occasion': 'Urlop okolicznościowy',
        'Care leave day': 'Dzień opieki nad dzieckiem',
        'Sick leave': 'Dzień chorobowy',
        'Taking back overtime': 'Odbieranie nadgodzin',
      },
    } },
  };
</script>
