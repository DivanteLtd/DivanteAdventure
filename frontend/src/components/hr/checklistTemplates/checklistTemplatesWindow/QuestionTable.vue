<template>
  <div style="width: 100%;">
    <div class="d-flex justify-end mr-2">
      <v-btn color="primary"
             dark class="mb-2"
             @click="dialogVisible = true">
        {{ $t('New task') }}
      </v-btn>
    </div>
    <checklist-templates-window-table-dialog
      :united="united"
      :employees="employees"
      :edited-item="editedItem"
      :loading-question="loadingQuestion"
      v-model="dialogVisible"
      v-if="dialogVisible"
      @save="save"
      @close="close"
    />
    <v-data-table mobile-breakpoint="0"
                  :items-per-page="5"
                  :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                  :headers="headers"
                  :loading="loading"
                  :items="checklistTemplateQuestions"
                  class="px-2">
      <template v-slot:item="{ item }">
        <ChecklistTemplatesWindowTableRow
          :item="item"
          :united="united"
          @editItem="editItem"
          @deleteItem="deleteItem"
        />
      </template>
    </v-data-table>
  </div>
</template>

<script>
  import { mapState } from 'vuex';
  import ChecklistTemplatesWindowTableRow from './QuestionRow';
  import ChecklistTemplatesWindowTableDialog from './QuestionDialog';

  export default {
    name: 'QuestionTable',
    components: {
      ChecklistTemplatesWindowTableDialog,
      ChecklistTemplatesWindowTableRow,
    },
    props: {
      united: { required: true, type: Boolean, default: true },
      employees: { required: true, type: Array },
      checklistTemplate: { type: Object, required: false, default: () => {} },
    },
    data() {
      return {
        dialogVisible: false,
        loading: false,
        dialog: false,
        editedIndex: -1,
        checklistTemplateDetails: {},
        loadingQuestion: false,
        employee: null,
        editedItem: {
          namePl: '',
          nameEn: '',
          descriptionPl: '',
          descriptionEn: '',
          responsibleId: null,
          possibleStatuses: [{
            label_pl: 'Oczekujący',
            label_en: 'Waiting',
            default: true,
            color: 'red',
            icon: 'error_outline',
          }, {
            label_pl: 'W przygotowaniu',
            label_en: 'In progress',
            color: 'yellow',
            icon: 'schedule',
          }, {
            label_pl: 'Gotowe',
            label_en: 'Done',
            color: 'green',
            icon: 'done',
            done: true,
          }],
        },
        defaultItem: {
          namePl: '',
          nameEn: '',
          descriptionPl: '',
          descriptionEn: '',
          responsibleId: null,
          possibleStatuses: [{
            label_pl: 'Oczekujący',
            label_en: 'Waiting',
            default: true,
            color: 'red',
            icon: 'error_outline',
          }, {
            label_pl: 'W przygotowaniu',
            label_en: 'In progress',
            color: 'yellow',
            icon: 'schedule',
          }, {
            label_pl: 'Gotowe',
            label_en: 'Done',
            color: 'green',
            icon: 'done',
            done: true,
          }],
        },
        possibleStatuses: [{
          label_pl: 'Oczekujący',
          label_en: 'Waiting',
          default: true,
          color: 'red',
          icon: 'error_outline',
        }, {
          label_pl: 'W przygotowaniu',
          label_en: 'In progress',
          color: 'yellow',
          icon: 'schedule',
        }, {
          label_pl: 'Gotowe',
          label_en: 'Done',
          color: 'green',
          icon: 'done',
          done: true,
        }],
      };
    },
    computed: {
      ...mapState({
        checklistTemplateQuestions: state => state.Checklist.checklistTemplateQuestions,
      }),
      headers() {
        return this.united ? [
          {
            text: this.$t('Name PL'),
            value: 'namePl',
            align: 'center',
            sortable: false,
          },
          {
            text: this.$t('Name EN'),
            value: 'nameEn',
            sortable: false,
            align: 'center',
          },
          {
            text: this.$t('Description PL'),
            value: 'descriptionPl',
            sortable: false,
            align: 'center',
          },
          {
            text: this.$t('Description EN'),
            value: 'descriptionEn',
            sortable: false,
            align: 'center',
          },
          {
            text: this.$t('Actions'),
            sortable: false,
            align: 'center',
          },
        ] : [
          {
            text: this.$t('Name PL'),
            align: 'center',
            value: 'namePl',
            sortable: false,
          },
          {
            text: this.$t('Name EN'),
            value: 'nameEn',
            sortable: false,
            align: 'center',
          },
          {
            text: this.$t('Description PL'),
            value: 'descriptionPl',
            sortable: false,
            align: 'center',
          },
          {
            text: this.$t('Description EN'),
            value: 'descriptionEn',
            sortable: false,
            align: 'center',
          },
          {
            text: this.$t('Employee'),
            value: 'responsibleId',
            sortable: false,
            align: 'center',
          },
          {
            text: this.$t('Actions'),
            sortable: false,
            align: 'center',
          },
        ];
      },
    },
    methods: {
      async initialize() {
        this.loading = true;
        await this.$store.dispatch('Checklist/getQuestionsFromTemplate', this.checklistTemplate);
        this.loading = false;
      },
      editItem(item) {
        this.editedIndex = item.id;
        this.editedItem = Object.assign({}, item);
        this.dialogVisible = true;
      },
      async deleteItem(item) {
        this.loadingQuestion = true;
        this.editedItem.id = item.id;
        this.editedItem.templateId = this.checklistTemplate.id;
        await this.$store.dispatch('Checklist/deleteQuestionFromTemplate', this.editedItem);
      },
      close() {
        this.loadingQuestion = false;
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      },
      async save(editedItem) {
        this.loadingQuestion = true;
        if(!this.united) {
          editedItem.responsibleId = this.editedItem.responsible.id;
        }
        if (this.editedIndex > -1) {
          editedItem.templateId = this.checklistTemplate.id;
          await this.$store.dispatch('Checklist/updateQuestionInTemplate', editedItem);
        } else {
          editedItem.id = this.checklistTemplate.id;
          await this.$store.dispatch('Checklist/addQuestionToTemplate', editedItem);
        }
        this.close();
      },
    },
    created() {
      this.initialize();
    },
    i18n: {
      messages: {
        pl: {
          'No data available.': 'Brak danych.',
          'Rows per page:': 'Wierszy na stronę:',
          'All': 'Wszystkie',
          'New task': 'Nowe zadanie',
          'New status': 'Nowy status',
          'Name PL': 'Nazwa PL',
          'Name EN': 'Nazwa EN',
          'Description EN': 'Opis EN',
          'Description PL': 'Opis PL',
          'Employee': 'Pracownik',
          'Statuses': 'Statusy',
          'Distributed': 'Rozdzielony',
          'United': 'Złączony',
          'Add': 'Dodaj',
          'Actions': 'Akcje',
        },
      },
    },
  };
</script>
<style>
  .checklist-table-employee .v-input__prepend-outer{
    display: none;
  }
  #checklist-templates-window-table-dialog .v-app-bar__content, .v-app-bar__extension{
    padding: 0 !important;
  }
</style>
