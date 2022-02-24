<template>
  <dialog-form :show-event="showEvent" :title="$t('Occupancy')" max-width="800" @save="save" @show="show">
    <v-col cols="12" md="4">
      <v-text-field :value="pair.employeeName + ' ' + pair.employeeLastName"
                    :label="$t('Employee')"
                    disabled/>
    </v-col>
    <v-col cols="12" md="4">
      <v-text-field :value="pair.projectName"
                    :label="$t('Project')"
                    disabled/>
    </v-col>
    <v-col cols="12" md="4">
      <v-text-field v-model="occupancy"
                    :label="$t('Hours')"
                    :rules="[rules.correctTimeFormat, rules.valueInLimit, rules.integersOnly]"
                    required/>
    </v-col>
    <v-col cols="12" md="12" class="headline">
      {{ $t('Occupancy range') }}
    </v-col>
    <v-col cols="12" md="6" class="subheading">
      {{ $t('From date:') }}
      <v-text-field v-model="dateFrom"
                    :rules="[rules.validate]"
                    persistent-hint
                    hint="YYYY-MM-DD"
                    required/>
    </v-col>
    <v-col cols="12" md="6" class="subheading">
      {{ $t('To date:') }}
      <v-text-field v-model="dateTo"
                    :rules="[rules.validate]"
                    persistent-hint
                    hint="YYYY-MM-DD"
                    required/>
    </v-col>
    <v-col cols="12" md="6" class="mt-3">
      <v-date-picker v-model="allDatesFrom"
                     color="red lighten-1"
                     :locale="locale"
                     :picker-date.sync="pickerDate"
                     :min="minimalDate"
                     :first-day-of-week="$t('first-day-of-week')"
                     multiple no-title full-width/>
    </v-col>
    <v-col cols="12" md="6" class="mt-3">
      <v-date-picker v-model="allDatesTo"
                     color="blue lighten-1"
                     :locale="locale"
                     :picker-date.sync="pickerDate"
                     :min="minimalDate"
                     :first-day-of-week="$t('first-day-of-week')"
                     multiple no-title full-width/>
    </v-col>
  </dialog-form>
</template>

