<template>
  <v-dialog v-model="dialogVisible" max-width="800px" >
    <v-card id="checklist-templates-question-dialog">
      <v-app-bar color="transparent" class="headline" flat >
        <span>
          {{ $t('New task') }}
        </span>
        <v-spacer/>
        <v-btn icon @click="close"><v-icon>close</v-icon></v-btn>
      </v-app-bar>
      <v-card-text>
        <v-progress-linear height="6" indeterminate v-if="loadingQuestion"/>
        <v-container grid-list-md class="checklist-table-employee pa-2">
          <employee-chooser v-if="!united"
                            v-model="editedItem.responsible"
                            :employees="employees"
                            :label="$t('Task owner')"
                            prepend-icon="person"/>
          <v-text-field v-model="editedItem.namePl" :label="this.$t('Name PL')"/>
          <v-text-field v-model="editedItem.nameEn" :label="this.$t('Name EN')"/>
          <v-textarea v-model="editedItem.descriptionPl" :label="this.$t('Description PL')" rows="1" auto-grow/>
          <v-textarea v-model="editedItem.descriptionEn" :label="this.$t('Description EN')" rows="1" auto-grow/>
          <div class="button-wrapper">
            <v-btn color="primary" dark class="ma-0" @click="newStatus">{{ $t('New status') }}</v-btn>
          </div>
          <checklist-templates-window-table-states
            :value="dialogStatus"
            :possible-statuses="editedStatus"
            :includes-default="includesDefault"
            @save="saveStatus"
            @close="dialogStatus = false"
          />
          <v-data-table mobile-breakpoint="0"
                        :items-per-page="5"
                        :headers="headersState"
                        :items="editedItem.possibleStatuses">
            <template v-slot:item="{ item }">
              <ChecklistTemplatesWindowStateRow
                :item="item"
                :united="united"
                @editItem="editItem(item, index)"
                @deleteItem="deleteItem"
              />
            </template>
          </v-data-table>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer/>
        <v-btn color="red" text @click="close">{{ $t('Cancel') }}</v-btn>
        <v-btn color="blue darken-1" text @click="save" :disabled="!valid">{{ $t('Save') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>


<script>
  import EmployeeChooser from '../../../utils/EmployeeChooser';
  import ChecklistTemplatesWindowTableStates from './StateDialog';
  import ChecklistTemplatesWindowStateRow from './StateRow';

  export default {
    name: 'QuestionDialog',
    components: { EmployeeChooser, ChecklistTemplatesWindowTableStates, ChecklistTemplatesWindowStateRow },
    props: {
      editedItem: { type: Object, required: true, default: () => {} },
      employees: { type: Array, required: true, default: () => [] },
      loadingQuestion: { type: Boolean, required: true, default: false },
      united: { type: Boolean, required: true, default: false },
      value: { type: Boolean, required: true },
    },
    data() {
      return {
        dialogStatus: false,
        edited: false,
        itemIndex: -1,
        editedStatus: {
          label_pl: '',
          label_en: '',
          default: false,
          done: false,
          color: '',
          icon: '',
        },
        headersState: [
          {
            text: this.$t('Label PL'),
            align: 'center',
            sortable: false,
            value: 'label_pl',
          },
          {
            text: this.$t('Label EN'),
            sortable: false,
            align: 'center',
            value: 'label_en',
          },
          {
            text: this.$t('Default'),
            sortable: false,
            align: 'center',
            value: 'default',
          },
          {
            text: this.$t('Done'),
            sortable: false,
            align: 'center',
            value: 'done',
          },
          {
            text: this.$t('Icon'),
            sortable: false,
            align: 'center',
            value: 'icon',
          },
          {
            text: this.$t('Actions'),
            sortable: false,
            align: 'center',
          },
        ],
      };
    },
    computed: {
      dialogVisible: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
          if (!val) {
            this.$emit('close');
          }
        },
      },
      includesDefault() {
        return this.editedItem.possibleStatuses.filter(status => status.default === true).length === 1;
      },
      includesDone() {
        return this.editedItem.possibleStatuses.some(status => status.done === true);
      },
      includesDoneAndDefault() {
        return this.includesDone && this.includesDefault;
      },
      valid() {
        if (!this.united && !this.editedItem.hasOwnProperty('responsible')) {
          return false;
        }
        return this.includesDoneAndDefault
          && this.editedItem.namePl
          && this.editedItem.nameEn
          && this.editedItem.descriptionPl
          && this.editedItem.descriptionEn;
      },
    },
    methods: {
      newStatus() {
        this.edited = false;
        this.editedStatus = {
          label_pl: '',
          label_en: '',
          default: false,
          done: false,
          color: '',
          icon: '',
        };
        this.dialogStatus = true;
      },
      close() {
        this.$emit('close');
        this.dialogVisible = false;
      },
      save() {
        this.$emit('save', this.editedItem);
        this.dialogVisible = false;
      },
      saveStatus(status) {
        if(this.edited) {
          this.editedItem.possibleStatuses[this.itemIndex] = status;
          this.editedItem.possibleStatuses.push();
        } else {
          this.editedItem.possibleStatuses.push(status);
        }
      },
      editItem(item, index) {
        this.itemIndex = index;
        this.edited = true;
        this.editedStatus = Object.assign({}, item);
        this.dialogStatus = true;
      },
      async deleteItem(item) {
        this.editedItem.possibleStatuses = this.editedItem.possibleStatuses.filter(el =>
        { return el.label_pl !== item.label_pl; });
      },
    },
    i18n: {
      messages: {
        pl: {
          'New task': 'Nowe zadanie',
          'New status': 'Nowy status',
          'Name PL': 'Nazwa PL',
          'Name EN': 'Nazwa EN',
          'Description EN': 'Opis EN',
          'Description PL': 'Opis PL',
          'Task owner': 'Osoba odpowiedzialna',
          'Label PL': 'Nazwa PL',
          'Label EN': 'Nazwa EN',
          'Default': 'Domyślny',
          'Done': 'Gotowe',
          'Icon': 'Ikona',
          'Actions': 'Akcje',
          'Cancel': 'Anuluj',
          'Save': 'Zapisz',
          'No data available.': 'Brak danych.',
          'Rows per page:': 'Wierszy na stronę:',
          'All': 'Wszystkie',
        },
      },
    },
  };
</script>
<style>
  #checklist-templates-question-dialog .button-wrapper{
    width: 100%;
    display: flex;
    justify-content: flex-end;
  }
</style>
