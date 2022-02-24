<template>
  <div>
    <v-text-field
      v-model="query"
      append-icon="search"
      @keyup.enter="enter()"
      placeholder="Eg. firstName='Pawel' or practiceName='Undefined'"
    >
      <v-tooltip slot="prepend" right>
        <template v-slot:activator="{ on }">
          <v-icon v-on="on" color="primary" dark>help</v-icon>
        </template>
        <span v-html="$t('Tooltip')">{{ $t('Tooltip') }}</span>
      </v-tooltip>
    </v-text-field>
  </div>
</template>

<script>
  import { mapActions, mapMutations, mapGetters } from 'vuex';

  export default {
    name: 'FilterField',
    data() {
      return {
        dialogVisible: false,
      };
    },
    computed: {
      query: {
        get() {
          return this.getQuery();
        },
        set(val) {
          this.setQuery(val);
        },
      },
    },
    methods: {
      ...mapActions('Planner/Filters', [
        'filterByQuery', 'loadQuery',
      ]),
      ...mapMutations('Planner/Filters', ['setQuery']),
      ...mapGetters('Planner/Filters', ['getQuery']),
      enter() {
        this.filterByQuery(this.query);
      },
    },
    mounted() {
      this.loadQuery();
    },
    i18n: { messages: {
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
        'Search. You can use firstName, lastName, tribeName, levelName, projectName and positionName': 'Szukaj. Możesz używać firstName, lastName, tribeName, levelName, projectName and positionName',
        'Tooltip': 'Dodaliśmy obsługę języka quasi SQL. Możesz używać takich operatorów jak:'
          + ' <b>=</b>, <b>LIKE</b>, <b>%</b>, <b>AND</b> i <b>OR</b>.'
          + ' Możesz budować zapytania z takich pól jak: <b>firsName</b>, <b>lastName</b>, <b>tribeName</b>,'
          + ' <b>levelName</b>, <b>projectName</b> i <b>positionName</b>',
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
        Tooltip: 'We added quasi SQL language.  You can use operators:'
          + '<b>=</b>, <b>LIKE</b>, <b>%</b>, <b>AND</b> and <b>OR</b>.'
          + 'You can build questions with fields like: <b>firsName</b>, <b>lastName</b>, <b>tribeName</b>,'
          + ' <b>levelName</b>, <b>projectName</b> and <b>positionName</b>',
      },
    } },
  };
</script>
