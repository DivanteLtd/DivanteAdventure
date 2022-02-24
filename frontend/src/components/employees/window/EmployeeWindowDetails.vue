<template>
  <v-container :class="{'pa-0': $vuetify.breakpoint.xs}">
    <v-row wrap class="justify-center mx-0">
      <v-col class="employee-photo" cols="8" sm="6" md="4">
        <img v-if="employee.photo !== '' && employee.photo"
             :src="employee.photo"
             :alt="employee.name + ' ' + employee.lastName"
             :title="employee.name + ' ' + employee.lastName"
             @error="employee.photo = ''"
             class="logo"/>
        <v-icon v-else large>perm_identity</v-icon>
      </v-col>
      <v-col cols="12" sm="6" md="8">
        <v-list two-line>
          <simple-edit-field :employee="employee"
                             :editable="isSuperAdmin"
                             :employee-to-value="(e) => e.name + ' ' + e.lastName"
                             :input-to-value="inputToEmployeeName"
                             :label="$t('Name and lastName')"
                             :validate="validateName"
                             icon="person"/>
          <simple-edit-field :employee="employee"
                             field="email"
                             :label="$t('E-mail address')"
                             icon="mail"/>
          <simple-edit-field :employee="employee"
                             :editable="false"
                             :validate="validateTelephoneNumber"
                             field="phone"
                             :label="$t('Business phone number')"
                             icon="phone"/>
          <v-list-item :disabled="loading" v-if="isManager && employee.contract" class="dayOff"
                       @click="goToEmployeeDaysOff()">
            <v-list-item-action>
              <v-icon>event</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              {{ daysOffMessage }}
              <v-list-item-subtitle v-if="loading">
                {{ $t('Option will be available after the profile has been loaded') }}
              </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <v-list-item class="dayOff" @click="openPinDialog">
            <v-list-item-action>
              <v-icon>fingerprint</v-icon>
            </v-list-item-action>
            <v-list-item-title>{{ $t('Password change') }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-col>
    </v-row>
    <v-dialog v-model="pinDialog" max-width="600px">
      <v-card>
        <v-card-title class="title">
          {{ $t('Password change') }}
          <v-spacer/>
          <v-btn @click="pinDialog = false" icon rounded><v-icon>clear</v-icon></v-btn>
        </v-card-title>
        <v-card-text>
          <v-text-field v-model="oldPin"
                        :append-icon="showOldPin ? 'mdi-eye' : 'mdi-eye-off'"
                        :label="$t('Provide old 4-digit PIN number')"
                        :type="showOldPin ? 'text' : 'password'"
                        class="required"
                        maxlength="4"
                        @click:append="showOldPin = !showOldPin"
                        persistent-hint/>
          <v-text-field v-model="pin"
                        :append-icon="showNewPin ? 'mdi-eye' : 'mdi-eye-off'"
                        :label="$t('Provide new 4-digit PIN number')"
                        :type="showNewPin ? 'text' : 'password'"
                        class="required"
                        maxlength="4"
                        @click:append="showNewPin = !showNewPin"
                        persistent-hint/>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="pinDialog = false">{{ $t('Close') }}</v-btn>
          <v-btn color="blue darken-1"
                 :disabled="validatePin"
                 :loading="loadingPin"
                 @click="save"
                 text>
            {{ $t('Save') }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
  import SimpleEditField from './SimpleEditField';
  import { mapGetters } from 'vuex';
  import { EventBus, eventNames } from '../../../eventbus';
  import { PHONE_REGEX } from '../../../util/validateRules';

  export default {
    name: 'EmployeeWindowDetails',
    components: { SimpleEditField },
    props: {
      loading: { type: Boolean, default: false },
      employee: { type: Object, required: true },
      editable: { type: Boolean, default: false },
    },
    data() {
      return {
        pinDialog: false,
        showOldPin: false,
        showNewPin: false,
        loadingPin: false,
        oldPin: '',
        pin: '',
        validateNotEmpty: t => !!t || 'Field cannot be empty',
        validateName: t => {
          const [ nameFromInput ] = t.split(' ', 2);
          const name = nameFromInput.trim();
          const lastName = t.substring(nameFromInput.length + 1).trim();
          return (name.length > 0 && lastName.length > 0) || this.$t('Please enter name and lastName divided by space');
        },
        validateTelephoneNumber: t => {
          if (t.length > 0) {
            return t.match(PHONE_REGEX) || this.$t('Please enter correct phone number');
          }
          return true;
        },
      };
    },
    computed: {
      ...mapGetters({
        isManager: 'Authorization/isManager',
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
      validatePin() {
        return !this.oldPin.match(/^[0-9]{4}$/) || !this.pin.match(/^[0-9]{4}$/);
      },
      daysOffMessage() {
        if (this.employee.id !== -1) {
          switch (this.employee.contract.name) {
            case 'CoE': return this.$t('Leave days');
            case 'CLC LUMP SUM':
            case 'B2B LUMP SUM': return this.$t('Free days');
            default: return this.$t('Unavailability days');
          }
        } else {
          return '';
        }
      },
    },
    methods: {
      openPinDialog() {
        this.oldPin = '';
        this.pin = '';
        this.pinDialog = true;
      },
      async save() {
        this.loadingPin = true;
        try {
          const { oldPin, pin } = this;
          await this.$store.dispatch('Employees/saveEmployee', { oldPin, pin });
          this.$store.commit('showSnackbar', {
            text: this.$t('Password has been changed'),
            color: 'success',
          });
          this.pinDialog = false;
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Old password provided is incorrect'),
            color: 'error',
          });
        }
        this.loadingPin = false;
      },
      inputToEmployeeName(input) {
        const [ name ] = input.split(' ', 2);
        const lastName = input.substring(name.length + 1);
        return { name: name.trim(), lastName: lastName.trim() };
      },
      async goToEmployeeDaysOff() {
        const path = `${this.$route.name}`;
        EventBus.$emit(eventNames.redirect, path);
        this.$emit('close');
      },
    },
    i18n: {
      messages: {
        pl: {
          'Name and lastName': 'Imię i nazwisko',
          'E-mail address': 'Adres e-mail',
          'Business phone number': 'Służbowy numer telefonu',
          'Field cannot be empty': 'Pole nie może być puste',
          'Leave days': 'Dni urlopowe',
          'Free days': 'Dni wolne',
          'Provide old 4-digit PIN number': 'Podaj stary 4 cyfrowy numer PIN',
          'Provide new 4-digit PIN number': 'Podaj nowy 4 cyfrowy numer PIN',
          'Password change': 'Zmiana hasła',
          'Unavailability days': 'Dni niedostępności',
          'Please enter correct phone number': 'Wprowadź poprawny numer telefonu',
          'Please enter name and lastName divided by space': 'Wprowadź imię i nazwisko oddzielone spacją',
          'Option will be available after the profile has been loaded': 'Opcja będzie dostępna po załadowaniu profilu',
          'Close': 'Zamknij',
          'Password has been changed': 'Hasło zostało zmienione',
          'Old password provided is incorrect': 'Podane stare hasło jest nieprawidłowe',
          'Save': 'Zapisz',
        },
      },
    },
  };
</script>
<style scoped>
  img.logo {
    height: auto;
    width: 90%;
    max-width: 560px;
  }
  .dayOff {
    cursor: pointer;
    font-size: 16px;
  }
  .employee-photo{
    display: flex;
    justify-content: center;
  }
</style>
