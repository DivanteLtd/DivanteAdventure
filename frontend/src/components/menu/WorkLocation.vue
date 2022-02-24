<template>
  <v-menu :close-on-content-click="false"
          v-model="menuOpened" :max-width="calculatedWidth" offset-y down>
    <template v-slot:activator="{ on }">
      <v-btn icon large text v-on="on">
        <v-icon :color="checkToday">
          {{ icon }}
        </v-icon>
      </v-btn>
    </template>
    <v-list class="pt-0">
      <v-list-item-content class="align-center pb-0" :class="{'pa-0': $vuetify.breakpoint.xs}">
        <v-list-item :class="{'mb-2': $vuetify.breakpoint.smAndUp}">
          <span :class="{'title': $vuetify.breakpoint.smAndUp}">{{ getTitle }}</span>
        </v-list-item>
        <v-date-picker color="indigo" v-model="days"
                       :locale="locale"
                       :min="today"
                       :first-day-of-week="$t('date.firstDayOfWeek')"
                       :allowed-dates="isDateAllowed"
                       class="elevation-0"
                       full-width multiple no-title/>
      </v-list-item-content>
      <v-list-item-content class="pt-0 ml-2 mr-2">
        <v-list-item v-for="(item, index) in chosenManagers" :key="index">
          <v-list-item-title>
            <employee-chip v-if="typeof(item) === 'object'" :employee="item" />
          </v-list-item-title>
          <v-tooltip left>
            <template v-slot:activator="{ on }">
              <v-btn v-on="on" @click="deleteManager(item)" icon>
                <v-icon>cancel</v-icon>
              </v-btn>
            </template>
            {{ $t('Cancel') }}
          </v-tooltip>
        </v-list-item>
      </v-list-item-content>
      <v-list-item-content class="ml-2 mr-2">
        <v-btn class="pt-1 pb-1 subheading" color="success" block @click="confirm" :loading="loading"
               :disabled="compareArrays">
          {{ $t('Confirm') }}
        </v-btn>
      </v-list-item-content>
    </v-list>
  </v-menu>
