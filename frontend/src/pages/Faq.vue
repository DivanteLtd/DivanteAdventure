<template>
  <page-card :title="$t('FAQ')">
    <v-container grid-list-xl="2" fluid id="faq-page">
      <ask-question-dialog
        v-if="askDialogVisible"
        v-model="askDialogVisible"
        :categories="categoriesIncludingEmpty"
      />
      <add-question-dialog
        v-if="addQuestionDialogVisible"
        v-model="addQuestionDialogVisible"
        :categories="categoriesIncludingEmpty"
        @reload="loadData"
      />
      <awaiting-questions-dialog
        v-if="awaitingQuestionsDialogVisible"
        v-model="awaitingQuestionsDialogVisible"
        :questions="awaitingQuestions"
        :categories="categoriesIncludingEmpty"
        @reload="loadData"
      />
      <manage-f-a-q-categories
        v-if="manageCategories"
        v-model="manageCategories"
      />
      <v-card>
        <v-card-text class="pa-0">
          <v-progress-linear height="6" v-if="loading" indeterminate/>
          <v-btn v-else block color="primary" @click="askDialogVisible = true">{{ $t('Ask question') }}</v-btn>
          <categories-list
            v-if="categoriesWithContent.length > 1 && filter.length > 0"
            :categories="categoriesIncludingEmpty"
            :show-question-id="showQuestionId"
            :category-questions="categoriesWithContent"
            @reload="loadData"
            :search="search"/>
          <questions-list
            v-else-if="categoriesWithContent.length === 1 && filter.length > 0"
            :category="categoriesIncludingEmpty[0]"
            :show-question-id="showQuestionId"
            :questions="filter"
            @reload="loadData"
          />
          <v-alert v-else type="info" :value="true">{{ $t('no-questions-info') }}</v-alert>
        </v-card-text>
      </v-card>
    </v-container>
    <template slot="options">
      <v-row no-gutters class="d-flex align-center">
        <v-text-field
          v-model="search"
          append-icon="search"
          :label="$t('search-label')"
          single-line hide-details
        />
        <v-menu v-if="showAddQuestionButton" offset-y>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" icon><v-icon>more_vert</v-icon></v-btn>
          </template>
          <v-list dense>
            <v-list-item v-if="showAddQuestionButton"
                         @click="addQuestionDialogVisible = true">
              {{ $t('Add new question and answer') }}
            </v-list-item>
            <v-list-item v-if="showAddQuestionButton && awaitingQuestions.length > 0"
                         @click="awaitingQuestionsDialogVisible = true">
              {{ $t('Awaiting questions') }}
            </v-list-item>
            <v-list-item v-if="isSuperAdmin"
                         @click="manageCategories = true">
              {{ $t('Manage categories') }}
            </v-list-item>
          </v-list>
        </v-menu>
      </v-row>
    </template>
  </page-card>
</template>

<script>
  import ApiAccess from '../mixins/ApiAccess';
  import { mapGetters } from 'vuex';
  import { EventBus, eventNames } from '../eventbus';
  import PageCard from '../components/utils/PageCard';
  import AwaitingQuestionsDialog from '../components/faq/AwaitingQuestionsDialog';
  import ManageFAQCategories from '../components/faq/ManageFAQCategories';
  import AddQuestionDialog from '../components/faq/AddQuestionDialog';
  import AskQuestionDialog from '../components/faq/AskQuestionDialog';
  import QuestionsList from '../components/faq/QuestionsList';
  import CategoriesList from '../components/faq/CategoriesList';

  export default {
    name: 'Faq',
    components: {
      AwaitingQuestionsDialog,
      ManageFAQCategories,
      AddQuestionDialog,
      AskQuestionDialog,
      QuestionsList,
      CategoriesList,
      PageCard,
    },
    mixins: [ ApiAccess ],
    data() {
      return {
        categories: [],
        loading: false,
        search: '',
        categoriesIncludingEmpty: [],
        awaitingQuestions: [],
        askDialogVisible: false,
        addQuestionDialogVisible: false,
        manageCategories: false,
        awaitingQuestionsDialogVisible: false,
        showQuestionId: -1,
      };
    },
    computed: {
      ...mapGetters({
        isSuperAdmin: 'Authorization/isSuperAdmin',
        userId: 'Authorization/getUserId',
      }),
      filter() {
        const questions = this.categoriesWithContent
          .map(cat => cat.questions
            .map(question => ({ ...question, categoryNamePl: cat.namePl, categoryNameEn: cat.nameEn })))
          .reduce((a, b) => [ ...a, ...b], []);
        if (this.search === '') {
          return questions;
        } else {
          const searchLower = this.search.toLowerCase().split(/[\s,.;]+/);
          return questions.filter(question => {
            const entryPartA = `${question.questionPl} ${question.questionEn}`;
            const entryPartB = `${question.answerPl} ${question.answerEn}`;
            const entryPartC = `${question.author.name} ${question.author.lastName} ${question.author.email}`;
            const entryPartD = `${question.categoryNamePl} ${question.categoryNameEn}`;
            const entry = `${entryPartA} ${entryPartB} ${entryPartC} ${entryPartD}`.toLowerCase();
            return searchLower.map(key => entry.includes(key)).reduce((a, b) => a && b, true);
          });
        }
      },
      categoriesWithContent() {
        return this.categories.filter(category => category.questions.length > 0);
      },
      showAddQuestionButton() {
        if (this.isSuperAdmin) {
          return true;
        }
        return this.categoriesIncludingEmpty
          .filter(category => category.responsibles.map(responsible => responsible.id).includes(this.userId))
          .length > 0;
      },
    },
    methods: {
      async loadData() {
        this.loading = true;
        const [ categoriesWithQuestions, categoriesIncludingEmpty, awaitingQuestions ] = await Promise.all([
          this.apiClient.faq.getQuestions(),
          this.apiClient.faq.getCategories(),
          this.apiClient.faq.getAskedQuestions(),
        ]);
        this.categories = categoriesWithQuestions;
        this.categoriesIncludingEmpty = categoriesIncludingEmpty;
        this.awaitingQuestions = awaitingQuestions;
        this.showQuestionId = typeof(this.$route.params.id) === 'undefined' ? -1 : parseInt(this.$route.params.id);
        this.loading = false;
      },
    },
    mounted() {
      EventBus.$on(eventNames.reloadFAQContent, this.loadData);
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
    beforeRouteUpdate(to, from, next) {
      next();
      const paramsId = this.$route.params.id;
      this.showQuestionId = typeof (paramsId) === 'undefined' ? -1 : parseInt(paramsId);
    },
    i18n: {
      messages: {
        pl: {
          'FAQ': 'FAQ',
          'Add new question and answer': 'Dodaj nowe pytanie i odpowiedź',
          'no-questions-info': 'Nie ma dostępnych pytań i odpowiedzi',
          'Ask question': 'Zadaj pytanie',
          'Awaiting questions': 'Oczekujące pytania',
          'Manage categories': 'Zarządzaj kategoriami',
          'search-label': 'Szukaj po pytaniu, odpowiedzi...',
        },
        en: {
          'no-questions-info': 'There are no questions available',
          'search-label': 'Search by question, answer...',
        },
      },
    },
  };
</script>
