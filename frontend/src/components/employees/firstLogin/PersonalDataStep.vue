<template>
  <v-card-text :class="{'pa-1': $vuetify.breakpoint.xs}" >
    <v-alert :value="true" type="info">{{ $t('alert-text') }}</v-alert>
    <v-list two-line>
      <v-list-item>
        <v-list-item-action><v-icon>language</v-icon></v-list-item-action>
        <v-select :items="languages" :click="selectLanguage(employee.language)" v-model="employee.language"
                  :label="'Language / Język'" class="required"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>person</v-icon></v-list-item-action>
        <v-text-field v-model.trim="employee.name" :label="$t('First name')" class="required mr-3"/>
        <v-text-field v-model.trim="employee.lastName" :label="$t('Last name')" class="required"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>grade</v-icon></v-list-item-action>
        <v-menu ref="birthdayVisible"
                v-model="birthdayVisible"
                :close-on-content-click="false" max-width="300" offset-y>
          <template v-slot:activator="{ on }">
            <v-text-field v-on="on"
                          v-model="employee.dateOfBirth"
                          :label="$t('Date of birth')"
                          class="required"
                          :disabled="!!firstDataDateOfBirth"
                          readonly/>
          </template>
          <v-date-picker color="indigo" v-model="employee.dateOfBirth"
                         :locale="locale"
                         ref="birthdayPicker"
                         :first-day-of-week="$t('date.firstDayOfWeek')"
                         min="1900-01-01"
                         :max="new Date().toISOString().substr(0, 10)"
                         no-title>
            <v-spacer/>
            <v-btn style="margin-top: -20px" text color="primary" @click="$refs.birthdayVisible.save(employee.hiredAt)">
              OK
            </v-btn>
          </v-date-picker>
        </v-menu>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>email</v-icon></v-list-item-action>
        <v-text-field :disabled="!!employee.email"
                      v-model.trim="employee.email"
                      :label="$t('E-mail address')"
                      class="required"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>phone</v-icon></v-list-item-action>
        <v-text-field
          v-model="employee.privatePhone"
          :label="$t('Private phone number')"
          :validate="validateTelephoneNumber"
          class="required"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>supervised_user_circle</v-icon></v-list-item-action>
        <v-select :items="genders"
                  v-model="employee.gender"
                  :label="$t('Gender')"
                  :disabled="Number.isInteger(genderVisible)"
                  class="required"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>supervised_user_circle</v-icon></v-list-item-action>
        <v-select :items="shoeSizes"
                  v-model="employee.shoeSize"
                  item-text="value"
                  item-value="value"
                  :label="$t('Shoe size')"
                  class="required"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>supervised_user_circle</v-icon></v-list-item-action>
        <v-select :items="shirtSizes"
                  v-model="employee.sweatshirtSize"
                  item-text="value"
                  item-value="value"
                  :label="$t('Sweatshirt size')"
                  class="required"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>supervised_user_circle</v-icon></v-list-item-action>
        <v-select :items="shirtSizes"
                  v-model="employee.shirtSize"
                  item-text="value"
                  item-value="value"
                  :label="$t('Shirt size')"
                  class="required"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>directions_car</v-icon></v-list-item-action>
        <v-text-field v-model="employee.licencePlate"
                      :label="$t('Car plate')"
                      :hint="$t('licence-plate-hint')"
                      persistent-hint/>
      </v-list-item>
      <v-subheader :class="{'pa-0': $vuetify.breakpoint.smAndUp}">
        {{ $t('Correspondence address') }}
      </v-subheader>
      <v-list-item>
        <v-list-item-action><v-icon>location_on</v-icon></v-list-item-action>
        <v-text-field v-model="employee.street" :label="$t('Street and number')" class="required"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>location_city</v-icon></v-list-item-action>
        <v-text-field v-model="employee.city" :label="$t('City')" class="required"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>post_add</v-icon></v-list-item-action>
        <v-text-field v-model="employee.postalCode" :label="$t('Postal code')" class="required"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>vpn_lock</v-icon></v-list-item-action>
        <v-text-field v-model="employee.country" :label="$t('Country')" class="required"/>
      </v-list-item>
    </v-list>
  </v-card-text>
