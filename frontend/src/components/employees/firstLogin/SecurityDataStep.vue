<template>
  <v-card-text :class="{'pa-1': $vuetify.breakpoint.xs}">
    <v-list two-line>
      <v-list-item>
        <v-list-item-action><v-icon>fingerprint</v-icon></v-list-item-action>
        <v-text-field v-model="employee.pin"
                      :append-icon="showPin ? 'mdi-eye' : 'mdi-eye-off'"
                      :label="$t('PIN number')"
                      :type="showPin ? 'text' : 'password'"
                      class="required"
                      maxlength="4"
                      :hint="$t('pin-hint')"
                      @click:append="showPin = !showPin"
                      persistent-hint/>
      </v-list-item>
    </v-list>
  </v-card-text>
</template>

<script>
  export default {
    name: 'SecurityDataStep',
    props: {
      value: { type: Object, default: () => ({}) },
    },
    data() {
      return {
        showPin: false,
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
          'PIN number': 'Numer PIN',
          'pin-hint': 'Numer PIN, składający się z czterech cyfr, stanowi drugi etap weryfikacji przy logowaniu do systemu.',
        },
        en: {
          'pin-hint': '4-digit PIN number is a second factor of authentication.',
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
