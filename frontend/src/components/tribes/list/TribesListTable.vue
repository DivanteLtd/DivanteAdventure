<template>
  <v-data-table mobile-breakpoint="0"
                :items-per-page="5" :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                :no-results-text="$t('No results found.')"
                :items="tribes"
                :headers="headers"
                :loading="loading"
                :custom-filter="filter"
                @update:page="moveToTop"
                must-sort>
    <template v-slot:item="{ item }">
      <tribe-list-row :tribe="item"/>
      <template slot="pageText" slot-scope="props">
        {{ $t(pageTextKey, props) }}
      </template>
    </template>
  </v-data-table>
</template>

<script>
  import TribeListRow from './TribeListRow';

  export default {
    name: 'TribesListTable',
    components: { TribeListRow },
    props: {
      loading: { type: Boolean, default: false },
      tribes: { type: Array, required: true },
      pageTextKey: { type: String, default: 'page-text-tribe' },
    },
    data() {
      return {
        headers: [
          {
            text: this.$t('Name'),
            align: 'center',
            value: 'name',
          }, {
            text: this.$t('Website'),
            align: 'center',
            value: 'url',
          }, {
            text: this.$t('Number of members'),
            align: 'center',
            value: 'employeesCount',
          },
        ],
      };
    },
    methods: {
      filter(items, search) {
        const searchLower = search.replace(/\s/g, '').toLowerCase().split(/[ ,.;]+/);
        return items.filter(tribe => {
          const entryPartA = `${tribe.name}`;
          const entry = `${entryPartA}`.replace(/\s/g, '').toLowerCase();
          return searchLower.map(key => entry.includes(key)).reduce((a, b) => a && b, true);
        });
      },
      moveToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      },
    },
    i18n: { messages: {
      pl: {
        'All': 'Wszystkie',
        'No data available.': 'Brak danych.',
        'No results found.': 'Nie znaleziono.',
        'Rows per page:': 'Wierszy na stronę:',
        'Loading data...': 'Wczytywanie...',
        'Name': 'Nazwa',
        'Website': 'Strona',
        'Number of members': 'Liczba członków',

        'page-text-tribe': 'Plemiona {pageStart}-{pageStop} z {itemsLength}',
        'page-text-department': 'Działy {pageStart}-{pageStop} z {itemsLength}',
      },
      en: {
        'page-text-tribe': 'Tribes {pageStart}-{pageStop} of {itemsLength}',
        'page-text-department': 'Departments {pageStart}-{pageStop} of {itemsLength}',
      },
    },
    },
  };
</script>
