<template>
  <v-date-picker color="indigo" v-model="pickerModel"
                 :locale="locale"
                 :first-day-of-week="firstDayOfWeek"
                 :allowed-dates="isDateAllowed"
                 :events="allAcceptedRequestDays"
                 :min="period.dateFrom"
                 :max="period.dateTo"
                 class="pb-2"
                 :full-width="!$vuetify.breakpoint.xs"
                 :picker-date.sync="pickerDate"
                 multiple no-title/>
</template>

<script>
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import moment from '@divante-adventure/work-moment';
  import { mapState } from 'vuex';
  import { leaveRequestsStatuses, leaveDaysStatuses } from '../../../util/freeDays';
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'RequestDayPicker',
    props: {
      value: { type: Array, default: () => ([]) },
      notAllowedDates: { type: Array, default: () => ([]) },
      period: { type: Object, default: () => ({}) },
    },
    data() { return {
      pickerModel: this.value,
      locale: getSuggestedLanguage(),
      pickerDate: null,
    };},
    computed: {
      ...mapState({
        freeDays: state => state.FreeDays.freeDays,
        freeDaysMonth: state => state.FreeDays.freeDaysMonth,
        allPeriods: state => state.FreeDays.myPeriods,
      }),
      freeDaysFormatted() {
        return this.freeDays.map(day => moment.unix(day).format('YYYY-MM-DD'));
      },
      firstDayOfWeek() {
        const stringText = this.$t('date.firstDayOfWeek');
        return parseInt(stringText);
      },
      allAcceptedRequestDays() {
        const reqStatuses = [
          leaveRequestsStatuses.pending,
          leaveRequestsStatuses.accepted,
          leaveRequestsStatuses.pendingResignation,
          leaveRequestsStatuses.planned,
        ];
        return this.allPeriods
          .flatMap(period => period.requests.filter(request => reqStatuses.includes(request.status)))
          .flatMap(request => request.days
            .filter(day => day.status === leaveDaysStatuses.active)
            .map(day => day.date));
      },
    },
    watch: {
      pickerModel() {
        this.$emit('input', this.pickerModel);
      },
      pickerDate() {
        if (!this.freeDaysMonth.includes(this.pickerDate)) {
          this.$store.dispatch('FreeDays/loadFreeDaysForMonth', this.pickerDate);
        }
      },
    },
    methods: {
      isDateAllowed(date) {
        const weekDay = moment(date, 'YYYY-MM-DD').day();
        if (weekDay === 0 || weekDay === 6) {
          return false;
        }
        else if (this.allAcceptedRequestDays.includes(date)) {
          return false;
        }
        else if (this.notAllowedDates.includes(date)) {
          return false;
        }
        else if (typeof(this.pickerDate) === 'undefined') {
          return true;
        }
        else if (this.freeDaysMonth.includes(this.pickerDate)) {
          return !this.freeDaysFormatted.includes(date);
        }
        else {
          return true;
        }
      },
      clear(period) {
        this.pickerDate = new Date().toISOString().substr(0, 10);
        if (moment(period.dateFrom) > moment(this.pickerDate)) {
          this.pickerDate = period.dateFrom;
        }
        this.pickerModel = [];
        this.$store.dispatch('FreeDays/loadFreeDaysForMonth', moment().format('YYYY-MM'));
      },
    },
    mounted() {
      EventBus.$on(eventNames.createNewLeaveRequest, this.clear);
    },
  };
</script>
