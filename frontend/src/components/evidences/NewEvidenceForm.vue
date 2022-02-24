<template>
  <v-form v-model="formValid" id="new-evidence-form">
    <v-container :class="{'pa-0': $vuetify.breakpoint.xs}">
      <v-row no-gutters wrap>
        <v-col cols="12">
          <evidence-info-alert/>
        </v-col>
        <v-col cols="12" class="pb-0">
          <evidence-month-chooser v-model="month"/>
        </v-col>
        <v-col cols="12" md="12" v-if="getContactName === 'B2B HOURLY' || getContactName === 'CLC HOURLY'">
          <v-text-field :label="$t('Days of unavailability')"
                        v-model="unavailabilityDays"
                        :hint="$t('Given in days')"
                        type="number" :min="0"
                        :disabled="true"
                        persistent-hint/>
        </v-col>
        <v-col v-else v-for="(contractType, index) in contractTypes" :key="index" cols="12" md="4">
          <v-text-field :label="contractType.label"
                        v-model="contractType.model"
                        :hint="$t('Given in days')"
                        type="number" :min="0"
                        :disabled="true"
                        persistent-hint/>
        </v-col>
        <v-col cols="12">
          <v-text-field :label="getLabelPerContract"
                        v-model="workingHours"
                        :hint="getHintPerContract"
                        type="number" :min="0"
                        :loading="workingHoursUpdating"
                        :disabled="showOvertime && overtimeOnly"
                        :rules="rules.workingHours"
                        persistent-hint/>
        </v-col>
        <v-col cols="12" v-if="isB2B">
          <file-uploader class="my-4" :title="$t('Add invoice')" :selected-callback="file => invoices.push(file)"/>
        </v-col>
        <v-col class="mt-5" cols="12">
          <span class="label">{{ $t('Sum of paid hours') }}:</span>
          <span class="value">{{ workingHours * 1 + paidFree * 8 + sickLeave * 8 }}</span>
        </v-col>
        <v-col v-if="getContactName !== 'B2B HOURLY' && getContactName !== 'CLC HOURLY'" cols="12" md="6">
          <v-checkbox class="ma-0 pa-0 pt-3" v-model="showOvertime" :label="$t('Additional hours')"/>
        </v-col>
        <v-col cols="12" md="6" v-if="showOvertime">
          <v-checkbox v-model="overtimeOnly"
                      class="ma-0 pa-0"
                      :label="$t('Send overtime evidences only')"
                      :hint="$t('Mark this if your monthly evidence was already sent')"
                      persistent-hint/>
        </v-col>
        <v-col cols="12" v-if="showOvertime">
          <v-row >
            <v-col cols="12">
              <evidence-overtime-entries v-if="showOvertime" v-model="overtimeEntries"/>
            </v-col>
          </v-row>
          <div class="body-1 font-weight-light" :class="{'evidence-tooltip': $vuetify.breakpoint.xs}">
            {{ $t('Your project is not on the list?') }}
            <v-tooltip right>
              <template v-slot:activator="{ on }">
                <v-avatar v-on="on">
                  <v-icon>help_outline</v-icon>
                </v-avatar>
              </template>
              <span v-html="$t('no-project-available-tooltip')"/>
            </v-tooltip>
          </div>
        </v-col>
        <v-col cols="12" v-if="showOvertime">
          <evidence200-percent-info :overtime-entries="overtimeEntries"/>
          <v-combobox :items="sortedManagers"
                      v-model="manager"
                      :label="$t('Supervisor accepting request')"
                      prepend-icon="supervisor_account"
                      :item-text="getItemText"
                      :rules="rules.overtimeManager"
                      chips dense/>
        </v-col>
        <v-col v-if="getContactName === 'B2B HOURLY' || getContactName === 'B2B LUMP SUM'" cols="12" md="6">
          <v-checkbox class="ma-0 pa-0 pt-3" v-model="showReport" :label="$t('Avaza report')"/>
        </v-col>
        <v-col cols="12" v-if="showReport">
          <file-uploader class="my-4" :title="$t('Add Avaza report')" :selected-callback="file => invoices.push(file)"/>
        </v-col>
        <v-col cols="12" v-if="isB2B && invoices.length > 0">
          <span class="label">{{ $t('Uploaded files') }}:</span>
          <v-list dense>
            <v-list-item v-for="(invoice, index) in invoices" :key="index">
              <v-list-item-action>
                <v-btn icon @click="invoices.splice(index, 1)"><v-icon color="red">highlight_off</v-icon></v-btn>
              </v-list-item-action>
              <v-list-item-content>{{ invoice.name }}</v-list-item-content>
            </v-list-item>
          </v-list>
        </v-col>
        <v-col cols="12">
          <v-btn color="success"
                 :disabled="!formValid"
                 @click="showConfirmDialog"
                 :loading="sendingInProgress"
                 block>
            {{ $t('Send') }}
          </v-btn>
          <confirm-dialog v-if="confirmDialogVisible"
                          v-model="confirmDialogVisible"
                          :question="$t('Do you really want to send this document to administration?')"
                          @yes="send"/>
        </v-col>
      </v-row>
    </v-container>
  </v-form>
