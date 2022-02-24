<template>
  <v-menu bottom offset-y max-width="300">
    <template v-slot:activator="{ on }">
      <v-text-field v-on="on" v-model="picker" :label="$t('Choose date')" readonly/>
    </template>

    <v-date-picker
      v-model="picker"
      :locale="locale"
      type="month"
      :min="minDate.format('YYYY-MM-DD')"
      :max="new Date().toISOString().substr(0, 10)" no-title/>
  </v-menu>
</template>
<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import { mapGetters } from 'vuex';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'TribeStatsCalendar',
    data() {
      return {
        picker: moment().format('YYYY-MM'),
        locale: getSuggestedLanguage(),
      };
    },
    computed: {
      ...mapGetters('Stats', {
        minDate: 'minDate',
      }),
    },
    watch: {
      picker(newValue) {
        EventBus.$emit(eventNames.selectDate, newValue);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Choose date': 'Wybierz datę',
        },
      },
    },
  };
</script>
