<template>
  <v-data-table mobile-breakpoint="0"
                :items-per-page="5" :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                :no-results-text="$t('No results found.')"
                :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                :items="employees"
                :loading="loading"
                :headers="headers"
                :custom-sort="customSort"
                @update:page="moveToTop"
                must-sort>
    <template v-slot:item="{ item }">
      <persons-list-row :item="item"/>
    </template>
    <template slot="pageText" slot-scope="props">
      {{ $t('page-text', props) }}
    </template>
  </v-data-table>
</template>

<script>
  import PersonsListRow from '../personsList/PersonsListRow';

  export default {
    name: 'PersonsListTable',
    components: { PersonsListRow },
    props: {
      loading: { type: Boolean, required: true },
      employees: { type: Array, required: true },
    },
    data() { return {
      headers: [{
        align: 'center',
        value: 'photo',
        sortable: false,
      }, {
        align: 'center',
        value: 'availableToday',
        sortable: false,
        width: '0',
      }, {
        text: this.$t('Person'),
        align: 'center',
        value: 'lastName',
        sortable: true,
      }, {
        text: this.$t('Date of birth'),
        align: 'center',
        value: 'dateOfBirth',
        sortable: true,
        width: 180,
      }, {
        text: this.$t('Gender'),
        align: 'center',
        value: 'gender',
        sortable: false,
      }, {
        text: this.$t('E-mail address'),
        align: 'center',
        value: 'email',
      }, {
        text: this.$t('Work mode'),
        align: 'center',
        value: 'workMode',
        sortable: true,
      }, {
        text: this.$t('Position'),
        align: 'center',
        value: 'city',
        sortable: false,
      }, {
        text: this.$t('Contract type'),
        align: 'center',
        value: 'contract',
        sortable: true,
        width: 180,
      }, {
        text: this.$t('Hired at'),
        align: 'center',
        value: 'city',
        sortable: false,
      }, {
        text: this.$t('Hired to'),
        align: 'center',
        value: 'city',
        sortable: false,
      }, {
        text: this.$t('Actions'),
        align: 'center',
        sortable: false,
      }],
    };},
    methods: {
      customSort(items, index, isDesc) {
        let aPerson = ``;
        let bPerson = ``;
        index = index[0];
        isDesc = isDesc[0];
        items.sort((a, b) => {
          if (index === 'lastName') {
            aPerson = `${a.lastName} ${a.name}`;
            bPerson = `${b.lastName} ${b.name}`;
            if (!isDesc) {
              return aPerson.localeCompare(bPerson);
            } else {
              return bPerson.localeCompare(aPerson);
            }
          }
          else if (index === 'contract' || index === 'tribe') {
            aPerson = `${(a[index] || {}).name}`;
            bPerson = `${(b[index] || {}).name}`;
            if (!isDesc) {
              return aPerson.localeCompare(bPerson);
            } else {
              return bPerson.localeCompare(aPerson);
            }
          }
          else {
            aPerson = `${a[index]}`;
            bPerson = `${b[index]}`;
            if (!isDesc) {
              return aPerson.localeCompare(bPerson);
            } else {
              return bPerson.localeCompare(aPerson);
            }
          }
        });
        return items;
      },
      moveToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      },
    },
    i18n: { messages: {
      pl: {
        'Loading data...': 'Wczytywanie...',
        'No data available.': 'Brak danych.',
        'No results found.': 'Nie znaleziono.',
        'Rows per page:': 'Wierszy na stronę:',
        'All': 'Wszystkie',

        'Man': 'Mężczyzna',
        'Woman': 'Kobieta',
        'Person': 'Osoba',
        'Gender': 'Płeć',
        'Date of birth': 'Data urodzenia',
        'Work mode': 'Tryb pracy',
        'Position': 'Stanowisko',
        'Contract type': 'Forma zatrudnienia',
        'Hired at': 'Start współpracy',
        'Hired to': 'Koniec współpracy',
        'E-mail address': 'Adres e-mail',
        'Actions': 'Akcje',

        'page-text': 'Osoby {pageStart}-{pageStop} z {itemsLength}',
      },
      en: {
        'page-text': 'People {pageStart}-{pageStop} of {itemsLength}',
      },
    } },
  };
</script>
