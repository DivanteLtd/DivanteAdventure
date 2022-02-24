<template>
  <v-list-item>
    <v-list-item-action v-if="icon">
      <v-icon>{{ icon }}</v-icon>
    </v-list-item-action>
    <v-list-item-content v-if="editMode">
      <v-menu v-model="menuVisible" :close-on-content-click="false" offset-y max-width="480">
        <template v-slot:activator="{ on }">
          <v-text-field v-on="on" :label="label" :value="textValue" readonly/>
        </template>
        <v-date-picker min="2008-01-01" v-model="inputModel" :locale="locale" width="300"
                       :no-title="$vuetify.breakpoint.xsOnly" :landscape="$vuetify.breakpoint.smAndUp"
                       :first-day-of-week="$t('date.firstDayOfWeek')"/>
      </v-menu>
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
          <v-btn v-on="on" @click="editMode = false" icon><v-icon>cancel</v-icon></v-btn>
        </template>
        {{ $t('Cancel') }}
      </v-tooltip>
    </v-list-item-action>
    <v-list-item-action v-if="editable && !editMode">
      <v-tooltip left>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="editMode = true" icon><v-icon>edit</v-icon></v-btn>
        </template>
        {{ $t('Edit') }}
      </v-tooltip>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import { EventBus, eventNames } from '../../../eventbus';

  const PICKER_FORMAT = 'YYYY-MM-DD';
  const DISPLAY_FORMAT = 'D MMMM YYYY';

  export default {
    name: 'DateEditField',
    props: {
      employee: { type: Object, required: true },
      editable: { type: Boolean, default: false },
      field: { type: String, required: true },
      label: { type: String, required: true },
      icon: { type: String, default: '' },
    },
    data() {
      return {
        locale: getSuggestedLanguage(),
        editMode: false,
        saving: false,
        menuVisible: false,
        inputModel: '',
      };
    },
    computed: {
      dataLabel() {
        const field = this.employee[this.field];
        if (typeof field === 'string' && field.length > 4) {
          const date = moment(this.employee[this.field]);
          return date.format(DISPLAY_FORMAT);
        } else {
          return false;
        }
      },
      textValue() {
        return this.inputModel ? moment(this.inputModel, PICKER_FORMAT).format(DISPLAY_FORMAT) : '';
      },
    },
    watch: {
      employee() {
        this.editMode = false;
        this.saving = false;
        this.menuVisible = false;
        const currentDate = this.employee[this.field];
        this.inputModel = currentDate ? moment(currentDate, PICKER_FORMAT).format(PICKER_FORMAT) : '';
      },
      inputModel() {
        this.menuVisible = false;
      },
    },
    methods: {
      async save() {
        this.saving = true;
        const data = { id: this.employee.id };
        data[this.field] = this.inputModel;
        await this.$store.dispatch('Employees/saveEmployee', data);
        this.saving = false;
        this.editMode = false;
        EventBus.$emit(eventNames.employeeEdited);
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
