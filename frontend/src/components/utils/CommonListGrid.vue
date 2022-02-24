<template>
  <v-data-table mobile-breakpoint="0"
                :items-per-page="5"
                :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                :no-results-text="$t('No results found.')"
                :items="items"
                :headers="headers"
                :loading="loading"
                @update:page="moveToTop"
                must-sort>
    <common-list-grid-page-text slot="pageText" slot-scope="props" :page-text="pageText" :slot-props="props"/>
    <template v-slot:item="{ item }">
      <slot :item="item">{{ item }}</slot>
    </template>
  </v-data-table>
</template>

<script>
  import CommonListGridPageText from './CommonListGridPageText';

  export default {
    name: 'CommonListGrid',
    components: { CommonListGridPageText },
    props: {
      items: { type: Array, default: () => ([]) },
      headers: { type: Array, default: () => ([]) },
      loading: { type: Boolean, default: false },
      pageText: { type: String, default: '{pageStart}-{pageStop} of {itemsLength}' },
    },
    methods: {
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
      },
    },
    },
  };
</script>
