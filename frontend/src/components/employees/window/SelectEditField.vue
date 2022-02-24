<template>
  <v-list-item>
    <v-list-item-action v-if="icon">
      <v-icon>{{ icon }}</v-icon>
    </v-list-item-action>
    <v-list-item-content v-if="editMode">
      <v-autocomplete :label="label"
                      v-model="inputModel"
                      style="width: 100%"
                      :item-text="text"
                      :item-value="value"
                      :no-data-text="$t('No data available')"
                      :items="values"
                      return-object/>
    </v-list-item-content>
    <v-list-item-content v-else>
      <v-list-item-title v-if="dataLabel">{{ dataLabel }}</v-list-item-title>
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
          <v-btn v-on="on" @click="cancelEdit" icon><v-icon>cancel</v-icon></v-btn>
        </template>
        {{ $t('Cancel') }}
      </v-tooltip>
    </v-list-item-action>
    <v-list-item-action v-if="editable && !editMode">
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="startEdit" icon><v-icon>edit</v-icon></v-btn>
        </template>
        {{ $t('Edit') }}
      </v-tooltip>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'SelectEditField',
    props: {
      employee: { type: Object, required: true },
      editable: { type: Boolean, default: false },
      field: { type: String, required: true },
      employeeToValue: { type: Function, default: null },
      label: { type: String, default: '' },
      icon: { type: String, default: '' },
      values: { type: Array, required: true },
      value: { type: String, default: 'id' },
      text: { type: String, default: 'name' },
    },
    data() {
      return {
        editMode: false,
        saving: false,
        inputModel: -1,
        backupModel: -1,
      };
    },
    computed: {
      valueObject() {
        const value = this.employeeToValue === null ? this.employee[this.field] : this.employeeToValue(this.employee);
        const filtered = this.values.filter(val => val[this.value] === value);
        return filtered.length === 1 ? filtered[0] : null;
      },
      dataLabel() {
        const value = this.valueObject;
        if (value === null) {
          return '';
        }
        return value[this.text];
      },
    },
    watch: {
      employee() {
        this.editMode = false;
        this.saving = false;
        const value = this.valueObject;
        this.inputModel = value === null ? null : value[this.value];
      },
    },
    methods: {
      startEdit() {
        this.backupModel = this.inputModel;
        this.editMode = true;
      },
      cancelEdit() {
        this.editMode = false;
        this.inputModel = this.backupModel;
      },
      async save() {
        this.saving = true;
        const data = { id: this.employee.id };
        data[this.field] = this.inputModel[this.value];
        await this.$store.dispatch('Employees/saveEmployee', data);
        this.saving = false;
        this.editMode = false;
        EventBus.$emit(eventNames.employeeEdited);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Edit': 'Edytuj',
          'Cancel': 'Anuluj',
          'Save': 'Zapisz',
          'No data available': 'Brak dostępnych danych',
        },
      },
    },
  };
</script>
