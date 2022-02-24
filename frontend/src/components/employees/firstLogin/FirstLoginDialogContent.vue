<template>
  <v-card-text id="first-login-dialog" :class="{'pa-2': $vuetify.breakpoint.xs}">
    <div class="subheading">{{ $t('Take a moment to fill in the information we need from you.') }}</div>
    <v-dialog max-width="600" v-model="dialogInfo" v-if="getDataUpdateTime">
      <v-alert class="mb-0 pb-0" :value="true" type="info">
        <div class="mb-3">
          {{ $t('It has been six months since your data was last updated. Check that nothing has changed.') }}
        </div>
      </v-alert>
      <v-btn @click="dialogInfo = false">
        {{ $t('Close') }}
      </v-btn>
    </v-dialog>
    <v-stepper v-model="stepper" class="mt-2">
      <v-stepper-header>
        <v-divider/>
        <v-stepper-step :complete="stepper > 1" step="1">{{ $t('Data') }}</v-stepper-step>
        <v-divider/>
        <v-stepper-step :complete="stepper > 2" step="2">{{ $t('In firm') }}</v-stepper-step>
        <v-divider/>
        <v-stepper-step :complete="stepper > 3" step="3">{{ $t('Contact in case of emergency') }}</v-stepper-step>
        <v-divider/>
        <v-stepper-step v-if="!hasSetPin" :complete="stepper > 4" step="4">
          {{ $t('Security') }}
        </v-stepper-step>
        <v-divider/>
      </v-stepper-header>
      <v-stepper-items>
        <v-stepper-content step="1" :class="{'pa-1': $vuetify.breakpoint.xs}">
          <v-card>
            <personal-data-step v-model="employee"/>
            <v-card-actions>
              <v-btn :disabled="!firstStepValid" @click="stepper++" color="primary">{{ $t('Next') }}</v-btn>
            </v-card-actions>
          </v-card>
        </v-stepper-content>
        <v-stepper-content step="2" :class="{'pa-1': $vuetify.breakpoint.xs}">
          <v-card>
            <firm-data-step v-model="employee"
                            :loading="loading"
                            :tribes="tribes"
                            :positions="positions"
                            :levels="levels"/>
            <v-card-actions>
              <v-btn @click="stepper--">{{ $t('Previous') }}</v-btn>
              <v-btn :disabled="!secondStepValid" @click="stepper++" color="primary">{{ $t('Next') }}</v-btn>
            </v-card-actions>
          </v-card>
        </v-stepper-content>
        <v-stepper-content step="3" :class="{'pa-1': $vuetify.breakpoint.xs}">
          <v-card>
            <emergency-data-step v-model="employee"/>
            <v-card-actions>
              <v-btn @click="stepper--">{{ $t('Previous') }}</v-btn>
              <v-btn v-if="!hasSetPin" :disabled="!thirdStepValid" @click="stepper++" color="primary">
                {{ $t('Next') }}
              </v-btn>
              <v-btn v-else
                     :loading="saving"
                     :disabled="!thirdStepValid"
                     @click="save"
                     color="primary">
                {{ $t('Save') }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-stepper-content>
        <v-stepper-content v-if="!hasSetPin" step="4" :class="{'pa-1': $vuetify.breakpoint.xs}">
          <v-card>
            <security-data-step v-model="employee"/>
            <v-card-actions>
              <v-btn :disabled="saving" @click="stepper--">{{ $t('Previous') }}</v-btn>
              <v-btn :loading="saving"
                     :disabled="!fourthStepValid"
                     @click="save"
                     color="primary">
                {{ $t('Save') }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-stepper-content>
      </v-stepper-items>
    </v-stepper>
  </v-card-text>
</template>

<script>
  import PersonalDataStep from './PersonalDataStep';
  import FirmDataStep from './FirmDataStep';
  import EmergencyDataStep from './EmergencyDataStep';
  import SecurityDataStep from './SecurityDataStep';
  import { mapState } from 'vuex';
  import { PHONE_REGEX } from '../../../util/validateRules';
  import moment from '@divante-adventure/work-moment';
  import { contractsName } from '../../../util/contracts';

  export default {
    name: 'FirstLoginDialogContent',
    components: { SecurityDataStep, EmergencyDataStep, FirmDataStep, PersonalDataStep },
    props: {
      value: { type: Object, default: () => ({ tribe: { id: -1 }, position: { id: -1 } }) },
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        stepper: 1,
        saving: false,
        dialogInfo: false,
      };
    },
    computed: {
      ...mapState({
        tribes: state => state.Tribes.tribes,
        hasSetPin: state => state.Employees.loggedEmployee.hasSetPin,
      }),
      getDataUpdateTime() {
        if (moment(this.employee.dataUpdate) < moment().subtract(6, 'months')) {
          this.employee.dataUpdate = moment().format('YYYY-MM-DD');
          this.dialogInfo = true;
          return true;
        }
        return false;
      },
      positions() {
        const tribe = this.value.tribe || {};
        const tribeId = typeof tribe === 'number' ? tribe : tribe.id;
        return this.tribes
          .filter(item => item.id === tribeId)
          .map(item => item.positions)
          .reduce((a, b) => [ ...a, ...b ], []);
      },
      levels() {
        const position = this.value.position || {};
        const positionId = typeof position === 'number' ? position : position.id;
        return this.positions
          .filter(item => item.id === positionId)
          .map(item => item.strategy.levels)
          .reduce((a, b) => [ ...a, ...b ], []);
      },
      employee: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
      firstStepValid() {
        return typeof this.employee.name === 'string' && this.employee.name.trim().length > 0
          && typeof this.employee.lastName === 'string' && this.employee.lastName.trim().length > 0
          && typeof this.employee.email === 'string' && this.employee.email.trim().length > 0
          && typeof this.employee.street === 'string' && this.employee.street.trim().length > 0
          && typeof this.employee.city === 'string' && this.employee.city.trim().length > 0
          && typeof this.employee.country === 'string' && this.employee.country.trim().length > 0
          && typeof this.employee.postalCode === 'string' && this.employee.postalCode.trim().length > 0
          && typeof this.employee.privatePhone === 'string' && this.employee.privatePhone.match(PHONE_REGEX)
          && typeof this.employee.dateOfBirth === 'string' && this.employee.dateOfBirth.match(/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/)
          && (this.employee.gender === 0 || this.employee.gender === 1)
          && typeof this.employee.language === 'string' && this.employee.language.trim().length > 0;
      },
      secondStepValid() {
        let rules = typeof this.employee.hiredAt === 'string' && this.employee.hiredAt.match(/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/)
          && typeof this.employee.contract !== 'undefined'
          && typeof this.employee.tribe !== 'undefined'
          && this.employee.workMode !== 0
          && (this.positions.length === 0 || typeof this.employee.position !== 'undefined')
          && typeof this.employee.jobTimeValue !== 'undefined'
          && `${this.employee.jobTimeValue}`.replace(',', '.') > 0
          && `${this.employee.jobTimeValue}`.replace(',', '.') <= 1;

        if (typeof this.employee.phone !== 'undefined' && this.employee.phone.length !== 0) {
          rules = rules && this.employee.phone.match(PHONE_REGEX);
        }
        return rules;
      },
      thirdStepValid() {
        return typeof this.employee.emergencyFirstName === 'string' && this.employee.emergencyFirstName.trim().length > 0
          && typeof this.employee.emergencyLastName === 'string' && this.employee.emergencyLastName.trim().length > 0
          && typeof this.employee.emergencyPhone === 'string' && this.employee.emergencyPhone.match(PHONE_REGEX);
      },
      fourthStepValid() {
        return typeof this.employee.pin === 'string' && this.employee.pin.match(/^[0-9]{4}$/);
      },
    },
    methods: {
      async save() {
        this.saving = true;
        this.employee.jobTimeValue = `${this.employee.jobTimeValue}`.replace(',', '.') * 28800;
        this.employee.dataUpdate = moment().format('YYYY-MM-DD');
        await this.$store.dispatch('Employees/saveEmployee', this.employee);
        if (parseInt(this.employee.contract.id) === parseInt(contractsName.OUTSOURCE.id)) {
          this.$router.push('/thanks');
        } else if (this.$store.getters['Employees/redirectToGdpr']) {
          this.$store.commit('showSnackbar', { text: this.$t('snackbar_redirect.agreements'), color: 'green' });
          this.$router.push('/agreements/general');
        }
        this.$emit('hide');
        this.saving = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Take a moment to fill in the information we need from you.': 'Poświęć chwilę na uzupełnienie potrzebnych nam informacji.',
          'Data': 'Dane',
          'In firm': 'W firmie',
          'Contact in case of emergency': 'W razie wypadku',
          'Security': 'Zabezpieczenie',
          'Next': 'Dalej',
          'Previous': 'Cofnij',
          'Save': 'Zapisz',
          'Close': 'Zamknij',
          'It has been six months since your data was last updated. Check that nothing has changed.': 'Minęło sześć miesięcy od ostatniej aktualizacji Twoich danych. Sprawdź, czy nic się nie zmieniło.',
        },
      },
    },
  };
</script>
<style>
  @media only screen and (max-width: 600px) {
    #first-login-dialog .v-list__tile {
      padding: 0 !important;
      height: unset;
    }
    #first-login-dialog .v-list--two-line {
      height: unset;
    }
  }
</style>
