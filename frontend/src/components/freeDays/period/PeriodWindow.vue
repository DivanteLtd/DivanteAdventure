<template>
  <v-dialog v-model="dialogVisible" v-if="dialogVisible" width="1000" transition="slide-y-transition">
    <v-card id="dialog-period-form">
      <v-card-title class="headline" :class="{'pa-4': $vuetify.breakpoint.xs}">
        <span v-if="!isEdit" :class="{'period-title': $vuetify.breakpoint.xs}">{{ $t('Creating new period') }}</span>
        <span v-else>{{ $t('Period edition') }}</span>
        <v-spacer/>
        <v-btn icon text @click="close">
          <v-icon>clear</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text :class="{'pa-0 ma-0': $vuetify.breakpoint.xs}">
        <v-stepper vertical v-model="stepper">
          <v-stepper-step :complete="stepper > 1" step="1" :class="{'pa-4': $vuetify.breakpoint.xs}">
            {{ $t('Basic information') }}
          </v-stepper-step>
          <v-stepper-content step="1" :class="{'pa-1 ma-0': $vuetify.breakpoint.xs}">
            <period-first-step v-model="newPeriod"
                               :min-starting-date="minStartingDate"
                               :max-ending-date="maxEndingDate"
                               :is-edit="isEdit"
                               @next-page="stepper++"
                               @previous-page="dialogVisible = false"/>
          </v-stepper-content>
          <v-stepper-step step="2" :class="{'pa-4': $vuetify.breakpoint.xs}">{{ $t('Available days') }}</v-stepper-step>
          <v-stepper-content step="2" :class="{'pa-2 ma-0': $vuetify.breakpoint.xs}">
            <period-second-step v-model="newPeriod"
                                @next-page="createNewPeriod"
                                @previous-page="stepper--"/>
          </v-stepper-content>
        </v-stepper>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import { mapState } from 'vuex';
  import moment from '@divante-adventure/work-moment';
  import PeriodFirstStep from './PeriodFirstStep';
  import PeriodSecondStep from './PeriodSecondStep';

  export default {
    name: 'PeriodWindow',
    components: { PeriodSecondStep, PeriodFirstStep },
    data() { return {
      dialogVisible: false,
      formValid: false,
      minStartingDate: undefined,
      maxEndingDate: undefined,
      newPeriod: {},
      stepper: 1,
      periodId: 0,
      isEdit: false,
    };},
    computed: {
      ...mapState({
        employees: state => state.Employees.employees,
        myPeriods: state => state.FreeDays.myPeriods,
      }),
    },
    methods: {
      show(data) {
        if (this.dialogVisible) {
          return;
        }
        this.minStartingDate = undefined;
        this.maxEndingDate = undefined;
        this.newPeriod = {};
        this.isEdit = false;
        if (!data.isEdit) {
          this.$store.dispatch('Employees/loadEmployees');
          this.newPeriod = {
            employee: null,
            sickLeaveDays: null,
            freeDays: null,
          };
          if (this.myPeriods.length === 0) {
            this.newPeriod.startDate = moment().format('YYYY-MM');
            this.newPeriod.endDate = moment().format('YYYY-MM');
            this.minStartingDate = moment().subtract(1, 'M').format('YYYY-MM');
          } else {
            this.newPeriod.startDate = moment(data.lastPeriod.dateTo, 'YYYY-MM-DD').add(1, 'd').format('YYYY-MM');
            this.newPeriod.endDate = moment(data.lastPeriod.dateTo, 'YYYY-MM-DD').add(1, 'M').format('YYYY-MM');
            this.minStartingDate = moment(data.lastPeriod.dateTo, 'YYYY-MM').add(1, 'd').format('YYYY-MM');
          }
          this.formValid = false;
          if (data.hasOwnProperty('employee')) {
            this.newPeriod.employee = data.employee;
          }
        } else {
          if (this.myPeriods.length > 1) {
            const periodDateFrom = this.myPeriods
              .filter(val => val.dateToMoment < data.dateFromMoment)
              .map(val => val.dateToMoment)
              .reduce((a, b) => (a > b ? a : b), moment('2008-01-01'));
            this.minStartingDate = moment(periodDateFrom).add({ day: 1 }).format('YYYY-MM-DD');
            const periodDateTo = this.myPeriods
              .filter(val => val.dateFromMoment > data.dateToMoment)
              .map(val => val.dateFromMoment)
              .reduce((a, b) => (a < b ? a : b), moment('2050-01-01'));
            this.maxEndingDate = moment(periodDateTo).subtract({ day: 1 }).format('YYYY-MM-DD');
          }
          this.isEdit = true;
          this.newPeriod = {
            employee: data.employee,
            startDate: data.dateFrom,
            endDate: data.dateTo,
            sickLeaveDays: data.sickLeaveDays,
            freeDays: data.freeDays,
          };
          this.periodId = data.id;
        }
        if (data.hasOwnProperty('lastPeriod')) {
          this.minStartingDate = moment(data.lastPeriod.dateToMoment).add({ day: 1 }).format('YYYY-MM-DD');
        }
        this.stepper = 1;
        this.dialogVisible = true;
      },
      createNewPeriod() {
        const requestData = {
          dateFrom: this.newPeriod.startDate,
          dateTo: this.newPeriod.endDate,
          sickLeaveDays: this.newPeriod.sickLeaveDays,
          freeDays: this.newPeriod.freeDays,
          employeeId: this.newPeriod.employee.id,
        };
        requestData.periodId = this.periodId;
        const createOrEditPath = !this.isEdit ? 'FreeDays/createNewPeriod' : 'FreeDays/editPeriod';
        this.$store.dispatch(createOrEditPath, requestData)
          .then(() => {
            this.dialogVisible = false;
            EventBus.$emit(eventNames.reloadPeriods);
          });
      },
      close() {
        this.dialogVisible = false;
        Object.assign(this.$data, this.$options.data());
      },
    },
    mounted() {
      EventBus.$on(eventNames.createEditNewPeriod, this.show);
    },
    i18n: { messages: {
      pl: {
        'Creating new period': 'Tworzenie nowego okresu',
        'Cancel': 'Anuluj',
        'Create': 'Utwórz',
        'Basic information': 'Podstawowe informacje',
        'Available days': 'Dostępne dni',
        'Period edition': 'Edycja okresu',
      },
    } },
  };
</script>
<style scoped>
  .period-title{
    font-size: medium;
    letter-spacing: initial;
  }
</style>
