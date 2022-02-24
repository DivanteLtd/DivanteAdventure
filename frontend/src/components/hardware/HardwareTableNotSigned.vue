<template>
  <div>
    <v-data-table :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                  :no-results-text="$t('No results found.')"
                  :class="{'px-3': $vuetify.breakpoint.smAndUp}"
                  :items-per-page="15"
                  :search="search"
                  :items="hardwareAgreements"
                  :loading="loading"
                  :headers="headers"
                  :custom-sort="customSort"
                  :custom-filter="customFilter"
                  id="checklist-templates-table"
                  must-sort>
      <template v-slot:item="{ item }">
        <hardware-row :generated="true"
                      @change-loading="$emit('changeLoading')"
                      :item="item"/>
      </template>
      <template slot="pageText" slot-scope="props">
        {{ $t('page-text', props) }}
      </template>
    </v-data-table>
  </div>
</template>

<script>
  import HardwareRow from './HardwareRow';

  export default {
    name: 'HardwareTableNotSigned',
    components: { HardwareRow },
    props: {
      loading: { type: Boolean, required: true },
      search: { type: String, default: '' },
      hardwareAgreements: { type: Array, required: true },
    },
    data() { return {
      deleteDialogVisible: false,
      headers: [{
        text: this.$t('Name'),
        value: 'name',
        sortable: true,
      }, {
        text: this.$t('Last name'),
        value: 'lastName',
        sortable: true,
      }, {
        text: this.$t('Contract'),
        value: 'model',
        sortable: true,
      }, {
        text: this.$t('Category'),
        value: 'category',
        sortable: true,
      }, {
        text: this.$t('Manufacturer'),
        value: 'manufacturer',
        sortable: true,
      }, {
        text: this.$t('Model'),
        value: 'model',
        sortable: true,
      }, {
        text: this.$t('Serial number'),
        value: 'serialNumber',
        sortable: true,
      }, {
        text: this.$t('Signed by helpdesk'),
        value: 'signedByHelpdesk',
        sortable: false,
      }, {
        text: this.$t('Signed by user'),
        value: 'signedByUser',
        sortable: false,
      }],
    };},
    methods: {
      customSort(items, index, isDesc) {
        let itemA = ``;
        let itemB = ``;
        items.sort((a, b) => {
          itemA = `${a[index]}`;
          itemB = `${b[index]}`;
          if (!isDesc) {
            return itemA.localeCompare(itemB);
          } else {
            return itemB.localeCompare(itemA);
          }
        });
        return items;
      },
      customFilter(value, search, item) {
        const searchLower = search.toLowerCase().split(/[ ,.;]+/);
        const entryPartA = `${item.name} ${item.lastName}`;
        const entryPartB = `${item.contract}`;
        const entryPartC = `${item.category}`;
        const entryPartD = `${item.model}`;
        const entryPartE = `${item.manufacturer}`;
        const entryPartF = `${item.serialNumber}`;
        const entry = `${entryPartA} ${entryPartB} ${entryPartC} ${entryPartD} ${entryPartE} ${entryPartF}`
          .replace(/\s/g, '').toLowerCase();
        return searchLower.map(key => entry.includes(key)).reduce((a, b) => a && b, true);
      },
    },
    i18n: { messages: {
      pl: {
        'Loading data...': 'Wczytywanie...',
        'No data available.': 'Brak danych.',
        'No results found.': 'Nie znaleziono.',
        'Name': 'Nazwa',
        'Contract': 'Kontrakt',
        'Category': 'Kategoria',
        'Manufacturer': 'Producent',
        'Serial number': 'Numer seryjny',
        'Signed by helpdesk': 'Podpisana przez helpdesk',
        'Signed by user': 'Podpisana przez użytkownika',
        'Last name': 'Nazwisko',
        'page-text': 'Osoby {pageStart}-{pageStop} z {itemsLength}',
      },
      en: {
        'page-text': 'People {pageStart}-{pageStop} of {itemsLength}',
      },
    },
    },
  };
</script>
