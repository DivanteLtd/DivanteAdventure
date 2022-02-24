<template>
  <v-container :class="{'pa-1': $vuetify.breakpoint.xs}">
    <v-row no-gutters wrap>
      <v-col cols="12" :class="{'period-form-employee pa-2': $vuetify.breakpoint.xs}">
        <employee-chooser v-model="employee"
                          :is-edit="isEdit"
                          :employees="employees"
                          :label="$t('Period owner')"
                          prepend-icon="person"/>
      </v-col>
      <v-col class="pa-1" cols="12" md="6">
        <h2 class="day-picker-title mb-2">{{ $t('From:') }}</h2>
        <period-day-picker v-model="startDate" :min="minStartingDate" :max="maxEndingDate"/>
      </v-col>
      <v-col class="pa-1" cols="12" md="6">
        <h2 class="day-picker-title mb-2">{{ $t('To:') }}</h2>
        <period-day-picker v-model="endDate" :min="minEndingDate" :max="maxEndingDate"/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import { mapState } from 'vuex';
  import EmployeeChooser from '../../utils/EmployeeChooser';
  import PeriodDayPicker from './PeriodDayPicker';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'PeriodFormEmployeeTime',
    components: { PeriodDayPicker, EmployeeChooser },
    props: {
      value: { type: Object, default: () => ({}) },
      valid: { type: Boolean, default: false },
      minStartingDate: { type: String, required: false, default: null },
      maxEndingDate: { type: String, required: false, default: null },
      isEdit: { type: Boolean, required: false },
    },
    data() { return {
      newPeriod: this.value,
      minEndingDate: this.value.startDate || moment().add({ day: 1 }).format('YYYY-MM-DD'),
    };},
    computed: {
      ...mapState({
        employees: state => state.Employees.employees,
      }),
      employee: {
        get() {
          return this.value.employee;
        },
        set(val) {
          this.value.employee = val;
          this.updateValidation();
        },
      },
      startDate: {
        get() {
          return this.value.startDate || this.minStartingDate || moment().format('YYYY-MM-DD');
        },
        set(val) {
          this.value.startDate = val;
          this.minEndingDate = moment(val, 'YYYY-MM-DD').add({ day: 1 }).format('YYYY-MM-DD');
          this.updateValidation();
        },
      },
      endDate: {
        get() {
          return this.value.endDate || this.minEndingDate;
        },
        set(val) {
          this.value.endDate = val;
          this.updateValidation();
        },
      },
    },
    watch: {
      newPeriod() {
        this.$emit('input', this.newPeriod);
      },
    },
    methods: {
      updateValidation() {
        const validation = this.getCurrentValidation();
        this.$emit('update:valid', validation);
        this.$emit('input', this.value);
      },
      getCurrentValidation() {
        if (!this.value.hasOwnProperty('employee') || this.value.employee === null) {
          return false;
        }
        if (!this.value.hasOwnProperty('startDate') || !this.value.hasOwnProperty('endDate')) {
          return false;
        }
        const startDate = moment(this.value.startDate, 'YYYY-MM-DD');
        const endDate = moment(this.value.endDate, 'YYYY-MM-DD');
        const minDate = moment(this.minStartingDate, 'YYYY-MM-DD');
        if (startDate.isBefore(minDate) || endDate.isSameOrBefore(startDate)) {
          return false;
        }
        return true;
      },
    },
    i18n: { messages: {
      pl: {
        'Period owner': 'Właściciel okresu',
        'From:': 'Od:',
        'To:': 'Do:',
        'Available free days': 'Dostępne dni wolne',
        'Available sick leave days': 'Dostępne dni chorobowe',
      },
    } },
  };
</script>
<style scoped>
  .day-picker-title {
    width: 100%;
    text-align: center;
    font-size: 20px;
    font-weight: normal;
  }
</style>
<style>
  .period-form-employee .v-input__prepend-outer{
    display: none;
  }
</style>
