<template>
  <v-list-item @click="agreementDialogVisible = true">
    <agreement-dialog v-if="agreementDialogVisible" v-model="agreementDialogVisible" :agreement="agreement"/>
    <v-list-item-action>
      <v-icon color="primary">assignment</v-icon>
    </v-list-item-action>
    <v-list-item-content>
      <v-list-item-title>
        {{ agreement.manufacturer }} {{ agreement.model }} (S/N {{ agreement.serialNumber }})
      </v-list-item-title>
      <v-list-item-subtitle v-if="!agreement.signedByHelpdesk">
        {{ $t('For:') }} {{ agreement.name }} {{ agreement.lastName }}
      </v-list-item-subtitle>
      <template v-if="$vuetify.breakpoint.xs">
        {{ $t('data-info', { date: agreement.createdAt }) }}
      </template>
    </v-list-item-content>
    <template v-if="$vuetify.breakpoint.smAndUp">
      {{ $t('data-info', { date: agreement.createdAt }) }}
    </template>
  </v-list-item>
</template>

<script>
  import AgreementDialog from './AgreementDialog';

  export default {
    name: 'AgreementEntry',
    components: { AgreementDialog },
    props: {
      agreement: { type: Object, required: true },
    },
    data() {
      return {
        agreementDialogVisible: false,
      };
    },
    i18n: {
      messages: {
        pl: {
          'For:': 'Dla:',
          'data-info': 'Wygenerowano: {date}',
        },
        en: {
          'data-info': 'Generated at: {date}',
        },
      },
    },
  };
</script>
