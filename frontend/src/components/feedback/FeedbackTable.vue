<template>
  <div v-if="feedback.length">
    <v-data-table mobile-breakpoint="0"
                  :items-per-page="5"
                  :no-results-text="$t('No results found')"
                  :class="{'px-3': $vuetify.breakpoint.smAndUp}"
                  :items="feedback"
                  :loading="loading"
                  :headers="filteredHeaders"
                  sort-by="[updatedAt]"
                  sort-desc
                  must-sort>
      <template v-slot:item="{ item }">
        <feedback-list-row :current-user="currentUser" @delete="deleteFeedback" :item="item"/>
      </template>
    </v-data-table>
  </div>
</template>

<script>
  import FeedbackListRow from './FeedbackListRow';

  export default {
    name: 'FeedbackTable',
    components: { FeedbackListRow },
    props: {
      feedback: { type: Array, required: true },
      currentUser: { type: Object, required: true },
      employeeId: { type: Number, required: true },
      loading: { type: Boolean, required: true },
    },
    data() {
      return {
        headers: [
          {
            text: this.$t('Author'),
            value: 'leader',
            sortable: true,
          }, {
            text: this.$t('Type'),
            value: 'type',
            sortable: false,
          }, {
            text: this.$t('Date'),
            value: 'dateCreated',
            sortable: true,
          },
        ],
      };
    },
    computed: {
      filteredHeaders() {
        return this.employeeId !== this.currentUser.id
          ? [...this.headers, { text: this.$t('Action'), sortable: false }] : this.headers;
      },
    },
    methods: {
      async deleteFeedback(id) {
        this.$emit('delete', id);
      },
    },
    i18n: { messages: {
      pl: {
        'No results found': 'Nie znaleziono',
        'Author': 'Autor',
        'Type': 'Typ',
        'Action': 'Akcje',
        'Date': 'Data',
      },
    },
    },
  };
</script>
