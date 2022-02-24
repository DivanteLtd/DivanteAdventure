<template >
  <v-alert :value="true" type="info">
    <div v-html="getInfoPerLanguage">
      {{ getInfoPerLanguage }}
    </div>
  </v-alert>
</template>

<script>
  import { mapState } from 'vuex';
  import { getSuggestedLanguage } from '../../../i18n/i18n';

  export default {
    name: 'MarketingInfoTop',
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
        return this.entries.filter(val => val.key === `content.agreement_marketing_main_${this.locale}`)
          .map(val => val.value)[0];
      },
    },
  };
</script>
