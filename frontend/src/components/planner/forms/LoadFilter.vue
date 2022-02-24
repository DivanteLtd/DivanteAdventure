<template>
  <dialog-form :show-event="showEvent" max-width="400" :title="$t('Loading filters')" @show="show" @save="apply" load>
    <v-autocomplete :items="savedSettings" v-model="chosenSetting" :rules="[rules.noEmpty]" hide-no-data/>
  </dialog-form>
</template>

<script>
  import DialogForm from './DialogForm';
  import { eventNames } from '../../../eventbus';
  import DataStorage from '@divante/data-storage';
  import { mapGetters } from 'vuex';

  export default {
    name: 'LoadFilter',
    components: { DialogForm },
    data() { return {
      showEvent: eventNames.showLoadFilterDialog,
      chosenSetting: undefined,
      rules: {
        noEmpty: value => typeof(value) !== 'undefined' || this.$t('This field is required'),
      },
    };},
    computed: {
      ...mapGetters({
        savedSettings: 'Planner/SavedFilters/savedFiltersAsArray',
        stateFilters: 'Planner/Filters/getFilters',
      }),
    },
    methods: {
      apply() {
        this.$store.commit('Planner/setDataMode', this.chosenSetting.dataMode);
        this.$store.commit('Planner/setMode', this.chosenSetting.viewMode);
        this.$store.commit('Planner/Time/setTimeMode', this.chosenSetting.timeMode);
        this.$store.commit('Planner/Filters/setQuery', this.chosenSetting.query);
        this.$store.dispatch('Planner/Filters/filterByQuery', this.chosenSetting.query);
        this.$store.dispatch('Planner/loadEntries');

        this.$store.commit('Planner/Filters/clearFilters');
        new DataStorage().setObjectValue('filters', this.chosenSetting.filters);
        this.$store.commit('Planner/Filters/loadSelectedFiltersFromStorage', this.stateFilters);
      },
      show() {
        this.chosenSetting = undefined;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Loading filters': 'Wczytywanie filtrów',
          'This field is required': 'To pole jest wymagane',
        },
      },
    },
  };
</script>
