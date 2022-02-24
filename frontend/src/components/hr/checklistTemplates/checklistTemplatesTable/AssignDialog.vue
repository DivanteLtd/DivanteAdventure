<template>
  <v-dialog v-model="dialogVisible" width="700">
    <v-card v-if="!checklistTemplateQuestions.length">
      <v-card-text>
        <v-alert :value="true" type="info">
          {{ $t('The checklist template has no task. Before assigning a person, edit the template and add the task') }}
        </v-alert>
        <v-card-actions>
          <v-spacer/>
          <v-btn color="primary" @click="editTemplate" text>{{ $t('Edit') }}</v-btn>
          <v-btn color="red" @click="dialogVisible = false" text>{{ $t('Cancel') }}</v-btn>
        </v-card-actions>
      </v-card-text>
    </v-card>
    <v-card v-else>
      <v-app-bar flat color="transparent" class="title">
        <span>{{ $t('Assign person') }}</span>
        <v-spacer/>
        <v-btn @click="dialogVisible = false" icon><v-icon>clear</v-icon></v-btn>
      </v-app-bar>
      <v-divider/>
      <v-card-text>
        <assign-dialog-chooser :employees="employees"
                               :departments="departments"
                               :title="$t('Checklist owner')"
                               :subtitle="$t('Responsibles for checklist:')"
                               @update="updateOwners"
        />
        <div class="caption pb-2">{{ $t('caption-owner') }}</div>
        <employee-chooser :employees="employees"
                          :label="$t('Checklist subject')"
                          v-model="subject"
                          class="mt-4"
                          prepend-icon="supervisor_account"
        />
        <div class="caption pb-2">{{ $t('caption-subject') }}</div>
        <v-switch v-model="hidden" class="mx-2" :label="$t('Hidden for subject')"></v-switch>
        <v-card-text>
          <v-menu v-model="dateVisible"
                  :close-on-content-click="false"
                  min-width="290px">
            <template v-slot:activator="{ on }">
              <v-text-field v-model="dueDate"
                            :label="$t('Provide due date')"
                            v-on="on"
                            readonly/>
            </template>
            <v-date-picker :min="today"
                           v-model="dueDate"
                           :first-day-of-week="$t('date.firstDayOfWeek')"
                           @input="dateVisible = false"/>
          </v-menu>
        </v-card-text>
        <v-btn color="primary" :disabled="!valid"
               :loading="loading" @click="assign"
               class="mt-4" block>
          {{ $t('Assign') }}
        </v-btn>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  import { mapState, mapGetters } from 'vuex';
  import { ChecklistType } from '../../../../util/checklists';
  import AssignDialogChooser from './AssignDialogChooser';
  import EmployeeChooser from '../../../utils/EmployeeChooser';
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'ChecklistTemplateAssignDialog',
    components: { AssignDialogChooser, EmployeeChooser },
    props: {
      template: { type: Object, required: true },
      value: { type: Boolean, required: true },
    },
    data() {
      return {
        owners: [],
        subject: null,
        loading: false,
        hidden: false,
        today: moment().format('YYYY-MM-DD'),
        dueDate: '',
      };
    },
    computed: {
      ...mapState({
        employees: state => state.Employees.employees,
        apiClient: state => state.apiClient,
        checklistTemplateQuestions: state => state.Checklist.checklistTemplateQuestions,
      }),
      ...mapGetters({
        departments: 'Tribes/filteredDepartments',
      }),
      valid() {
        if (this.subject === null || this.dueDate === '') {
          return false;
        }
        if (this.template.type === ChecklistType.united) {
          return this.owners.length;
        }
        return true;
      },
      dialogVisible: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
    methods: {
      editTemplate() {
        this.$emit('editTemplate');
        this.dialogVisible = false;
      },
      async assign() {
        this.loading = true;
        const templateId = this.template.id;
        const subject = this.subject || {};
        const subjectId = subject.id || null;
        const ownersId = this.owners;
        const hidden = this.hidden;
        const dueDate = this.dueDate;
        try {
          await this.apiClient.checklist.assignTemplate(templateId, subjectId, ownersId, hidden, dueDate);
          this.$store.commit('showSnackbar', {
            text: this.$t('Checklist assigned correctly'),
            color: 'success',
          });
          this.dialogVisible = false;
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('There was an error during assigning checklist'),
            color: 'error',
          });
        }
        this.loading = false;
      },
      updateOwners(owners) {
        this.owners = owners;
      },
    },
    async mounted() {
      await this.$store.dispatch('Tribes/loadTribes');
    },
    i18n: {
      messages: {
        pl: {
          'Cancel': 'Anuluj',
          'Edit': 'Edytuj',
          'Provide due date': 'Podaj termin do którego należy wykonać zadania',
          'The checklist template has no task. Before assigning a person, edit the template and add the task': 'Szablon checklisty nie posiada żadnego zadania. Zanim przypiszesz osobę, edytuj szablon i dodaj zadanie.',
          'Checklist assigned correctly': 'Checklista przypisana pomyślnie',
          'There was an error during assigning checklist': 'Wystąpił błąd podczas przypisywania checklisty',
          'Assign': 'Przypisz',
          'Assign person': 'Przypisz osobę',
          'Checklist owner': 'Właściciel checklisty',
          'Checklist subject': 'Podmiot checklisty',
          'caption-owner': 'Właściciel to osoba odpowiedzialna za checklistę. Właściciele checklist widzą je na swoim dashboardzie. W przypadku checklist złączonych to pole jest wymagane.',
          'caption-subject': 'Podmiot to osoba, której dotyczy checklista. Przykładowo, jeżeli checklista przedstawia procedurę wprowadzania nowej osoby do firmy, wtedy ta nowa osoba jest podmiotem checklisty. To pole jest wymagane.',
          'Responsibles for checklist:': 'Odpowiedzialni za checkliste:',
          'Hidden for subject': 'Ukryj checklistę dla podmiotu',
        },
        en: {
          'caption-owner': 'Owner is a person responsible for a checklist. Owners see checklist on their dashboard. In case of united checklists this field is required.',
          'caption-subject': 'Subject is a person to whom the checklist applies, for example if checklist describes procedure of introducing new person, then this person is a subject of a checklist. This field is required.',
        },
      },
    },
  };
</script>
