<template>
  <v-list dense>
    <agreement-download-link v-for="(lang,index) in agreement.languages"
                             :key="index"
                             :agreement="agreement"
                             :language="lang"/>
  </v-list>
</template>

<script>
  import AgreementDownloadLink from './AgreementDownloadLink';

  export default {
    name: 'AgreementDownloads',
    components: { AgreementDownloadLink },
    props: {
      agreement: { type: Object, required: true },
    },
  };
</script>
