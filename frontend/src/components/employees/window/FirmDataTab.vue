<template>
  <v-list two-line>
    <v-btn v-if="isSuperAdmin && employee.locked"
           @click="unlock"
           color="error"
           :loading="unlockInProgress"
           block>
      {{ $t('Unlock') }}
    </v-btn>
    <slack-status-tile v-if="isCurrentUser" :employee="employee"/>
    <template v-if="isSuperAdmin">
      <simple-edit-field :employee="employee"
                         :editable="false"
                         field="financeCode"
                         :label="$t('Finance Code identifier')"
                         icon="calendar_today"/>
    </template>
    <template v-if="isSuperAdmin">
      <simple-edit-field :employee="employee"
                         :editable="false"
                         field="superiorEmail"
                         :label="$t('Superior email')"
                         icon="calendar_today"/>
    </template>
    <template v-if="isSuperAdmin">
      <simple-edit-field :employee="employee"
                         :editable="false"
                         field="employee_code"
                         :label="$t('Id number')"
                         icon="calendar_today"/>
    </template>
    <template v-if="isSuperAdmin && (parseInt(employee.contract.id) === parseInt(contractsName.B2B_LUMP_SUM.id)
      || parseInt(employee.contract.id) === parseInt(contractsName.B2B_HOURLY.id))">
      <simple-edit-field :employee="employee"
                         :editable="false"
                         field="nip"
                         :label="$t('Tax number')"
                         icon="calendar_today"/>
      <simple-edit-field :employee="employee"
                         :editable="false"
                         field="firmName"
                         :label="$t('Firm name')"
                         icon="calendar_today"/>
      <simple-edit-field :employee="employee"
                         :editable="false"
                         field="firmAddress"
                         :label="$t('Firm address')"
                         icon="calendar_today"/>
    </template>
    <simple-edit-field v-if="editable || isCurrentUser"
                       :employee="employee"
                       :editable="false"
                       field="photo"
                       :label="$t('Url to photo')"
                       icon="photo"/>
    <select-edit-field v-if="(editable && isSuperAdmin) || isCurrentUser"
                       :employee="employee"
                       :editable="false"
                       field="contract"
                       :label="$t('Contract type')"
                       :values="contracts"
                       :employee-to-value="e => (e.contract || {}).id"
                       icon="account_box"/>
    <select-edit-field v-if="subContractTypeVisible"
                       :employee="employee"
                       :editable="false"
                       field="subTypeContract"
                       :label="$t('Sub contract type')"
                       :values="outsourceSubTypes"
                       icon="account_box"/>
    <role-edit-field :employee="employee" v-if="isCurrentUser || isSuperAdmin" :editable="canEditPermissions"/>
    <simple-edit-field v-if="isTribeMaster || isCurrentUser"
                       :employee="employee"
                       :editable="false"
                       field="jobTimeValue"
                       :employee-to-value="employeeToJobTime"
                       :input-to-value="stringToJobTime"
                       :label="$t('Job time')"
                       :validate="validateJobTime"
                       icon="av_timer"/>
    <select-edit-field :employee="employee"
                       :editable="false"
                       field="workMode"
                       :values="workModeOptions"
                       icon="home_work"/>
    <date-edit-field v-if="hireDateVisible"
                     :employee="employee"
                     :editable="false"
                     field="hiredAt"
                     :label="$t('Hire date')"
                     icon="calendar_today"/>
    <v-list-item>
      <v-list-item-action><v-icon>group_work</v-icon></v-list-item-action>
      <v-list-item-content>
        <v-list-item-title v-if="employee.tribe">{{ employee.tribe.name }}</v-list-item-title>
        <v-list-item-title v-else style="color: rgba(0, 0, 0, 0.54)">{{ $t('Tribe') }}</v-list-item-title>
        <v-list-item-subtitle v-if="employee.tribe">{{ $t('Tribe') }}</v-list-item-subtitle>
      </v-list-item-content>
      <v-list-item-action v-if="isSuperAdmin || isHr || isHelpdesk">
      </v-list-item-action>
    </v-list-item>
    <v-list-item v-if="employee.position">
      <v-list-item-action><v-icon>work</v-icon></v-list-item-action>
      <v-list-item-content>
        <v-list-item-title>{{ (employee.level || {}).name }} {{ employee.position.name }}</v-list-item-title>
        <v-list-item-subtitle>{{ $t('Position') }}</v-list-item-subtitle>
      </v-list-item-content>
    </v-list-item>
  </v-list>
</template>

