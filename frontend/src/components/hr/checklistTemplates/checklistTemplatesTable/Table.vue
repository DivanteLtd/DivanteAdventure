<template>
  <div>
    <v-data-table mobile-breakpoint="0"
                  :items-per-page="5" :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                  :no-results-text="$t('No results found.')"
                  :class="{'px-3': $vuetify.breakpoint.smAndUp}"
                  :search="search"
                  :items="checklistTemplates"
                  :loading="loading"
                  :headers="headers"
                  :custom-sort="customSort"
                  id="checklist-templates-table"
                  must-sort>
      <template v-slot:item="{ item }">
        <checklist-templates-list-row :item="item" :language="language" @show-delete-dialog="showDeleteDialog"/>
      </template>
      <template slot="pageText" slot-scope="props">
        {{ $t('page-text', props) }}
      </template>
    </v-data-table>
    <confirm-dialog v-model="deleteDialogVisible"
                    v-if="deleteDialogVisible"
                    @yes="deleteChecklistTemplate"
                    :question="$t('Do you really want to delete this checklist template?')"
                    yes-color="red"/>
  </div>
</template>

<script>
  import ChecklistTemplatesListRow from './Row';
  import { getSuggestedLanguage } from '../../../../i18n/i18n';
  import ConfirmDialog from '../../../utils/ConfirmDialog';

  export default {
    name: 'ChecklistTemplatesTable',
    components: { ChecklistTemplatesListRow, ConfirmDialog },
    props: {
      loading: { type: Boolean, required: true },
      search: { type: String, default: '' },
      checklistTemplates: { type: Array, required: true },
    },
    data() { return {
      itemToDelete: {},
      showAssigned: true,
      language: getSuggestedLanguage(),
      showUnassigned: true,
      deleteDialogVisible: false,
      headers: [{
                  text: this.$t('Name'),
                  align: 'center',
                  value: (this.language && this.language === 'pl') ? 'namePl' : 'nameEn',
                  sortable: true,
                }, {
                  text: this.$t('Type'),
                  align: 'center',
                  value: 'type',
                  sortable: true,
                },
                {
                  text: this.$t('Action'),
                  align: 'center',
                  sortable: false,
                }],
    };},
    methods: {
      customSort(items, index, isDesc) {
        let itemA = ``;
        let itemB = ``;
        items.sort((a, b) => {
          itemA = `${a[index]}`;
          itemB = `${b[index]}`;
          if(!isDesc[0]) {
            return itemA.localeCompare(itemB);
          } else {
            return itemB.localeCompare(itemA);
          }
        });
        return items;
      },
      async deleteChecklistTemplate() {
        await this.$store.dispatch('Checklist/deleteChecklistTemplate', this.itemToDelete);
        this.$store.commit('showSnackbar', {
          text: this.$t('Checklist template has been deleted'),
          color: 'success',
        });
      },
      showDeleteDialog(item) {
        this.itemToDelete = item;
        this.deleteDialogVisible = true;
      },
    },
    i18n: { messages: {
      pl: {
        'Loading data...': 'Wczytywanie...',
        'No data available.': 'Brak danych.',
        'No results found.': 'Nie znaleziono.',
        'Rows per page:': 'Wierszy na stronę:',
        'Checklist template has been deleted': 'Szablon checklisty został usunięty',
        'All': 'Wszystkie',
        'Action': 'Akcja',
        'Name': 'Nazwa',
        'Do you really want to delete this checklist template?': 'Czy na pewno chcesz usunąć szablon checklisty?',
        'Type': 'Typ',
        'page-text': 'Osoby {pageStart}-{pageStop} z {itemsLength}',
      },
      en: {
        'page-text': 'People {pageStart}-{pageStop} of {itemsLength}',
      },
    },
    },
  };
</script>
