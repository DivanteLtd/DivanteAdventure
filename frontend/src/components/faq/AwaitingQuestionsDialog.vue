<template>
  <v-dialog v-model="valueHandler" width="1000">
    <v-card>
      <v-card-title class="title">
        {{ $t('Awaiting questions') }}
        <v-spacer/>
        <v-btn @click="valueHandler = false" icon><v-icon>clear</v-icon></v-btn>
      </v-card-title>
      <v-divider/>
      <v-card-text>
        <v-data-table mobile-breakpoint="0" :items-per-page="5"
                      :no-data-text="$t('No data available.')"
                      :items="questions"
                      :headers="headers">
          <template v-slot:item="{ item }">
            <awaiting-questions-row :question="item"
                                    :categories="categories"
                                    @reload="$emit('reload')"/>
          </template>
          <template slot="pageText" slot-scope="props">
            {{ $t('page-text', props) }}
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  import AwaitingQuestionsRow from './AwaitingQuestionsRow';

  export default {
    name: 'AwaitingQuestionsDialog',
    components: { AwaitingQuestionsRow },
    props: {
      value: { type: Boolean, required: true },
      questions: { type: Array, required: true },
      categories: { type: Array, required: true },
    },
    data() {
      return {
        headers: [{
          align: 'center',
          value: 'question',
          sortable: false,
          text: this.$t('Question'),
        }, {
          align: 'center',
          value: 'category',
          sortable: false,
          text: this.$t('Category'),
        }, {
          align: 'center',
          value: 'questioner',
          sortable: false,
          text: this.$t('Questioner'),
        }, {
          align: 'center',
          value: 'actions',
          sortable: false,
          text: this.$t('Actions'),
        }],
      };
    },
    computed: {
      valueHandler: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
    i18n: {
      messages: {
        pl: {
          'Awaiting questions': 'Oczekujące pytania',
          'Rows per page:': 'Wierszy na stronę:',
          'All': 'Wszystkie',
          'Question': 'Pytanie',
          'Questioner': 'Osoba pytająca',
          'Category': 'Kategoria',
          'Actions': 'Akcje',
          'page-text': 'Pytania {pageStart}-{pageStop} z {itemsLength}',
        },
        en: {
          'page-text': 'Questions {pageStart}-{pageStop} of {itemsLength}',
        },
      },
    },
  };
</script>
