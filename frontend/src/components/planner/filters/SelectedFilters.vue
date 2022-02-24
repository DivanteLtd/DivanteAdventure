<template>
  <div>
    <v-chip v-for="(item, index) in selectedFilters" :key="index"
            @update:active="deleteItem(item)"
            :click:close="!disableClosing"
            small outlined>
      {{ getLabel(item) }}
    </v-chip>
  </div>
</template>

<script>
  import { mapState } from 'vuex';

  export default {
    name: 'SelectedFilters',
    props: {
      disableClosing: { type: Boolean, default: false },
    },
    computed: {
      ...mapState({
        selectedFilters: state => state.Planner.Filters.selectedFilters,
      }),
    },
    methods: {
      deleteItem(item) {
        this.$store.commit('Planner/Filters/removeFilter', item);
      },
      getLabel(item) {
        let label = item.filter.label;
        if (typeof(label) === 'function') {
          label = label(item.enteredText);
        }
        if (typeof(label) === 'object') {
          label = this.$t(label.i18n, label.params);
        }
        return label;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Search': 'Szukaj',
          'No filters available': 'Brak dostępnych filtrów',
          'filter_by_name': 'Znajdź: {name}',
          'filter_by_summaryLesserThan': 'Bilans mniejszy niż {value}h',
          'filter_by_summaryGreaterThan': 'Bilans większy niż {value}h',
          'filter_by_assignedWorktimeLesserThan': 'Zajęść etatu mniejsza niż {value}',
          'filter_by_assignedWorktimeGreaterThan': 'Zajęść etatu większa niż {value}',
          'filter_by_worktimeLesserThan': 'Etat mniejszy niż {value}',
          'filter_by_worktimeGreaterThan': 'Etat większy niż {value}',
          'filter_by_assignmentPercentageLesserThan': 'Zajętość mniejsza niż {value}%',
          'filter_by_assignmentPercentageGreaterThan': 'Zajętość większa niż {value}%',
        },
        en: {
          filter_by_name: 'Find: {name}',
          filter_by_summaryLesserThan: 'Summary lesser than {value}h',
          filter_by_summaryGreaterThan: 'Summary greater than {value}h',
          filter_by_assignedWorktimeLesserThan: 'Assigned worktime lesser than {value}',
          filter_by_assignedWorktimeGreaterThan: 'Assigned worktime greater than {value}',
          filter_by_worktimeLesserThan: 'Worktime lesser than {value}',
          filter_by_worktimeGreaterThan: 'Worktime greater than {value}',
          filter_by_assignmentPercentageLesserThan: 'Assignment lesser than {value}%',
          filter_by_assignmentPercentageGreaterThan: 'Assignment greater than {value}%',
        },
      },
    },
  };
</script>
