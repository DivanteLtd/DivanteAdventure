<template>
  <v-data-table mobile-breakpoint="0"
                :items-per-page="5"
                :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                :loading="loading"
                :items="projects"
                :headers="headers"
                :no-results-text="$t('No results found.')"
                :custom-filter="filter"
                :custom-sort="customSort"
                @update:page="moveToTop"
                must-sort>
    <template v-slot:item="{ item }">
      <project-list-row :project="item"/>
      <template slot="pageText" slot-scope="props">
        {{ $t('page-text', props) }}
      </template>
    </template>
  </v-data-table>
</template>

<script>
  import ProjectListRow from './ProjectListRow';

  export default {
    name: 'ProjectListTable',
    components: { ProjectListRow },
    props: {
      loading: { type: Boolean, default: false },
      projects: { type: Array, required: true },
    },
    data() {
      return {
        sortBy: 'name',
        sortDesc: false,
        headers: [{
          text: this.$t('Project'),
          align: 'center',
          value: 'name',
        }, {
          text: this.$t('Billable'),
          align: 'center',
          value: 'billable',
        }, {
          text: this.$t('Type of project'),
          align: 'center',
          value: 'tags',
          sortable: false,
        }, {
          text: this.$t('Start date'),
          align: 'center',
          value: 'started_at',
        }, {
          text: this.$t('End date'),
          align: 'center',
          value: 'ended_at',
        }, {
          text: this.$t('Report'),
          align: 'center',
          value: 'options',
        }],
      };
    },
    methods: {
      filter(value, search, item) {
        const searchLower = search.replace(/\s/g, '').toLowerCase().split(/[ ,.;]+/);
        const entryPartA = item.name;
        const entry = entryPartA.replace(/\s/g, '').toLowerCase();
        return searchLower.map(key => entry.includes(key)).reduce((a, b) => a && b, true);
      },
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
      moveToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      },
    },
    i18n: {
      messages: {
        pl: {
          'All': 'Wszystkie',
          'No data available.': 'Brak danych.',
          'Loading data...': 'Wczytywanie...',
          'No results found.': 'Nie znaleziono.',
          'Rows per page:': 'Wierszy na stronę:',
          'Project': 'Projekt',
          'Start date': 'Data rozpoczęcia',
          'End date': 'Data zakończenia',
          'Type of project': 'Typ projektu',
          'page-text': 'Projekty {pageStart}-{pageStop} z {itemsLength}',
          'Billable': 'Płatny',
          'Report': 'Raport',
        },
        en: {
          'page-text': 'Projects {pageStart}-{pageStop} of {itemsLength}',
        },
      },
    },
  };
</script>
