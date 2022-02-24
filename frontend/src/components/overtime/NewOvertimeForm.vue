<template>
  <v-form v-model="formValid" id="overtime-form">
    <v-container :class="{'pa-0': $vuetify.breakpoint.xs}">
      <v-row no-gutters wrap>
        <v-col cols="12">
          <overtime-info-alert/>
        </v-col>
        <v-col cols="12" class="pb-0">
          <evidence-month-chooser v-model="month"/>
        </v-col>
        <v-col cols="12">
          <v-row no-gutters >
            <v-col cols="12">
              <evidence-overtime-entries v-model="overtimeEntries"/>
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
        <v-col cols="12">
          <evidence200-percent-info :overtime-entries="overtimeEntries"/>
          <v-combobox :items="sortedManagers"
                      v-model="manager"
                      :label="$t('Supervisor accepting request')"
                      prepend-icon="supervisor_account"
                      :item-text="getItemText"
                      :rules="rules.overtimeManager"
                      chips dense/>
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
                          :question="$t('Are you sure, you want to send this overtime request to be approved?')"
                          @yes="send"/>
        </v-col>
      </v-row>
    </v-container>
  </v-form>
</template>

<script>
  import { mapGetters } from 'vuex';
  import OvertimeInfoAlert from '../overtime/OvertimeInfoAlert';
  import EvidenceOvertimeEntries from '../evidences/EvidenceOvertimeEntries';
  import Evidence200PercentInfo from '../evidences/Evidence200PercentInfo';
  import { EventBus, eventNames } from '../../eventbus';
  import ConfirmDialog from '../utils/ConfirmDialog';
  import EvidenceMonthChooser from '../evidences/EvidenceMonthChooser';

  export default {
    name: 'NewEvidenceForm',
    components: {
      ConfirmDialog,
      Evidence200PercentInfo,
      EvidenceOvertimeEntries,
      OvertimeInfoAlert,
      EvidenceMonthChooser,
    },
    data() { return {
      confirmDialogVisible: false,
      month: null,
      formValid: true,
      manager: null,
      overtimeEntries: [{}],
      sendingInProgress: false,
      monthChanged: false,
      rules: {
        overtimeManager: [
          v => !!v || this.$t('This field is required'),
        ],
      },
    };},
    computed: {
      ...mapGetters({
        managers: 'Employees/getManagersAcceptingLeaveRequests',
        bestDefaultMonth: 'Evidences/bestDefaultMonth',
      }),
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
        await this.$store.dispatch('FreeDays/loadFreeDaysForMonth', this.month);
      },
      bestDefaultMonth() {
        this.month = this.bestDefaultMonth;
      },
    },
    methods: {
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
        const overtime = this.overtimeEntries.map(entry => ({
          project: entry.name,
          code: entry.code,
          hours: (entry.hours * 1) + (entry.minutes / 60),
          percent: entry.rate === undefined ? 100 : entry.rate.replace('%', ''),
          date: entry.days,
        }));

        const data = {
          month,
          year,
          manager,
          overtime,
          overtimeOnly: true,
        };
        this.sendingInProgress = true;
        try {
          await this.$store.dispatch('Evidences/sendEvidence', data);
          this.$store.commit('showSnackbar', {
            text: this.$t('Overtime evidence waiting for manager approval'),
            color: 'success',
          });
          this.sendingInProgress = false;
          EventBus.$emit(eventNames.evidenceCreatedAfter);
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Overtime evidence can not be sent'),
            color: 'error',
          });
          this.sendingInProgress = false;
        }
      },
    },
    i18n: { messages: {
      pl: {
        'Overtime': 'Dodatkowe godziny',
        'Send': 'Wyślij',
        'Supervisor accepting request': 'Akceptujący wniosek',
        'Your project is not on the list?': 'Twój projekt nie jest obecny na liście?',

        'This field is required': 'To pole jest wymagane',
        'This value cannot be lesser than zero': 'Ta wartość nie może być mniejsza od zera',
        'Overtime evidence waiting for manager approval': 'Ewidencja dodatkowych godzin wysłana do akceptacji',
        'Overtime evidence was not sent': 'Ewidencja nadgodzin nie została wysłana',
        'Are you sure, you want to send this overtime request to be approved?': 'Czy na pewno chcesz wysłać wniosek o nadgodziny do zaakceptowania?',

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
