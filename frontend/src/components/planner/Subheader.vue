<template>
  <div>
    <v-container fluid>
      <v-row no-gutters wrap align-center>
        <v-col md="3" sm="12">
          <v-btn @click="saveButtonClicked">{{ $t('Save') }}</v-btn>
          <v-btn @click="loadButtonClicked">{{ $t('Load') }}</v-btn>
          <v-btn @click="exportButtonClicked">{{ $t('Export') }}</v-btn>
        </v-col>
        <v-col md="2" sm="12"><button-group v-model="viewMode" :items="modes"/></v-col>
        <v-col md="3" sm="12"><time-pager/></v-col>
        <v-col md="2" sm="12"><button-group v-model="dataMode" :items="dataModes"/></v-col>
        <v-col md="2" sm="12"><button-group v-model="timeMode" :items="times"/></v-col>
        <v-col md="12" sm="12" class="align-left"><selected-filters/></v-col>
        <loading-dialog :visible="visible"/>
      </v-row>
    </v-container>
  </div>
</template>

<script>
  import { createDataModeFilterEntries } from '../../util/planner/DataMode';
  import { createViewModeFilterEntries } from '../../util/planner/ViewMode';
  import { createTimeModeFilterEntries } from '../../util/planner/TimeMode';
  import { EventBus, eventNames } from '../../eventbus';
  import ButtonGroup from './ButtonGroup';
  import TimePager from './TimePager';
  import SelectedFilters from './filters/SelectedFilters';
  import LoadingDialog from '../utils/LoadingDialog';
  import { mapActions, mapGetters } from 'vuex';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'Subheader',
    components: { SelectedFilters, TimePager, ButtonGroup, LoadingDialog },
    data() {
      return {
        visible: false,
        dataModes: createDataModeFilterEntries().map(this.updateLabel),
        modes: createViewModeFilterEntries().map(this.updateLabel),
        times: createTimeModeFilterEntries().map(this.updateLabel),
      };
    },
    computed: {
      currentDate: {
        get() {
          return this.$store.state.Planner.Time.currentDate;
        },
      },
      timeModeObject: {
        get() {
          return this.$store.state.Planner.Time.timeMode;
        },
      },
      dataMode: {
        get() {
          return this.$store.state.Planner.dataMode.value;
        },
        set(value) {
          this.$store.commit('Planner/setDataMode', value);
        },
      },
      viewMode: {
        get() {
          return this.$store.state.Planner.viewMode.value;
        },
        set(value) {
          this.$store.commit('Planner/setMode', value);
          this.$store.commit('Planner/Filters/clearFilters');
        },
      },
      timeMode: {
        get() {
          return this.$store.state.Planner.Time.timeMode.value;
        },
        set(value) {
          this.$store.commit('Planner/Time/setTimeMode', value);
          this.$store.dispatch('Planner/loadEntries');
        },
      },
    },
    methods: {
      ...mapActions('Planner/Filters', [
        'loadQuery',
      ]),
      ...mapGetters('Planner/Filters', ['getQuery']),

      updateLabel(btn) {
        btn.label = this.$t(btn.label);
        return btn;
      },
      saveButtonClicked() {
        EventBus.$emit(eventNames.showSaveFilterDialog);
      },
      loadButtonClicked() {
        EventBus.$emit(eventNames.showLoadFilterDialog);
      },
      exportButtonClicked() {
        this.loadQuery();
        this.visible = true;
        const borders = this.timeModeObject.displayControl.getBorders(moment(this.currentDate));
        const beginning = borders.beginning.unix();
        const ending = borders.ending.unix();
        this.$store.dispatch('Planner/createReport', {
          query: this.getQuery(),
          timestamp_gte: beginning,
          timestamp_lte: ending,
          view_mode: this.viewMode,
        }).finally(() => {
          setTimeout(() => {
            this.visible = false;
          }, 1000);
        });
      },
    },
    i18n: {
      messages: {
        pl: {
          Employee: 'Osoba',
          Project: 'Projekt',
          Hours: 'Godziny',
          Worktime: 'Etat',
          Day: 'Dzień',
          Week: 'Tydzień',
          Month: 'Miesiąc',
          Save: 'Zapisz',
          Load: 'Wczytaj',
          Export: 'Eksportuj',
        },
      },
    },
  };
</script>
<style scoped>
  .align-left {
    text-align: left;
  }
</style>
