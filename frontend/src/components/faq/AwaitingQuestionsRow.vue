<template>
  <tr>
    <td class="text-center">{{ question.question }}</td>
    <td class="text-center">{{ question.category[$t('name-key')] }}</td>
    <td class="text-center"><employee-chip :employee="question.questioner"/></td>
    <td class="text-center">
      <add-question-dialog v-if="showAddQuestionDialog"
                           v-model="showAddQuestionDialog"
                           :categories="categories"
                           :question="questionToEdit"
                           @reload="sendConfirmationAndReload"/>
      <reject-question-dialog v-if="showRejectDialog"
                              v-model="showRejectDialog"
                              :question="question"
                              @reload="$emit('reload')"/>
      <v-tooltip top>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="showAddQuestionDialog = true" icon><v-icon>chat_bubble_outline</v-icon></v-btn>
        </template>
        {{ $t('Answer question') }}
      </v-tooltip>
      <v-tooltip top>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="showRejectDialog = true" icon><v-icon>delete_outline</v-icon></v-btn>
        </template>
        {{ $t('Reject question') }}
      </v-tooltip>
    </td>
  </tr>
</template>

<script>
  import EmployeeChip from '../utils/EmployeeChip';
  import AddQuestionDialog from './AddQuestionDialog';
  import ApiAccess from '../../mixins/ApiAccess';
  import RejectQuestionDialog from './RejectQuestionDialog';

  export default {
    name: 'AwaitingQuestionsRow',
    components: { RejectQuestionDialog, AddQuestionDialog, EmployeeChip },
    mixins: [ ApiAccess ],
    props: {
      question: { type: Object, required: true },
      categories: { type: Array, required: true },
    },
    data() {
      return {
        showAddQuestionDialog: false,
        showRejectDialog: false,
        loadingConfirmation: false,
      };
    },
    computed: {
      questionToEdit() {
        const { question, language, category } = this.question;
        const obj = { category };
        if (language === 'pl') {
          obj.questionPl = question;
        } else {
          obj.questionEn = question;
        }
        return obj;
      },
    },
    methods: {
      async sendConfirmationAndReload() {
        await this.apiClient.faq.confirmQuestion(this.question.id);
        this.$emit('reload');
      },
    },
    i18n: {
      messages: {
        pl: {
          'Answer question': 'Odpowiedz na pytanie',
          'Reject question': 'Odrzuć pytanie',
          'name-key': 'namePl',
          'page-text': 'Pytania {pageStart}-{pageStop} z {itemsLength}',
        },
        en: {
          'name-key': 'nameEn',
          'page-text': 'Questions {pageStart}-{pageStop} of {itemsLength}',
        },
      },
    },
  };
</script>
<style scoped>
  @media only screen and (max-width: 600px) {
    td {
      padding: 0 !important;
    }
  }
</style>
