<template>
  <v-dialog v-model="modelWrapper" width="600">
    <v-card>
      <v-card-title class="title">
        {{ $t('Ask a new question') }}
        <v-spacer/>
        <v-btn @click="modelWrapper = false" icon rounded><v-icon>clear</v-icon></v-btn>
      </v-card-title>
      <v-divider/>
      <v-card-text>
        <v-select v-model="category" :items="categoriesList" :label="$t('Category')" :rules="[rules.selected]"/>
        <v-text-field v-model="question" :label="$t('Question')" :rules="[rules.minLength]"/>
        <v-btn :disabled="!formValid"
               :loading="loading"
               color="success"
               @click="send"
               block>
          {{ $t('Send question') }}
        </v-btn>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  import ApiAccess from '../../mixins/ApiAccess';

  export default {
    name: 'AskQuestionDialog',
    mixins: [ ApiAccess ],
    props: {
      value: { type: Boolean, required: true },
      categories: { type: Array, required: true },
    },
    data() {
      return {
        rules: {
          selected: v => v !== -1 || this.$t('Select category'),
          minLength: s => s.length >= 1 || this.$t('Question cannot be empty'),
        },
        category: -1,
        question: '',
        loading: false,
      };
    },
    computed: {
      modelWrapper: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
      categoriesList() {
        return this.categories.map(cat => ({ value: cat.id, text: cat[this.$t('name-key')] }));
      },
      formValid() {
        return this.category !== -1 && this.question.length > 0;
      },
    },
    methods: {
      async send() {
        this.loading = true;
        try {
          const categoryId = this.category;
          const question = this.question;
          const data = { categoryId, question };
          await this.apiClient.faq.sendFAQMessage(data);
          this.modelWrapper = false;
          this.$store.commit('showSnackbar', {
            text: this.$t('Question has been sent.'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('There was an error while sending question.'),
            color: 'error',
          });
        }
        this.loading = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Ask a new question': 'Zadaj nowe pytanie',
          'Category': 'Kategoria',
          'Question': 'Pytanie',
          'Select category': 'Wybierz kategorię',
          'Question cannot be empty': 'Pytanie nie może być puste',
          'Send question': 'Wyślij pytanie',
          'Question has been sent.': 'Pytanie zostało wysłane.',
          'There was an error while sending question.': 'Wystąpił błąd podczas wysyłania pytania.',
          'name-key': 'namePl',
        },
        en: {
          'name-key': 'nameEn',
        },
      },
    },
  };
</script>
