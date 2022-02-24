<template>
  <v-dialog v-model="modelWrapper" width="1000">
    <v-card>
      <v-card-title class="title">
        {{ editMode ? $t('Edit question') : $t('Add a new question') }}
        <v-spacer/>
        <v-btn @click="modelWrapper = false" icon rounded><v-icon>clear</v-icon></v-btn>
      </v-card-title>
      <v-divider/>
      <v-card-text>
        <v-select :disabled="editMode"
                  v-model="category"
                  :items="categoriesDisplay"
                  :label="$t('Category')"
                  :rules="[rules.selected]"/>
        <v-row no-gutters wrap>
          <v-col :class="{'pr-2': $vuetify.breakpoint.mdAndUp}" cols="12" md="6">
            <v-text-field v-model="questionPl" :label="$t('Question (in Polish)')" :rules="[rules.nonEmpty]"/>
            <v-textarea v-model="answerPl"
                        :label="$t('Answer (in Polish)')"
                        :rules="[rules.nonEmpty]"
                        rows="1"
                        auto-grow/>
          </v-col>
          <v-col :class="{'pl-2': $vuetify.breakpoint.mdAndUp}" cols="12" md="6">
            <v-text-field v-model="questionEn" :label="$t('Question (in English)')" :rules="[rules.nonEmpty]"/>
            <v-textarea v-model="answerEn"
                        :label="$t('Answer (in English)')"
                        :rules="[rules.nonEmpty]"
                        rows="1"
                        auto-grow/>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-spacer/>
        <v-btn @click="modelWrapper = false" text>{{ $t('Cancel') }}</v-btn>
        <v-btn @click="sendQuestion" color="primary" :disabled="!formValid" :loading="loading" text>
          {{ $t('Save') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { mapGetters } from 'vuex';
  import ApiAccess from '../../mixins/ApiAccess';

  export default {
    name: 'AddQuestionDialog',
    mixins: [ ApiAccess ],
    props: {
      value: { type: Boolean, required: true },
      categories: { type: Array, required: true },
      question: { type: Object, required: false, default: () => ({}) },
    },
    data() {
      return {
        editMode: false,
        category: -1,
        questionPl: '',
        questionEn: '',
        answerPl: '',
        answerEn: '',
        loading: false,
        rules: {
          selected: v => v !== -1 || this.$t('Select category'),
          nonEmpty: s => s.length >= 1 || this.$t('Field cannot be empty'),
        },
      };
    },
    computed: {
      ...mapGetters({
        isSuperAdmin: 'Authorization/isSuperAdmin',
        userId: 'Authorization/getUserId',
      }),
      modelWrapper: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
      formValid() {
        return this.category !== -1
          && this.questionPl !== '' && this.questionEn !== ''
          && this.answerPl !== '' && this.answerEn !== '';
      },
      allowedCategories() {
        if (this.isSuperAdmin) {
          return this.categories;
        } else {
          return this.categories.filter(cat => cat.responsibles.map(res => res.id).includes(this.userId));
        }
      },
      categoriesDisplay() {
        return this.allowedCategories.map(cat => ({ value: cat.id, text: cat[this.$t('name-key')] }));
      },
    },
    methods: {
      async sendQuestion() {
        this.loading = true;
        try {
          const categoryId = this.category;
          const { questionPl, questionEn, answerPl, answerEn } = this;
          const data = { categoryId, questionPl, questionEn, answerPl, answerEn };
          if (this.editMode) {
            await this.apiClient.faq.updateQuestion(this.question.id, data);
          } else {
            await this.apiClient.faq.createQuestion(data);
          }
          this.modelWrapper = false;
          this.$store.commit('showSnackbar', {
            text: this.editMode ? this.$t('Question has been updated.') : this.$t('Question has been created.'),
            color: 'success',
          });
          this.$emit('reload');
        } catch (e) {
          const color = 'error';
          let text = '';
          if (this.editMode) {
            text = this.$t('There was an error while updating question.');
          } else {
            text = this.$t('There was an error while creating question.');
          }
          this.$store.commit('showSnackbar', { text, color });
        }
        this.loading = false;
      },
    },
    mounted() {
      this.editMode = this.question.hasOwnProperty('id');
      this.category = (this.question.category || { id: -1 }).id;
      this.questionPl = this.question.questionPl || '';
      this.questionEn = this.question.questionEn || '';
      this.answerPl = this.question.answerPl || '';
      this.answerEn = this.question.answerEn || '';
    },
    i18n: {
      messages: {
        pl: {
          'Cancel': 'Anuluj',
          'Add a new question': 'Dodaj nowe pytanie',
          'Edit question': 'Edytuj pytanie',
          'Save': 'Zapisz',
          'Select category': 'Wybierz kategorię',
          'Field cannot be empty': 'Pole nie może być puste',
          'Category': 'Kategoria',
          'Question (in Polish)': 'Pytanie (po polsku)',
          'Answer (in Polish)': 'Odpowiedź (po polsku)',
          'Question (in English)': 'Pytanie (po angielsku)',
          'Answer (in English)': 'Odpowiedź (po angielsku)',
          'Question has been created.': 'Pytanie zostało utworzone.',
          'There was an error while creating question.': 'Wystąpił błąd w trakcie tworzenia pytania.',
          'Question has been updated.': 'Pytanie zostało zaktualizowane',
          'There was an error while updating question.': 'Wystąpił błąd w trakcie aktualizacji pytania.',
          'name-key': 'namePl',
        },
        en: {
          'name-key': 'nameEn',
        },
      },
    },
  };
</script>

<style scoped>
  .pr-2 {
    border-right: 1px solid rgba(0, 0, 0, .12);
  }
</style>
