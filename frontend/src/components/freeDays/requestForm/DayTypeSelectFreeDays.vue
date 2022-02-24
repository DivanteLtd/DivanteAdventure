<template>
  <v-select :items="displayFreeDaysTypes"
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
  import { leaveDaysTypes, getAvailablePaidDays } from '../../../util/freeDays';

  export default {
    name: 'DayTypeSelectFreeDays',
    props: {
      value: { type: Number, default: -1 },
      period: { type: Object, default: () => ({}) },
    },
    data() { return {
      valueHolder: this.value,
      freeDaysTypes: [
        {
          value: leaveDaysTypes.freePaid,
          text: this.$t('Paid free day'),
          icon: 'weekend',
          validate: period => getAvailablePaidDays(period) > 0,
        }, {
          value: leaveDaysTypes.freeUnpaid,
          text: this.$t('Unpaid free day'),
          icon: 'weekend',
          validate: () => true,
        }, {
          value: leaveDaysTypes.sickLeavePaid,
          text: this.$t('Sick leave'),
          icon: 'airline_seat_individual_suite',
          validate: () => true,
        },
      ],
    };},
    computed: {
      displayFreeDaysTypes() {
        return this.freeDaysTypes.filter(type => type.validate(this.period));
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
        'Paid free day': 'Płatny dzień wolny',
        'Unpaid free day': 'Bezpłatny dzień wolny',
        'Sick leave': 'Dzień chorobowy',
        'Taking back additional hours': 'Odbieranie godzin dodatkowych',
      },
    } },
  };
</script>
