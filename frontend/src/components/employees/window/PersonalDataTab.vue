<template>
  <v-list two-line>
    <simple-edit-field v-if="editable"
                       :employee="employee"
                       :editable="editable"
                       field="privatePhone"
                       :validate="validateTelephoneNumber"
                       :label="$t('Private phone number')"
                       icon="phone"/>
    <simple-edit-field :employee="employee"
                       :editable="editable"
                       field="licencePlate"
                       :label="$t('Car plate')"
                       icon="directions_car"/>
    <select-edit-field v-if="(currentUserId === employee.id) || isSuperAdmin || isHr"
                       :employee="employee"
                       :editable="editable"
                       field="shoeSize"
                       :label="$t('Shoe size')"
                       :values="shoeSizes"
                       icon="supervised_user_circle"/>
    <select-edit-field v-if="(currentUserId === employee.id) || isSuperAdmin || isHr"
                       :employee="employee"
                       :editable="editable"
                       field="sweatshirtSize"
                       :label="$t('Sweatshirt size')"
                       :values="shirtSizes"
                       icon="supervised_user_circle"/>
    <select-edit-field v-if="(currentUserId === employee.id) || isSuperAdmin || isHr"
                       :employee="employee"
                       :editable="editable"
                       field="shirtSize"
                       :label="$t('Shirt size')"
                       :values="shirtSizes"
                       icon="supervised_user_circle"/>
    <select-edit-field :employee="employee"
                       :editable="editable"
                       field="gender"
                       :label="$t('Gender')"
                       :values="genders"
                       icon="supervised_user_circle"/>
    <select-edit-field v-if="isSuperAdmin"
                       :employee="employee"
                       :editable="editable"
                       field="childCare"
                       :label="$t('Child care')"
                       :values="childCares"
                       icon="child_care"/>
    <select-edit-field v-if="isHr"
                       :employee="employee"
                       :editable="editable"
                       field="student"
                       :label="$t('Is studying')"
                       :values="isStudent"
                       icon="person"/>
    <select-edit-field v-if="isHr"
                       :employee="employee"
                       :editable="editable"
                       field="taxDeductibleCosts"
                       :label="$t('Tax deductible costs')"
                       :values="deductibles"
                       icon="person"/>
    <template v-if="editable || isTribeMaster || isHr">
      <v-subheader :class="{'pa-0': $vuetify.breakpoint.smAndUp}">
        {{ $t('Correspondence address') }}
      </v-subheader>
      <div v-for="(item, idx) in editableFields" :key="idx">
        <simple-edit-field :employee="employee"
                           :editable="editable"
                           :field="item.field"
                           :label="item.label"
                           :icon="item.icon"/>
      </div>
      <v-subheader :class="{'pa-0': $vuetify.breakpoint.smAndUp}">
        {{ $t('Work address') }}
      </v-subheader>
      <v-switch
        v-model="workAddress"
        :label="$t('work address is same as correspondence address')"
      ></v-switch>
      <div v-for="(item, idx) in workEditableFields" :key="idx">
        <simple-edit-field v-if="workAddress === false"
                           :employee="employee"
                           :editable="editable"
                           :field="item.field"
                           :label="item.label"
                           :icon="item.icon"/>
      </div>
      <v-subheader :class="{'pa-0': $vuetify.breakpoint.smAndUp}">
        {{ $t('Contact in case of emergency') }}
      </v-subheader>
      <simple-edit-field :employee="employee"
                         :editable="editable"
                         :employee-to-value="(e) => (e.emergencyFirstName || '') + ' ' + (e.emergencyLastName || '')"
                         :input-to-value="inputToEmergencyName"
                         :label="$t('Name and lastName')"
                         :validate="validateName"
                         icon="person"/>
      <simple-edit-field v-if="editable"
                         :employee="employee"
                         :editable="editable"
                         :validate="validateTelephoneNumber"
                         field="emergencyPhone"
                         :label="$t('Phone number')"
                         icon="phone"/>
    </template>
  </v-list>
</template>

