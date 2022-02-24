<template>
  <dashboard-card :title="$t('Agreements to sign')" :loading="!loaded">
    <agreements-list :agreements="agreements"/>
  </dashboard-card>
</template>

<script>
  import DashboardCard from './DashboardCard';
  import AgreementsList from '../hardware/toSign/AgreementsList';

  export default {
    name: 'HardwareAgreementsView',
    components: { AgreementsList, DashboardCard },
    props: {
      agreements: { type: Array, default: () => ([]) },
      loaded: { type: Boolean, default: false },
    },
    i18n: {
      messages: {
        pl: {
          'Agreements to sign': 'Umowy do podpisania',
        },
      },
    },
  };
</script>
