<template>
  <v-dialog v-model="modelWrapper" width="600">
    <v-card>
      <v-card-title class="title">
        {{ $t('Hardware assignment agreement') }}
        <v-spacer/>
        <v-btn @click="modelWrapper = false" icon rounded><v-icon>clear</v-icon></v-btn>
      </v-card-title>
      <v-divider/>
      <v-card-text>
        <agreement-info :agreement="agreement"/>
        <v-divider/>
        <agreement-downloads :agreement="agreement"/>
      </v-card-text>
      <v-card-actions>
        <password-dialog v-if="passwordDialogVisible"
                         v-model="passwordDialogVisible"
                         :action-name="$t('Sign agreement')"
                         @password-entered="sign"/>
        <v-btn color="success" @click="passwordDialogVisible = true" block>{{ $t('Sign') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import AgreementInfo from './AgreementInfo';
  import AgreementDownloads from './AgreementDownloads';
  import PasswordDialog from './PasswordDialog';

  export default {
    name: 'AgreementDialog',
    components: { PasswordDialog, AgreementDownloads, AgreementInfo },
    props: {
      value: { type: Boolean, required: true },
      agreement: { type: Object, required: true },
    },
    data() {
      return {
        passwordDialogVisible: false,
        loading: false,
      };
    },
    computed: {
      modelWrapper: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
    methods: {
      async sign(password) {
        this.loading = true;
        const { id } = this.agreement;
        try {
          await this.$store.dispatch('Hardware/signAgreement', { id, password });
          this.$store.commit('showSnackbar', { text: this.$t('Agreement signed correctly'), color: 'success' });
          this.modelWrapper = false;
        } catch (e) {
          this.$store.commit('showSnackbar', { text: this.$t('Wrong password'), color: 'error' });
          this.passwordDialogVisible = true;
        }
        this.loading = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Hardware assignment agreement': 'Umowa użyczenia sprzętu',
          'Sign agreement': 'Podpisz umowę',
          'Sign': 'Podpisz',
          'Agreement signed correctly': 'Umowa podpisana pomyślnie',
          'Wrong password': 'Błędne hasło',
        },
      },
    },
  };
</script>
