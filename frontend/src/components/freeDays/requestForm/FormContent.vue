<template>
  <v-container :class="{'pa-0': $vuetify.breakpoint.xs}">
    <v-row no-gutters wrap>
      <v-col cols="12" md="5" :class="{'request-form-mobile': $vuetify.breakpoint.xs}">
        <employee-chooser v-model="manager"
                          :employees="managers"
                          :label="managerLabel"
                          prepend-icon="supervisor_account"/>
        <div class="space_between_components"></div>
        <request-day-picker id="new-free-day-request-calendar" v-model="days" :period="period"/>
        <attachments v-if="attachmentsVisible" v-model="attachments"/>
        <v-spacer cols="1"/>
        <v-textarea
          v-model="comment"
          :label="$t('Comment')"
          rows="1"
          counter="100"
          auto-grow
          single-line
          @keydown="limit( $event, 100)"
        >
        </v-textarea>
      </v-col>
      <v-spacer md="1"/>
      <v-col cols="12" md="6">
        <day-type-select-leave-days v-if="leaveDaysUsed"
                                    v-model="dayType"
                                    @input="recalculateValidation"
                                    :child-care="childCare"
                                    :period="period"/>
        <day-type-select-free-days v-else-if="freeDaysUsed"
                                   v-model="dayType"
                                   @input="recalculateValidation"
                                   :period="period"/>
        <request-day-type-table v-model="days" :employee="period.employee" :is-valid.sync="formValid"/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import EmployeeChooser from '../../utils/EmployeeChooser';
  import RequestDayPicker from './RequestDayPicker';
  import RequestDayTypeTable from './RequestDayTypeTable';
  import { EventBus, eventNames } from '../../../eventbus';
  import Attachments from './Attachments';
  import { canUseFreeDays, canUseLeaveDays, canUseUnavailabilityDays, leaveDaysTypes } from '../../../util/freeDays';
  import DayTypeSelectFreeDays from './DayTypeSelectFreeDays';
  import DayTypeSelectLeaveDays from './DayTypeSelectLeaveDays';
  import { getContractDataByEmployee } from '../../../util/contracts';

  export default {
    name: 'FormContent',
    components: {
      DayTypeSelectLeaveDays,
      DayTypeSelectFreeDays,
      Attachments,
      RequestDayTypeTable,
      RequestDayPicker,
      EmployeeChooser,
    },
    props: {
      period: { type: Object, required: true },
      managers: { type: Array, required: true },
      isValid: { type: Boolean, required: true },
      value: { type: Object, default: () => ({}) },
    },
    data() {
      return {
        manager: null,
        days: [],
        attachments: [],
        dayType: null,
        formValid: false,
        comment: '',
        childCare: 0,
      };
    },
    computed: {
      freeDaysUsed() {
        return this.period.hasOwnProperty('employee') ? canUseFreeDays(this.period.employee) : false;
      },
      leaveDaysUsed() {
        return this.period.hasOwnProperty('employee') ? canUseLeaveDays(this.period.employee) : false;
      },
      unavailabilityDaysOnly() {
        return this.period.hasOwnProperty('employee') ? canUseUnavailabilityDays(this.period.employee) : false;
      },
      attachmentsVisible() {
        const employee = this.period.employee;
        const dayTypeCorrect = [leaveDaysTypes.sickLeavePaid, leaveDaysTypes.sickLeaveUnpaid].includes(this.dayType);
        return getContractDataByEmployee(employee).useSickLeaveAttachments && dayTypeCorrect;
      },
      daysWithTypes() {
        return this.days.map(day => ({ day, type: this.dayType }));
      },
      managerLabel() {
        if (this.dayType === leaveDaysTypes.additionalHours) {
          return this.$t('Supervisor notified about additional hours');
        } else if (this.dayType === leaveDaysTypes.overtime) {
          return this.$t('Supervisor notified about overtime');
        } else {
          return this.$t('Supervisor accepting request');
        }
      },
    },
    watch: {
      manager() {
        this.recalculateValidation();
      },
      days() {
        this.recalculateValidation();
      },
      dayType() {
        this.days.splice(999);
      },
      attachments() {
        this.recalculateValidation();
      },
      formValid() {
        this.recalculateValidation();
      },
      comment() {
        this.recalculateValidation();
      },
    },
    methods: {
      recalculateValidation() {
        const fieldsValid = (this.dayType !== null || this.unavailabilityDaysOnly !== false)
          && this.manager !== null
          && this.days.length > 0
          && this.comment.length <= 100;
        const attachmentsValid = !this.attachmentsVisible || this.attachments.length > 0;
        let isValid = false;
        if (this.dayType === leaveDaysTypes.leaveCare) {
          isValid = fieldsValid && attachmentsValid && this.formValid;
        } else {
          isValid = fieldsValid && attachmentsValid;
        }
        if (this.unavailabilityDaysOnly !== false) {
          this.dayType = leaveDaysTypes.unavailability;
        }
        this.$emit('update:is-valid', isValid);
        const eventObj = {
          manager: this.manager,
          days: this.daysWithTypes,
          attachments: this.attachments,
          comment: this.comment,
        };
        if (this.period.employee !== undefined) {
          if (this.dayType === leaveDaysTypes.leaveCare) {
            this.period.employee.dayType = true;
          } else {
            this.period.employee.dayType = false;
          }
        }
        this.$emit('input', eventObj);
      },
      clear(data) {
        this.childCare = data.employee.childCare;
        this.dayType = null;
        this.manager = null;
        this.comment = '';
        this.days = [];
        this.attachments = [];
      },
      limit(event, limit) {
        const ALLOWED_KEY = [8, 46];
        if (!ALLOWED_KEY.includes(event.keyCode)) {
          if (this.comment.length >= limit) {
            event.preventDefault();
          }
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'User:': 'Użytkownik:',
          'Supervisor accepting request': 'Akceptujący wniosek',
          'Comment': 'Uwagi',
          'Supervisor notified about additional hours': 'Osoba informowana o godzinach dodatkowych',
          'Supervisor notified about overtime': 'Osoba informowana o nadgodzinach',
        },
        en: {
          'Supervisor accepting request': 'Approver accepting request',
        },
      },
    },
    mounted() {
      EventBus.$on(eventNames.createNewLeaveRequest, this.clear);
    },
  };
</script>
<style scoped>
  .space_between_components {
    margin-bottom: 20px;
  }
</style>
<style>
  .request-form-mobile .v-input__prepend-outer{
    display: none;
  }
</style>
