<template>
  <v-tooltip v-if="showTooltip"
             :disabled="checkIfMobile"
             open-on-hover top right>
    <template v-slot:activator="{ on }">
      <v-avatar v-on="on">
        <v-icon @click="downloadAttachment">attach_file</v-icon>
      </v-avatar>
    </template>
    <span>
      {{ $t('Download attachment') }}
    </span>
  </v-tooltip>
</template>

<script>
  import { getSuggestedLanguage, isSupportedLanguage } from '../../../i18n/i18n';

  export default {
    name: 'RowAttachmentTooltip',
    props: {
      attachmentName: { type: String, required: true },
      attachmentId: { type: Number, required: true },
      agreement: { type: Object, required: true },
    },
    data() {
      return {
        language: getSuggestedLanguage(),
      };
    },
    computed: {
      checkIfMobile() {
        return this.$vuetify.breakpoint.xs;
      },
      attachmentLanguage() {
        return this.attachmentName.substr(0, 2).toLowerCase();
      },
      showTooltip() {
        const subtract = this.attachmentLanguage;
        const language = getSuggestedLanguage();
        return subtract === language || isSupportedLanguage(subtract) === false;
      },
    },
    methods: {
      downloadAttachment() {
        const realAttachmentId = this.agreement.attachmentsId[this.attachmentId];
        this.$store.dispatch('Agreements/downloadAttachment', realAttachmentId);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Download attachment': 'Pobierz załącznik',
        },
      },
    },
  };
</script>
