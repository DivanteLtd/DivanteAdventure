<template>
  <v-alert :value="true" type="info" color="#808080">
    <div v-html="getInfoPerLanguage" class="marketing-alert__message">
      {{ getInfoPerLanguage }}
    </div>
  </v-alert>
</template>
<script>
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import { mapState } from 'vuex';

  export default {
    name: 'EvidenceInfoBottom',
    data() {
      return {
        locale: getSuggestedLanguage(),
      };
    },
    computed: {
      ...mapState({
        entries: state => state.Config.contentConfig,
      }),
      getInfoPerLanguage() {
        return `${this.entries.filter(val => val.key === `content.agreement_marketing_${this.locale}`)
          .map(val => val.value)[0]}`;
      },
    },
  };
</script>
<style scoped>
  @media only screen and (max-width: 600px) {
    .marketing-alert__message{
      font-size: small;
    }
  }
</style>
