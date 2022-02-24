<template>
  <v-list-item>
    <password-dialog v-if="passwordDialogVisible"
                     :action-name="$t('Download')"
                     @password-entered="download"
                     v-model="passwordDialogVisible"/>
    <v-list-item-action/>
    <v-list-item-content>
      <v-list-item-title class="mt-2">{{ label }}</v-list-item-title>
    </v-list-item-content>
    <v-list-item-action class="mt-2">
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on"
                 :loading="loading"
                 @click="passwordDialogVisible = true"
                 icon>
            <v-icon large>cloud_download</v-icon>
          </v-btn>
        </template>
        {{ $t('Download') }}
      </v-tooltip>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
  import PasswordDialog from './PasswordDialog';

  export default {
    name: 'AgreementDownloadLink',
    components: { PasswordDialog },
    props: {
      agreement: { type: Object, required: true },
      language: { type: String, required: true },
    },
    data() {
      return {
        passwordDialogVisible: false,
        loading: false,
      };
    },
    computed: {
      label() {
        switch (this.language) {
          case 'en': return this.$t('Agreement in English');
          case 'pl': return this.$t('Agreement in Polish');
          default: return 'N/A';
        }
      },
    },
    methods: {
      async download(password) {
        this.loading = true;
        const { id } = this.agreement;
        const language = this.language;
        await this.$store.dispatch('Hardware/downloadAgreement', { id, password, language });
        this.loading = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Agreement in Polish': 'Umowa w języku polskim',
          'Agreement in English': 'Umowa w języku angielskim',
          'Download': 'Pobierz',
        },
      },
    },
  };
</script>
