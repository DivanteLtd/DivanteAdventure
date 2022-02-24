<template>
  <v-container>
    <v-row no-gutters align-start justify-start wrap>
      <v-col v-for="(question, index) in questions" :key="index" cols="12" sm="6" md="3">
        <question-card :category="category"
                       :question="question"
                       :show-question-id="showQuestionId"
                       @reload="$emit('reload')"/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import QuestionCard from './QuestionCard';

  export default {
    name: 'QuestionsList',
    components: { QuestionCard },
    props: {
      questions: { type: Array, required: true },
      category: { type: Object, required: true },
      showQuestionId: { type: Number, default: - 1 },
    },
  };
</script>
