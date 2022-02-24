<template>
  <v-dialog v-model="dialogVisible" width="1000">
    <v-card id="new-free-day-request-window">
      <v-card-title class="headline" :class="{'pb-0': $vuetify.breakpoint.xs}">
        <span :class="{'pb-3 request-title': $vuetify.breakpoint.xs}">{{ $t('Creating new request') }}</span>
        <v-spacer/>
        <employee-chip v-if="typeof(period.employee) === 'object'" :employee="period.employee"/>
      </v-card-title>
      <v-card-text>
        <form-content :period="period" :managers="managers" :is-valid.sync="formValid" v-model="request"/>
      </v-card-text>
      <v-card-actions>
        <v-alert :value="showError" type="error" transition="scale-transition" dismissible>
          {{ errorMessage }}
        </v-alert>
        <v-spacer/>
        <v-btn color="red" text @click="dialogVisible = false">
          {{ $t('Cancel') }}
        </v-btn>
        <v-btn color="blue" text @click="createNewRequest()" :disabled="!formValid">
          {{ createMessage }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import { mapGetters } from 'vuex';
  import FormContent from './FormContent';
  import EmployeeChip from '../../utils/EmployeeChip';
  import { leaveDaysTypes, verifyNewRequest } from '../../../util/freeDays';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'RequestFormWindow',
    components: { EmployeeChip, FormContent },
    data() { return {
      dialogVisible: false,
      period: {},
      formValid: false,
      request: {},
      errorMessage: '',
      showError: false,
      hours: [],
    };},
    computed: {
      ...mapGetters({
        managers: 'Employees/getManagersAcceptingLeaveRequests',
      }),
      createMessage() {
        return this.isCurrent ? this.$t('Create') : this.$t('Plan');
      },
      isCurrent() {
        const from = moment(this.period.dateFromMoment).startOf('day');
        const to = moment(this.period.dateToMoment).endOf('day');
        return moment.range(from, to).contains(moment());
      },
    },
    methods: {
      show(data) {
        if (!this.dialogVisible) {
          this.dialogVisible = true;
          this.$store.dispatch('Employees/loadEmployees');
          this.period = data;
          this.request = {};
          this.errorMessage = '';
          this.showError = false;
        }
      },
      updateHours(data) {
        this.hours = data;
        if (this.request.days[0].type === leaveDaysTypes.leaveCare) {
          this.hours = data.filter(item => (typeof item === 'string' || item instanceof String));
        }
      },
      async createNewRequest() {
        this.request.days.hours = this.hours;
        const verification = verifyNewRequest(this.period, this.request);
        if (typeof(verification) === 'boolean' && verification) {
          this.dialogVisible = false;
          const backendMessage = {
            periodId: this.period.id,
            managerId: this.request.manager.id,
            attachments: this.request.attachments,
            days: this.request.days.map((day, idx) => (
              { type: day.type, date: day.day, hour: Number(this.hours[idx]) })),
            subject: `${this.period.employee.name} ${this.period.employee.lastName}`,
            comment: this.request.comment,
            status: this.isCurrent ? 0 : 1,
          };
          await this.$store.dispatch('FreeDays/createNewRequest', backendMessage)
            .finally(() => {
              this.period = {};
              this.request = {};
              EventBus.$emit(eventNames.createNewLeaveRequestAfter);
            });
          if (backendMessage.days[0].type === leaveDaysTypes.overtime) {
            window.location.reload();
          }
        }
        else {
          this.errorMessage = this.$t(verification);
          this.showError = true;
        }
      },
    },
    mounted() {
      EventBus.$on(eventNames.createNewLeaveRequest, this.show);
      EventBus.$on(eventNames.updateChildCareHours, this.updateHours);
    },
    i18n: { messages: {
      pl: {
        'Creating new request': 'Tworzenie nowego wniosku',
        'Cancel': 'Anuluj',
        'Create': 'Utwórz',
        'Plan': 'Zaplanuj',
        'You have used too many paid free days': 'Wybrałeś zbyt wiele płatnych dni wolnych',
        'You have used too many paid leave days': 'Wybrałeś zbyt wiele dni urlopu płatnego',
        'You have used too many leaves on request': 'Wybrałeś zbyt wiele dni urlopu na żądanie',
        'You have used too many care leaves hours': 'Wybrałeś zbyt wiele godzin opieki nad dzieckiem',
      },
    } },
  };
</script>
<style scoped>
  .request-title{
    font-size: medium;
  }
</style>
