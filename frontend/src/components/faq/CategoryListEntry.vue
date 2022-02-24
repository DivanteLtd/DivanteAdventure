<template>
  <v-expansion-panel class="pa-3" v-if="filter.length > 0">
    <v-expansion-panel-header>
      <v-toolbar-title>
        <h5>{{ categoryQuestions[$t('name-key')] }}</h5>
      </v-toolbar-title>
    </v-expansion-panel-header>
    <v-expansion-panel-content>
      <v-card-text class="pt-0">
        <questions-list :search="search"
                        :category="category"
                        :questions="filter"
                        :show-question-id="showQuestionId"
                        @reload="$emit('reload')"/>
      </v-card-text>
    </v-expansion-panel-content>
  </v-expansion-panel>
</template>

<script>
  import QuestionsList from './QuestionsList';

  export default {
    name: 'CategoryListEntry',
    components: { QuestionsList },
    props: {
      category: { type: Object, required: true },
      categoryQuestions: { type: Object, required: true },
      search: { type: String, default: '' },
      showQuestionId: { type: Number, default: - 1 },
    },
    computed: {
      filter() {
        if (this.search === '') {
          return this.categoryQuestions.questions;
        } else {
          const searchLower = this.search.toLowerCase().split(/[ ,.;]+/);
          return this.categoryQuestions.questions.filter(question => {
            const entryPartA = `${question.questionPl} ${question.questionEn}`;
            const entryPartB = `${question.answerPl} ${question.answerEn}`;
            const entryPartC = `${question.author.name} ${question.author.lastName} ${question.author.email}`;
            const entryPartD = `${this.categoryQuestions.namePl} ${this.categoryQuestions.nameEn}`;
            const entry = `${entryPartA} ${entryPartB} ${entryPartC} ${entryPartD}`.toLowerCase();
            return searchLower.map(key => entry.includes(key)).reduce((a, b) => a && b, true);
          });
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'name-key': 'namePl',
        },
        en: {
          'name-key': 'nameEn',
        },
      },
    },
  };
</script>
