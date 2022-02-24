<template>
  <v-data-table mobile-breakpoint="0"
                disable-pagination
                :no-data-text="$t('No data available.')"
                :no-results-text="$t('No results found.')"
                :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                :items="categories"
                hide-default-footer>
    <template v-slot:item="{ item }">
      <f-a-q-category-row :category="item"/>
      <f-a-q-responsible-row :category="item"/>
    </template>
  </v-data-table>
</template>

<script>
  import { mapState } from 'vuex';
  import FAQCategoryRow from './FAQCategoryRow';
  import FAQResponsibleRow from './FAQResponsibleRow';

  export default {
    name: 'FAQFormTable',
    components: { FAQResponsibleRow, FAQCategoryRow },
    computed: {
      ...mapState({
        categories: state => state.FAQ.fAQCategories,
      }),
    },
    i18n: {
      messages: {
        pl: {
          'Category': 'Kategoria',
          'Loading data...': 'Wczytywanie...',
          'No data available.': 'Brak danych.',
          'No results found.': 'Nie znaleziono.',
          'Rows per page:': 'Wierszy na stronę:',
          'All': 'Wszystkie',
        },
      },
    },
  };
</script>

<style scoped>

</style>
