<template>
  <v-alert :value="true" type="info">
    <div v-html="getMessage()"/>
    <div v-if="agreementType === agreementsType.TYPE_ISO" class="presentation mt-2">
      <v-btn :href="getLink()" target="_blank" color="success">
        {{ $t('isoPresentation') }}
      </v-btn>
    </div>
  </v-alert>
</template>

<script>
  import { agreementsType } from '../../../util/agreements';
  import { mapState } from 'vuex';
  import { getSuggestedLanguage } from '../../../i18n/i18n';

  export default {
    name: 'AgreementInfoTop',
    props: {
      agreementType: { type: Number, required: true },
    },
    data() {
      return {
        agreementsType,
        locale: getSuggestedLanguage(),
      };
    },
    computed: {
      ...mapState({
        entries: state => state.Config.contentConfig,
      }),
    },
    methods: {
      getLink() {
        return this.entries.filter(val => val.key === `content.iso_link_${this.locale}`).map(val => val.value)[0];
      },
      getMessage() {
        switch (this.agreementType) {
          case agreementsType.TYPE_GDPR:
            return this.entries.filter(val => val.key === `content.agreement_gdpr_${this.locale}`).map(val => val.value)[0];
          case agreementsType.TYPE_FIRE_SAFETY:
            return this.entries.filter(val => val.key === `content.agreement_ohs_${this.locale}`).map(val => val.value)[0];
          case agreementsType.TYPE_ISO:
            return this.entries.filter(val => val.key === `content.agreement_iso_${this.locale}`).map(val => val.value)[0];
          default:
            return this.entries.filter(val => val.key === `content.agreement_general_${this.locale}`).map(val => val.value)[0];
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          isoPresentation: 'System Zarządzania ISO',
        },
        en: {
          isoPresentation: 'ISO Management System',
        },
      },
    },
  };
</script>
<style scoped>
  .presentation {
    text-align: center;
  }
</style>