<script>
  import DialogForm from './DialogForm';
  import { allModesMixin } from '../../../mixins/PlannerMixins';
  import moment from '@divante-adventure/work-moment';
  import { eventNames } from '../../../eventbus';
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import { mapState } from 'vuex';

  const SECONDS_IN_HOUR = 3600;

  export default {
    name: 'RangeOccupancy',
    components: { DialogForm },
    mixins: [ allModesMixin ],
    data() {
      return {
        maxHoursPerDay: 16,
        showEvent: eventNames.createOccupancyRange,
        dateFromHolder: moment(),
        dateToHolder: moment(),
        pair: {},
        locale: getSuggestedLanguage(),
        occupancy: '',
        pickerDate: null,
        rules: {
          integersOnly: value => {
            // eslint-disable-next-line eqeqeq
            return (value == parseInt(value)) || this.$t('Integers only');
          },
          correctTimeFormat: value => {
            return (!isNaN(value) && parseInt(value) >= 0) || this.$t('Invalid value');
          },
          valueInLimit: value => {
            if (this.pair.overtime === true) {
              return (!isNaN(value) && parseInt(value) <= this.maxHoursPerDay) || this.$t('Value is too big');
            } else {
              return (!isNaN(value) && parseInt(value) <= 8) || this.$t('Value is too big');
            }
          },
          validate: () => {
            return this.pair.dateFrom.some((val, idx) => (
              (this.checkIfMomentBefore(this.dateTo, this.pair.dateTo[idx])
              || this.checkIfMomentSame(this.dateTo, this.pair.dateTo[idx]))
              && (this.checkIfMomentAfter(this.dateFrom, this.pair.dateFrom[idx])
              || this.checkIfMomentSame(this.dateFrom, this.pair.dateFrom[idx]))))
              ? true : this.$t('Provide the date range within the duration of the assigned employee-project period');
          },
        },
      };
    },
    computed: {
      ...mapState({
        currentDate: state => state.Planner.Time.currentDate,
        allEmployees: state => state.Planner.employees,
      }),
      minimalDate() {
        return moment().subtract({ months: 1 }).format('YYYY-MM-DD');
      },
      allDatesFrom: {
        get() {
          return this.getEvents();
        },
        set(dates) {
          this.setEvents(dates, true);
        },
      },
      allDatesTo: {
        get() {
          return this.getEvents();
        },
        set(dates) {
          this.setEvents(dates, false);
        },
      },
      dateFrom: {
        get() {
          return this.dateFromHolder.format('YYYY-MM-DD');
        },
        set(date) {
          if (date.length !== 10) {
            return;
          }
          const day = moment(date, 'YYYY-MM-DD');
          if (day.isValid()) {
            this.dateFromHolder = day;
            this.checkDateOrder(true);
          }
        },
      },
      dateTo: {
        get() {
          return this.dateToHolder.format('YYYY-MM-DD');
        },
        set(date) {
          if (date.length !== 10) {
            return;
          }
          const day = moment(date, 'YYYY-MM-DD');
          if (day.isValid()) {
            this.dateToHolder = day;
            this.checkDateOrder(false);
          }
        },
      },
    },
    watch: {
      dateFrom() {
        return this.rules.validate;
      },
      dateTo() {
        return this.rules.validate;
      },
    },
    methods: {
      checkIfMomentBefore(date, pairDate) {
        return this.momentUtc(moment(date).format('MM-YYYY')).isBefore(this.momentUtc(pairDate));
      },
      checkIfMomentAfter(date, pairDate) {
        return this.momentUtc(moment(date).format('MM-YYYY')).isAfter(this.momentUtc(pairDate));
      },
      checkIfMomentSame(date, pairDate) {
        return this.momentUtc(moment(date).format('MM-YYYY')).isSame(this.momentUtc(pairDate));
      },
      momentUtc(date) {
        return moment.utc(date, 'MM-YYYY');
      },
      checkDateOrder(isFrom) {
        if (this.dateToHolder.isBefore(this.dateFromHolder)) {
          if (isFrom) {
            this.dateToHolder = this.dateFromHolder;
          }
          else {
            this.dateFromHolder = this.dateToHolder;
          }
        }
      },
      getEvents() {
        return Array.from(moment.range(this.dateFromHolder, this.dateToHolder).by('day'))
          .map(m => m.startOf('day').format('YYYY-MM-DD'));
      },
      setEvents(dates, isFrom) {
        if (dates.length === 0) {
          return;
        }
        const currentEvents = this.getEvents();

        let clickedDate = '';
        for (let i = 0; i < dates.length && clickedDate === ''; i++) {
          if (currentEvents.indexOf(dates[i]) === -1) {
            clickedDate = dates[i];
          }
        }
        for (let i = 0; i < currentEvents.length && clickedDate === ''; i++) {
          if (dates.indexOf(currentEvents[i]) === -1) {
            clickedDate = currentEvents[i];
          }
        }
        if (clickedDate !== '') {
          const clickedMoment = moment(clickedDate, 'YYYY-MM-DD').startOf('day');
          if (isFrom) {
            this.dateFromHolder = moment(clickedMoment);
          }
          else {
            this.dateToHolder = moment(clickedMoment);
          }
          this.checkDateOrder(isFrom);
        }
      },
      save() {
        if (!isNaN(this.occupancy) && parseInt(this.occupancy) >= 0) {
          this.occupancy = `${this.occupancy}h`;
        }
        let employeeWorkTime = this.allEmployees.filter(val => val.id === this.pair.employeeId)
          .map(val => val.worktime)[0];
        if (this.pair.overtime === true && this.timeMode.value === 'day') {
          employeeWorkTime = this.maxHoursPerDay * SECONDS_IN_HOUR;
        }
        const eventData = {
          entries: this.$store.state.Planner.entries,
          workTime: employeeWorkTime,
          start: moment.min(this.dateFromHolder, this.dateToHolder),
          end: moment.max(this.dateFromHolder, this.dateToHolder),
          employeeId: this.pair.employeeId,
          projectId: this.pair.projectId,
          occupancy: parseFloat(this.occupancy) * SECONDS_IN_HOUR,
        };
        this.$store.dispatch('Planner/prepareRangeEntries', eventData);
      },
      show(data) {
        this.pair = data.pairings;
        this.occupancy = '';
        this.pickerDate = moment(this.currentDate).format('YYYY-MM');
      },
    },
    i18n: {
      messages: {
        pl: {
          'Employee': 'Osoba',
          'From date:': 'Od dnia:',
          'Hours': 'Godziny',
          'Integers only': 'Tylko liczby całkowite',
          'Invalid value': 'Niepoprawna wartość',
          'Occupancy': 'Zajętość',
          'Occupancy range': 'Zakres zajętości',
          'Project': 'Projekt',
          'Provide the date range within the duration of the assigned employee-project period':
            'Podaj zakres dat w ramach trwania przypisanego okresu osoba-projekt',
          'To date:': 'Do dnia:',
          'Value is too big': 'Wartość jest zbyt duża',
          'first-day-of-week': '1',
        },
        en: {
          'first-day-of-week': '0',
        },
      },
    },
  };
</script>