</template>
<script>
  import { mapState } from 'vuex';
  import moment from '@divante-adventure/work-moment';
  import EmployeeChip from '../utils/EmployeeChip';
  import { getSuggestedLanguage } from '../../i18n/i18n';

  const REMOTELY_TYPE = 1;
  const DELEGATION_TYPE = 2;

  export default {
    name: 'WorkLocation',
    components: { EmployeeChip },
    props: {
      allManagers: { type: Array, required: true },
      workLocationType: { type: Number, required: true },
      icon: { type: String, required: true },
      employee: { type: Object, required: true },
    },
    data() {
      return {
        today: moment().format('YYYY-MM-DD'),
        locale: getSuggestedLanguage(),
        days: [],
        manager: {},
        managers: [],
        chosenManagers: [],
        menuOpened: false,
        loading: false,
      };
    },
    computed: {
      ...mapState({
        savedWorkLocations: state => state.Employees.employeeWorkLocations,
        confirmedLeaveDays: state => state.FreeDays.confirmedLeaveDays,
        statutoryFreeDays: state => state.FreeDays.statutoryFreeDays,
      }),
      getTitle() {
        if(typeof this.employee.contract !== 'undefined') {
          if (this.workLocationType === DELEGATION_TYPE) {
            switch (this.employee.contract.name) {
              case 'CoE': return this.$t('Provide the period of a business trip');
              default: return this.$t('Provide the period of trip');
            }
          } else {
            switch (this.employee.contract.name) {
              case 'CoE': return this.$t('Period of remote work');
              case 'CLC': return this.$t('Period of execution of the order in remote mode');
              default: return this.$t('The period of providing services in remote mode');
            }
          }
        } else {
          return '';
        }
      },
      calculatedWidth() {
        return this.$vuetify.breakpoint.smAndUp ? 500 : 250;
      },
      currentTypeWorkLocations() {
        return this.savedWorkLocations
          .filter(val => val.type === this.workLocationType
            && moment(val.date).isSameOrAfter(moment().subtract(1, 'days')))
          .map(val => moment(val.date).format('YYYY-MM-DD'));
      },
      excludedWorkLocations() {
        return this.savedWorkLocations
          .filter(val => val.type !== this.workLocationType)
          .map(val => moment(val.date).format('YYYY-MM-DD'));
      },
      checkToday() {
        return this.currentTypeWorkLocations.includes(moment().format('YYYY-MM-DD')) && !this.employee.freeToday
          ? 'green' : '';
      },
      compareArrays() {
        return (this.currentTypeWorkLocations.every(elem => this.days.includes(elem))
          && this.days.every(elem => this.currentTypeWorkLocations.includes(elem)));
      },
      getManagerLabel() {
        return this.managers.length === 0 ? this.$t('Choose manager to send a notification and click add')
          : this.$t('Choose another manager to send a notification and click add');
      },
    },
    watch: {
      currentTypeWorkLocations() {
        this.days = [...this.currentTypeWorkLocations];
      },
      menuOpened() {
        this.days = [...this.currentTypeWorkLocations];
        this.chosenManagers = [];
        this.managers = this.allManagers;
      },
    },
    methods: {
      deleteManager(item) {
        this.chosenManagers = this.chosenManagers.filter(val => val !== item);
        this.managers.push(item);
      },
      addManager() {
        this.managers = this.managers.filter(val => val !== this.manager);
        this.chosenManagers.push(this.manager);
        this.manager = {};
      },
      isDateAllowed(date) {
        const weekDay = moment(date, 'YYYY-MM-DD').day();
        const leaveDaysArray = this.confirmedLeaveDays.map(val => moment(val.date).format('YYYY-MM-DD'));
        const statutoryFreeDays = this.statutoryFreeDays.map(val => moment.unix(val).format('YYYY-MM-DD'));
        return !(weekDay === 0 || weekDay === 6
          || leaveDaysArray.includes(date)
          || statutoryFreeDays.includes(date)
          || this.excludedWorkLocations.includes(date));
      },
      async confirm() {
        this.loading = true;
        let successText = '';
        let failText = '';
        if(this.workLocationType === DELEGATION_TYPE) {
          successText = this.$t('Time of work in trip have been confirmed');
          failText = this.$t('Time of work in trip can not be confirmed');
        } else if(this.workLocationType === REMOTELY_TYPE) {
          successText = this.$t('Time of remote work have been confirmed');
          failText = this.$t('Time of remote work can not be confirmed');
        } else {
          successText = this.$t('Time of work location have been confirmed');
          failText = this.$t('Time of work location can not be confirmed');
        }
        try {
          await this.$store.dispatch('Employees/saveWorkLocation',
                                     {
                                       type: this.workLocationType,
                                       dates: this.days,
                                       managers: this.chosenManagers.map(val => val.id),
                                     });
          await this.$store.dispatch('Employees/loadEmployeesTodayWorkLocations');
          this.menuOpened = false;
          this.$store.commit('showSnackbar', { text: successText, color: 'success' });
        } catch (e) {
          this.$store.commit('showSnackbar', { text: failText, color: 'error' });
        }
        this.loading = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Confirm': 'Potwierdź',
          'Add': 'Dodaj',
          'Choose manager to send a notification and click add': 'Wybierz osobę aby wysłać powiadomienie i kliknij dodaj',
          'Choose another manager to send a notification and click add': 'Wybierz następną osobę aby wysłać powiadomienie i kliknij dodaj',
          'Period of execution of the order in remote mode': 'Okres realizacji zlecenia w trybie zdalnym',
          'The period of providing services in remote mode': 'Okres świadczenia usług w trybie zdalnym',
          'Period of remote work': 'Okres pracy zdalnej',
          'Provide the period of a business trip': 'Podaj okres podróży służbowej',
          'Provide the period of trip': 'Podaj okres podróży',
          'Time of work in delegation have been confirmed': 'Czas pracy w podróży został potwierdzony',
          'Time of work in delegation can not be confirmed': 'Czas pracy w podróży nie został potwierdzony',
          'Time of work location have been confirmed': 'Czas pracy w podróży został potwierdzony',
          'Time of work location can not be confirmed': 'Czas pracy w podróży nie został potwierdzony',
          'Time of remote work have been confirmed': 'Czas pracy zdalnej został potwierdzony',
          'Time of remote work can not be confirmed': 'Czas pracy zdalnej nie został potwierdzony',
        },
      },
    },
  };
</script>
