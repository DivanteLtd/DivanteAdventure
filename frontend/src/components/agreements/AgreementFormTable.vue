<template>
  <v-card-text class="pa-0">
    <v-card-title class="title">
      <span>{{ marketing ? $t('List of current agreements') : $t('Expand to show consents') }}</span>
    </v-card-title>
    <v-col v-if="!marketing" :class="{'mb-3': $vuetify.breakpoint.smAndUp}" sm="12">
      <agreement-form-card :agreements="fireSafetyAgreements" :title="$t('Fire safety/OSH consents')"/>
    </v-col>
    <v-col v-if="!marketing" :class="{'mb-3': $vuetify.breakpoint.smAndUp}" sm="12">
      <agreement-form-card :agreements="gdprAgreements" :title="$t('GDPR consents')"/>
    </v-col>
    <v-col v-if="!marketing" :class="{'mb-3': $vuetify.breakpoint.smAndUp}" sm="12">
      <agreement-form-card :agreements="ISOAgreements" :title="$t('ISO consents')"/>
    </v-col>
    <v-data-table mobile-breakpoint="0"
                  v-if="marketing"
                  disable-pagination
                  :items="allMarketingConsents"
                  hide-default-header
                  hide-default-footer>
      <template v-slot:item="{ item }">
        <agreement-form-row :agreement="item" @reload="reloadAgreements"/>
      </template>
    </v-data-table>
  </v-card-text>
</template>

<script>
  import { mapState } from 'vuex';
  import AgreementFormCard from './AgreementFormCard';
  import AgreementFormRow from './AgreementFormRow';
  import { agreementsType } from '../../util/agreements';

  export default {
    name: 'AgreementFormTable',
    components: { AgreementFormCard, AgreementFormRow },
    props: {
      marketing: { type: Boolean, required: true },
    },
    computed: {
      ...mapState({
        agreements: state => state.Agreements.agreements,
        allMarketingConsents: state => state.Agreements.marketingConsents,
      }),
      gdprAgreements() {
        return this.agreements.filter(agreement => agreement.type === agreementsType.TYPE_GDPR);
      },
      fireSafetyAgreements() {
        return this.agreements.filter(agreement => agreement.type === agreementsType.TYPE_FIRE_SAFETY);
      },
      ISOAgreements() {
        return this.agreements.filter(agreement => agreement.type === agreementsType.TYPE_ISO);
      },
    },
    methods: {
      reloadAgreements() {
        this.$store.dispatch('Agreements/loadAgreements');
        this.$store.dispatch('Agreements/loadMarketingConsents');
      },
    },
    i18n: {
      messages: {
        pl: {
          'List of current agreements': 'Lista obecnych zgód',
          'Expand to show consents': 'Rozwiń aby wyświetlić zgody',
          'GDPR consents': 'Zgody RODO',
          'ISO consents': 'Zgody ISO',
          'Fire safety/OSH consents': 'Zgody PPOŻ/BHP',
        },
      },
    },
  };
</script>
