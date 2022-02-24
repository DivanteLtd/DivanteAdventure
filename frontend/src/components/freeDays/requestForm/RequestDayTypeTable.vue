<template>
  <v-data-table mobile-breakpoint="0" :headers="correctHeaders()"
                :items-per-page="1000"
                :items="orderedDays"
                class="scroll-y"
                :hide-default-header="orderedDays.length === 0"
                hide-default-footer>
    <template slot="no-data">
      <v-alert :value="true" type="info" icon="info">
        <h5>
          <span>{{ $t('Select one or more days from the calendar on the left') }}</span>
        </h5>
      </v-alert>
    </template>
    <template v-slot:item="{ item }">
      <tr>
        <td>
          {{ item }}
        </td>
        <td width="210px" v-if="isLeaveCareType()">
          <v-text-field class="textClass" :min="1" :max="8" type="number" v-model="hours[index]"
                        :rules="[rules.maxValue, rules.minValue, validateValue]"/>
        </td>
      </tr>
    </template>
  </v-data-table>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'RequestDayTypeTable',
    props: {
      value: { type: Array, default: () => ([]) },
      employee: { type: Object, default: () => ({}) },
      isValid: { type: Boolean, required: true },
    },
    data() {
      return {
        headers: [{
          text: this.$t('Date'),
          align: 'center',
          sortable: false,
        }],
        hours: [],
        formValid: false,
        validateValue: true,
        index: 0,
        rules: {
          maxValue: val => val <= 8 || this.$t('Max hours value is 8'),
          minValue: val => val >= 1 || this.$t('Min hours value is 1'),
        },
      };
    },
    computed: {
      orderedDays() {
        return this.value.sort((a, b) => moment(a, 'YYYY-MM-DD') - moment(b, 'YYYY-MM-DD'));
      },
    },
    watch: {
      value(newVal, oldVal) {
        if (newVal.length === 0) {
          oldVal = [];
          this.hours = [];
        } else {
          this.index++;
        }
        if (newVal.length === 1 && oldVal.length === 0) {
          this.hours.splice(0, 0, 8);
        }
        if (oldVal.length > newVal.length) {
          let flag = true;
          oldVal.forEach((val, idx) => {
            if (val !== newVal[idx] && flag) {
              this.hours.splice(idx, 1);
              flag = false;
            }
          });
        }
        if (oldVal.length < newVal.length) {
          let flag = true;
          const newValMoment = moment(newVal[newVal.length - 1], 'YYYY-MM-DD');
          const oldValMoment = moment(oldVal[oldVal.length - 1], 'YYYY-MM-DD');
          if (newValMoment > oldValMoment && flag) {
            let availableHours = 8;
            if (this.hours.reduce((a, b) => Number(a) + Number(b)) >= 16) {
              availableHours = 0;
            }
            this.hours.splice(newVal.length - 1, 0, availableHours);
            flag = false;
          }
          oldVal.forEach((val, idx) => {
            if (moment(val, 'YYYY-MM-DD') > newValMoment && flag) {
              let availableHours = 8;
              if (this.hours.reduce((a, b) => Number(a) + Number(b)) >= 16) {
                availableHours = 0;
              }
              this.hours.splice(idx, 0, availableHours);
              flag = false;
            }
          });
        }
        if (this.hours.length > 0) {
          this.validateValue = this.hours.reduce((a, b) => Number(a) + Number(b)) <= 16;
        }
        let isMinMax = true;
        this.hours.forEach(val => {
          if (Number(val) > 8 || Number(val) < 1) {
            isMinMax = false;
          }
        });
        const valid = isMinMax && this.validateValue;
        this.$emit('update:is-valid', valid);
      },
      hours() {
        if (this.hours.length > 0) {
          this.validateValue = this.hours.reduce((a, b) => Number(a) + Number(b)) <= 16;
          let isMinMax = true;
          this.hours.forEach(val => {
            if (Number(val) > 8 || Number(val) < 1) {
              isMinMax = false;
            }
          });
          const valid = isMinMax && this.validateValue;
          this.$emit('update:is-valid', valid);
          EventBus.$emit(eventNames.updateChildCareHours, this.hours);
          if (!this.validateValue) {
            this.validateValue = this.$t('Can not take more than 16 hours per year');
          }
        }
      },
    },
    methods: {
      correctHeaders() {
        if (this.isLeaveCareType()) {
          return this.headers.concat({
            text: this.$t('Hours'),
            align: 'center',
            sortable: false,
          });
        } else {
          return this.headers;
        }
      },
      isLeaveCareType() {
        return this.employee.dayType;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Select one or more days from the calendar on the left': 'Wybierz jeden lub więcej dni z kalendarza po lewej',
          'Date': 'Data',
          'Hours': 'Godziny',
          'Max hours value is 8': 'Maksymalna ilość godzin to 8',
          'Min hours value is 1': 'Minimalna ilość godzin to 1',
          'Can not take more than 16 hours per year': 'Nie możesz wziąć więcej niż 16 godzin w roku',
        },
      },
    },
  };
</script>
<style scoped>
  td {
    text-align: center;
  }

  .textClass >>> input {
    text-align: center;
  }
</style>
