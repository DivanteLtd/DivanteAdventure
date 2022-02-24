<template>
  <v-card-text>
    <v-form v-model="formValid">
      <employee-chooser :employees="hrEmployees"
                        v-if="!details"
                        v-model="recruiter"
                        :label="$t('Recruiter')"
                        prepend-icon="supervisor_account"/>
      <v-text-field v-else :value="recruiter.name + ' ' + recruiter.lastName" readonly/>
      <v-text-field :readonly="details" class="required" v-model="name"
                    :label="$t('First name')" :rules="[ rules.required ]" required/>
      <v-text-field :readonly="details" class="required" v-model="lastName" :label="$t('Last name')"
                    :rules="[ rules.required ]" required/>
      <v-select v-if="!details" :items="genders" v-model="gender" :label="$t('Gender')" class="required"/>
      <v-text-field v-else readonly :value="getGender" :label="$t('Gender')"/>
      <v-menu
        :disabled="details"
        v-model="birthdayVisible"
        :close-on-content-click="false"
        :nudge-right="40"
        :rules="[ rules.required ]"
        transition="scale-transition"
        offset-y
        min-width="290px"
      >
        <template v-slot:activator="{ on }">
          <v-text-field
            v-model="dateOfBirth"
            :label="$t('Date of birth')"
            readonly
            :rules="[ rules.required ]"
            v-on="on"
            class="required"
          />
        </template>
        <v-date-picker v-model="dateOfBirth"
                       :first-day-of-week="$t('date.firstDayOfWeek')"
                       :picker-date.sync="pickerDateOfBirth"
                       @input="birthdayVisible = false"
                       :max="today"
                       ref="birthdayPicker"/>
      </v-menu>
      <v-menu :disabled="details"
              v-model="welcomeDayVisible"
              :first-day-of-week="$t('date.firstDayOfWeek')"
              :close-on-content-click="false"
              :nudge-right="40"
              transition="scale-transition"
              min-width="290px"
              offset-y>
        <template v-slot:activator="{ on }">
          <v-text-field v-model="dateOfWelcomeDay"
                        :label="$t('Date of Welcome Day')"
                        v-on="on"
                        readonly/>
        </template>
        <v-date-picker v-model="dateOfWelcomeDay"
                       :picker-date.sync="pickerDate"
                       @input="welcomeDayVisible = false"
                       :first-day-of-week="$t('date.firstDayOfWeek')"
                       :min="today"/>
      </v-menu>
      <v-text-field class="required"
                    :readonly="details"
                    :disabled="id !== -1 && !details"
                    v-model="email"
                    :label="$t('Suggested e-mail address')"
                    :rules="[ rules.required, rules.at ]" required
      />
      <v-text-field class="required"
                    :readonly="details"
                    v-model="privateEmail"
                    :label="$t('Private e-mail address')"
                    :rules="[ rules.required, rules.at ]"
                    required
      />
      <v-select v-model="language" :items="availableLanguages" :label="$t('Language')"/>
      <v-text-field
        :readonly="details"
        v-model="privatePhone"
        :label="$t('Private phone number')"
        :rules="[ rules.required ]"
        :validate="validateTelephoneNumber"
        class="required"/>
      <v-text-field :readonly="details" v-model="company" :label="$t('Previous company')"/>
      <v-text-field :readonly="details" v-model="source" :label="$t('Source f.ex company website')"/>
      <v-text-field :readonly="details" v-model="street" :label="$t('Street and number')"/>
      <v-text-field :readonly="details" v-model="city" :label="$t('City')"/>
      <v-text-field :readonly="details" v-model="postalCode" :label="$t('Postal code')"/>
      <v-text-field :readonly="details" v-model="country" :label="$t('Country')"/>
      <v-menu
        :disabled="details"
        v-model="visibleHireDate"
        :close-on-content-click="false"
        :nudge-right="40"
        transition="scale-transition"
        offset-y
        min-width="290px"
      >
        <template v-slot:activator="{ on }">
          <v-text-field
            v-model="hireDate"
            :label="$t('Designated hire date')"
            readonly
            v-on="on"
          />
        </template>
        <v-date-picker v-model="hireDate"
                       @input="visibleHireDate = false"
                       :picker-date.sync="pickerHireDate"
                       :locale="locale"
                       :first-day-of-week="$t('date.firstDayOfWeek')"/>
      </v-menu>
      <v-select v-if="!details"
                v-model="tribe"
                :items="tribes"
                :label="$t('Designated tribe')"
                item-text="name"
                return-object
                :rules="[ rules.required ]"
                class="required"
                required/>
      <v-text-field v-else-if="tribe" readonly :value="tribe.name" :label="$t('Designated tribe')"/>
      <v-text-field v-else readonly :label="$t('Designated tribe')"/>
      <v-select v-if="tribe && tribe.positions.length > 0 && !details"
                v-model="position"
                :items="tribe.positions"
                :label="$t('Designated position')"
                item-text="name"
                return-object/>
      <v-text-field v-else-if="tribe && tribe.positions.length" readonly :value="position.name"
                    :label="$t('Designated position')"/>
      <v-select v-if="!details"
                class="required"
                v-model="contractType"
                :items="contractTypes"
                :label="$t('Designated contract')"
                :rules="[ rules.required ]" required
                item-text="name"
                return-object/>
      <v-text-field v-else readonly :value="getContractType" :label="$t('Designated contract')"/>
      <v-select v-if="contractType === $t('OUTSOURCE')"
                class="required"
                v-model="outsourceSubType"
                :items="outsourceSubTypes"
                :label="$t('Designated outsource contract subtype')"
                :rules="[ rules.required ]" required
                item-text="name"
                return-object/>
      <template v-if="contractType === 'B2B LUMP SUM' || contractType === 'B2B HOURLY'">
        <v-text-field v-model="nip"
                      :readonly="details"
                      :label="$t('TIN')"/>
        <v-text-field v-model="firmName"
                      :readonly="details"
                      :label="$t('Firm name')"/>
        <v-text-field v-model="firmAddress"
                      :readonly="details"
                      :label="$t('Firm address')"/>
      </template>
    </v-form>
    <v-btn v-if="!details" color="success" :disabled="!formValid" @click="save" :loading="loading" block>
      {{ $t('Save') }}
    </v-btn>
    <div v-if="emailRepeated" class="card__div--info">
      {{ $t('Email address is already used in Adventure. We changed it already,' +
        ' please recheck it and confirm form again.') }}
    </div>
  </v-card-text>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import { mapState } from 'vuex';
  import moment from '@divante-adventure/work-moment';
  import EmployeeChooser from '../../utils/EmployeeChooser';
  import { PHONE_REGEX } from '../../../util/validateRules';

  const ONE = 1;
  const HR_ID = 8;

  export default {
    name: 'EditorContent',
    components: { EmployeeChooser },
    data() {
      return {
        locale: getSuggestedLanguage(),
        today: moment().format('YYYY-MM-DD'),
        id: -1,
        name: '',
        lastName: '',
        recruiter: {},
        gender: 0,
        dateOfBirth: '',
        dateOfWelcomeDay: '',
        privatePhone: '',
        privateEmail: '',
        source: '',
        details: false,
        pickerDateOfBirth: null,
        pickerDate: null,
        pickerHireDate: null,
        company: '',
        city: null,
        postalCode: null,
        street: null,
        country: null,
        email: '',
        hireDate: '',
        tribe: null,
        position: null,
        formValid: false,
        visibleHireDate: false,
        loading: false,
        emailRepeated: false,
        birthdayVisible: false,
        welcomeDayVisible: false,
        contractType: '',
        outsourceSubType: '',
        contractTypes: [
          this.$t('B2B LUMP SUM'),
          this.$t('B2B HOURLY'),
          this.$t('CLC LUMP SUM'),
          this.$t('CLC HOURLY'),
          this.$t('CoE'),
          this.$t('OUTSOURCE'),
        ],
        outsourceSubTypes: [
          this.$t('Outsourcing'),
          this.$t('Freelancing'),
          this.$t('Outsourcing-passive'),
        ],
        nip: '',
        firmName: '',
        firmAddress: '',
        genders: [
          {
            text: this.$t('Male'),
            value: 0,
          }, {
            text: this.$t('Female'),
            value: 1,
          },
        ],
        validateTelephoneNumber: t => {
          return t.match(PHONE_REGEX) || this.$t('Please enter correct phone number');
        },
        language: 'en',
        availableLanguages: [
          {
            text: this.$t('English'),
            value: 'en',
          }, {
            text: this.$t('Polish'),
            value: 'pl',
          },
        ],
        rules: {
          at: val => String(val).match(/.+@.+\..+/) !== null || this.$t('Incorrect email'),
          required: val => !!val || this.$t('Field is required'),
        },
      };
    },
    computed: {
      ...mapState({
        employee: state => state.Employees.loggedEmployee,
        tribes: state => state.Tribes.tribes,
        employees: state => state.Employees.employees,
        potentialEmployees: state => state.Hr.potentialEmployees,
        entries: state => state.Config.contentConfig,
      }),
      getGender() {
        return this.genders.filter(val => val.value === this.gender).map(val => val.text);
      },
      getContractType() {
        return this.contractTypes.filter(val => val === this.contractType);
      },
      hrEmployees() {
        if (this.employees.length > 0) {
          const hrEmployees = this.employees.filter(val => val.tribe && val.tribe.id === HR_ID);
          if (!this.employees.filter(val => val.tribe && val.tribe.id === HR_ID).includes(this.employee)) {
            hrEmployees.push(this.employee);
          }
          return hrEmployees;
        } else {
          return [];
        }
      },
    },
    watch: {
      birthdayVisible() {
        setTimeout(
          // eslint-disable-next-line no-return-assign
          () => (this.$refs.birthdayPicker.activePicker = 'YEAR')
        );
      },
      dateOfBirth() {
        this.birthdayVisible = false;
      },
      hireDate() {
        this.visibleHireDate = false;
      },
      name() {
        if (this.id === -1) {
          const name = this.name.charAt(0).toLowerCase().latinize() ? this.name.charAt(0).toLowerCase().latinize() : '';
          const lastname = this.lastName.toLowerCase().latinize() ? this.lastName.toLowerCase().latinize() : '';
          const domain = this.entries.filter(val => val.key === 'content.email_domain').map(val => val.value)[0];
          this.email = `${name + lastname + domain}`;
        }
      },
      lastName() {
        if (this.id === -1) {
          const name = this.name.charAt(0).toLowerCase().latinize() ? this.name.charAt(0).toLowerCase().latinize() : '';
          const lastname = this.lastName.toLowerCase().latinize() ? this.lastName.toLowerCase().latinize() : '';
          const domain = this.entries.filter(val => val.key === 'content.email_domain').map(val => val.value)[0];
          this.email = `${name + lastname + domain}`;
        }
      },
    },
    methods: {
      reload(employee) {
        this.loading = false;
        this.details = false;
        this.emailRepeated = false;
        if (typeof employee === 'undefined') {
          this.id = -1;
          this.pickerDateOfBirth = moment().format('YYYY-MM');
          this.pickerDate = moment().format('YYYY-MM-DD');
          this.pickerHireDate = moment().format('YYYY-MM-DD');
          this.recruiter = this.employee;
          this.name = '';
          this.lastName = '';
          this.email = '';
          this.gender = 0;
          this.dateOfBirth = '';
          this.dateOfWelcomeDay = '';
          this.hireDate = '';
          this.contractType = '';
          this.outsourceSubType = '';
          this.tribe = null;
          this.position = null;
          this.privatePhone = '';
          this.city = null;
          this.postalCode = null;
          this.street = null;
          this.country = null;
          this.privateEmail = '';
          this.source = null;
          this.company = null;
          this.nip = '';
          this.firmName = '';
          this.firmAddress = '';
          this.language = 'en';
        } else {
          this.pickerHireDate = moment(employee.hireDate).format('YYYY-MM-DD');
          this.pickerDate = moment(employee.welcomeDay).format('YYYY-MM-DD');
          this.pickerDateOfBirth = moment(employee.dateOfBirth).format('YYYY-MM');
          this.details = employee.details;
          this.id = employee.id;
          this.name = employee.name;
          this.lastName = employee.lastName;
          this.email = employee.email;
          this.hireDate = employee.hireDate;
          this.gender = employee.gender;
          this.dateOfBirth = employee.dateOfBirth;
          this.dateOfWelcomeDay = employee.welcomeDay;
          this.contractType = employee.contractType;
          this.tribe = employee.tribe;
          this.position = employee.position;
          this.privatePhone = employee.privatePhone;
          this.city = employee.city;
          this.postalCode = employee.postalCode;
          this.street = employee.street;
          this.country = employee.country;
          this.privateEmail = employee.privateEmail;
          this.source = employee.source;
          this.company = employee.company;
          this.recruiter = employee.recruiter;
          this.nip = employee.nip;
          this.firmName = employee.firmName;
          this.firmAddress = employee.firmAddress;
          this.language = employee.language;
          this.outsourceSubType = employee.outsourceSubType;
        }
      },
      async save() {
        this.loading = true;
        const data = {
          id: this.id,
          name: this.name.trim(),
          lastName: this.lastName.trim(),
          gender: this.gender,
          dateOfBirth: this.dateOfBirth,
          welcomeDay: this.dateOfWelcomeDay,
          email: this.email,
          contractType: this.$t(this.contractType),
          privatePhone: this.privatePhone,
          recruiterId: this.recruiter.id,
          privateEmail: this.privateEmail,
          nip: this.nip,
          firmName: this.firmName,
          firmAddress: this.firmAddress,
          language: this.language,
          outsourceSubType: this.$t(this.outsourceSubType),
        };
        if (this.hireDate) {
          data.hireDate = moment(this.hireDate, 'YYYY-MM-DD').format('YYYY-MM-DD');
        }
        if (this.source) {
          data.source = this.source;
        }
        if (this.city) {
          data.city = this.city;
        }
        if (this.postalCode) {
          data.postalCode = this.postalCode;
        }
        if (this.street) {
          data.street = this.street;
        }
        if (this.country) {
          data.country = this.country;
        }
        if (this.company) {
          data.company = this.company;
        }
        if (this.tribe) {
          data.tribeId = this.tribe.id;
        }
        if (this.position) {
          data.positionId = this.position.id;
        }
        if (this.id === -1) {
          const emails = [ ...this.employees.map(val => val.email), ...this.potentialEmployees.map(val => val.email) ];
          if (emails.includes(this.email)) {
            this.$store.commit('showSnackbar', {
              color: 'error',
              text: this.$t('Email address is already used in Adventure. Please recheck and confirm the form.'),
            });
            this.emailRepeated = true;
            this.changeEmail(ONE);
          } else {
            await this.$store.dispatch('Hr/createPotentialEmployee', data);
            this.$store.commit('showSnackbar', { color: 'success', text: this.$t('Potential person has been created') });
            this.$emit('close');
          }
        } else {
          await this.$store.dispatch('Hr/updatePotentialEmployee', data);
          this.$store.commit('showSnackbar', { color: 'success', text: this.$t('Data of potential person has been edited') });
          this.$emit('close');
        }
        this.loading = false;
      },
      changeEmail(number) {
        // first, try with first letters of name, i.e. for John Smith: jsmith@divante.pl, josmith@divante.pl,
        // johsmith@example.pl, johnsmith@example.pl
        const name = this.name.substring(0, number).toLowerCase().latinize()
          ? this.name.substring(0, number).toLowerCase().latinize() : '';
        const lastname = this.lastName.toLowerCase().latinize() ? this.lastName.toLowerCase().latinize() : '';
        const domain = this.entries.filter(val => val.key === 'content.email_domain').map(val => val.value)[0];
        this.email = `${name + lastname + domain}`;
        const emails = [ ...this.employees.map(val => val.email), ...this.potentialEmployees.map(val => val.email) ];
        if (emails.includes(this.email)) {
          this.changeEmail(number + 1);
        }
      },
    },
    mounted() {
      EventBus.$on(eventNames.showPotentialEditorContent, this.reload);
    },
    i18n: {
      messages: {
        pl: {
          'CoE': 'UoP',
          'CLC LUMP SUM': 'UCP RYCZAŁT',
          'CLC HOURLY': 'UCP GODZINOWE',
          'B2B LUMP SUM': 'B2B RYCZAŁT',
          'B2B HOURLY': 'B2B GODZINOWE',
          'OUTSOURCE': 'Outsourcing',
          'First name': 'Imię',
          'Last name': 'Nazwisko',
          'Yes': 'Tak',
          'No': 'Nie',
          'Gender': 'Płeć',
          'Private e-mail address': 'Prywatny adres mailowy',
          'Male': 'Mężczyzna',
          'Female': 'Kobieta',
          'Recruiter': 'Rekruter',
          'Private phone number': 'Prywatny numer telefonu',
          'Suggested e-mail address': 'Sugerowany adres e-mail',
          'Designated hire date': 'Planowana data zatrudnienia',
          'Designated tribe': 'Planowana praktyka',
          'Designated contract': 'Planowana umowa',
          'Designated position': 'Planowane stanowisko',
          'Save': 'Zapisz',
          'City': 'Miasto',
          'Postal code': 'Kod pocztowy',
          'Street and number': 'Ulica i numer',
          'Country': 'Kraj',
          'Source f.ex company website': 'Źródło np. strona firmowa',
          'Previous company': 'Poprzednia firma',
          'Please enter correct phone number': 'Wprowadź poprawny numer telefonu',
          'Incorrect email': 'Błędny adres mailowy',
          'Field is required': 'Pole jest wymagane',
          'Potential person has been created': 'Potencjalna osoba została utworzona',
          'Data of potential person has been edited': 'Dane potencjalnej osoby została zmienione',
          'Email address is already used in Adventure. We changed it already, please recheck it and confirm form again.': 'Adres email jest już używany w Adventure. Zmieniliśmy już na inny, sprawdź proszę i potwierdź formularz ponownie.',
          'Email address is already used in Adventure. Please recheck and confirm the form.': 'Adres email jest już używany w Adventure. Proszę ponownie sprawdzić i potwierdzić formularz',
          'Firm name': 'Nazwa firmy',
          'Firm address': 'Adres firmy',
          'TIN': 'NIP',
          'Date of birth': 'Data urodzenia',
          'Date of Welcome Day': 'Data Welcome Day',
          'Language': 'Język',
          'English': 'Angielski',
          'Polish': 'Polski',
          'Designated outsource contract subtype': 'Typ umowy outsource',
        },
        en: {
          'OUTSOURCE': 'Outsourcing',
          'Designated tribe': 'Designated practice',
        },
      },
    },
  };
</script>
<style scoped>
  .required >>> label::after {
    content: "*";
  }
  .required >>> label.v-label--active::after {
    color: red;
  }
  .card__div--info {
    color: red;
    margin-top: 10px;
  }
</style>
