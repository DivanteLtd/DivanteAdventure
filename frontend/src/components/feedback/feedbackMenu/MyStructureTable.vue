<template>
  <v-data-table mobile-breakpoint="0"
                :items-per-page="15"
                :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                :items="structure"
                :loading="loading"
                :headers="headers"
                must-sort>
    <template v-slot:item="{ item }">
      <my-structure-row :item="item"/>
    </template>
  </v-data-table>
</template>

<script>
  import MyStructureRow from './MyStructureRow';

  export default {
    name: 'MyStructureTable',
    components: { MyStructureRow },
    props: {
      loading: { type: Boolean, required: true },
      structure: { type: Array, required: true },
    },
    data() {
      return {
        headers: [{
          text: this.$t('For'),
          value: 'photo',
          sortable: false,
        }, {
          text: this.$t('Planned feedback date'),
          value: 'date',
          sortable: true,
        }, {
          text: this.$t('Last feedback date'),
          value: 'lastFeedbackDate',
          sortable: true,
        }, {
          text: this.$t('It`s been over since the last feedback'),
          value: 'months',
          sortable: true,
        }],
      };
    },
    i18n: {
      messages: {
        pl: {
          'Date': 'Data',
          'Planned feedback date': 'Data zaplanowanego feedbacku',
          'Loading data...': 'Ładowanie danych...',
          'Last feedback date': 'Data ostatniego feedbacku',
          'It`s been over since the last feedback': 'Od ostatniego feedbacku minęło',
          'For': 'Dla',
        },
      },
    },
  };
</script>
