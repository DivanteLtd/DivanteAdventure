<template>
  <div>
    <v-alert v-if="usedUnavailabilityDaysCount" :value="true" type="info" icon="info">
      <h5>
        {{ $t('We remind you that under the contract selected for this person, only days of unavailability ' +
          'are available') }}
      </h5>
    </v-alert>
    <period-leave-days v-else-if="canEmployeeUseLeaveDays" v-model="valueWrapper" :valid.sync="formValid"/>
    <period-free-days v-else-if="canEmployeeUseFreeDays" v-model="valueWrapper" :valid.sync="formValid"/>
    <v-btn text @click="previousPage">{{ $t('Back') }}</v-btn>
    <v-btn text color="primary" @click="nextPage" :disabled="!formValid">{{ $t('Save') }}</v-btn>
  </div>
</template>

<script>
  import { canUseLeaveDays, canUseFreeDays, canUseUnavailabilityDays } from '../../../util/freeDays';
  import PeriodLeaveDays from './PeriodLeaveDays';
  import PeriodFreeDays from './PeriodFreeDays';

  export default {
    name: 'PeriodSecondStep',
    components: { PeriodFreeDays, PeriodLeaveDays },
    props: {
      value: { type: Object, required: false, default: () => ({}) },
    },
    data() { return {
      formValid: true,
    };},
    computed: {
      usedUnavailabilityDaysCount() {
        return typeof this.value.employee !== 'undefined' && this.value.employee
          && canUseUnavailabilityDays(this.value.employee);
      },
      canEmployeeUseLeaveDays() {
        return typeof this.value.employee !== 'undefined' && this.value.employee
          && canUseLeaveDays(this.value.employee);
      },
      canEmployeeUseFreeDays() {
        return typeof this.value.employee !== 'undefined' && this.value.employee && canUseFreeDays(this.value.employee);
      },
      valueWrapper: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
    methods: {
      nextPage() {
        this.$emit('next-page');
      },
      previousPage() {
        this.$emit('previous-page');
      },
    },
    i18n: { messages: {
      pl: {
        'We remind you that under the contract selected for this person, only days of unavailability are available':
          'Przypominamy, że w ramach wybranego dla tej osoby kontraktu, przysługują wyłącznie dni niedostępności',
        'Back': 'Cofnij',
        'Save': 'Zapisz',
      },
    } },
  };
</script>
