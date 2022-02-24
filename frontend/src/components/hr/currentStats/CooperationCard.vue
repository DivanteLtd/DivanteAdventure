<template>
  <v-card class="card">
    <v-app-bar color="transparent" flat dense>
      <v-toolbar-title class="subheading ft-200">
        {{ $t('Forms of cooperation') }}
      </v-toolbar-title>
    </v-app-bar>
    <v-divider/>
    <v-card-text>
      <table class="stats">
        <tr v-for="(val, key) in stats.contracts" :key="key">
          <th>{{ $t(key) }}</th>
          <td>{{ val }} ({{ calculatePercent(val) }}%)</td>
        </tr>
      </table>
    </v-card-text>
  </v-card>
</template>

<script>
  export default {
    name: 'CooperationCard',
    props: {
      stats: { type: Object, required: true },
    },
    computed: {
      sum() {
        let currentSum = 0;
        for (const key in this.stats.contracts) {
          if (Object.prototype.hasOwnProperty.call(this.stats.contracts, key)) {
            currentSum += this.stats.contracts[key];
          }
        }
        return currentSum;
      },
    },
    methods: {
      calculatePercent(val) {
        const sum = this.sum;
        if (sum === 0) {
          return 0;
        } else {
          return (val * 100 / sum).toFixed(2);
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Forms of cooperation': 'Formy współpracy',
          'CoE': 'UoP',
          'CLC LUMP SUM': 'UCP RYCZAŁT',
          'CLC HOURLY': 'UCP GODZINOWE',
          'B2B LUMP SUM': 'B2B RYCZAŁT',
          'B2B HOURLY': 'B2B GODZINOWE',
        },
      },
    },
  };
</script>

<style scoped>
  .ft-200 {
    font-weight: 200;
  }
  .stats {
    width: 100%;
    text-align: center;
  }
  .card {
    height: 13em;
  }
  tr {
    width: 100%;
  }
  th, td {
    width: 50%;
  }
</style>
