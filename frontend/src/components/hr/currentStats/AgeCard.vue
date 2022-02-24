<template>
  <v-card class="card">
    <v-app-bar color="transparent" flat dense>
      <v-toolbar-title class="subheading ft-200">
        {{ $t('People count and age') }}
      </v-toolbar-title>
    </v-app-bar>
    <v-divider/>
    <v-card-text>
      <table class="data-table">
        <tr>
          <th></th>
          <th>{{ $t('Count') }}</th>
          <th>{{ $t('Medium age') }}</th>
        </tr>
        <tr>
          <th>{{ $t('Women') }}</th>
          <td>{{ stats.women }}</td>
          <td>
            {{ formatAge(stats.womenMediumAge).years }}
            {{ formatAge(stats.womenMediumAge).months }} {{ $t('mnth.') }}
          </td>
        </tr>
        <tr>
          <th>{{ $t('Men') }}</th>
          <td>{{ stats.men }}</td>
          <td>
            {{ formatAge(stats.menMediumAge).years }}
            {{ formatAge(stats.menMediumAge).months }} {{ $t('mnth.') }}
          </td>
        </tr>
        <tr class="all">
          <th>{{ $t('All') }}</th>
          <td>{{ stats.women + stats.men }}</td>
          <td>
            {{ formatAge(stats.mediumAge).years }}
            {{ formatAge(stats.mediumAge).months }} {{ $t('mnth.') }}
          </td>
        </tr>
      </table>
    </v-card-text>
  </v-card>
</template>

<script>
  export default {
    name: 'AgeCard',
    props: {
      stats: { type: Object, required: true },
    },
    methods: {
      formatAge(age) {
        let years = Math.floor(age);
        let months = Math.round((age - years) * 12);
        while (months >= 12) {
          months -= 12;
          years++;
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
          'People count and age': 'Liczba ludzi i wiek',
          'Count': 'Liczba',
          'Medium age': 'Średni wiek',
          'Women': 'Kobiety',
          'Men': 'Mężczyźni',
          'All': 'Razem',
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
  .data-table {
    width: 100%;
    text-align: center;
  }
  .card {
    height: 13em;
  }
</style>
