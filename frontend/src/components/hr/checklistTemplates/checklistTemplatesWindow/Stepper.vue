<template>
  <v-dialog v-model="dialogVisible" v-if="dialogVisible" width="1100">
    <v-card id="checklist-templates-window">
      <v-app-bar color="transparent" class="headline" flat >
        <span :class="{'stepper__header--mobile': $vuetify.breakpoint.xs}">
          {{ $t('Checklist templates') }}
        </span>
        <v-spacer/>
        <v-btn icon @click="save"><v-icon>close</v-icon></v-btn>
      </v-app-bar>
      <v-card-text>
        <v-progress-linear height="6" indeterminate v-if="loading"/>
        <v-stepper v-model="step" else>
          <v-stepper-header>
            <v-stepper-step :complete="step > 1" step="1">{{ this.$t('Create checklist template') }}</v-stepper-step>
            <v-divider></v-divider>
            <v-stepper-step step="2">
              {{ this.$t('Create checklist template tasks') }}
            </v-stepper-step>
          </v-stepper-header>
          <v-stepper-items>
            <v-stepper-content step="1" :class="{'pa-0': $vuetify.breakpoint.xs}">
              <checklist-templates-window-content
                id="checklist-templates-window-content"
                :checklist-template="checklistTemplate"
                :edit="edit"
                @saveChecklist="saveChecklist"
                @close="dialogVisible = false"
                @goToNextStep="goToNextStep"
                :employees="employees"/>
            </v-stepper-content>
            <v-stepper-content step="2" :class="{'pa-0': $vuetify.breakpoint.xs}">
              <checklist-templates-window-table
                v-if="step===2"
                :united="united"
                :employees="employees"
                :checklist-template="checklistTemplate"/>
              <div class="stepper_buttons">
                <v-btn @click="step = 1" text class="stepper_button">
                  {{ $t('Return') }}
                </v-btn>
                <v-spacer/>
                <v-btn color="red" @click="dialogVisible = false" text class="stepper_button">
                  {{ $t('Cancel') }}
                </v-btn>
                <v-btn color="primary" @click="save" text class="stepper_button">
                  {{ $t('Save') }}
                </v-btn>
              </div>
            </v-stepper-content>
          </v-stepper-items>
        </v-stepper>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../../../eventbus';
  import ChecklistTemplatesWindowContent from './Content';
  import ChecklistTemplatesWindowTable from './QuestionTable';
  import { mapState } from 'vuex';

  export default {
    name: 'ChecklistStepper',
    components: { ChecklistTemplatesWindowContent, ChecklistTemplatesWindowTable },
    data() {
      return {
        edit: false,
        dialogVisible: false,
        deleteDialogVisible: false,
        loading: false,
        united: true,
        step: 1,
        checklistTemplate: {},
      };
    },
    computed: {
      ...mapState({
        employees: state => state.Employees.employees,
      }),
    },
    methods: {
      async show(item) {
        this.step = 1;
        if(item) {
          this.checklistTemplate = item;
          this.edit = true;
        }
        else{
          Object.assign(this.$data, this.$options.data());
        }
        if (!this.dialogVisible) {
          this.dialogVisible = true;
        }
      },
      async saveChecklist(data, united) {
        this.united = united;
        this.loading = true;
        if(!this.edit) {
          this.checklistTemplate = await this.$store.dispatch('Checklist/createChecklistTemplate', data);
        } else {
          const allChecklists = await this.$store.dispatch('Checklist/updateChecklistTemplate', data);
          this.checklistTemplate = allChecklists.find(val => val.id === this.checklistTemplate.id);
        }
        this.loading = false;
        this.step = 2;
      },
      goToNextStep(united) {
        this.united = united;
        this.step = 2;
      },
      save() {
        this.dialogVisible = false;
        Object.assign(this.$data, this.$options.data());
      },
    },
    mounted() {
      EventBus.$on(eventNames.showChecklistTemplatesWindow, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Details': 'Szczegóły',
          'Create checklist template': 'Stwórz nowy szablon checklisty',
          'Create checklist template tasks': 'Stwórz zadania do szablonu checklisty',
          'Checklist template has been deleted': 'Szablon checklisty został usunięty',
          'Checklist template has been created': 'Szablon checklisty został dodany',
          'Do you really want to delete this checklist template?': 'Czy na pewno chcesz usunąć ten szablon checklisty?',
          'History': 'Historia',
          'Save': 'Zapisz',
          'Cancel': 'Anuluj',
          'Return': 'Powrót',
          'Checklist templates': 'Szablony checklist',
        },
      },
    },
  };
</script>
<style>
  #checklist-templates-window .v-stepper .v-stepper__header{
    box-shadow: none;
  }
</style>
<style scoped>
  @media (max-width: 600px) {
    .stepper_button {
      margin-left: -6px;
    }
  }
  .stepper_buttons {
    display: flex;
  }
  .stepper__header--mobile{
    font-size: large;
  }
</style>
