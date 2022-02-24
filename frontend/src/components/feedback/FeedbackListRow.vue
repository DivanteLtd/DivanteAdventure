<template>
  <tr>
    <td @click="feedbackDetails = true" class="pointer">
      <employee-chip :employee="item.leader"/>
    </td>
    <td @click="feedbackDetails = true" class="pointer">
      {{ item.type === FEEDBACK ? $t('Feedback Tribe Master') : $t('Feedback Leader') }}
    </td>
    <td @click="feedbackDetails = true" class="pointer text-no-wrap">
      {{ moment.unix(item.dateCreated).format('YYYY-MM-DD') }}
    </td>
    <td class="centered" v-if="currentUser.id !== item.employee.id">
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="feedbackDialog = true" icon :disabled="currentUser.id !== item.leader.id">
            <v-icon>edit</v-icon>
          </v-btn>
        </template>
        {{ $t('Edit') }}
      </v-tooltip>
      <v-tooltip right>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="dialogDelete = true" icon :disabled="currentUser.id !== item.leader.id">
            <v-icon>delete</v-icon>
          </v-btn>
        </template>
        {{ $t('Delete') }}
      </v-tooltip>
    </td>
    <confirm-dialog v-model="dialogDelete"
                    v-if="dialogDelete"
                    @yes="$emit('delete', item.id)"
                    :question="$t('confirm-delete')"
                    yes-color="red"/>
    <feedback-form
      v-if="feedbackDialog"
      :item="item"
      v-model="feedbackDialog"
      :employee="item.employee"
      :current-user="currentUser"
    />
    <feedback-details
      v-if="feedbackDetails"
      :feedback="item"
      v-model="feedbackDetails"
    />
  </tr>
</template>

<script>
  import EmployeeChip from '../utils/EmployeeChip';
  import ConfirmDialog from '../utils/ConfirmDialog';
  import FeedbackForm from './FeedbackForm';
  import FeedbackDetails from './FeedbackDetails';
  import moment from '@divante-adventure/work-moment';

  const FEEDBACK = 2;
  export default {
    name: 'FeedbackListRow',
    components: { EmployeeChip, ConfirmDialog, FeedbackForm, FeedbackDetails },
    props: {
      item: { type: Object, required: true },
      currentUser: { type: Object, required: true },
    },
    data() {
      return {
        moment,
        FEEDBACK,
        loading: false,
        dialogDelete: false,
        feedbackDetails: false,
        feedbackDialog: false,
      };
    },
    i18n: {
      messages: {
        pl: {
          'confirm-delete': 'Czy na pewno chcesz usunąć ten feedback?',
          'Delete': 'Usuń',
          'Edit': 'Edytuj',
          'Feedback Leader': 'Feedback Lider',
          'Feedback Tribe Master': 'Feedback Dyrektor',
        },
        en: {
          'confirm-delete': 'Are you sure you want to delete this feedback?',
          'Feedback Tribe Master': 'Feedback Director',
        },
      },
    },
  };
</script>
<style scoped>
    .pointer {
      cursor: pointer;
    }
</style>
