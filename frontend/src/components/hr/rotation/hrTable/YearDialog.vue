<template>
  <v-dialog v-model="dialogVisible">
    <v-card>
      <v-app-bar color="transparent" flat >
        <span class="headline">{{ $t('year', [ year ]) }}</span>
        <v-spacer/>
        <v-btn ref="focusButton" @click="dialogVisible = false" icon><v-icon>close</v-icon></v-btn>
      </v-app-bar>
      <v-divider/>
      <v-card-text :class="{'pa-0': $vuetify.breakpoint.xs}">
        <hr-table :statistics="stats" :loading="loading" year/>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../../../eventbus';
  import HrTable from './HrTable';

  export default {
    name: 'YearDialog',
    components: { HrTable },
    data() {
      return {
        dialogVisible: false,
        year: -1,
        stats: [],
        loading: false,
      };
    },
    methods: {
      async show({ year }) {
        this.year = year;
        this.stats = [];
        this.dialogVisible = true;
        this.loading = true;
        this.stats = await this.$store.dispatch('Stats/loadRotationForYear', { year });
        this.loading = false;
      },
      getFocus() {
        this.$nextTick(() => this.$refs.focusButton.$el.focus());
      },
    },
    mounted() {
      EventBus.$on(eventNames.hrStatsShowYear, this.show);
      EventBus.$on(eventNames.getFocus, this.getFocus);
    },
    i18n: {
      messages: {
        pl: {
          year: 'Rok {0}',
        },
        en: {
          year: 'Year {0}',
        },
      },
    },
  };
</script>
