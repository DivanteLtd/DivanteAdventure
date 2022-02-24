<template>
  <v-list-item>
    <v-list-item-action>
      <v-icon>supervisor_account</v-icon>
    </v-list-item-action>
    <v-list-item-content v-if="editMode">
      <v-container class="pa-0 ma-0" fluid>
        <v-row no-gutters class="pa-0 ma-0" wrap>
          <v-col>
            <v-select :label="$t('Permissions')"
                      v-model="selectModel"
                      item-text="name"
                      item-value="id"
                      :no-data-text="$t('No data available')"
                      :items="selectRoles"
                      return-object/>
          </v-col>
          <v-col v-for="(checkbox, index) in checkboxModel" :key="index">
            <v-checkbox v-model="checkbox.selected"
                        class="pl-1"
                        :disabled="checkbox.disabled"
                        :label="checkbox.name"/>
          </v-col>
        </v-row>
      </v-container>
    </v-list-item-content>
    <v-list-item-content v-else>
      <v-list-item-title>{{ displayModel }}</v-list-item-title>
      <v-list-item-subtitle>{{ $t('Permissions') }}</v-list-item-subtitle>
    </v-list-item-content>
    <v-list-item-action v-if="editable && editMode">
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="save" :loading="saving" icon><v-icon>save</v-icon></v-btn>
        </template>
        {{ $t('Save') }}
      </v-tooltip>
    </v-list-item-action>
    <v-list-item-action v-if="editable && editMode">
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="cancelEditing" icon><v-icon>cancel</v-icon></v-btn>
        </template>
        {{ $t('Cancel') }}
      </v-tooltip>
    </v-list-item-action>
    <v-list-item-action v-if="editable && !editMode">
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="startEditing" icon><v-icon>edit</v-icon></v-btn>
        </template>
        {{ $t('Edit') }}
      </v-tooltip>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
  import { allRolesI18n, getTopRole, appRoles } from '../../../util/roles';
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'RoleEditField',
    props: {
      employee: { type: Object, required: true },
      editable: { type: Boolean, default: false },
    },
    data() {
      return {
        editMode: false,
        saving: false,
        selectModel: {},
        checkboxModel: [],
        backupData: { select: {}, checkbox: [] },
      };
    },
    computed: {
      userRoles() {
        const rolesFromPayload = this.employee.roles;
        const userRoles = [];
        if (Array.isArray(rolesFromPayload)) {
          return rolesFromPayload;
        } else if (typeof rolesFromPayload === 'object') {
          for (const key in rolesFromPayload) {
            if (rolesFromPayload.hasOwnProperty(key)) {
              userRoles.push(rolesFromPayload[key]);
            }
          }
          return userRoles;
        } else {
          return [];
        }
      },
      selectRoles() {
        return allRolesI18n.filter(role => !role.special).map(role => ({ ...role, name: this.$t(role.i18nName) }));
      },
      checkboxRoles() {
        return allRolesI18n.filter(role => role.special).map(role => ({ ...role, name: this.$t(role.i18nName) }));
      },
      displayModel() {
        const topRole = this.selectModel;
        const childrenRoles = appRoles[topRole.i18nName];
        const checkboxNames = this.checkboxModel
          .filter(checkbox => checkbox.selected && !childrenRoles.includes(checkbox.i18nName))
          .map(checkbox => checkbox.name)
          .join(', ');

        if (checkboxNames.length > 0) {
          return `${topRole.name} (+ ${checkboxNames})`;
        } else {
          return topRole.name;
        }
      },
    },
    watch: {
      selectModel() {
        this.updateCheckboxModel([this.selectModel.i18nName]);
      },
      employee() {
        this.editMode = false;
        this.saving = false;
        const topRole = getTopRole(this.userRoles, true);
        this.selectModel = { ...topRole, name: this.$t(topRole.i18nName) };
        this.updateCheckboxModel(this.userRoles);
      },
    },
    methods: {
      startEditing() {
        this.backupData = {
          select: this.selectModel,
          checkbox: this.checkboxModel,
        };
        this.editMode = true;
      },
      cancelEditing() {
        this.editMode = false;
        this.selectModel = this.backupData.select;
        this.checkboxModel = this.backupData.checkbox;
      },
      updateCheckboxModel(roles) {
        const topRole = getTopRole(roles, true);
        const childrenRoles = appRoles[topRole.i18nName];
        this.checkboxModel = this.checkboxRoles.map(role => ({
          ...role,
          selected: childrenRoles.includes(role.i18nName) || this.userRoles.includes(role.i18nName),
          disabled: childrenRoles.includes(role.i18nName),
        }));
      },
      async save() {
        this.saving = true;
        const id = this.employee.id;
        const roles = [
          this.selectModel.i18nName,
          ...this.checkboxModel.filter(checkbox => checkbox.selected).map(checkbox => checkbox.i18nName),
        ];
        const data = { id, roles };
        await this.$store.dispatch('Employees/saveEmployee', data);
        this.saving = false;
        this.editMode = false;
        EventBus.$emit(eventNames.employeeEdited);
      },
    },
    mounted() {
      const topRole = getTopRole(this.userRoles, true);
      this.selectModel = { ...topRole, name: this.$t(topRole.i18nName) };
    },
    i18n: {
      messages: {
        pl: {
          'Permissions': 'Uprawnienia',
          'Edit': 'Edytuj',
          'Cancel': 'Anuluj',
          'Save': 'Zapisz',
          'No data available': 'Brak dostępnych danych',
          'ROLE_USER': 'Użytkownik',
          'ROLE_MANAGER': 'Menedżer',
          'ROLE_HR': 'HR',
          'ROLE_TRIBE_MASTER': 'Dyrektor',
          'ROLE_HELPDESK': 'Helpdesk',
          'ROLE_SUPER_ADMIN': 'Administrator',
        },
        en: {
          ROLE_USER: 'User',
          ROLE_MANAGER: 'Manager',
          ROLE_HR: 'HR',
          ROLE_TRIBE_MASTER: 'Director',
          ROLE_HELPDESK: 'Helpdesk',
          ROLE_SUPER_ADMIN: 'Administrator',
        },
      },
    },
  };
</script>
