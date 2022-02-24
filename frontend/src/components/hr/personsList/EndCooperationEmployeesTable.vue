<template>
  <v-data-table mobile-breakpoint="0"
                :items-per-page="5"
                :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                :no-results-text="$t('No results found.')"
                :items="employees"
                :loading="loading"
                :headers="headers"
                :custom-sort="customSort"
                must-sort>
    <template v-slot:item="{ item }">
      <end-cooperation-employee-row :item="item"/>
      <template slot="pageText" slot-scope="props">
        {{ $t('page-text', props) }}
      </template>
    </template>
  </v-data-table>
</template>

<script>
  import EndCooperationEmployeeRow from './EndCooperationEmployeeRow';

  export default {
    name: 'EndCooperationEmployeesTable',
    components: { EndCooperationEmployeeRow },
    props: {
      loading: { type: Boolean, required: true },
      employees: { type: Array, required: true },
    },
    data() { return {
      headers: [ {
        text: this.$t('Name'),
        align: 'center',
        value: 'name',
        sortable: false,
      }, {
        text: this.$t('LastName'),
        align: 'center',
        value: 'lastName',
        sortable: true,
      }, {
        text: this.$t('Position'),
        align: 'center',
        value: 'position',
        sortable: false,
      }, {
        text: this.$t('who-end-cooperation'),
        align: 'center',
        value: 'whoEndedCooperation',
        sortable: false,
      }, {
        text: this.$t('next-company'),
        align: 'center',
        value: 'nextCompany',
        sortable: false,
      }, {
        text: this.$t('exit-interview'),
        align: 'center',
        value: 'exitInterview',
        sortable: false,
      }, {
        text: this.$t('checklist'),
        align: 'center',
        value: 'checklist',
        sortable: false,
      }, {
        text: this.$t('Comment'),
        align: 'center',
        value: 'comment',
        sortable: false,
      }, {
        text: this.$t('Hired to'),
        align: 'center',
        value: 'dismissDate',
        sortable: true,
      }, {
        text: this.$t('Options'),
        align: 'center',
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
          if(!isDesc[0]) {
            return itemA.localeCompare(itemB);
          } else {
            return itemB.localeCompare(itemA);
          }
        });
        return items;
      },
    },
    i18n: { messages: {
      pl: {
        'Loading data...': 'Wczytywanie...',
        'No data available.': 'Brak danych.',
        'No results found.': 'Nie znaleziono.',
        'Rows per page:': 'Wierszy na stronę:',
        'All': 'Wszystkie',

        'Name': 'Imię',
        'LastName': 'Nazwisko',
        'who-end-cooperation': 'Kto zakończył współpracę',
        'next-company': 'Następna firma',
        'exit-interview': 'Czy było exit interview',
        'checklist': 'Czy checklista zakończenia współpracy wypełniona',
        'Comment': 'Uwagi',
        'Man': 'Mężczyzna',
        'Woman': 'Kobieta',
        'Person': 'Osoba',
        'Gender': 'Płeć',
        'Date of birth': 'Data urodzenia',
        'Position': 'Stanowisko',
        'Contract type': 'Forma zatrudnienia',
        'Hired at': 'Start współpracy',
        'Hired to': 'Data zakończenia współpracy',
        'E-mail address': 'Adres e-mail',
        'Options': 'Opcje',

        'page-text': 'Osoby {pageStart}-{pageStop} z {itemsLength}',
      },
      en: {
        'page-text': 'People {pageStart}-{pageStop} of {itemsLength}',
        'next-company': 'Next company',
        'exit-interview': 'Was there exit interview?',
        'who-end-cooperation': 'Who end cooperation',
        'checklist': 'Checklist',
      },
    } },
  };
</script>
