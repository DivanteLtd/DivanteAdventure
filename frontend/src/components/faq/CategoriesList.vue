<template>
  <v-expansion-panels v-model="expandPanel" multiple>
    <category-list-entry v-for="(categoryQuestion, key) in categoryQuestions" :key="key"
                         :category="categoryQuestion"
                         :category-questions="categoryQuestion"
                         :search="search"
                         :show-question-id="showQuestionId"
                         @reload="$emit('reload')"/>
  </v-expansion-panels>
</template>

<script>
  import CategoryListEntry from './CategoryListEntry';

  export default {
    name: 'CategoriesList',
    components: { CategoryListEntry },
    props: {
      categories: { type: Array, required: true },
      categoryQuestions: { type: Array, required: true },
      search: { type: String, default: '' },
      showQuestionId: { type: Number, default: - 1 },
    },
    data() {
      return {
        expandPanel: [0],
      };
    },
  };
</script>
