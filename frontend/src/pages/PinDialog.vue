<template>
  <v-dialog v-model="dialogVisible" width="400" id="dialog-pin" persistent>
    <v-card>
      <v-card-title>{{ $t('Enter your PIN number') }}</v-card-title>
      <v-card-text>
        <v-text-field :disabled="locked || inputDisabled"
                      mask="####"
                      v-model="pin"
                      @input="inputErrors = []"
                      :label="$t('PIN')"
                      autofocus
                      :error-messages="inputErrors"
                      ref="pinField"
                      type="password"
                      outlined/>
        <v-btn :disabled="!pinEntered"
               :loading="loading"
               color="primary"
               ref="verifyButton"
               @click="verify"
               block>
          {{ $t('Verify') }}
        </v-btn>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../eventbus';
  import DataStorage from '@divante/data-storage';
  import { mapState } from 'vuex';

  const PIN_VERIFICATION_FAILED = 'not verified';
  const PIN_ACCOUNT_BLOCKED = 'account blocked';

  export default {
    name: 'PinDialog',
    data() {
      return {
        dialogVisible: false,
        pin: '',
        loading: false,
        inputErrors: [],
        inputDisabled: false,
      };
    },
    computed: {
      ...mapState({
        locked: state => state.Employees.employee.locked,

      }),
      pinEntered() {
        return this.pin.length === 4;
      },
    },
    watch: {
      pin() {
        if (this.pin.length === 4) {
          this.$nextTick(() => this.$refs.verifyButton.$el.focus());
        }
      },
      locked(val) {
        if (val) {
          this.inputErrors = [
            this.$t(
              'Your account has been blocked. Please try to contact with administration. Check your email inbox.'
            ),
          ];
        }
      },
    },
    methods: {
      show() {
        if (this.dialogVisible) {
          return;
        }
        this.pin = '';
        this.dialogVisible = true;
        this.$nextTick(() => this.$refs.pinField.focus());
      },
      async verify() {
        this.loading = true;
        try {
          await this.$store.dispatch('Employees/verifyPin', this.pin);
          const storage = new DataStorage();
          storage.setValue('pinSet', true);
          this.$store.commit('setPinEntered');
          this.$store.dispatch('loadGlobalSearch');
          this.loading = false;
          this.dialogVisible = false;
          this.$router.push('/dashboard');
        } catch (e) {
          this.loading = false;
          if (e === PIN_VERIFICATION_FAILED) {
            this.inputErrors = [
              this.$t('Invalid PIN number'),
            ];
          } else if (e === PIN_ACCOUNT_BLOCKED) {
            this.inputErrors = [
              this.$t(
                'Your account has been blocked. Please try to contact with administration. Check your email inbox.'
              ),
            ];
            this.inputDisabled = true;
          }
          this.pin = '';
        }
      },
    },
    mounted() {
      EventBus.$on(eventNames.showPinWindow, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Enter your PIN number': 'Podaj swój numer PIN',
          'Verify': 'Weryfikuj',
          'PIN': 'PIN',
          'Invalid PIN number': 'Niepoprawny numer PIN',
          'Your account has been blocked. Please try to contact with administration. Check your email inbox.': 'Twoje konto zostało zablokowane. Prosimy o kontakt z administracją. Sprawdź swoją skrzynkę pocztową',
        },
      },
    },
  };
</script>
