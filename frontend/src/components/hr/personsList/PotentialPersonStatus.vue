<template>
  <v-chip :color="color" outlined>{{ label }}</v-chip>
</template>

<script>
  const STATUS_POTENTIAL_EMPLOYEE = 0;
  const STATUS_ACCEPTED = 1;
  const STATUS_REJECTED = 2;

  export default {
    name: 'PotentialPersonStatus',
    props: {
      status: { type: Number, required: true },
    },
    computed: {
      color() {
        switch (this.status) {
          case STATUS_POTENTIAL_EMPLOYEE: return '#AAAA00';
          case STATUS_ACCEPTED: return '#00AA00';
          case STATUS_REJECTED: return '#FF0000';
          default: return 'grey';
        }
      },
      label() {
        switch (this.status) {
          case STATUS_POTENTIAL_EMPLOYEE: return this.$t('Potential');
          case STATUS_ACCEPTED: return this.$t('Accepted');
          case STATUS_REJECTED: return this.$t('Rejected');
          default: return 'N/A';
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          Potential: 'Potencjalna',
          Accepted: 'Zaakceptowana',
          Rejected: 'Odrzucona',
        },
      },
    },
  };
</script>
