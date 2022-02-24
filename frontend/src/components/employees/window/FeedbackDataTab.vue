<template>
  <div>
    <v-container>
      <v-row no-gutters wrap class="mb-2">
        <v-col lg="6" sm="12" cols="12" class="pa-2">
          <v-btn v-if="employee.id !== currentUser.id" color="success" block @click="addFeedback">
            {{ $t('Add feedback') }}
          </v-btn>
        </v-col>
        <v-col lg="6" sm="12" cols="12" class="pa-2">
          <v-btn v-if="employee.id !== currentUser.id" color="primary" block @click="planFeedbackDialog = true">
            {{ $t('Plan feedback') }}
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
    <feedback-planning-dialog v-if="planFeedbackDialog"
                              v-model="planFeedbackDialog"
                              :employee="employee"
                              @reload="$emit('reload')"/>
    <feedback-form
      v-if="feedbackDialog"
      :item="item"
      v-model="feedbackDialog"
      :employee="employee"
      :current-user="currentUser"
      @reload="$emit('reload')"
    />
    <v-alert class="mt-4" type="info" v-if="feedback.length === 0 && planned.length === 0 && !loading">
      {{ $t('No feedback was added') }}
    </v-alert>
    <div v-else>
      <planned-feedback-table v-if="planned.length > 0" :feedbacks="planned"/>
      <feedback-table class="mt-4"
                      :loading="loading"
                      v-if="feedback.length > 0"
                      :current-user="currentUser"
                      :employee-id="employee.id"
                      @delete="deleteFeedback"
                      :feedback="feedback"/>
    </div>
  </div>
</template>

<script>
  import FeedbackForm from '../../feedback/FeedbackForm';
  import FeedbackTable from '../../feedback/FeedbackTable';
  import FeedbackPlanningDialog from '../../feedback/FeedbackPlanningDialog';
  import PlannedFeedbackTable from './PlannedFeedbackTable';

  const TECH_FEEDBACK = 1;
  export default {
    name: 'FeedbackDataTab',
    components: { PlannedFeedbackTable, FeedbackPlanningDialog, FeedbackForm, FeedbackTable },
    props: {
      feedback: { type: Array, default: () => ([]) },
      planned: { type: Array, default: () => ([]) },
      employee: { type: Object, required: true },
      currentUser: { type: Object, required: true },
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        feedbackDialog: false,
        item: {},
        planFeedbackDialog: false,
      };
    },
    methods: {
      addFeedback() {
        this.item = {
          id: -1,
          feedback: '',
          type: TECH_FEEDBACK,
        };
        this.feedbackDialog = true;
      },
      async deleteFeedback(id) {
        try {
          await this.$store.dispatch('Feedback/deleteFeedback', id);
          await this.$store.dispatch('Feedback/getMyStructure', this.currentUser.id);
          this.$store.commit('showSnackbar', {
            text: this.$t('Feedback has been deleted'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Feedback can not be deleted'),
            color: 'error',
          });
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Add feedback': 'Dodaj feedback',
          'Plan feedback': 'Zaplanuj feedback',
          'No feedback was added': 'Nie dodano żadnego feedbacku',
          'Feedback has been deleted': 'Feedback został usunięty',
          'Feedback can not be deleted': 'Feedback nie może zostać usunięty',
        },
      },
    },
  };
</script>