<script>
  import SimpleEditField from './SimpleEditField';
  import SelectEditField from './SelectEditField';
  import DateEditField from './DateEditField';
  import { EventBus, eventNames } from '../../../eventbus';
  import { mapGetters, mapState } from 'vuex';
  import RoleEditField from './RoleEditField';
  import SlackStatusTile from './SlackStatusTile';
  import { contractsName } from '../../../util/contracts';

  export default {
    name: 'FirmDataTab',
    components: { SlackStatusTile, RoleEditField, DateEditField, SelectEditField, SimpleEditField },
    props: {
      employee: { type: Object, required: true },
      editable: { type: Boolean, default: false },
    },
    data() {
      return {
        contractsName,
        unlockInProgress: false,
        workModeOptions: [{
          name: this.$t('Work from office'),
          id: 1,
        }, {
          name: this.$t('Work remotely'),
          id: 2,
        }, {
          name: this.$t('Work partial remotely'),
          id: 3,
        }],
        contracts: [{
          name: this.$t('Business-to-business contract lump sum'),
          id: 1,
        }, {
          name: this.$t('Business-to-business contract hourly billing'),
          id: 2,
        }, {
          name: this.$t('Civil law contract lump sum'),
          id: 3,
        }, {
          name: this.$t('Civil law contract hourly billing'),
          id: 4,
        }, {
          name: this.$t('Employment contract'),
          id: 5,
        }, {
          name: this.$t('Outsourcing'),
          id: 6,
        }],
        outsourceSubTypes: [
          {
            name: this.$t('Outsourcing'),
            id: 'Outsourcing',
          },
          {
            name: this.$t('Freelancing'),
            id: 'Freelancing',
          },
          {
            name: this.$t('Outsourcing-passive'),
            id: 'Outsourcing-passive',
          },
        ],
        showSubContractField: false,
      };
    },
    computed: {
      ...mapGetters({
        isSuperAdmin: 'Authorization/isSuperAdmin',
        isTribeMaster: 'Authorization/isTribeMaster',
        isHr: 'Authorization/isHr',
        isHelpdesk: 'Authorization/isHelpdesk',
      }),
      ...mapState({
        currentUser: state => state.Employees.loggedEmployee,
        employees: state => state.Employees.employees,
      }),
      hireDateVisible() {
        if (this.isCurrentUser || this.isSuperAdmin || this.isHr) {
          return true;
        }
        else if (typeof this.currentUser.tribe === 'undefined' || typeof this.employee.tribe === 'undefined') {
          return false;
        }
        else if (this.isTribeMaster) {
          const currentUser = this.currentUser || {};
          const currentUserTribe = currentUser.tribe || {};
          const userTribe = this.employee.tribe;
          return currentUserTribe.id === userTribe.id;
        } else {
          return false;
        }
      },
      isCurrentUser() {
        const currentUser = this.currentUser || {};
        return currentUser.id === this.employee.id;
      },
      canEditPermissions() {
        const demoEnabled = window.ADVENTURE_DEMO_ENABLED || false;
        return this.isSuperAdmin || demoEnabled ;
      },
      subContractTypeVisible() {
        if (this.showSubContractField === true
          || parseInt(this.employee.contract.id) === parseInt(this.contractsName.OUTSOURCE.id)) {
          return (this.editable && this.isSuperAdmin) || this.isCurrentUser;
        }
        return false;
      },
    },
    watch: {
      'employee.contract': function(n) {
        if (parseInt(n.id) === parseInt(this.contractsName.OUTSOURCE.id)) {
          this.showSubContractField = true;
        } else {
          this.showSubContractField = false;
        }
      },

    },
    methods: {
      editTribeAssignment() {
        EventBus.$emit(eventNames.showTribeAssignToEmployeeWindow, this.employee);
      },
      employeeToJobTime(employee) {
        const jobTimeValue = employee.jobTimeValue || 0;
        const jobTime = jobTimeValue / 28800;
        return Math.round(jobTime * 100) / 100;
      },
      stringToJobTime(str) {
        const jobTime = parseFloat(str.replace(',', '.'));
        const jobTimeValue = Number.isNaN(jobTime) ? -1 : jobTime * 28800;
        return { jobTimeValue };
      },
      validateJobTime(val) {
        return ((val.replace(',', '.')) > 0 && (val.replace(',', '.')) <= 1)
          || this.$t('Worktime must be a real number between 0 and 1');
      },
      async unlock() {
        this.unlockInProgress = true;
        await this.$store.dispatch('Employees/unlockUser', this.employee.id);
        this.unlockInProgress = false;
        EventBus.$emit(eventNames.employeeEdited);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Contract type': 'Forma współpracy',
          'Employment contract': 'Umowa o pracę',
          'Civil law contract lump sum': 'Umowa cywilnoprawna ryczałt',
          'Civil law contract hourly billing': 'Umowa cywilnoprawna rozliczenie godzinowe',
          'Business-to-business contract lump sum': 'Umowa business-to-business ryczałt',
          'Business-to-business contract hourly billing': 'Umowa business-to-business rozliczenie godzinowe',
          'Hire date': 'Data rozpoczęcia współpracy',
          'Work from office': 'Praca z biura',
          'Work remotely': 'Praca zdalna',
          'Work partial remotely': 'Praca cześciowo zdalna',
          'Tribe': 'Praktyka',
          'Position': 'Stanowisko',
          'Your Leader': 'Twój Lider',
          'Edit': 'Edytuj',
          'Job time': 'Etat',
          'Unlock': 'Odblokuj',
          'Url to photo': 'Url do zdjęcia',
          'Worktime must be a real number between 0 and 1': 'Etat musi być liczbą rzeczywistą między 0 a 1',
          'Firm name': 'Nazwa firmy',
          'Firm address': 'Adres firmy',
          'Tax number': 'NIP',
          'Finance Code identifier': 'Identyfiaktor finansowy pracownika',
          'Id number': 'Idenyfaktor pracownika (WZ)',
          'Superior email': 'Email przełożonego',
          'Sub contract type': 'Typ umowy outsource',
        },
        en: {
          'Sub contract type': 'Sub contract for outsource type',
          'Tribe': 'Practice',
        },
      },
    },
  };
</script>
