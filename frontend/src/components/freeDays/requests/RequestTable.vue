<template>
  <v-data-table mobile-breakpoint="0" :items-per-page="5"
                :no-results-text="$t('No results found.')"
                :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                :items="requests"
                :headers="headers"
                :sort-by="['id']"
                :sort-desc="[true]"
                must-sort>
    <template slot="no-data">
      <v-alert class="mt-4" :value="true" type="warning" icon="warning">
        <h3>
          <span class="mr-5">{{ $t('No request') }}</span>
        </h3>
      </v-alert>
    </template>
    <template v-slot:item="{ item }">
      <request-row :request="item" :period-start-date="period.dateFromMoment" :reduced="reduced"/>
      <request-dates-row v-if="!reduced" :request="item" :period-start-date="period.dateFromMoment"/>
    </template>
    <template slot="pageText" slot-scope="props">
      {{ $t('page-text', props) }}
    </template>
  </v-data-table>
</template>

<script>
  import { eventNames, EventBus } from '../../../eventbus';
  import moment from '@divante-adventure/work-moment';
  import RequestRow from './RequestRow';
  import { mapGetters } from 'vuex';
  import RequestDatesRow from './RequestDatesRow';

  export default {
    name: 'RequestTable',
    components: { RequestDatesRow, RequestRow },
    props: {
      period: { type: Object, required: true },
      reduced: { type: Boolean, default: false },
    },
    data() { return {
      headers: [{
        text: 'ID',
        value: 'id',
        align: 'center',
      }, {
        text: this.$t('Date of application'),
        value: 'createdAt',
        align: 'center',
      }, {
        text: this.$t('Supervisor'),
        value: 'manager.lastName',
        align: 'center',
      }, {
        text: this.$t('Status'),
        value: 'status',
        align: 'center',
      }, {
        text: '',
        sortable: false,
        align: 'center',
      }],
      pagination: {
        descending: true,
        sortBy: 'id',
      },
    };},
    computed: {
      ...mapGetters({
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
      canRequest() {
        return moment(this.period.dateToMoment).endOf('day') >= moment();
      },
      requests() {
        const requests = this.period.requests;
        if (!this.isSuperAdmin) {
          return requests.filter(request => !request.hidden);
        }
        return requests;
      },
    },
    methods: {
      createNewRequest() {
        EventBus.$emit(eventNames.createNewLeaveRequest, this.period);
      },
    },
    i18n: { messages: {
      pl: {
        'No request': 'Brak wniosków',
        'Create a new request': 'Utwórz nowy wniosek',
        'No results found.': 'Nie znaleziono.',
        'Rows per page:': 'Wierszy na stronę:',
        'All': 'Wszystkie',
        'Date of application': 'Data złożenia wniosku',
        'Supervisor': 'Akceptujący',
        'Status': 'Status',
        'page-text': 'Wnioski {pageStart}-{pageStop} z {itemsLength}',
      },
      en: {
        'page-text': 'Requests {pageStart}-{pageStop} of {itemsLength}',
        'Supervisor': 'Approver',
      },
    } },
  };
</script>
