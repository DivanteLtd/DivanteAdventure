<template>
  <v-dialog v-model="dialogVisible" width="1000">
    <v-card id="agreement-form">
      <v-row no-gutters wrap class="justify-center">
        <v-col cols="12">
          <v-card-title class="headline">
            <span>{{ $t('Manage agreements') }}</span>
          </v-card-title>
        </v-col>
        <v-col cols="12" sm="6" md="4" class="pa-4">
          <v-btn block color="primary" @click="addAgreement">
            {{ $t('Add agreement') }}
          </v-btn>
        </v-col>
      </v-row>
      <agreement-form-table :marketing="marketingFlag"/>
      <v-card-actions>
        <v-spacer/>
        <v-btn color="red" text @click="dialogVisible = false">
          {{ $t('Cancel') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import AgreementFormTable from './AgreementFormTable';

  export default {
    name: 'AgreementForm',
    components: { AgreementFormTable },
    data() {
      return {
        dialogVisible: false,
        marketingFlag: false,
      };
    },
    methods: {
      async show(marketingFlag) {
        this.marketingFlag = false;
        if (marketingFlag) {
          await this.$store.dispatch('Agreements/loadMarketingConsents');
          this.marketingFlag = true;
          this.dialogVisible = true;
        } else if (!this.dialogVisible) {
          await this.$store.dispatch('Agreements/loadAgreements');
          this.dialogVisible = true;
        }
      },
      addAgreement() {
        return this.marketingFlag ? EventBus.$emit(eventNames.agreementAttachmentForm, this.marketingFlag)
          : EventBus.$emit(eventNames.agreementAttachmentForm);
      },
    },
    mounted() {
      EventBus.$on(eventNames.agreementForm, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Manage agreements': 'Zarządzaj zgodami',
          'Add agreement': 'Dodaj zgodę',
          'Cancel': 'Anuluj',
        },
      },
    },
  };
</script>
<style>
  @media only screen and (max-width: 600px) {
    #agreement-form td {
      padding: 0 12px;
    }
  }
</style>
