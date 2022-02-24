<template>
  <div>
    <v-btn text icon @click="left"><v-icon>chevron_left</v-icon></v-btn>
    <h3 class="current-time">{{ dateLabel }}</h3>
    <v-btn text icon @click="right"><v-icon>chevron_right</v-icon></v-btn>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';

  export default {
    name: 'TimePager',
    computed: {
      ...mapGetters({
        currentDateLabel: 'Planner/Time/currentDateLabel',
      }),
      dateLabel() {
        if (typeof(this.currentDateLabel) === 'object') {
          const i18n = this.currentDateLabel.i18n;
          const params = this.currentDateLabel.params;
          return this.$t(i18n, params);
        } else {
          return this.currentDateLabel;
        }
      },
    },
    methods: {
      left() {
        this.$store.commit('Planner/Time/subtractFromTime');
        this.$store.dispatch('Planner/reloadAfterTimeChange');
      },
      right() {
        this.$store.commit('Planner/Time/addToTime');
        this.$store.dispatch('Planner/reloadAfterTimeChange');
      },
    },
    i18n: {
      messages: {
        en: { 'nth-quarter': '{quarterRoman} quarter {year}' },
        pl: { 'nth-quarter': '{quarterRoman} kwartał {year}' },
      },
    },
  };
</script>

<style scoped>
  .current-time {
    display: inline-block;
    text-align: center;
    width: 200px;
  }
</style>
