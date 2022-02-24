<template>
  <div>
    <v-progress-linear height="6" v-if="loading" indeterminate/>
    <v-container class="mb-3">
      <v-row no-gutters wrap v-if="isSuperAdmin" class="justify-end"
             :class="{'pb-2 pt-2': $vuetify.breakpoint.xs, 'pb-3': $vuetify.breakpoint.smAndUp}">
      </v-row>
      <v-alert v-if="!myPeriods" :value="true" type="info">{{ $t('no-period') }}</v-alert>
      <v-expansion-panels v-model="checkPeriods" multiple>
        <period-view v-for="(period, index) in myPeriods" :key="index" :period="period"/>
      </v-expansion-panels>
    </v-container>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex';
  import PeriodView from './PeriodView';
  import { EventBus, eventNames } from '../../eventbus';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'PeriodViewContainer',
    components: { PeriodView },
    props: {
      loading: { type: Boolean, required: true },
      periodUpdateFunction: { type: Function, required: true },
    },
    data() { return {
      openFirstPeriod: false,
    };},
    computed: {
      ...mapState({
        myPeriods: state => state.FreeDays.myPeriods,
      }),
      ...mapGetters({
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
      checkPeriods: {
        get() {
          if (this.openFirstPeriod) {
            return [0];
          } else if (this.myPeriods[1] !== undefined) {
            return moment(this.myPeriods[0].dateTo) >= new Date()
              && moment(this.myPeriods[0].dateFrom) <= new Date() ? [0] : [1];
          } else {return [0];}
        },
        set() {},
      },
    },
    methods: {
      callUpdate() {
        this.openFirstPeriod = true;
        this.periodUpdateFunction();
      },
      createNewPeriod() {
        const data = {};
        if (this.myPeriods.length > 0) {
          data.employee = this.myPeriods[0].employee;
          data.lastPeriod = this.myPeriods.sort((a, b) => b.dateFromMoment.unix() - a.dateFromMoment.unix())[0];
        }
        EventBus.$emit(eventNames.createEditNewPeriod, data);
      },
    },
    mounted() {
      EventBus.$on(eventNames.createNewLeaveRequestAfter, this.callUpdate);
      EventBus.$on(eventNames.reloadPeriods, this.callUpdate);
    },
    i18n: { messages: {
      pl: {
        'Create new period': 'Utwórz nowy okres',
        'no-period': 'Nie posiadasz żadnych okresów',
      },
      en: {
        'no-period': 'You don\'t have any periods',
      },
    } },
  };
</script>
