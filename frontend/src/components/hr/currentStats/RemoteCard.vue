<template>
  <v-card class="card">
    <v-app-bar color="transparent" flat >
      <v-toolbar-title class="subheading ft-200">
        {{ $t('Remote work and work time') }}
      </v-toolbar-title>
    </v-app-bar>
    <v-divider/>
    <v-card-text>
      <table class="stats">
        <tr>
          <th>{{ $t('Work from office') }}</th>
          <td>{{ stats.workFromOffice }} ({{ workFromOfficePercent.toFixed(2) }}%)</td>
        </tr>
        <tr>
          <th>{{ $t('Work remotely') }}</th>
          <td>{{ stats.workRemotely }} ({{ workRemotelyPercent.toFixed(2) }}%)</td>
        </tr>
        <tr>
          <th>{{ $t('Work partial remotely') }}</th>
          <td>{{ stats.workPartialRemotely }} ({{ workPartialRemotelyPercent.toFixed(2) }}%)</td>
        </tr>
        <tr>
          <th>{{ $t('Average work time') }}</th>
          <td>{{ workTime.years }} {{ workTime.months }} {{ $t('mnth.') }}</td>
        </tr>
      </table>
    </v-card-text>
  </v-card>
</template>

<script>
  export default {
    name: 'RemoteCard',
    props: {
      stats: { type: Object, required: true },
    },
    computed: {
      workFromOfficePercent() {
        return this.stats.workFromOffice * 100 / this.stats.definedWorkModeSum;
      },
      workRemotelyPercent() {
        return this.stats.workRemotely * 100 / this.stats.definedWorkModeSum;
      },
      workPartialRemotelyPercent() {
        return this.stats.workPartialRemotely * 100 / this.stats.definedWorkModeSum;
      },
      workTime() {
        let years = Math.floor(this.stats.workTime);
        let months = ((this.stats.workTime - years) * 12).toFixed(0);
        while (months >= 12) {
          years++;
          months -= 12;
        }
        if (years === 1) {
          years = `${years} ${this.$t('y-1')}`;
        } else {
          years = `${years} ${this.$t('y-n')}`;
        }
        return { years, months };
      },
    },
    i18n: {
      messages: {
        pl: {
          'Remote work and work time': 'Praca zdalna oraz staż pracy',
          'Work from office': 'Praca z biura',
          'Work remotely': 'Praca zdalna',
          'Work partial remotely': 'Praca cześciowo zdalna',
          'Average work time': 'Średni staż pracy',
          'y-1': 'r.',
          'y-n': 'l.',
          'mnth.': 'mies.',
        },
        en: {
          'y-1': 'y.',
          'y-n': 'y.',
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
</style>
