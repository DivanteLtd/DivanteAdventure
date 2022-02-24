<template>
  <div>
    <v-data-table mobile-breakpoint="0"
                  :items-per-page="15"
                  :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                  :items="feedback"
                  :headers="headers"
                  :custom-sort="customSort"
                  must-sort>
      <template v-slot:item="{ item }">
        <feedback-provided-row @delete="prepareDelete"
                               :employee="employee" :item="item"/>
      </template>
    </v-data-table>
  </div>
</template>

<script>
  import FeedbackProvidedRow from './FeedbackProvidedRow';

  export default {
    name: 'FeedbackProvidedTable',
    components: { FeedbackProvidedRow },
    props: {
      loading: { type: Boolean, required: true },
      employee: { type: Object, required: true },
      feedback: { type: Array, required: true },
    },
    data() {
      return {
        headers: [{
          text: this.$t('For'),
          value: 'photo',
          sortable: false,
        }, {
          text: this.$t('Feedback created date'),
          value: 'dateCreated',
          sortable: true,
        }, {
          text: this.$t('Last modified date'),
          value: 'date',
          sortable: true,
        }, {
          text: this.$t('Type'),
          value: 'type',
        }, {
          text: this.$t('Actions'),
          value: 'type',
          sortable: false,
        }],
      };
    },
    methods: {
      prepareDelete(id) {
        this.$emit('delete', id);
      },
      feedbackUpdate(data) {
        this.$emit('feedbackUpdate', data);
      },
      customSort(items, index, isDesc) {
        items.sort((a, b) => {
          if (index[0] === 'date') {
            if (!isDesc[0]) {
              return b.updatedAt - a.updatedAt;
            } else {
              return a.updatedAt - b.updatedAt;
            }
          }
          const itemA = `${a[index]}`;
          const itemB = `${b[index]}`;
          if(!isDesc[0]) {
            return itemA.localeCompare(itemB);
          } else {
            return itemB.localeCompare(itemA);
          }
        });
        return items;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Last modified date': 'Data ostatniej modyfikacji',
          'Type': 'Typ',
          'Loading data...': 'Ładowanie danych...',
          'Actions': 'Akcje',
          'For': 'Dla',
          'Feedback created date': 'Data stworzenia feedbacku',
          'Feedback has been deleted': 'Feedback został usunięty',
          'Feedback can not be deleted': 'Feedback nie może zostać usunięty',
          'confirm-delete': 'Czy na pewno chcesz usunąć ten feedback?',
        },
        en: {
          'confirm-delete': 'Are you sure you want to delete this feedback?',
        },
      },
    },
  };
</script>
