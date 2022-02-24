<template>
  <td :class="cellClass + ' pa-0 ma-0'" style="text-align: center">
    <v-tooltip width="100%" v-if="cellClass !== ''" bottom>
      <template v-slot:activator="{ on }">
        <v-avatar style="width: 100%" v-on="on" class="pa-0 ma-0">
          <v-icon>{{ icon }}</v-icon>
        </v-avatar>
      </template>
      {{ $t(cellClass) }}
    </v-tooltip>
    <v-avatar v-else class="pa-0 ma-0">
      <v-icon></v-icon>
    </v-avatar>
  </td>
</template>

<script>
  import moment from '@divante-adventure/work-moment';

  const DELEGATION = 2;
  const REMOTE = 1;
  export default {
    name: 'DashboardCell',
    props: {
      items: { type: Array, required: true },
      header: { type: Object, required: true },
      location: { type: Number, required: true },
    },
    computed: {
      icon() {
        if (this.cellClass !== 'weekend' && this.cellClass !== 'today' && this.cellClass !== 'free-day'
          && !this.$store.getters['Authorization/isManager']) {
          return 'work_off';
        }
        if (!this.header.free && this.location === DELEGATION && this.cellClass === 'delegation') {
          return 'emoji_transportation';
        }
        if (!this.header.free && this.location === REMOTE && this.cellClass === 'remote') {
          return 'home';
        }
        switch(this.cellClass) {
          case 'n-a':
          case 'day-off':
          case 'day-off_not': return 'pause_circle_outline';
          case 'sick-leave':
          case 'sick-leave_not': return 'airline_seat_individual_suite';
          case 'planned': return 'watch_later';
          default: return '';
        }
      },
      cellClass() {
        const day = this.day;
        if (day === null && this.header.cssClass === 'pa-0 ma-0 today-column' && this.location === 0) {
          return 'today';
        }
        if (this.header.free) {
          return (this.header.day.day() === 0 || this.header.day.day() === 6) ? 'weekend' : 'free-day';
        }
        if (this.location !== 0 && day === null) {
          return this.location === DELEGATION ? 'delegation' : 'remote';
        }
        if (day === null) {
          return '';
        }
        if (day.type === 'N/A') {
          return 'unavailable';
        }
        if (day.planned) {
          return 'planned';
        }
        const type = day.type;
        return day.notAccepted ? `${type}_not` : type;
      },
      day() {
        if (this.items.length === 0) {
          return null;
        }
        const range = moment(this.header.day).rangeOfDay();
        const days = this.items.filter(item => moment(item.date).within(range));
        if (days.length === 0) {
          return null;
        }
        return days[0];
      },
    },
    i18n: { messages: {
      pl: {
        'planned': 'Zaplanowany dzień wolny',
        'today': 'Dzisiaj',
        'day-off': 'Dzisiaj niedostępny',
        'day-off_not': 'Dzisiaj niedostępny (oczekuje na akceptację)',
        'sick-leave': 'Zwolnienie chorobowe',
        'sick-leave_not': 'Zwolnienie chorobowe (oczekuje na akceptację)',
        'free-day': 'Dzień ustawowo wolny od pracy',
        'weekend': 'Weekend',
        'remote': 'Praca zdalnie',
        'delegation': 'W podróży',
        'n-a': 'Dzisiaj niedostępny',
        'unavailable': 'Niedostępny',
      },
      en: {
        'planned': 'Planned free day',
        'today:': 'Today',
        'day-off': 'Unavailable today',
        'day-off_not': 'Unavailable today (pending)',
        'sick-leave': 'Sick leave',
        'sick-leave_not': 'Sick leave (pending)',
        'free-day': 'Public holiday',
        'weekend': 'Weekend',
        'delegation': 'In trip',
        'remote': 'Work remotely',
        'n-a': 'Unavailable today',
        'unavailable': 'Unavailable',
      },
    } },
  };
</script>

<style scoped>
  .today {
    background-color: cornflowerblue;
  }
  .free-day {
    background-color: pink;
    max-width: 10px !important;
  }
  .weekend {
    background-color: grey;
    max-width: 10px !important;
  }
  .day-off, .n-a {
    background-color: rgba(54, 209, 116, 1);
  }
  .day-off_not {
    background-color: rgba(54, 209, 116, 0.3);
  }
  .planned {
    background-color: rgba(209, 209, 128, 0.3);
  }
  .sick-leave {
    background-color: rgba(128, 0, 128, 1);
  }
  .sick-leave_not {
    background-color: rgba(128, 0, 128, 0.3);
  }
  .unavailable {
    background-color: rgba(255, 0, 0, 1);
  }
  .remote {
    background-color: rgba(231, 128, 54, 1)
  }
  .delegation {
    background-color: rgba(141, 60, 2, 1)
  }
</style>
