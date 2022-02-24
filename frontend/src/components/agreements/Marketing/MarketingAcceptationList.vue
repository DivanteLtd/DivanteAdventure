<template>
  <v-data-table mobile-breakpoint="0"
                :items-per-page="5" :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                :no-results-text="$t('No results found.')"
                :class="{'pa-4': $vuetify.breakpoint.smAndUp, 'pt-3': $vuetify.breakpoint.xs}"
                :items="marketingAcceptationList"
                :headers="loading ? [] : headers"
                :search="search"
                :loading="loading"
                :custom-filter="filter"
                :custom-sort="customSort"
                @update:page="moveToTop"
                class="marketing-accept-list"
                must-sort>
    <template v-slot:item="{ item }">
      <marketing-acceptation-list-row :marketing="item"/>
      <template slot="pageText" slot-scope="props">
        {{ $t('page-text', props) }}
      </template>
    </template>
  </v-data-table>
</template>

<script>
  import { mapState } from 'vuex';
  import MarketingAcceptationListRow from './MarketingAcceptationListRow';
  import { getSuggestedLanguage } from '../../../i18n/i18n';

  export default {
    name: 'MarketingAcceptationList',
    components: { MarketingAcceptationListRow },
    props: {
      search: { type: String, default: '' },
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        language: getSuggestedLanguage(),
      };
    },
    computed: {
      ...mapState({
        marketingAcceptationList: state => state.Agreements.marketingAcceptationList,
        marketingConsents: state => state.Agreements.marketingConsents,
      }),
      headers() {
        const headers = [{
          text: this.$t('Person'),
          align: 'center',
          sortable: true,
          value: 'lastName',
        }, {
          text: 'Email',
          align: 'center',
          sortable: true,
          value: 'email',
        }];
        this.marketingConsents.forEach(val => {
          const correctLanguageDescription = this.language === 'pl' ? val.descriptionPl : val.descriptionEn;
          headers.push({
            text: correctLanguageDescription,
            align: 'center',
            sortable: false,
            value: 'language',
          });
        });
        return headers;
      },
    },
    methods: {
      customSort(items, index, isDesc) {
        let aPerson = ``;
        let bPerson = ``;
        items.sort((a, b) => {
          if (index === 'lastName') {
            aPerson = `${a.lastName} ${a.name}`;
            bPerson = `${b.lastName} ${b.name}`;
          }
          else{
            aPerson = `${a[index]}`;
            bPerson = `${b[index]}`;
          }
          if (!isDesc) {
            return aPerson.localeCompare(bPerson);
          } else {
            return bPerson.localeCompare(aPerson);
          }
        });
        return items;
      },
      filter(value, search, item) {
        const searchLower = search.toLowerCase().split(/[ ,.;]+/);
        const entryPartA = `${item.name} ${item.lastName} ${item.email}`;
        const entry = `${entryPartA}`.toLowerCase();
        return searchLower.map(key => entry.includes(key)).reduce((a, b) => a && b, true);
      },
      moveToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      },
    },
    i18n: {
      messages: {
        pl: {
          'Loading data...': 'Wczytywanie...',
          'No data available.': 'Brak danych.',
          'No results found.': 'Nie znaleziono.',
          'Person': 'Osoba',
          'All': 'Wszystkie',
          'Consent': 'Zgoda',
          'Rows per page:': 'Wierszy na stronę:',
          'page-text': 'Zgody {pageStart}-{pageStop} z {itemsLength}',
        },
        en: {
          'page-text':
            'Consents {pageStart}-{pageStop} of {itemsLength}',
        },
      },
    },
  };
</script>
<style>
  .marketing-accept-list th{
    min-width: 220px;
    white-space: unset !important;
    padding: 0 12px 8px 12px !important;
    vertical-align: top;
  }
</style>
