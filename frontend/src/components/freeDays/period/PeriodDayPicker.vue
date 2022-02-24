<template>
  <v-date-picker color="indigo" v-model="date"
                 :locale="locale"
                 :min="min"
                 :max="max"
                 :picker-date.sync="pickerDate"
                 :first-day-of-week="firstDayOfWeek"
                 :full-width="!$vuetify.breakpoint.xs"
                 no-title
  />
</template>

<script>
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'PeriodDayPicker',
    props: {
      value: { type: String, required: false, default: null },
      min: { type: String, required: false, default: null },
      max: { type: String, required: false, default: null },
    },
    data() { return {
      date: this.value,
      locale: getSuggestedLanguage(),
      pickerDate: moment(this.value || this.min, 'YYYY-MM-DD').format('YYYY-MM'),
    };},
    computed: {
      firstDayOfWeek() {
        const stringText = this.$t('date.firstDayOfWeek');
        return parseInt(stringText);
      },
    },
    watch: {
      min() {
        let value = this.value;
        if (typeof value === 'string' && value.length === 7) {
          value = `${value}-01`;
        }
        const valueMoment = moment(value, 'YYYY-MM-DD');
        const minMoment = moment(this.min, 'YYYY-MM-DD');
        const endMoment = minMoment.isAfter(valueMoment) ? minMoment : valueMoment;
        this.pickerDate = endMoment.format('YYYY-MM');
      },
      value() {
        let value = this.value;
        if (typeof value === 'string' && value.length === 7) {
          value = `${value}-01`;
        }
        const valueMoment = moment(value, 'YYYY-MM-DD');
        const minMoment = moment(this.min, 'YYYY-MM-DD');
        const update = this.value !== this.date;
        if (minMoment.isAfter(valueMoment)) {
          this.pickerDate = minMoment.format('YYYY-MM');
        } else if (update) {
          this.pickerDate = moment(this.value, 'YYYY-MM-DD').format('YYYY-MM');
        }
        if (update) {
          this.date = this.value;
        }
      },
      date() {
        this.$emit('input', this.date);
      },
    },
  };
</script>
