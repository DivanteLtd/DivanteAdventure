<template>
  <v-dialog id="hardware-password-dialog" v-model="dialogVisible" v-if="dialogVisible" width="600" persistent>
    <v-card>
      <v-toolbar color="transparent" class="headline" card dense>
        <v-row>
          {{ $t('Generated password') }}
        </v-row>
        <v-spacer/>
        <v-btn icon @click="dialogVisible = false"><v-icon>close</v-icon></v-btn>
      </v-toolbar>
      <v-divider/>
      <v-card-text>
        {{ $t('message-start') }}
        <div class="generated-password mt-2 mb-2">{{ password }}</div>
        {{ $t('message-end') }}
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>

  export default {
    name: 'PasswordDialog',
    data() {
      return {
        dialogVisible: false,
        password: '',
      };
    },
    methods: {
      show({ password }) {
        this.password = password;
        this.dialogVisible = true;
      },
    },
    i18n: {
      messages: {
        pl: {
          'message-start': 'W celu ochrony wrażliwych danych osobowych podgląd i podpis umowy użyczenia sprzętu wymaga podania hasła. Wygenerowane hasło to:',
          'message-end': 'Hasło to zostało wysłane drogą mailową do osób upoważnionych.',
          'Generated password': 'Wygenerowane hasło',
        },
        en: {
          'message-start': 'To protect sensitive personal data, view of agreement and signing that agreement requires passing password. Generated password is:',
          'message-end': 'This password was sent via e-mail to all authorized people.',
        },
      },
    },
  };
</script>

<style scoped>
  div.generated-password {
    font-family: monospace;
    font-size: 1.2em;
    text-align: center;
    border: 1px solid black;
  }
</style>
