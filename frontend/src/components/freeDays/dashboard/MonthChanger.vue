<template>
  <v-container class="centered" id="month-changer">
    <v-row no-gutters wrap>
      <v-spacer/>
      <v-col cols="2" sm="1">
        <v-btn icon class="pa-0 ma-0" @click="decrement">
          <v-icon>chevron_left</v-icon>
        </v-btn>
      </v-col>
      <v-col cols="8" sm="3">
        <v-menu v-model="pickerVisible"
                transition="scale-transition"
                :close-on-content-click="false"
                lazy offset-y>
          <template v-slot:activator="{ on }">
            <h2 v-on="on" class="hover">
              {{ currentMonth }}
            </h2>
          </template>
          <v-date-picker color="indigo" v-model="pickerModel" type="month" :locale="locale" no-title/>
        </v-menu>
      </v-col>
      <v-col cols="2" sm="1">
        <v-btn icon class="pa-0 ma-0" @click="increment">
          <v-icon>chevron_right</v-icon>
        </v-btn>
      </v-col>
      <v-spacer/>
      <v-col cols="12">
        <v-progress-linear height="6" v-if="loading" indeterminate/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import { mapState } from 'vuex';
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'MonthChanger',
    props: {
      loading: { type: Boolean, required: true },
      updateFunction: { type: Function, required: true },
    },
    data() { return {
      pickerVisible: false,
      locale: getSuggestedLanguage(),
    };},
    computed: {
      ...mapState({
        currentMonthStore: state => state.FreeDays.dashboardDate,
      }),
      currentMonth() {
        return this.currentMonthStore.format(this.$t('month_format'));
      },
      pickerModel: {
        get() {
          return this.currentMonthStore.format('YYYY-MM');
        },
        set(newValue) {
          const newMonth = moment(newValue, 'YYYY-MM');
          this.$store.commit('FreeDays/setDashboardMonth', newMonth);
          this.pickerVisible = false;
        },
      },
    },
    watch: {
      currentMonthStore() {
        this.updateFunction();
      },
    },
    methods: {
      decrement() {
        const newMonth = moment(this.currentMonthStore).subtract({ months: 1 });
        this.$store.commit('FreeDays/setDashboardMonth', newMonth);
      },
      increment() {
        const newMonth = moment(this.currentMonthStore).add({ months: 1 });
        this.$store.commit('FreeDays/setDashboardMonth', newMonth);
      },
    },
    i18n: { messages: {
      en: {
        month_format: 'MMMM YYYY',
      },
    } },
  };
</script>

<style scoped>
  .centered {
    text-align: center;
  }
  .hover:hover {
    color: blue;
  }
</style>
