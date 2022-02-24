<template>
  <v-dialog v-model="dialogVisible" width="800">
    <v-card id="dialog-tribe-form">
      <v-card-title class="headline pb-0">
        <span>{{ $t('Tribe form') }}</span>
      </v-card-title>
      <v-card-text :class="{'px-4': $vuetify.breakpoint.smAndUp}">
        <slack-status-switcher v-if="item.id !== null"
                               :slack-status="item.connectedToSlack ? SLACK_STATUS_ENABLED : SLACK_STATUS_DISABLED"
                               :loading="slackLoading"
                               @connect="connectToSlack"
                               @disconnect="disconnectFromSlack"/>
        <v-text-field v-model="name" class="required" :label="$t('Name')" required/>
        <v-text-field v-model="description" :label="$t('Description')"/>
        <v-text-field v-model="photoUrl" :label="$t('Photo Url')"/>
        <v-text-field v-model="url" :label="$t('Website')"/>
        <v-text-field v-model="hrEmail" type="email" :label="$t('Email for HR-related communications')"/>
        <employee-chooser :employees="getEmployees"
                          v-model="employee"
                          :label="$t('Add responsible f.ex Tribe Master')"
                          prepend-icon="supervisor_account"/>
        <v-btn color="primary" class="button" :disabled="!employee" @click="addEmployee" block
               :loading="slackLoading">
          {{ $t('Add') }}
        </v-btn>
        <span v-if="responsibleEmployees.length" class="body-1">{{ $t('Responsible for tribe:') }}</span>
        <div v-for="(responsibleEmployee, key) in responsibleEmployees" :key="key">
          <employee-chip v-if="typeof(responsibleEmployee) === 'object'" :employee="responsibleEmployee"/>
          <v-btn @click="deleteEmployee(responsibleEmployee)" icon>
            <v-icon class="icon" color="error">highlight_off</v-icon>
          </v-btn>
        </div>
        <v-checkbox v-model="negatedVirtual" :label="$t('Allow projects')"/>
        <div v-if="!item.id" class="mb-4">
          <v-icon>person</v-icon>
          <span> {{ $t('Tech leader can be set after adding persons to tribe') }}</span>
        </div>
        <employee-chooser v-else
                          :employees="tribeEmployees"
                          v-model="techLeader"
                          :label="$t('Choose tech leader')"
                          prepend-icon="person"/>
        <span class="body-1">{{ $t('Overwrite sick leave and free day IDs in Avaza (leave empty for default)') }}</span>
        <v-text-field v-model="item.sickLeaveProjectId" :label="$t('Sick leave project ID')"/>
        <v-text-field v-model="item.sickLeaveCategoryId" :label="$t('Sick leave category ID')"/>
        <v-text-field v-model="item.freeDayProjectId" :label="$t('Free day project ID')"/>
        <v-text-field v-model="item.freeDayCategoryId" :label="$t('Free day category ID')"/>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text @click="dialogVisible = false">
          {{ $t('Close') }}
        </v-btn>
        <v-btn :loading="buttonLoading" :disabled="!name.length > 0"
               color="blue" @click="item.id ? editTribe() : addTribe()" text>
          {{ $t('Save') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import { mapState } from 'vuex';
  import SlackStatusSwitcher from '../utils/SlackStatusSwitcher';
  import EmployeeChooser from '../utils/EmployeeChooser';
  import EmployeeChip from '../utils/EmployeeChip';

  const SLACK_STATUS_ENABLED = 3;
  const SLACK_STATUS_DISABLED = 0;
  const ROLE_TRIBE_MASTER = 'ROLE_TRIBE_MASTER';

  export default {
    name: 'TribeForm',
    components: { SlackStatusSwitcher, EmployeeChooser, EmployeeChip },
    props: {
      value: { type: Boolean, required: true },
      item: { type: Object, required: true },
    },
    data() { return {
      SLACK_STATUS_ENABLED,
      SLACK_STATUS_DISABLED,
      buttonLoading: false,
      slackLoading: false,
      employee: null,
      responsibleEmployees: this.item.responsible,
      filteredEmployees: [],
      url: this.item.url,
      name: this.item.name,
      techLeader: this.item.techLeader ?? null,
      hrEmail: this.item.hrEmail,
      photoUrl: this.item.photoUrl,
      description: this.item.description,
    };},
    computed: {
      ...mapState({
        token: state => state.Authorization.token,
        employees: state => state.Employees.employees,
      }),
      dialogVisible: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
      tribeEmployees() {
        const employees = this.employees.filter(element => {
          return element.tribe !== undefined;
        });
        return employees.filter(val => val.tribe.id === this.item.id);
      },
      getEmployees() {
        if (this.item.id && !this.filteredEmployees.length) {
          const responsibleIds = this.item.responsible.map(val => val.id);
          this.filteredEmployees = this.employees
            .filter(val => val.roles.includes(ROLE_TRIBE_MASTER) && !responsibleIds.includes(val.id));
          return this.filteredEmployees;
        }
        return !this.responsibleEmployees.length
          ? this.employees.filter(val => val.roles.includes(ROLE_TRIBE_MASTER)) : this.filteredEmployees;
      },
      negatedVirtual: {
        get() {
          return !this.item.isVirtual;
        },
        set(val) {
          this.item.isVirtual = !val;
        },
      },
    },
    methods: {
      getData() {
        const { id, isVirtual, slackStatus, sickLeaveProjectId, sickLeaveCategoryId, freeDayProjectId,
                freeDayCategoryId } = this.item;
        return {
          id,
          name: this.name,
          description: this.description,
          url: this.url,
          hrEmail: this.hrEmail,
          photoUrl: this.photoUrl,
          isVirtual,
          responsible: this.responsibleEmployees.map(val => val.id),
          connectedToSlack: slackStatus === SLACK_STATUS_ENABLED,
          sickLeaveProjectId,
          sickLeaveCategoryId,
          freeDayProjectId,
          freeDayCategoryId,
          techLeader: this.techLeader,
          techLeaderId: this.techLeader !== null ? this.techLeader.id : null,
        };
      },
      deleteEmployee(employee) {
        this.responsibleEmployees = this.responsibleEmployees.filter(val => val !== employee);
        this.filteredEmployees.push(employee);
      },
      addEmployee() {
        this.filteredEmployees = !this.responsibleEmployees.length
          ? this.employees.filter(val => val.roles.includes(ROLE_TRIBE_MASTER)).filter(val => val !== this.employee)
          : this.filteredEmployees.filter(val => val !== this.employee);
        this.responsibleEmployees.push(this.employee);
        this.employee = null;
      },
      connectToSlack() {
        this.slackLoading = true;
        const redirect = `${window.ADVENTURE_BACKEND_URL}/slack/redirectUser?token=${this.token}&type=tribe&id=${this.item.id}`;
        window.location.replace(redirect);
      },
      async disconnectFromSlack() {
        this.slackLoading = true;
        await this.$store.dispatch('Tribes/disconnectFromSlack', this.id);
        this.item.slackStatus = SLACK_STATUS_DISABLED;
        const data = this.getData();
        EventBus.$emit(eventNames.refreshTribeWindow, data);
        this.slackLoading = false;
      },
      async editTribe() {
        this.buttonLoading = true;
        const data = this.getData();
        try {
          await this.$store.dispatch('Tribes/update', data);
          data.responsible = this.responsibleEmployees;
          await EventBus.$emit(eventNames.refreshTribeWindow, data);
          this.$store.commit('showSnackbar', {
            text: this.$t('Tribe has been edited'),
            color: 'success',
          });
          this.dialogVisible = false;
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Tribe can not be edit'),
            color: 'error',
          });
        }
        this.buttonLoading = false;
      },
      async addTribe() {
        this.buttonLoading = true;
        const data = this.getData();
        try {
          await this.$store.dispatch('Tribes/new', data);
          this.$store.commit('showSnackbar', {
            text: this.$t('Tribe has been added'),
            color: 'success',
          });
          this.dialogVisible = false;
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Tribe can not be added'),
            color: 'error',
          });
        }
        this.buttonLoading = false;
      },
    },
    i18n: { messages: {
      pl: {
        'Tribe form': 'Formularz praktyki',
        'Allow projects': 'Pozwól na dodawanie projektów',
        'Close': 'Zamknij',
        'Add': 'Dodaj',
        'Name': 'Nazwa',
        'Description': 'Opis',
        'Website': 'Strona',
        'Required': 'Wymagane',
        'Photo Url': 'Ścieżka do zdjęcia',
        'Save': 'Zapisz',
        'Tech leader can be set after adding persons to tribe': 'Lidera technicznego można ustawić po dodaniu osób do plemienia',
        'Choose tech leader': 'Wybierz lidera technicznego',
        'This name already exists': 'Ta nazwa już istnieje',
        'All': 'Wszystkie',
        'Tribe has been edited': 'Informacje o plemieniu zostały zmienione',
        'Tribe can not be edit': 'Informacje o plemieniu nie zostały zmienione',
        'Tribe can not be added': 'Plemię nie zostało dodane',
        'Tribe has been added': 'Plemię zostało dodane',
        'Add responsible f.ex Tribe Master': 'Dodaj odpowiedzialnych np. Tribe Master',
        'Responsible for tribe:': 'Odpowiedzialni za plemię',
        'Email for HR-related communications': 'Adres e-mail dla celów HR-owych',
        'Overwrite sick leave and free day IDs in Avaza (leave empty for default)':
          'Nadpisz ID zwolnień chorobowych i dni wolnych w Avazie (pozostaw puste dla domyślnych)',
        'Sick leave project ID': 'ID projektu zwolnienia chorobowego',
        'Sick leave category ID': 'ID kategorii zwolnienia chorobowego',
        'Free day project ID': 'ID projektu dnia wolnego',
        'Free day category ID': 'ID kategorii dnia wolnego',
      },
      en: {
        'Tribe form': 'Practice form',
      },
    } },
  };
</script>

<style scoped>
  .required >>> label::after {
    content: "*";
  }
</style>
