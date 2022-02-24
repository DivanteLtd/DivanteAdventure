<template>
  <v-card flat>
    <v-card-title><h5>{{ $t('Period') }}</h5></v-card-title>
    <v-divider/>
    <v-list dense>
      <info-tile :title="$t('Beginning of the period:')">
        {{ period.dateFromMoment.format('D MMM YYYY') }} ({{ period.dateFromMoment.fromNow() }})
      </info-tile>
      <info-tile :title="$t('End of the period:')">
        {{ period.dateToMoment.format('D MMM YYYY') }} ({{ period.dateToMoment.fromNow() }})
      </info-tile>
    </v-list>
  </v-card>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import InfoTile from './InfoTile';
  import { eventNames, EventBus } from '../../../eventbus';

  export default {
    name: 'PeriodGeneralInfo',
    components: { InfoTile },
    props: {
      period: { type: Object, required: true },
    },
    computed: {
      canRequest() {
        return moment(this.period.dateToMoment).endOf('day') > moment();
      },
    },
    methods: {
      createNewRequest() {
        EventBus.$emit(eventNames.createNewLeaveRequest, this.period);
      },
    },
    i18n: { messages: {
      pl: {
        'Period': 'Okres',
        'Beginning of the period:': 'Początek okresu:',
        'End of the period:': 'Koniec okresu:',
        'Create a new request': 'Utwórz nowy wniosek',
      },
    } },
  };
</script>
