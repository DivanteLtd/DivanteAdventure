<template>
  <v-data-table mobile-breakpoint="0" :items-per-page="5"
                :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                :items="requests"
                :headers="headers"
                :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                :no-results-text="$t('No results found.')"
                :loading="loading"
                must-sort>
    <template slot="pageText" slot-scope="props">
      {{ $t('page-text', props) }}
    </template>
    <template v-slot:item="{ item }">
      <leave-request-row v-if="item._reqType === 'leave'"
                         :request="item"
                         :awaiting="awaiting"
                         :planned="item._planned"/>
      <overtime-request-row v-else-if="item._reqType === 'overtime'"
                            :request="item"
                            :awaiting="awaiting"/>
    </template>
    <template slot="no-data">
      <v-alert :value="true" type="warning" icon="warning">
        <h3>
          <span>{{ $t('There are no existing applications.') }}</span>
        </h3>
      </v-alert>
    </template>
  </v-data-table>
</template>

<script>
  import LeaveRequestRow from './LeaveRequestRow';
  import OvertimeRequestRow from './OvertimeRequestRow';

  export default {
    name: 'RequestsTable',
    components: { OvertimeRequestRow, LeaveRequestRow },
    props: {
      loading: { type: Boolean, required: true },
      requests: { type: Array, required: true },
      awaiting: { type: Boolean, default: false },
    },
    data() { return {
      headers: [{
        text: '#',
        value: 'id',
      }, {
        text: this.$t('Type'),
        value: '_reqType',
      }, {
        text: this.$t('Applicant'),
        value: 'employee',
      }, {
        text: this.$t('Status'),
        value: 'status',
      }, {
        text: this.$t('Date of submitting the application'),
        value: '_orderTimestamp',
        width: 250,
      }],
      pagination: {
        descending: true,
        sortBy: '_orderTimestamp',
      },
    };},
    i18n: { messages: {
      pl: {
        'Type': 'Typ',
        'Applicant': 'Osoba składająca wniosek',
        'Status': 'Status',
        'Date of submitting the application': 'Data utworzenia wniosku',
        'No results found.': 'Nie znaleziono.',
        'Rows per page:': 'Wierszy na stronę:',
        'All': 'Wszystkie',
        'There are no existing applications.': 'Nie znaleziono żadnych wniosków.',

        'page-text': 'Wnioski {pageStart}-{pageStop} z {itemsLength}',
      },
      en: {
        'page-text': 'Applications {pageStart}-{pageStop} of {itemsLength}',
      },
    } },
  };
</script>
