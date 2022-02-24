<template>
  <v-list-item>
    <v-list-item-action v-if="icon">
      <v-icon>{{ icon }}</v-icon>
    </v-list-item-action>
    <v-list-item-content v-if="editMode">
      <v-text-field :label="label"
                    v-model="inputModel"
                    style="width: 100%"
                    :mask="mask"
                    :error-messages="errorMessages"/>
    </v-list-item-content>
    <v-list-item-content v-else>
      <v-list-item-title v-if="dataLabel"> {{ dataLabel }}</v-list-item-title>
      <v-list-item-title v-else style="color: rgba(0, 0, 0, 0.54)">{{ label }}</v-list-item-title>
      <v-list-item-subtitle v-if="dataLabel">{{ label }}</v-list-item-subtitle>
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
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'SimpleEditField',
    props: {
      employee: { type: Object, required: true },
      editable: { type: Boolean, default: false },
      field: { type: String, default: '' },
      employeeToValue: { type: Function, default: () => '' },
      inputToValue: { type: Function, default: null },
      label: { type: String, required: true },
      icon: { type: String, default: '' },
      validate: { type: Function, default: null },
      mask: { type: String, required: false, default: undefined },
    },
    data() {
      return {
        editMode: false,
        saving: false,
        inputModel: '',
        inputModelBackup: '',
        errorMessages: [],
      };
    },
    computed: {
      dataLabel() {
        let fromFunc = this.employeeToValue(this.employee);
        if (typeof fromFunc === 'undefined') {
          fromFunc = '';
        } else if (typeof fromFunc === 'number') {
          fromFunc = `${fromFunc}`;
        }
        fromFunc = fromFunc.trim();
        return fromFunc.length === 0 ? this.employee[this.field] : fromFunc;
      },
    },
    watch: {
      employee() {
        this.inputModel = this.dataLabel;
        this.editMode = false;
        this.saving = false;
        this.errorMessages = [];
      },
      inputModel() {
        this.errorMessages = [];
      },
    },
    methods: {
      startEditing() {
        this.inputModelBackup = this.inputModel;
        this.editMode = true;
      },
      cancelEditing() {
        this.editMode = false;
        this.inputModel = this.inputModelBackup;
      },
      async save() {
        const validateError = typeof this.validate === 'function' ? this.validate(this.inputModel.trim()) : '';
        if (typeof validateError === 'string' && validateError.trim().length > 0) {
          this.errorMessages = [ validateError.trim() ];
          return;
        }

        this.errorMessages = [];
        this.saving = true;
        let data = {};
        if (typeof this.inputToValue === 'function') {
          data = this.inputToValue(this.inputModel.trim());
        } else {
          data[this.field] = this.inputModel.trim();
        }
        data.id = this.employee.id;
        try {
          await this.$store.dispatch('Employees/saveEmployee', data);
          this.saving = false;
          this.editMode = false;
          EventBus.$emit(eventNames.employeeEdited);
        } catch (e) {
          this.saving = false;
          this.errorMessages.push(e);
          throw e;
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          Edit: 'Edytuj',
          Cancel: 'Anuluj',
          Save: 'Zapisz',
        },
      },
    },
  };
</script>
