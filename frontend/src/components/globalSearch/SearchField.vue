<template>
  <v-autocomplete prepend-icon="search"
                  :label="$t('Search')"
                  :no-data-text="$t('No data available.')"
                  class="ml-2"
                  :items="sortedSearchData"
                  v-model="selectedItem"
                  :filter="filterFunction"
                  item-text="displayLabel"
                  return-object flat solo-inverted hide-details>
    <search-item slot="item" slot-scope="item" :item="item.item" @open="openPage"/>
  </v-autocomplete>
</template>

<script>
  import { mapGetters } from 'vuex';
  import SearchItem from './SearchItem';

  export default {
    name: 'SearchField',
    components: { SearchItem },
    data() {
      return {
        selectedItem: undefined,
      };
    },
    computed: {
      ...mapGetters(['sortedSearchData']),
    },
    watch: {
      selectedItem() {
        this.openPage(this.selectedItem);
        this.selectedItem = undefined;
      },
    },
    methods: {
      filterFunction(item, queryText) {
        const itemEntry = item.searchLabels.join(' ');
        const searchEntry = queryText.toLowerCase().split(/[ ,.;]+/);
        return searchEntry.map(key => itemEntry.includes(key)).reduce((a, b) => a && b, true);
      },
      openPage(item) {
        if (item === undefined) {
          return;
        }
        const { link } = item;
        if (typeof link === 'string') {
          if (link === (`/${this.$route.name}/${this.$route.params.id}`)) {
            const route = `/${this.$route.name}`;
            this.$router.push(route);
          }
          this.$router.push(link);
        } else if (typeof link === 'object' && link.type === 'local') {
          this.$router.push(link.value);
        } else if (typeof link === 'object' && link.type === 'global') {
          window.location.href = link.value;
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Search': 'Szukaj',
          'No data available.': 'Brak dostępnych danych.',
        },
      },
    },
  };
</script>
