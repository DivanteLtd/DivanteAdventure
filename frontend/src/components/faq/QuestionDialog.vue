<template>
  <v-dialog v-model="valueHandler" width="600">
    <v-card>
      <v-card-title class="title">
        {{ question[$t('question-key')] }}
        <v-spacer/>
        <v-btn class="ml-5" @click="valueHandler = false" icon><v-icon>clear</v-icon></v-btn>
      </v-card-title>
      <v-divider/>
      <v-card-text class="pt-2">
        <v-container class="pa-0 ma-0">
          <v-row no-gutters class="pa-0 ma-0" align-center justify-space-between>
            <v-col class="caption" :class="{'mr-2': $vuetify.breakpoint.xs}">{{ date }}</v-col>
            <v-col class="d-flex justify-end">
              <employee-chip :employee="question.author"/>
            </v-col>
          </v-row>
        </v-container>
        <div class="text-xs-justify mt-4">
          {{ question[$t('answer-key')] }}
        </div>
        <confirm-dialog v-if="confirmDialogVisible"
                        v-model="confirmDialogVisible"
                        :question="$t('Are you sure you want to delete this question?')"
                        @yes="deleteQuestion"
                        yes-color="red"/>
      </v-card-text>
      <v-card-actions v-if="showActionButtons">
        <add-question-dialog v-if="editQuestionVisible"
                             v-model="editQuestionVisible"
                             :categories="[question.category]"
                             @reload="closeAndReload"
                             :question="question"/>
        <v-spacer/>
        <v-btn @click="editQuestionVisible = true" color="primary" text>{{ $t('Edit') }}</v-btn>
        <v-btn @click="confirmDialogVisible = true" color="error" text>{{ $t('Delete') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import { getSuggestedLanguage } from '../../i18n/i18n';
  import EmployeeChip from '../utils/EmployeeChip';
  import AddQuestionDialog from './AddQuestionDialog';
  import { mapGetters } from 'vuex';
  import ConfirmDialog from '../utils/ConfirmDialog';
  import ApiAccess from '../../mixins/ApiAccess';

  export default {
    name: 'QuestionDialog',
    components: { AddQuestionDialog, EmployeeChip, ConfirmDialog },
    mixins: [ ApiAccess ],
    props: {
      question: { type: Object, required: true },
      value: { type: Boolean, required: true },
    },
    data() {
      return {
        editQuestionVisible: false,
        confirmDialogVisible: false,
      };
    },
    computed: {
      ...mapGetters({
        isSuperAdmin: 'Authorization/isSuperAdmin',
        userId: 'Authorization/getUserId',
      }),
      valueHandler: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
      showActionButtons() {
        if (this.isSuperAdmin) {
          return true;
        }
        return this.question.category.responsibles.map(responsible => responsible.id).includes(this.userId);
      },
      date() {
        return moment(this.question.createdAt).locale(getSuggestedLanguage()).format(this.$t('date-format'));
      },
    },
    methods: {
      closeAndReload() {
        this.valueHandler = false;
        this.$emit('reload');
      },
      async deleteQuestion() {
        try {
          await this.apiClient.faq.deleteQuestion(this.question.id);
          this.$store.commit('showSnackbar', {
            text: this.$t('Question has been deleted.'),
            color: 'success',
          });
          this.closeAndReload();
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('There was an error while deleting question.'),
            color: 'error',
          });
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Edit': 'Edytuj',
          'Delete': 'Usuń',
          'Are you sure you want to delete this question?': 'Czy na pewno chcesz usunąć te pytanie? ',
          'question-key': 'questionPl',
          'answer-key': 'answerPl',
          'date-format': 'D MMM YYYY HH:mm:ss',
          'Question has been deleted.': 'Pytanie zostało usunięte',
          'There was an error while deleting question.': 'Wystapił błąd podczas usuwania pytania',
        },
        en: {
          'question-key': 'questionEn',
          'answer-key': 'answerEn',
          'date-format': 'MMM Do YYYY HH:mm:ss',
        },
      },
    },
  };
</script>
<style scoped>
  .title {
    flex-flow: row;
  }
</style>
