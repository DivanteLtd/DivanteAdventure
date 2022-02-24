<template>
  <v-list class="pa-0" dense two-line>
    <agreement-entry v-for="(agreement, index) in agreements" :key="index" :agreement="agreement"/>
  </v-list>
</template>

<script>
  import AgreementEntry from './AgreementEntry';

  export default {
    name: 'AgreementsList',
    components: { AgreementEntry },
    props: {
      agreements: { type: Array, default: () => ([]) },
    },
  };
</script>
