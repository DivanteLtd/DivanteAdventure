<template>
  <v-card @click="detailsVisible = true" class="cursor-pointer mr-4 mb-4" hover>
    <question-dialog v-if="detailsVisible"
                     v-model="detailsVisible"
                     :question="questionWithCategoryData"
                     @reload="$emit('reload')"/>
    <v-card-title class="title pb-0">
      {{ question[$t('question-key')] }}
    </v-card-title>
    <v-card-text class="pt-1">
      {{ answer }}
    </v-card-text>
  </v-card>
</template>

<script>
  import QuestionDialog from './QuestionDialog';

  export default {
    name: 'QuestionCard',
    components: { QuestionDialog },
    props: {
      question: { type: Object, required: true },
      category: { type: Object, required: true },
      showQuestionId: { type: Number, default: - 1 },
    },
    data() {
      return {
        detailsVisible: this.showQuestionId === this.question.id,
      };
    },
    computed: {
      answer() {
        const answer = this.question[this.$t('answer-key')];
        const shortedAnswer = answer.replace(/^(.{150}[^\s]*).*/, '$1');
        return answer.length === shortedAnswer.length ? answer : `${shortedAnswer}...`;
      },
      questionWithCategoryData() {
        return { ...this.question, category: this.category };
      },
    },
    watch: {
      showQuestionId() {
        this.detailsVisible = this.showQuestionId === this.question.id;
      },
    },
    i18n: {
      messages: {
        pl: {
          'question-key': 'questionPl',
          'answer-key': 'answerPl',
        },
        en: {
          'question-key': 'questionEn',
          'answer-key': 'answerEn',
        },
      },
    },
  };
</script>

<style scoped>
  .cursor-pointer {
    cursor: pointer;
  }
</style>
