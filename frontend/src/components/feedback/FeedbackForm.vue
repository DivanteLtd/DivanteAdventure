<template>
  <v-dialog v-model="dialogVisible" width="1000">
    <v-card>
      <v-card-title class="headline">
        {{ $t('Feedback form') }}
        <v-spacer/>
        <v-btn @click="dialogVisible = false" icon rounded><v-icon>clear</v-icon></v-btn>
      </v-card-title>
      <v-card-text>
        <v-form v-model="formValid">
          <v-select :items="types" v-model="item.type"
                    :disabled="!changeFeedbackType"
                    :label="this.$t('Change type of feedback')" class="required"/>
          <h3>{{ $t('360-degree feedback') }}</h3>
          <markdown-editor v-model="feedbackText" ref="markdownEditor"/>
          <h3>{{ $t('Progress feedback (goals, successes, fields to improve)') }}</h3>
          <markdown-editor v-model="progressFeedbackText" ref="markdownEditor"/>
          <h3>{{ $t('Technical feedback') }}</h3>
          <markdown-editor v-model="technicalFeedbackText" ref="markdownEditor"/>
          <v-menu v-model="dateVisible"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  min-width="290px"
                  offset-y>
            <template v-slot:activator="{ on }">
              <v-text-field v-model="dateCreated"
                            :label="$t('Change a date if the feedback did not take place today')"
                            v-on="on"
                            readonly/>
            </template>
            <v-date-picker v-model="dateCreated"
                           :first-day-of-week="$t('date.firstDayOfWeek')"
                           :max="today"
                           @input="dateVisible = false"/>
          </v-menu>
          <v-card-actions>
            <v-spacer/>
            <v-btn color="red" text @click="dialogVisible = false">{{ $t('Cancel') }}</v-btn>
            <v-btn color="primary" :loading="loading" text @click="save"
                   :disabled="!formValid || (!feedbackText && !progressFeedbackText && !technicalFeedbackText)">
              {{ $t('Save') }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  import { mapState } from 'vuex';
  import MarkdownEditor from 'vue-simplemde/src/markdown-editor';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'FeedbackForm',
    components: { MarkdownEditor },
    props: {
      value: { type: Boolean, required: true },
      employee: { type: Object, required: true },
      currentUser: { type: Object, required: true },
      item: { type: Object, required: true },
    },
    data() {
      return {
        loading: false,
        formValid: false,
        dateVisible: false,
        today: moment().format('YYYY-MM-DD'),
        feedbackText: '',
        dateCreated: '',
        progressFeedbackText: '',
        technicalFeedbackText: '',
        types: [
          {
            text: this.$t('Feedback Leader'),
            value: 1,
          }, {
            text: this.$t('Feedback Tribe Master'),
            value: 2,
          },
        ],
      };
    },
    computed: {
      ...mapState({
        tribes: state => state.Tribes.tribes,
      }),
      dialogVisible: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
      changeFeedbackType() {
        const leader = this.employee.leader ? this.employee.leader.id : -1;
        return this.isSuperAdmin || (this.currentUser.id === leader && this.tribeResponsible);
      },
      tribeResponsible() {
        const userTribe = this.tribes.find(val => val.id === this.employee.tribe.id);
        return userTribe && userTribe.responsible
          ? userTribe.responsible.some(val => val.id === this.currentUser.id) : false;
      },
    },
    methods: {
      async save() {
        try {
          this.loading = true;
          const data = {
            employeeId: this.employee.id,
            leaderId: this.currentUser.id,
            feedback: this.feedbackText,
            progressFeedback: this.progressFeedbackText,
            technicalFeedback: this.technicalFeedbackText,
            type: this.item.type,
            dateCreated: this.dateCreated,
          };
          if (this.item.id !== -1) {
            data.id = this.item.id;
            data.employee = this.employee;
            data.leader = this.currentUser;
            data.dateCreated = moment(this.dateCreated).unix();
            await this.$store.dispatch('Feedback/updateFeedback', data);
            await this.$store.dispatch('Feedback/getMyStructure', this.currentUser.id);
            await this.$store.dispatch('Feedback/getFeedbackProvided', this.currentUser.id);
          } else {
            await this.$store.dispatch('Feedback/addFeedback', data);
            await this.$store.dispatch('Feedback/getMyStructure', this.currentUser.id);
            await this.$store.dispatch('Feedback/getFeedbackProvided', this.currentUser.id);
            this.$emit('reload');
          }
          this.$store.commit('showSnackbar', {
            text: this.item.id !== -1 ? this.$t('Feedback has been updated') : this.$t('Feedback has been added'),
            color: 'success',
          });
          this.dialogVisible = false;
          this.loading = false;
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.item.id !== -1 ? this.$t('Feedback can not be updated') : this.$t('Feedback can not be added'),
            color: 'error',
          });
        }
      },
    },
    mounted() {
      const { feedback, progressFeedback, technicalFeedback } = this.item;
      this.dateCreated = this.item.id > -1 ? moment.unix(this.item.dateCreated).format('YYYY-MM-DD')
        : moment().format('YYYY-MM-DD');
      this.feedbackText = feedback || '';
      this.progressFeedbackText = progressFeedback || '';
      this.technicalFeedbackText = technicalFeedback || '';
    },
    i18n: {
      messages: {
        pl: {
          'Your feedback': 'Twój feedback',
          'Feedback form': 'Formularz Feedback`u',
          'Cancel': 'Anuluj',
          'Save': 'Zapisz',
          'Change a date if the feedback did not take place today': 'Zmień datę, jeśli feedback nie odbył się dzisiaj',
          'Feedback Tribe Master': 'Feedback Dyrektor',
          'Feedback Leader': 'Feedback Lider',
          'Change type of feedback': 'Zmień typ feedback`u',
          'This field is required': 'To pole jest wymagane',
          'Feedback has been updated': 'Feedback został zmieniony',
          'Feedback has been added': 'Feedback został dodany',
          'Feedback can not be updated': 'Feedback nie może zostać zmieniony',
          'Feedback can not be added': 'Feedback nie może zostać dodany',
          '360-degree feedback': 'Ocena 360 stopni',
          'Progress feedback (goals, successes, fields to improve)': 'Feedback rozwojowy (cele, sukcesy, obszary do poprawy)',
          'Technical feedback': 'Feedback techniczny',
        },
        en: {
          'Feedback Tribe Master': 'Feedback Tribe Director',
        },
      },
    },
  };
</script>
