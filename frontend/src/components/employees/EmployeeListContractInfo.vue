<template>
  <v-tooltip v-if="contractInfo !== undefined" left>
    <template v-slot:activator="{ on }">
      <v-chip v-on="on" :color="contractInfo.color" :text-color="contractInfo.textColor">
        {{ contractInfo.label }}
      </v-chip>
    </template>
    {{ contractInfo.tooltip }}
  </v-tooltip>
</template>

<script>
  export default {
    name: 'EmployeeListContractInfo',
    props: {
      employee: { type: Object, required: true },
    },
    computed: {
      contractInfo() {
        const contracts = {
          'CoE': {
            color: 'green',
            textColor: 'white',
            label: this.$t('CoE'),
            tooltip: this.$t('Employment contract'),
          },
          'CLC LUMP SUM': {
            color: '#0d2680',
            textColor: 'white',
            label: this.$t('CLC LUMP SUM'),
            tooltip: this.$t('Civil law contract lump sum'),
          },
          'CLC HOURLY': {
            color: 'blue',
            textColor: 'white',
            label: this.$t('CLC HOURLY'),
            tooltip: this.$t('Civil law contract hourly billing'),
          },
          'B2B LUMP SUM': {
            color: 'yellow',
            textColor: 'black',
            label: this.$t('B2B LUMP SUM'),
            tooltip: this.$t('Business-to-business lump sum contract'),
          },
          'B2B HOURLY': {
            color: 'orange',
            textColor: 'black',
            label: this.$t('B2B HOURLY'),
            tooltip: this.$t('Business-to-business hourly billing contract'),
          },
          'None': {
            color: 'red',
            textColor: 'black',
            label: this.$t('None'),
            tooltip: this.$t('No agreement specified'),
          },
        };
        if (this.employee.contract) {
          return contracts[this.employee.contract.name];
        } else {
          return contracts.None;
        }
      },
    },
    i18n: { messages: {
      pl: {
        'CoE': 'UOP',
        'CLC LUMP SUM': 'UCP RYCZAŁT',
        'CLC HOURLY': 'UCP GODZINOWE',
        'B2B LUMP SUM': 'B2B RYCZAŁT',
        'B2B HOURLY': 'B2B GODZINOWE',
        'None': 'Brak',
        'Employment contract': 'Umowa o pracę',
        'Civil law contract': 'Umowa cywilnoprawna',
        'Business-to-business contract': 'Umowa business-to-business',
        'No agreement specified': 'Nie sprecyzowano umowy',
      },
    } },
  };
</script>