</template>

<script>
  import { mapGetters, mapState } from 'vuex';
  import EvidenceMonthChooser from './EvidenceMonthChooser';
  import EvidenceInfoAlert from './EvidenceInfoAlert';
  import EvidenceOvertimeEntries from './EvidenceOvertimeEntries';
  import Evidence200PercentInfo from './Evidence200PercentInfo';
  import { EventBus, eventNames } from '../../eventbus';
  import ConfirmDialog from '../utils/ConfirmDialog';
  import { leaveDaysTypes, leaveDaysStatuses, leaveRequestsStatuses } from '../../util/freeDays';
  import moment from '@divante-adventure/work-moment';
  import FileUploader from '../utils/FileUploader';

  export default {
    name: 'NewEvidenceForm',
    components: {
      FileUploader,
      ConfirmDialog,
      Evidence200PercentInfo,
      EvidenceOvertimeEntries,
      EvidenceInfoAlert,
      EvidenceMonthChooser,
    },
    data() { return {
      confirmDialogVisible: false,
      month: null,
      formValid: true,
      workingHoursContainer: null,
      invoices: [],
      workingHoursUpdating: false,
      unavailabilityDays: 0,
      paidFree: 0,
      unpaidFree: 0,
      sickLeave: 0,
      showOvertime: false,
      showReport: false,
      overtimeOnly: false,
      manager: null,
      overtimeEntries: [{}],
      sendingInProgress: false,
      monthChanged: false,
      countToThree: 0,
      rules: {
        workingHours: [
          v => !!v || v === 0 || this.$t('This field is required'),
          v => v >= 0 || this.$t('This value cannot be lesser than zero'),
        ],
        overtimeManager: [
          v => !!v || this.$t('This field is required'),
        ],
      },
    };},
    computed: {
      ...mapGetters({
        hoursPerMonth: 'Evidences/hoursPerMonth',
        managers: 'Employees/getManagersAcceptingLeaveRequests',
        bestDefaultMonth: 'Evidences/bestDefaultMonth',
      }),
      ...mapState({
        periods: state => state.FreeDays.myPeriods,
      }),
      contractTypes() {
        return [{
          label: this.$t('Paid free days'),
          model: this.paidFree,
        }, {
          label: this.$t('Unpaid free days'),
          model: this.unpaidFree,
        }, {
          label: this.$t('Sick leave days'),
          model: this.sickLeave,
        }];
      },
      getContactName() {
        const currentUser = this.$store.state.Employees.loggedEmployee || {};
        const contract = currentUser.contract || {};
        return contract.name;
      },
      isB2B() {
        return this.getContactName === 'B2B LUMP SUM' || this.getContactName === 'B2B HOURLY';
      },
      getLabelPerContract() {
        switch(this.getContactName) {
          case 'CoE': return this.$t('Working hours');
          case 'B2B LUMP SUM': return this.$t('Number of hours performed services');
          case 'B2B HOURLY': return this.$t('Number of hours performed');
          case 'CLC LUMP SUM':
          case 'CLC HOURLY': return this.$t('Number of hours of the executed order');
          default: return '';
        }
      },
      getHintPerContract() {
        switch(this.getContactName) {
          case 'CoE': return this.$t('Without leave days and sick leaves');
          case 'CLC LUMP SUM':
          case 'B2B LUMP SUM': return this.$t('Without free days and sick leaves');
          case 'B2B HOURLY':
          case 'CLC HOURLY': return this.$t('Without unavailability days');
          default: return '';
        }
      },
      requestDays() {
        const currentMonth = this.month || moment().format('YYYY-MM');
        const monthMoment = moment(`${currentMonth}-01`, 'YYYY-MM-DD');
        return this.periods
          .filter(period => period.dateFromMoment.isSameOrBefore(monthMoment, 'month')
            && period.dateToMoment.isSameOrAfter(monthMoment, 'month'))
          .map(period => period.requests)
          .reduce((a, b) => [ ...a, ...b], [])
          .filter(request => request.status === leaveRequestsStatuses.accepted)
          .map(request => request.days)
          .reduce((a, b) => [ ...a, ...b], [])
          .filter(day => day.status === leaveDaysStatuses.active
            && moment(day.date).format('YYYY-MM') === currentMonth);
      },
      calculatedPaidFreeDays() {
        const types = [
          leaveDaysTypes.freePaid,
          leaveDaysTypes.leavePaid,
          leaveDaysTypes.leaveRequest,
          leaveDaysTypes.leaveOccasional,
          leaveDaysTypes.leaveCare,
        ];
        return this.requestDays.filter(day => types.includes(day.type)).length;
      },
      calculatedUnpaidFreeDays() {
        const types = [
          leaveDaysTypes.freeUnpaid,
          leaveDaysTypes.leaveUnpaid,
        ];
        return this.requestDays.filter(day => types.includes(day.type)).length;
      },
      calculatedSickLeaveDays() {
        const types = [
          leaveDaysTypes.sickLeavePaid,
        ];
        return this.requestDays.filter(day => types.includes(day.type)).length;
      },
      calculatedUnavailabilityDays() {
        const types = [
          leaveDaysTypes.unavailability,
        ];
        return this.requestDays.filter(day => types.includes(day.type)).length;
      },
      workingHours: {
        get() {
          if (this.workingHoursContainer === null) {
            this.workingHoursContainer = this.hoursPerMonth(this.month);
          }
          return this.workingHoursContainer;
        },
        set(value) {
          this.workingHoursContainer = value;
        },
      },
      sortedManagers() {
        return this.managers.sort((a, b) => {
          const textA = this.getItemText(a);
          const textB = this.getItemText(b);
          return textA.localeCompare(textB);
        });
      },
    },
    watch: {
      async month() {
        this.monthChanged = true;
        this.workingHoursUpdating = true;
        await this.$store.dispatch('FreeDays/loadFreeDaysForMonth', this.month);
        this.workingHours = this.hoursPerMonth(this.month);
        this.paidFree = this.calculatedPaidFreeDays;
        this.unpaidFree = this.calculatedUnpaidFreeDays;
        this.sickLeave = this.calculatedSickLeaveDays;
        this.unavailabilityDays = this.calculatedUnavailabilityDays;
        this.workingHoursUpdating = false;
      },
      bestDefaultMonth() {
        this.month = this.bestDefaultMonth;
      },
      paidFree(newVal, oldVal) {
        this.updateFreeDays(newVal, oldVal);
      },
      unpaidFree(newVal, oldVal) {
        this.updateFreeDays(newVal, oldVal);
      },
      sickLeave(newVal, oldVal) {
        this.updateFreeDays(newVal, oldVal);
      },
      unavailabilityDays(newVal, oldVal) {
        this.updateFreeDays(newVal, oldVal);
      },
    },
    methods: {
      updateFreeDays(newVal, oldVal) {
        if (!this.monthChanged) {
          this.workingHours = this.workingHours - 8 * (newVal - oldVal);
        } else {
          this.workingHours = this.workingHours - 8 * newVal;
          this.countToThree += 1;
          if (this.countToThree === 3) {
            this.countToThree = 0;
            this.monthChanged = false;
          }
        }
      },
      getItemText(item) {
        return `${item.lastName} ${item.name}`;
      },
      showConfirmDialog() {
        this.confirmDialogVisible = true;
      },
      async send() {
        const yearMonth = this.month || this.bestDefaultMonth;
        const [year, month] = yearMonth.split('-');
        const manager = this.manager === null ? null : this.manager.id;
        const overtime = this.showOvertime ? this.overtimeEntries.map(entry => ({
          project: entry.name,
          code: entry.code,
          hours: (entry.hours * 1) + (entry.minutes / 60),
          percent: entry.rate === undefined ? 100 : entry.rate.replace('%', ''),
          date: entry.days,
        })) : [];
        const invoices = this.invoices;
        const data = {
          month,
          year,
          manager,
          overtime,
          invoices,
          hours: this.workingHours,
          paidDaysoffHours: this.paidFree * 8,
          unpaidDaysoffHours: this.unpaidFree * 8,
          sickLeaveHours: this.sickLeave * 8,
          unavailabilityHours: this.unavailabilityDays * 8,
          overtimeOnly: this.showOvertime ? this.overtimeOnly : false,
        };
        this.sendingInProgress = true;
        try {
          const response = await this.$store.dispatch('Evidences/sendEvidence', data);
          if (response.response === 'Evidence sent') {
            this.$store.commit('showSnackbar', {
              text: this.$t('Evidence sent'),
              color: 'success',
            });
          } else if (response.response === 'Waiting for manager approval') {
            this.$store.commit('showSnackbar', {
              text: this.$t('Overtime evidence waiting for manager approval'),
              color: 'success',
            });
          } else {
            throw new Error('Unrecognized response for evidence generation');
          }
          this.sendingInProgress = false;
          EventBus.$emit(eventNames.evidenceCreatedAfter);
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Tried to send overtime but evidence doesn\'t exist'),
            color: 'error',
          });
          this.sendingInProgress = false;
        }
      },
    },
    i18n: { messages: {
      pl: {
        'Working hours': 'Godziny wykonanej pracy',
        'Number of hours performed': 'Ilość wykonanych godzin',
        'Number of hours performed services': 'Ilość godzin wykonanych usług',
        'Number of hours of the executed order': 'Ilość godzin wykonanego zlecenia',
        'Without free days and sick leaves': 'Bez dni wolnych i chorobowych',
        'Without leave days and sick leaves': 'Bez dni urlopowych i chorobowych',
        'Without unavailability days': 'Bez dni niedostępności',
        'Days of unavailability': 'Dni niedostępności',
        'Paid free days': 'Płatne dni wolne',
        'Unpaid free days': 'Bezpłatne dni wolne',
        'Sick leave days': 'Zwolnienie chorobowe',
        'Given in days': 'Podawane w dniach',
        'Sum of paid hours': 'Suma godzin płatnych',
        'Additional hours': 'Dodatkowe godziny',
        'Avaza report': 'Raport Avaza',
        'Add Avaza report': 'Dodaj raport Avaza',
        'Send overtime evidences only': 'Wyślij tylko ewidencję dodatkowych godzin',
        'Mark this if your monthly evidence was already sent': 'Zaznacz, jeżeli ewidencja miesięczna została już wysłana',
        'Send': 'Wyślij',
        'Uploaded files': 'Dołączone pliki',
        'Supervisor accepting request': 'Akceptujący wniosek',
        'Your project is not on the list?': 'Twój projekt nie jest obecny na liście?',
        'Add invoice': 'Dodaj fakturę',

        'This field is required': 'To pole jest wymagane',
        'This value cannot be lesser than zero': 'Ta wartość nie może być mniejsza od zera',
        'Evidence sent': 'Ewidencja wysłana',
        'Overtime evidence waiting for manager approval': 'Ewidencja dodatkowych godzin wysłana do akceptacji',
        'Tried to send overtime but evidence doesn\'t exist': 'Próbowano wysłać ewidencję dodatkowych godzin, ale faktyczna ewidencja nie istnieje',
        'Do you really want to send this document to administration?': 'Czy na pewno chcesz wysłać ten dokument do administracji?',

        'no-project-available-tooltip': 'Jeżeli miałeś dodatkowe godziny w jakimś projekcie i projekt ten nie jest widoczny na liście, oznacza to,<br/> że albo Twój projekt nie istnieje w Adventure, albo nie ma przydzielonego wymaganego kodu projektu.<br/>Skontaktuj się ze swoim menedżerem.',
      },
      en: {
        'Supervisor accepting request': 'Approver accepting request ',
        'no-project-available-tooltip': 'If you had an overtime in some project and that project is not displayed on the list,<br/> either your project doesn\'t exists in Adventure or it doesn\'t have a required project code.<br/> Contact your project manager.',
      },
    } },
  };
</script>
<style scoped>
  .label {
    font-weight: bold;
  }
  .evidence-tooltip{
    justify-content: center;
    display: flex;
    font-size: small !important;
    align-items: center;
  }
</style>
<style>
  @media only screen and (max-width: 600px) {
    #new-evidence-form .v-input--selection-controls.v-input .v-label {
      font-size: small;
    }
  }
</style>
