<template>
  <v-dialog v-model="dialogVisible">
    <v-card>
      <v-app-bar color="transparent" flat >
        <span class="headline">{{ $t('month', { month: $t(`date.months.${month - 1}`), year: year }) }}</span>
        <v-spacer/>
        <v-btn ref="focusButton" @click="dialogVisible = false" icon><v-icon>close</v-icon></v-btn>
      </v-app-bar>
      <v-divider/>
      <v-card-text>
        <hr-table :statistics="stats" :loading="loading" month/>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../../../eventbus';
  import HrTable from './HrTable';

  export default {
    name: 'MonthDialog',
    components: { HrTable },
    data() {
      return {
        dialogVisible: false,
        year: -1,
        month: -1,
        stats: [],
        loading: false,
      };
    },
    watch: {
      dialogVisible() {
        if (this.dialogVisible === false) {
          EventBus.$emit(eventNames.getFocus);
        }
      },
    },
    methods: {
      async show({ year, month }) {
        this.stats = [];
        this.dialogVisible = true;
        this.loading = true;
        this.stats = await this.$store.dispatch('Stats/loadRotationForMonth', { year, month });
        this.loading = false;
        this.$nextTick(() => this.$refs.focusButton.$el.focus());
        this.year = year;
        this.month = month;
      },
    },
    mounted() {
      EventBus.$on(eventNames.hrStatsShowMonth, this.show);
    },
    i18n: {
      messages: {
        pl: {
          month: '{month} {year}',
        },
        en: {
          month: '{month} {year}',
        },
      },
    },
  };
</script>
