<template>
  <dialog-form :show-event="showEvent" max-width="800" :title="$t('Saving filters')" @show="show" @save="save">
    <v-col md="12" sm="12" class="headline mb-1">
      {{ $t('Modes:') }}
    </v-col>
    <v-col md="4" sm="12">
      <button-group v-model="viewModeHolder" :items="viewModes"></button-group>
    </v-col>
    <v-col md="4" sm="12">
      <button-group v-model="dataModeHolder" :items="dataModes"></button-group>
    </v-col>
    <v-col md="4" sm="12">
      <button-group v-model="timeModeHolder" :items="timeModes"></button-group>
    </v-col>
    <v-col md="12" class="headline mt-3">
      {{ $t('Filters:') }}
    </v-col>
    <v-col md="12">
      <selected-filters disable-closing/>
    </v-col>
    <v-col md="12">
      <v-text-field v-model="name"
                    :label="$t('Filter name')"
                    :rules="[rules.notEmpty]"
                    required/>
    </v-col>
  </dialog-form>
</template>

<script>
  import DialogForm from './DialogForm';
  import SelectedFilters from '../filters/SelectedFilters';
  import ButtonGroup from '../ButtonGroup';
  import { allModesMixin } from '../../../mixins/PlannerMixins';
  import { createDataModeFilterEntries, getDataModeByValue } from '../../../util/planner/DataMode';
  import { createViewModeFilterEntries, getViewModeByValue } from '../../../util/planner/ViewMode';
  import { createTimeModeFilterEntries, getTimeModeByValue } from '../../../util/planner/TimeMode';
  import { eventNames } from '../../../eventbus';

  export default {
    name: 'SaveFilters',
    components: { ButtonGroup, SelectedFilters, DialogForm },
    mixins: [ allModesMixin ],
    data() {
      return {
        name: '',
        showEvent: eventNames.showSaveFilterDialog,
        dataModes: createDataModeFilterEntries().map(this.updateLabel),
        viewModes: createViewModeFilterEntries().map(this.updateLabel),
        timeModes: createTimeModeFilterEntries().map(this.updateLabel),
        dataModeHolder: '',
        viewModeHolder: '',
        timeModeHolder: '',
        rules: {
          notEmpty: value => value.length > 0 || this.$t('Name cannot be empty.'),
        },
      };
    },
    methods: {
      updateLabel(btn) {
        btn.label = this.$t(btn.label);
        return btn;
      },
      show() {
        this.dataModeHolder = this.dataMode.value;
        this.viewModeHolder = this.viewMode.value;
        this.timeModeHolder = this.timeMode.value;
        this.name = '';
      },
      save() {
        this.$store.dispatch('Planner/SavedFilters/saveFilter', {
          name: this.name,
          dataMode: getDataModeByValue(this.dataModeHolder),
          viewMode: getViewModeByValue(this.viewModeHolder),
          timeMode: getTimeModeByValue(this.timeModeHolder),
        });
      },
    },
    i18n: {
      messages: {
        pl: {
          'Employee': 'Osoba',
          'Project': 'Projekt',
          'Hours': 'Godziny',
          'Worktime': 'Etat',
          'Day': 'Dzień',
          'Week': 'Tydzień',
          'Month': 'Miesiąc',
          'Saving filters': 'Zapisywanie filtrów',
          'Modes:': 'Tryby:',
          'Filters:': 'Filtry:',
          'Filter name': 'Nazwa filtra',
          'Name cannot be empty.': 'Nazwa nie może być pusta.',
        },
      },
    },
  };
</script>
