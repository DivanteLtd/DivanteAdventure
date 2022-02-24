<template>
  <tr>
    <template>
      <td @click="feedbackDetails = true" class="pointer">
        <employee-chip :employee="item"/>
      </td>
      <td>
        {{ item.plannedFeedbackDate ? item.plannedFeedbackDate : $t('No feedback planned') }}
      </td>
      <td>
        {{ item.lastFeedbackDate ? item.lastFeedbackDate : $t('No feedback registered') }}
      </td>
      <td :class="getColor">
        {{ item.lastFeedbackDate ? getMonthNumber : '' }}
      </td>
    </template>
  </tr>
</template>
<script>
  import EmployeeChip from '../../utils/EmployeeChip';
  import moment from '@divante-adventure/work-moment';

  const AVERAGE_DAYS_IN_MONTH = 30.5;

  export default {
    name: 'MyStructureRow',
    components: { EmployeeChip },
    props: {
      item: { type: Object, required: true },
    },
    computed: {
      getDays() {
        return this.item.lastFeedbackDate ? moment().diff(moment(this.item.lastFeedbackDate), 'days') : -1;
      },
      monthsRounded() {
        return Math.floor(this.getDays / AVERAGE_DAYS_IN_MONTH);
      },
      getMonthNumber() {
        const days = this.getDays;
        const monthsRounded = Math.floor(days / AVERAGE_DAYS_IN_MONTH);
        const daysInfo = `${Math.floor(days % AVERAGE_DAYS_IN_MONTH)} ${(days % AVERAGE_DAYS_IN_MONTH) !== 1
          ? this.$t('days') : this.$t('day')}`;
        let monthConjugation = '';
        if (monthsRounded < 1) {
          return daysInfo;
        } else if (monthsRounded === 1) {
          monthConjugation = 'month';
        } else {
          monthConjugation = monthsRounded > 4 ? 'month-conjugation-2' : 'month-conjugation';
        }
        return `${monthsRounded} ${this.$t(monthConjugation)} ${daysInfo}`;
      },
      getColor() {
        if (this.monthsRounded >= 6) {
          return 'color-warning';
        } else if (this.getDays < 0) {
          return 'color-info';
        } else {
          return '';
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'No feedback registered': 'Brak zarejestrowanych feedbacków',
          'No feedback planned': 'Brak zaplanowanych feedbacków',
          'days': 'dni',
          'day': 'dzień',
          'month': 'miesiąc',
          'month-conjugation': 'miesiące',
          'month-conjugation-2': 'miesięcy',
        },
        en: {
          'month-conjugation': 'months',
          'month-conjugation-2': 'months',
        },
      },
    },
  };
</script>
<style scoped>
  .color-warning {
    background-color: #FF6600;
    border-bottom: #565656;
  }
  .color-info {
    background-color: #ffcc00;
    border-bottom: #565656;
  }
</style>