</template>

<script>
  import { PHONE_REGEX } from '../../../util/validateRules';
  import { getSuggestedLanguage, languages, setLanguage } from '../../../i18n/i18n';

  export default {
    name: 'PersonalDataStep',
    props: {
      value: { type: Object, default: () => ({}) },
    },
    data() {
      return {
        languages,
        firstDataDateOfBirth: '',
        genderVisible: '',
        birthdayVisible: false,
        locale: getSuggestedLanguage(),
        shirtSizes: [
          {
            value: 'XS',
          },
          {
            value: 'S',
          },
          {
            value: 'M',
          },
          {
            value: 'L',
          },
          {
            value: 'XL',
          },
          {
            value: 'XXL',
          },
          {
            value: 'XXXL',
          },
        ],
        shoeSizes: [
          {
            value: '36',
          },
          {
            value: '36',
          },
          {
            value: '37',
          },
          {
            value: '38',
          },
          {
            value: '39',
          },
          {
            value: '40',
          },
          {
            value: '41',
          },
          {
            value: '42',
          },
          {
            value: '43',
          },
          {
            value: '44',
          },
          {
            value: '45',
          },
          {
            value: '46',
          },
          {
            value: '47',
          },
          {
            value: '48',
          },
          {
            value: '49',
          },
          {
            value: '50',
          },
        ],
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
      };
    },
    computed: {
      employee: {
        get() {
          if (this.firstDataDateOfBirth === '') {
            this.firstDataDateOfBirth = this.value.dateOfBirth;
          }
          if (this.genderVisible === '') {
            this.genderVisible = this.value.gender;
          }
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
    watch: {
      birthdayVisible() {
        setTimeout(
          this.setActivePicker
        );
      },
    },
    methods: {
      setActivePicker() {
        this.$refs.birthdayPicker.activePicker = 'YEAR';
      },
      async selectLanguage(languageCode) {
        if (languageCode !== this.locale && this.employee.language !== undefined) {
          const data = { language: languageCode };
          await this.$store.dispatch('Employees/saveEmployee', data);
          setLanguage(languageCode);
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'First name': 'Imię',
          'Last name': 'Nazwisko',
          'E-mail address': 'Adres e-mail',
          'Private phone number': 'Prywatny numer telefonu',
          'Gender': 'Płeć',
          'Correspondence address': 'Adres do korespondencji',
          'City': 'Miasto',
          'Postal code': 'Kod pocztowy',
          'Street and number': 'Ulica i numer',
          'Country': 'Kraj',
          'Date of birth': 'Data urodzenia',
          'alert-text': 'Część informacji została dostarczona przez Google.',
          'Male': 'Mężczyzna',
          'Female': 'Kobieta',
          'Please enter correct phone number': 'Numer telefonu nie jest poprawny',
          'Car plate': 'Numer rejestracyjny pojazdu',
          'licence-plate-hint': 'Jeżeli używasz firmowego parkingu, informacja ta będzie przydatna dla innych kierowców w celu znalezienia właściciela pojazdu.',
          'Shirt size': 'Rozmiar koszulki',
          'Sweatshirt size': 'Rozmiar bluzy',
          'Shoe size': 'Rozmiar buta',
        },
        en: {
          'alert-text': 'Some of information is supplied by Google.',
          'licence-plate-hint': 'If you are using our parking, that information will be helpful for other drivers to find owner of a car.',
        },
      },
    },
  };
</script>

<style scoped>
  .required >>> label::after {
    content: ' *';
  }
  .required >>> label.v-label--active::after {
    color: red;
  }
</style>
