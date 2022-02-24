<template>
  <tr>
    <template>
      <td @click="feedbackDetails = true" class="pointer">
        <employee-chip :employee="item.employee"/>
      </td>
      <td @click="feedbackDetails = true" class="pointer">
        {{ moment.unix(item.dateCreated).format('YYYY-MM-DD') }}
      </td>
      <td @click="feedbackDetails = true" class="pointer">
        {{ moment.unix(item.updatedAt).format('YYYY-MM-DD HH:mm:ss') }}
      </td>
      <td @click="feedbackDetails = true" class="pointer">
        {{ item.type === FEEDBACK ? $t('Feedback Tribe Master') : $t('Feedback Leader') }}
      </td>
      <td>
        <v-tooltip left>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" @click="feedbackDialog = true" icon :disabled="employee.id !== item.leader.id">
              <v-icon>edit</v-icon>
            </v-btn>
          </template>
          {{ $t('Edit') }}
        </v-tooltip>
        <v-tooltip right>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" @click="$emit('delete', item.id)" icon :disabled="employee.id !== item.leader.id">
              <v-icon>delete</v-icon>
            </v-btn>
          </template>
          {{ $t('Delete') }}
        </v-tooltip>
      </td>
      <feedback-form
        v-if="feedbackDialog"
        :item="item"
        v-model="feedbackDialog"
        :employee="item.employee"
        :current-user="employee"
      />
      <feedback-details
        v-if="feedbackDetails"
        :feedback="item"
        v-model="feedbackDetails"
      />
    </template>
  </tr>
</template>
<script>
  import EmployeeChip from '../../utils/EmployeeChip';
  import FeedbackForm from '../FeedbackForm';
  import FeedbackDetails from '../FeedbackDetails';
  import moment from '@divante-adventure/work-moment';

  const FEEDBACK = 2;
  export default {
    name: 'FeedbackProvidedRow',
    components: { EmployeeChip, FeedbackForm, FeedbackDetails },
    props: {
      item: { type: Object, required: true },
      employee: { type: Object, required: true },
    },
    data() {
      return {
        FEEDBACK,
        moment,
        feedbackDetails: false,
        feedbackDialog: false,
      };
    },
    methods: {
      feedbackUpdate(data) {
        this.$emit('feedbackUpdate', data);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Delete': 'Usuń',
          'Edit': 'Edytuj',
          'Feedback Leader': 'Feedback Lider',
          'Feedback Tribe Master': 'Feedback Dyrektor',
        },
        en: {
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