<script>
  import SimpleEditField from './SimpleEditField';
  import SelectEditField from './SelectEditField';
  import { mapGetters } from 'vuex';
  import { PHONE_REGEX } from '../../../util/validateRules';

  export default {
    name: 'PersonalDataTab',
    components: { SelectEditField, SimpleEditField },
    props: {
      employee: { type: Object, required: true },
      editable: { type: Boolean, default: false },
    },
    data() {
      return {
        workAddress: false,
        shirtSizes: [
          {
            id: 'XS',
            name: 'XS',
          },
          {
            id: 'S',
            name: 'S',
          },
          {
            id: 'M',
            name: 'M',
          },
          {
            id: 'L',
            name: 'L',
          },
          {
            id: 'XL',
            name: 'XL',
          },
          {
            id: 'XXL',
            name: 'XXL',
          },
          {
            id: 'XXXL',
            name: 'XXXL',
          },
        ],
        shoeSizes: [
          {
            id: '35',
            name: '35',
          },
          {
            id: '36',
            name: '36',
          },
          {
            id: '37',
            name: '37',
          },
          {
            id: '38',
            name: '38',
          },
          {
            id: '39',
            name: '39',
          },
          {
            id: '40',
            name: '40',
          },
          {
            id: '41',
            name: '41',
          },
          {
            id: '42',
            name: '42',
          },
          {
            id: '43',
            name: '43',
          },
          {
            id: '44',
            name: '44',
          }, {
            id: '45',
            name: '45',
          }, {
            id: '46',
            name: '46',
          }, {
            id: '47',
            name: '47',
          },
          {
            id: '48',
            name: '48',
          },
          {
            id: '49',
            name: '49',
          },
          {
            id: '50',
            name: '50',
          },
        ],
        genders: [{
          id: 0,
          name: this.$t('Male'),
        }, {
          id: 1,
          name: this.$t('Female'),
        }],
        yesNo: [
          {
            id: 0,
            name: this.$t('Yes'),
          }, {
            id: 1,
            name: this.$t('No'),
          },
        ],
        isStudent: [
          {
            id: true,
            name: this.$t('Yes'),
          }, {
            id: false,
            name: this.$t('No'),
          },
        ],
        childCares: [{
          id: 0,
          name: this.$t('Not entitled'),
        }, {
          id: 1,
          name: this.$t('Entitled'),
        }],
        deductibles: [
          {
            id: 0,
            name: this.$t('None'),
          },
          {
            id: 1,
            name: this.$t('50%'),
          },
        ],
        validateNotEmpty: t => !!t || 'Field cannot be empty',
        validateTelephoneNumber: t => {
          return t.match(PHONE_REGEX) || this.$t('Please enter correct phone number');
        },
        validateName: t => {
          const [ nameFromInput ] = t.split(' ', 2);
          const name = nameFromInput.trim();
          const lastName = t.substring(nameFromInput.length + 1).trim();
          return (name.length > 0 && lastName.length > 0) || this.$t('Please enter name and lastName divided by space');
        },
      };
    },
    computed: {
      ...mapGetters({
        isTribeMaster: 'Authorization/isTribeMaster',
        isHr: 'Authorization/isHr',
        isSuperAdmin: 'Authorization/isSuperAdmin',
        currentUserId: 'Authorization/getUserId',
      }),
      editableFields() {
        return [{
          field: 'street', label: this.$t('Street and number'), icon: 'location_on',
        }, {
          field: 'city', label: this.$t('City'), icon: 'location_city',
        }, {
          field: 'postalCode', label: this.$t('Postal code'), icon: 'post_add',
        }, {
          field: 'country', label: this.$t('Country'), icon: 'vpn_lock',
        }];
      },
      workEditableFields() {
        return [{
          field: 'workStreet', label: this.$t('Street and number'), icon: 'location_on',
        }, {
          field: 'workCity', label: this.$t('City'), icon: 'location_city',
        }, {
          field: 'workPostalCode', label: this.$t('Postal code'), icon: 'post_add',
        }, {
          field: 'workCountry', label: this.$t('Country'), icon: 'vpn_lock',
        }];
      },
    },
    watch: {
      workAddress() {
        if (this.workAddress === true) {
          const data = {
            id: this.employee.id,
            workStreet: this.employee.street,
            workCity: this.employee.city,
            workPostalCode: this.employee.postalCode,
            workCountry: this.employee.country,
          };
          this.$store.dispatch('Employees/saveEmployee', data);
          this.employee.street = data.workStreet;
          this.employee.city = data.workCity;
          this.employee.postalCode = data.workPostalCode;
          this.employee.country = data.workCountry;
        }
      },
    },
    methods: {
      inputToEmergencyName(input) {
        const [ name ] = input.split(' ', 2);
        const lastName = input.substring(name.length + 1);
        return { emergencyFirstName: name.trim(), emergencyLastName: lastName.trim() };
      },
    },
    i18n: {
      messages: {
        pl: {
          'Private phone number': 'Prywatny numer telefonu',
          'City': 'Miasto',
          'Car plate': 'Numer rejestracyjny pojazdu',
          'Contact in case of emergency': 'Kontakt w razie nagłego wypadku',
          'Name and lastName': 'Imię i nazwisko',
          'Phone number': 'Numer telefonu',
          'Gender': 'Płeć',
          'Postal code': 'Kod pocztowy',
          'Street and number': 'Ulica i numer',
          'Country': 'Kraj',
          'Male': 'Mężczyzna',
          'Female': 'Kobieta',
          'Correspondence address': 'Adres do korespondencji',
          'Please enter correct phone number': 'Wprowadź poprawny numer telefonu',
          'Please enter name and lastName divided by space': 'Wprowadź imię i nazwisko oddzielone spacją',
          'Child care': 'Opieka nad dzieckiem',
          'Entitled': 'Przysługuje',
          'Not entitled': 'Nie przysługuje',
          'Work address': ' Adres miejsca pracy',
          'Is studying': 'Czy studiuje',
          'Yes': 'Tak',
          'No': 'Nie',
          'Tax deductible costs': 'Koszt uzyskania przychodu',
          'None': 'Nie dotyczy',
          'Shirt size': 'Rozmiar koszulki',
          'Sweatshirt size': 'Rozmiar bluzy',
          'Shoe size': 'Rozmiar buta',
        },
      },
    },
  };
</script>
