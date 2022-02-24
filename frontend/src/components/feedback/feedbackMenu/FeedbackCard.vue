<template>
  <v-container grid-list-xl fluid>
    <v-card class="align-center">
      <v-row no-gutters wrap class="align-center">
        <v-col cols="11." :class="{'pb-2': $vuetify.breakpoint.xs}">
          <v-tabs v-model="selectedTab" centered>
            <v-tab>{{ $t('Feedback received') }}</v-tab>
            <v-tab v-if="myStructure.length">{{ $t('My Structure') }}</v-tab>
            <v-tab v-if="tribeStructure.length">{{ $t('My tribe') }}</v-tab>
            <v-tab v-if="feedbackProvided.length">{{ $t('Feedback provided') }}</v-tab>
            <v-tab v-if="isSuperAdmin">{{ $t('Leaders structures') }}</v-tab>
          </v-tabs>
        </v-col>
      </v-row>
      <v-divider/>
      <v-card-text class="pa-0">
        <v-tabs-items v-model="selectedTab" touchless>
          <v-tab-item>
            <v-alert class="mt-4" type="info" v-if="!feedbackReceived.length && !loading">
              {{ $t('No feedback was added') }}
            </v-alert>
            <feedback-table v-else :loading="loading" :current-user="employee" :employee-id="employee.id"
                            :feedback="feedbackReceived"/>
          </v-tab-item>
          <v-tab-item v-if="myStructure.length">
            <my-structure-table :loading="loading" :structure="myStructure"/>
          </v-tab-item>
          <v-tab-item v-if="employee.techTribeLeader && tribeStructure.length">
            <my-structure-table :loading="loading" :structure="tribeStructure"/>
          </v-tab-item>
          <v-tab-item v-if="feedbackProvided.length">
            <feedback-provided-table @delete="prepareDelete" :employee="employee" :loading="loading"
                                     :feedback="feedbackProvided"/>
          </v-tab-item>
          <v-tab-item v-if="isSuperAdmin">
            <leaders-structure :loading="loading"/>
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>
      <confirm-dialog v-model="dialogDelete"
                      v-if="dialogDelete"
                      @yes="deleteFeedback"
                      :question="$t('confirm-delete')"
                      yes-color="red"/>
    </v-card>
  </v-container>
</template>

<script>
  import MyStructureTable from './MyStructureTable';
  import FeedbackProvidedTable from './FeedbackProvidedTable';
  import LeadersStructure from './LeadersStructure';
  import FeedbackTable from '../FeedbackTable';
  import { mapGetters, mapState } from 'vuex';
  import ConfirmDialog from '../../utils/ConfirmDialog';

  export default {
    name: 'FeedbackCard',
    components: { FeedbackProvidedTable, MyStructureTable, FeedbackTable, LeadersStructure, ConfirmDialog },
    props: {
      loading: { type: Boolean, required: true },
      employee: { type: Object, required: true },
      feedbackReceived: { type: Array, required: true },
    },
    data() {
      return {
        dialogDelete: false,
        id: -1,
        selectedTab: 0,
      };
    },
    computed: {
      ...mapGetters({
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
      ...mapState({
        myStructure: state => state.Feedback.myStructure,
        tribeStructure: state => state.Feedback.tribeStructure,
        feedbackProvided: state => state.Feedback.feedbackProvided,
      }),
    },
    methods: {
      prepareDelete(id) {
        this.id = id;
        this.dialogDelete = true;
      },
      async deleteFeedback() {
        this.dialogDelete = false;
        try {
          await this.$store.dispatch('Feedback/deleteFeedback', this.id);
          await this.$store.dispatch('Feedback/getMyStructure', this.employee.id);
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
      feedbackUpdate(data) {
        this.$emit('feedbackUpdate', data);
      },
    },
    i18n: {
      messages: {
        pl: {
          'No feedback was added': 'Nie dodano żadnego feedback`u',
          'Feedback received': 'Feedbacki otrzymane',
          'My Structure': 'Moja struktura',
          'My tribe': 'Moje plemię',
          'Feedback provided': 'Feedbacki udzielone',
          'Leaders structures': 'Struktury liderów',
          'Add leaders structures': 'Dodaj struktury liderów',
          'confirm-delete': 'Czy na pewno chcesz usunąć ten feedback?',
        },
        en: {
          'confirm-delete': 'Are you sure you want to delete this feedback?',
        },
      },
    },
  };
</script>
