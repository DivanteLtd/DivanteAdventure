<template>
  <div>
    <v-data-table mobile-breakpoint="0"
                  :items-per-page="5" :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                  :no-results-text="$t('No results found.')"
                  :class="{'px-3': $vuetify.breakpoint.smAndUp}"
                  :search="search"
                  :items="checklists"
                  :loading="loading"
                  :headers="headers"
                  :custom-filter="filter"
                  :custom-sort="customSort"
                  must-sort>
      <template v-slot:item="{ item }">
        <checklist-list-row :item="item" @loading="$emit('loading')" :reduced="reduced"/>
      </template>
      <template slot="pageText" slot-scope="props">
        {{ $t('page-text', props) }}
      </template>
    </v-data-table>
  </div>
</template>

<script>
  import ChecklistListRow from './ChecklistsListRow';
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import { mapGetters } from 'vuex';

  export default {
    name: 'ChecklistTable',
    components: { ChecklistListRow },
    props: {
      loading: { type: Boolean, default: false },
      search: { type: String, default: '' },
      checklists: { type: Array, required: true },
      reduced: { type: Boolean, default: false },
    },
    data() { return {
      showAssigned: true,
      showUnassigned: true,
      unfilteredHeaders: [{
        text: this.$t('Name'),
        align: 'center',
        value: this.getSuggestedLanguage === 'pl' ? 'namePl' : 'nameEn',
        sortable: true,
        reduced: true,
      }, {
        text: this.$t('Owner'),
        align: 'center',
        value: 'owner',
        sortable: true,
      }, {
        text: this.$t('Subject'),
        align: 'center',
        value: 'subject',
        sortable: true,
        reduced: true,
      }, {
        text: this.$t('Due date'),
        align: 'center',
        value: 'dueDate',
        sortable: true,
        reduced: true,
      }, {
        text: this.$t('Start date'),
        align: 'center',
        value: 'startedAt',
        sortable: true,
      }, {
        text: this.$t('Finish date'),
        align: 'center',
        value: 'finishedAt',
        sortable: true,
      }, {
        text: this.$t('Progress'),
        align: 'center',
        value: 'tasksFinishedPercent',
        sortable: false,
        reduced: true,
      }, {
        text: this.$t('Delete'),
        align: 'center',
        value: 'options',
        sortable: false,
        role: 'ROLE_HR',
      }],
    };},
    computed: {
      getSuggestedLanguage,
      ...mapGetters({
        isAuthorized: 'Authorization/isAuthorized',
      }),
      headers() {
        return this.unfilteredHeaders.filter(this.filterHeader);
      },
    },
    methods: {
      filterHeader(header) {
        return (!header.hasOwnProperty('role') || this.isAuthorized(header.role)) && (!this.reduced || header.reduced);
      },
      customSort(items, index, isDesc) {
        let aPerson = ``;
        let bPerson = ``;
        items.sort((a, b) => {
          if (index[0] === 'owner') {
            aPerson = typeof a.owner === 'undefined' ? '' : `${a.owner.lastName} ${a.owner.name}`;
            bPerson = typeof b.owner === 'undefined' ? '' : `${b.owner.lastName} ${b.owner.name}`;
            if (!isDesc[0]) {
              return aPerson.localeCompare(bPerson);
            } else {
              return bPerson.localeCompare(aPerson);
            }
          }
          else if (index[0] === 'subject') {
            aPerson = typeof a.subject === 'undefined' ? '' : `${a.subject.lastName} ${a.subject.name}`;
            bPerson = typeof b.subject === 'undefined' ? '' : `${b.subject.lastName} ${b.subject.name}`;
            if (!isDesc[0]) {
              return aPerson.localeCompare(bPerson);
            } else {
              return bPerson.localeCompare(aPerson);
            }
          }
          else if (index[0] === 'tasksFinishedPercent') {
            aPerson = a.tasksFinishedCount;
            bPerson = b.tasksFinishedCount;
            if (!isDesc[0]) {
              return aPerson < bPerson;
            } else {
              return bPerson < aPerson;
            }
          }
          else {
            aPerson = `${a[index]}`;
            bPerson = `${b[index]}`;
            if (!isDesc[0]) {
              return aPerson.localeCompare(bPerson);
            } else {
              return bPerson.localeCompare(aPerson);
            }
          }
        });
        return items;
      },
      filter(value, search, item) {
        const searchLower = search.toLowerCase().split(/[ ,.;]+/);
        let entry = `${item.namePl} ${item.nameEn}`;
        if (typeof item.owner !== 'undefined') {
          entry = `${entry} ${item.owner.name} ${item.owner.lastName}`;
        }
        if (typeof item.subject !== 'undefined') {
          entry = `${entry} ${item.subject.name} ${item.subject.lastName}`;
        }
        entry = entry.replace(/\s/g, '').toLowerCase();
        return searchLower.map(key => entry.includes(key)).reduce((a, b) => a && b, true);
      },
    },
    i18n: { messages: {
      pl: {
        'Name': 'Nazwa',
        'Delete': 'Usuń',
        'Due date': 'Termin realizacji',
        'Owner': 'Właściciel',
        'Subject': 'Podmiot',
        'Start date': 'Data rozpoczęcia',
        'Finish date': 'Data zakończenia',
        'Progress': 'Postęp',
        'No data available': 'Brak danych',
        'Loading data...': 'Wczytywanie...',
        'Rows per page:': 'Wierszy na stronę:',
        'All': 'Wszystkie',
        'No results found.': 'Nie znaleziono.',
        'page-text': 'Osoby {pageStart}-{pageStop} z {itemsLength}',
      },
      en: {
        'page-text': 'People {pageStart}-{pageStop} of {itemsLength}',
      },
    },
    },
  };
</script>
<style scoped>
    .switch{
        max-width: 150px;
    }
</style>
