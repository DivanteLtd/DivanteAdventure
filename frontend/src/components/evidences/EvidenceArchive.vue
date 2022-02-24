<template>
  <v-data-table mobile-breakpoint="0" class="mt-2"
                :items-per-page="5"
                :no-results-text="$t('No results found.')"
                :items="evidences"
                :headers="checkHeaders"
                :loading="!loaded"
                @update:page="moveToTop"
                must-sort>
    <template slot="pageText" slot-scope="props">
      {{ $t('page-text', props) }}
    </template>
    <template v-slot:item="{ item }">
      <evidence-archive-row :evidence="item"/>
    </template>
  </v-data-table>
</template>

<script>
  import { mapState } from 'vuex';
  import EvidenceArchiveRow from './EvidenceArchiveRow';
  import { contractsName } from '../../util/contracts';

  export default {
    name: 'EvidenceArchive',
    components: { EvidenceArchiveRow },
    props: {
      loaded: { type: Boolean, default: false },
    },
    data() { return {
      headers: [{
        text: '#',
        value: 'id',
        align: 'center',
        width: 60,
      }, {
        text: this.$t('Month'),
        value: 'month',
        align: 'center',
      }, {
        text: this.$t('Status'),
        value: 'status',
        align: 'center',
        sortable: false,
      }, {
        text: this.$t('Working hours'),
        value: 'workingHours',
        align: 'center',
        sortable: false,
        width: 200,
      }, {
        text: this.$t('Overtime hours'),
        value: 'overtime',
        align: 'center',
        sortable: false,
        width: 200,
      }, {
        text: this.$t('Date of creation'),
        value: 'createdAt',
        align: 'center',
        width: 250,
      }],
      pagination: {
        descending: true,
        sortBy: 'id',
      },
    };},
    computed: {
      ...mapState({
        evidences: state => state.Evidences.evidences,
      }),
      checkHeaders() {
        if (this.evidences[0] !== undefined) {
          if (this.evidences[0].employee.contract.id === Number(contractsName.CoE.id)) {
            this.headers.splice(3, 1);
            return this.headers;
          } else {
            return this.headers;
          }
        } else {
          return [];
        }
      },
    },
    methods: {
      moveToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      },
    },
    i18n: { messages: {
      pl: {
        'No results found.': 'Nie znaleziono.',
        'Rows per page:': 'Wierszy na stronę:',
        'All': 'Wszystkie',
        'Month': 'Miesiąc',
        'Date of creation': 'Data utworzenia ewidencji',
        'Status': 'Status',
        'Overtime hours': 'Suma godzin dodatkowych',
        'Working hours': 'Suma godzin pracujących',
        'page-text': 'Ewidencje {pageStart}-{pageStop} z {itemsLength}',
      },
      en: {
        'page-text': 'Ewidencje {pageStart}-{pageStop} of {itemsLength}',
      },
    } },
  };
</script>
