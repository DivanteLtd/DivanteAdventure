<template>
  <v-dialog v-model="dialogVisible" width="800">
    <v-card id="dialog-project-form">
      <v-card-title class="headline">
        <span>{{ formText }}</span>
      </v-card-title>
      <v-divider/>
      <v-card-text :class="{'px-4': $vuetify.breakpoint.smAndUp}">
        <v-tabs v-model="selectedTab" centered>
          <v-tab>{{ $t('Project') }}</v-tab>
          <v-tab>{{ $t('Integrations') }}</v-tab>
        </v-tabs>
        <v-tabs-items v-model="selectedTab" touchless>
          <v-tab-item :key="0">
            <v-text-field v-model="name" :label="$t('Name')" required class="required" :rules="rules.required"/>
            <v-text-field v-model="code"
                          :hint="$t('Project code, or ?')"
                          :label="$t('Code')"
                          class="required"
                          :rules="rules.required"/>
            <v-text-field v-model="description" :label="$t('Description')"/>
            <v-text-field v-model="photo" :label="$t('Photo')"/>
            <v-text-field v-model="url" :label="$t('Website')"/>
            <v-select :label="$t('Type of project')"
                      v-model="project_type.value"
                      item-text="name"
                      item-value="value"
                      :items="projectTypes"
                      class="required"/>
            <v-select :label="$t('Is billable')"
                      v-model="project_billable.value"
                      item-text="name"
                      item-value="value"
                      :items="billable"
                      class="required"/>
            <v-text-field type="number"
                          v-model="planned_budget"
                          :label="$t('Planned budget in hours per month')"/>
            <v-row no-gutters wrap>
              <v-col cols="12" sm="6" md="5">
                <v-menu v-model="menu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        min-width="290px"
                        offset-y>
                  <template v-slot:activator="{ on }">
                    <v-text-field v-model="started_at"
                                  :label="$t('Start project date')"
                                  :rules="rules.required"
                                  prepend-icon="event"
                                  v-on="on"
                                  class="required" readonly/>
                  </template>
                  <v-date-picker color="indigo" :locale="locale"
                                 v-model="started_at"
                                 :first-day-of-week="$t('date.firstDayOfWeek')"
                                 @input="menu = false"
                                 @change="validateDates">
                  </v-date-picker>
                </v-menu>
              </v-col>
              <v-spacer/>
              <v-col cols="12" sm="6" md="6">
                <v-menu v-model="menu2"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        min-width="290px"
                        offset-y>
                  <template v-slot:activator="{ on }">
                    <v-text-field v-model="ended_at"
                                  :label="$t('End project date')"
                                  :rules="[isValidate]"
                                  prepend-icon="event"
                                  v-on="on"
                                  readonly/>
                  </template>
                  <v-date-picker color="indigo" :locale="locale"
                                 v-model="ended_at"
                                 :first-day-of-week="$t('date.firstDayOfWeek')"
                                 @input="menu2 = false"
                                 @change="validateDates"
                                 @click="validateDates">
                  </v-date-picker>
                </v-menu>
              </v-col>
            </v-row>
          </v-tab-item>
          <v-tab-item :key="1">
            <v-list two-line>
              <slack-status-switcher :loading="slackLoading"
                                     :slack-status="slackStatus"
                                     @connect="connectToSlack"
                                     @disconnect="disconnectFromSlack"/>
              <v-autocomplete v-model="gitlab_projects"
                              class="my-2"
                              :items="allGitlabProjects"
                              item-value="id"
                              item-text="name"
                              :label="$t('GitLab repositories and groups')"
                              chips multiple deletable-chips
              />
            </v-list>
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>
      <v-card-actions>
        <v-spacer/>
        <v-btn :disabled="!isSaveButtonEnabled"
               color="blue" @click="id !== 0 ? editProject() : addProject()" text>
          {{ actionText }}
        </v-btn>
        <v-btn text @click="dialogVisible = false">
          {{ $t('Close') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import { projectsType } from '../../util/typeOfProjects';
  import moment from '@divante-adventure/work-moment';
  import { getSuggestedLanguage } from '../../i18n/i18n';
  import { mapState } from 'vuex';
  import SlackStatusSwitcher from '../utils/SlackStatusSwitcher';

  const SLACK_STATUS_ENABLED = 3;
  const SLACK_STATUS_DISABLED = 0;

  export default {
    name: 'ProjectForm',
    components: { SlackStatusSwitcher },
    data() {
      return {
        slackLoading: false,
        slackStatus: SLACK_STATUS_DISABLED,
        selectedTab: null,
        billable: [{
          name: this.$t('Billable project'),
          value: true,
        }, {
          name: this.$t('Non-billable project'),
          value: false,
        }],
        projectTypes: [
          {
            value: projectsType.undefined.value,
            name: this.$t(projectsType.undefined.name),
          }, {
            value: projectsType.implementation.value,
            name: this.$t(projectsType.implementation.name),
          }, {
            value: projectsType.maintenance.value,
            name: this.$t(projectsType.maintenance.name),
          },
        ],
        locale: getSuggestedLanguage(),
        dialogVisible: false,
        isValidate: true,
        menu: false,
        menu2: false,
        id: 0,
        planned_budget: 0,
        formText: '',
        actionText: '',
        name: '',
        code: '',
        description: '',
        url: '',
        photo: '',
        project_type: {},
        project_billable: {},
        gitlab_projects: [],
        started_at: null,
        ended_at: null,
        isVirtual: false,
        rules: {
          required: [v => !!v || this.$t('This field is required')],
        },
      };
    },
    computed: {
      ...mapState({
        allGitlabProjects: state => state.Projects.gitlabProjects,
        token: state => state.Authorization.token,
      }),
      isSaveButtonEnabled() {
        const response = this.name && this.isValidate === true && this.started_at !== null
          && (typeof(this.project_type.value) === 'number') && this.code
          && (this.project_billable.value || this.project_billable.value === false);
        return response;
      },
    },
    methods: {
      show(item) {
        if (item) {
          this.id = item.id;
          this.planned_budget = item.planned_budget > 0 ? item.planned_budget : 0;
          this.slackStatus = item.connectedToSlack ? SLACK_STATUS_ENABLED : SLACK_STATUS_DISABLED;
          this.slackLoading = false;
          this.name = item.name;
          this.description = item.description;
          this.photo = item.photo;
          this.url = item.url;
          if (typeof item.started_at === 'string' || typeof item.ended_at === 'string') {
            this.started_at = item.started_at ? item.started_at : null;
            this.ended_at = item.ended_at ? item.ended_at : null;
          } else {
            this.started_at = item.started_at > 0 ? moment.unix(item.started_at).format('YYYY-MM-DD') : null;
            this.ended_at = item.ended_at > 0 ? moment.unix(item.ended_at).format('YYYY-MM-DD') : null;
          }
          this.project_type.value = item.project_type;
          this.project_billable.value = item.billable;
          this.gitlab_projects = item.gitlab_projects;
          this.code = item.code;
          this.actionText = this.$t('Save');
          this.formText = this.$t('Edit project');
          if (this.started_at === null) {
            this.started_at = undefined;
          }
        } else {
          this.id = 0;
          this.planned_budget = 0;
          this.name = '';
          this.description = '';
          this.photo = '';
          this.url = '';
          this.project_type = {};
          this.project_billable = {};
          this.gitlab_projects = [];
          this.started_at = null;
          this.ended_at = null;
          this.code = '?';
          this.actionText = this.$t('Add');
          this.formText = this.$t('Add project');
        }
        this.selectedTab = 0;
        this.dialogVisible = true;
      },
      validateDates() {
        this.isValidate = moment(this.started_at) > moment(this.ended_at) ? this.$t('Incorrect dates') : true;
        if (this.started_at === null) {
          this.started_at = undefined;
        }
      },
      async editProject() {
        const data = {
          id: this.id,
          name: this.name,
          description: this.description,
          url: this.url,
          planned_budget: Number(this.planned_budget),
          photo: this.photo,
          project_type: this.project_type.value,
          started_at: this.started_at,
          ended_at: this.ended_at,
          billable: this.project_billable.value,
          code: this.code,
          gitlab_projects: this.gitlab_projects,
        };
        try {
          await this.$store.dispatch('Projects/update', data);
          await EventBus.$emit(eventNames.refreshProjectWindow, data);
          this.$store.commit('showSnackbar', {
            text: this.$t('Project has been edited'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Project cannot be edited'),
            color: 'error',
          });
        }
        this.dialogVisible = false;
      },
      async addProject() {
        const data = {
          name: this.name,
          description: this.description,
          url: this.url,
          planned_budget: this.planned_budget,
          photo: this.photo,
          project_type: this.project_type.value,
          started_at: this.started_at,
          ended_at: this.ended_at,
          billable: this.project_billable.value,
          code: this.code,
          gitlab_projects: this.gitlab_projects,
        };
        try {
          await this.$store.dispatch('Projects/new', data);
          this.$store.commit('showSnackbar', {
            text: this.$t('Project has been added'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Project cannot be added'),
            color: 'error',
          });
        }
        this.dialogVisible = false;
      },
      connectToSlack() {
        this.slackLoading = true;
        const redirect = `${window.ADVENTURE_BACKEND_URL}/slack/redirectUser?token=${this.token}&type=project&id=${this.id}`;
        window.location.replace(redirect);
      },
      async disconnectFromSlack() {
        this.slackLoading = true;
        await this.$store.dispatch('Projects/disconnectFromSlack', this.id);
        EventBus.$emit(eventNames.refreshProjectWindow);
        this.slackStatus = SLACK_STATUS_DISABLED;
        this.slackLoading = false;
      },
    },
    mounted() {
      EventBus.$on(eventNames.projectForm, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Edit project': 'Edytuj projekt',
          'Add project': 'Dodaj projekt',
          'Project': 'Projekt',
          'Integrations': 'Integracje',
          'Planned budget in hours per month': 'Planowany budżet miesięczny w godzinach',
          'Start project date': 'Start projektu',
          'End project date': 'Koniec projektu',
          'Close': 'Zamknij',
          'This field is required': 'To pole jest wymagane',
          'Add': 'Dodaj',
          'Type of project': 'Typ projektu',
          'Undefined': 'Niezdefiniowany',
          'Implementation': 'Wdrożeniowy',
          'Maintenance': 'Utrzymaniowy',
          'Name': 'Nazwa',
          'Description': 'Opis',
          'Website': 'Strona',
          'Photo': 'Ścieżka do zdjęcia',
          'Is billable': 'Czy płatny',
          'Save': 'Zapisz',
          'All': 'Wszystkie',
          'Incorrect dates': 'Niepoprawne daty',
          'Project has been edited': 'Projekt został zmieniony',
          'Project cannot be edited': 'Projekt nie został zmieniony',
          'Project cannot be added': 'Projekt nie został dodany',
          'Project has been added': 'Projekt został dodany',
          'Billable project': 'Płatny projekt',
          'Non-billable project': 'Projekt bezpłatny',
          'Code': 'Kod',
          'Project code, or ?': 'Kod projektu, lub ?',
          'GitLab repositories and groups': 'Repozytoria i grupy w GitLabie',
        },
      },
    },
  };
</script>
<style scoped>
  .required >>> label::after {
    content: "*";
  }
</style>
