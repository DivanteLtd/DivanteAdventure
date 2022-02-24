<template>
  <v-card-text :class="{'pa-1': $vuetify.breakpoint.xs}">
    <v-alert :value="true" type="info">{{ $t('alert-text') }}</v-alert>
    <v-list two-line>
      <v-list-item>
        <v-list-item-action><v-icon>person</v-icon></v-list-item-action>
        <v-text-field v-model="employee.emergencyFirstName" :label="$t('First name')" class="required mr-3"/>
        <v-text-field v-model="employee.emergencyLastName" :label="$t('Last name')" class="required"/>
      </v-list-item>
      <v-list-item>
        <v-list-item-action><v-icon>phone</v-icon></v-list-item-action>
        <v-text-field
          v-model="employee.emergencyPhone"
          :label="$t('Phone number')"
          class="required"
          :validate="validateTelephoneNumber"/>
      </v-list-item>
    </v-list>
  </v-card-text>
</template>

<script>
  import { PHONE_REGEX } from '../../../util/validateRules';

  export default {
    name: 'EmergencyDataStep',
    props: {
      value: { type: Object, default: () => ({}) },
    },
    data() {
      return {
        validateTelephoneNumber: t => {
          return t.match(PHONE_REGEX) || this.$t('Please enter correct phone number');
        },
      };
    },
    computed: {
      employee: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
    i18n: {
      messages: {
        pl: {
          'alert-text': 'Podaj dane kontaktowe osoby, którą powinniśmy powiadomić w razie wypadku lub innych nagłych zdarzeń.',
          'First name': 'Imię',
          'Last name': 'Nazwisko',
          'Phone number': 'Numer telefonu',
          'Please enter correct phone number': 'Numer telefonu nie jest poprawny',
        },
        en: {
          'alert-text': 'Please fill these fields with contact to person who we should notify in case of emergency.',
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
