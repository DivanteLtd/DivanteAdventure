<template>
  <v-data-table :loading="loading"
                :items-per-page="15"
                :items="displayEntries"
                :headers="headers"
                item-key="id">
    <template v-slot:item="{ item }">
      <free-day-row :item="item"/>
    </template>
  </v-data-table>
</template>

<script>
  import { mapState } from 'vuex';
  import FreeDayRow from './FreeDayRow';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'FreeDaysTable',
    components: { FreeDayRow },
    props: {
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        headers: [
          {
            text: this.$t('Name'),
            value: 'name',
            align: 'center',
            sortable: true,
          },
          {
            text: this.$t('(Next) date'),
            value: 'date',
            align: 'center',
            sortable: true,
          },
          {
            text: this.$t('Repeating every year'),
            value: 'repeating',
            align: 'center',
            sortable: false,
          },
          {
            text: this.$t('Enabled'),
            value: 'enabled',
            align: 'center',
            sortable: false,
          },
          {
            text: this.$t('Actions'),
            value: 'id',
            align: 'center',
            sortable: false,
          },
        ],
      };
    },
    computed: {
      ...mapState({
        entries: state => state.Config.freeDays,
      }),
      displayEntries() {
        return this.entries.map(this.entryMap).sort(this.entrySort);
      },
    },
    methods: {
      entrySort(a, b) {
        const momentA = moment(a.date);
        const momentB = moment(b.date);
        const currentMoment = moment();
        if (currentMoment.isBetween(momentA, momentB, 'day')) {
          return 1;
        } else if (currentMoment.isBetween(momentB, momentA, 'day')) {
          return -1;
        } else if (momentA.isBefore(momentB, 'day')) {
          return -1;
        } else if (momentA.isAfter(momentB, 'day')) {
          return 1;
        } else {
          return 0;
        }
      },
      entryMap(e) {
        const calculated = e.hasOwnProperty('calculationType') && e.calculationType > 0;
        return {
          id: e.id,
          name: calculated ? this.$t(`config.freeDays.${e.name}`) : e.name,
          date: e.date,
          repeating: e.repeating || false,
          enabled: e.enabled,
          calculated,
        };
      },
    },
    i18n: {
      messages: {
        pl: {
          'Name': 'Nazwa',
          '(Next) date': '(Następna) data',
          'Repeating every year': 'Powtarzane co roku',
          'Enabled': 'Włączone',
          'Actions': 'Akcje',
        },
      },
    },
  };
</script>
