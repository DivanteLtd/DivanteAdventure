<template>
  <v-card-text>
    <v-form v-model="formValid">
      <v-text-field
        class="required"
        v-model="namePl"
        :label="$t('Category name in polish')"
        :rules="[ rules.required ]"
        required
      />
      <v-text-field
        class="required"
        v-model="nameEn"
        :label="$t('Category name in english')"
        :rules="[ rules.required ]"
        required
      />
      <v-row>
        <v-col cols="8">
          <employee-chooser :employees="getEmployees"
                            v-model="employee"
                            :label="$t('Add responsibles')"
                            prepend-icon="supervisor_account"/>
        </v-col>
        <v-col cols="3">
          <v-btn color="success" class="mt-2" :class="{'ml-4': $vuetify.breakpoint.smAndUp}"
                 :disabled="!employee" @click="addEmployee" block :loading="loading">
            {{ $t('Add') }}
          </v-btn>
        </v-col>
      </v-row>
      <span v-if="responsibleEmployees.length" class="body-1">{{ $t('Responsibles for category:') }}</span>
      <div v-for="(responsibleEmployee, key) in responsibleEmployees" :key="key">
        <employee-chip v-if="typeof(responsibleEmployee) === 'object'" :employee="responsibleEmployee"/>
        <v-btn @click="deleteEmployee(responsibleEmployee)" icon>
          <v-icon class="icon" color="error">highlight_off</v-icon>
        </v-btn>
      </div>
      <v-card-actions>
        <v-spacer/>
        <v-btn class="mt-2" text @click="$emit('close')" :loading="loading">
          {{ $t('Cancel') }}
        </v-btn>
        <v-btn class="mt-2" text color="primary" :disabled="!formValid || responsibleEmployees.length === 0"
               @click="save" :loading="loading">
          {{ $t('Save') }}
        </v-btn>
      </v-card-actions>
    </v-form>
  </v-card-text>
</template>

<script>
  import { mapState } from 'vuex';
  import EmployeeChooser from '../utils/EmployeeChooser';
  import EmployeeChip from '../utils/EmployeeChip';
  import { EventBus, eventNames } from '../../eventbus';

  export default {
    name: 'FAQCategoryFormContent',
    components: { EmployeeChooser, EmployeeChip },
    props: {
      category: { type: Object, required: true },
    },
    data() {
      return {
        employee: null,
        responsibleEmployees: this.category.responsibles ? this.category.responsibles : [],
        filteredEmployees: [],
        nameEn: this.category.nameEn ? this.category.nameEn : '',
        namePl: this.category.namePl ? this.category.namePl : '',
        id: this.category.id ? this.category.id : -1,
        formValid: false,
        loading: false,
        rules: {
          required: val => !!val || this.$t('Field is required'),
        },
      };
    },
    computed: {
      ...mapState({
        employees: state => state.Employees.employees,
      }),
      getEmployees() {
        if (this.category.id && this.filteredEmployees.length === 0) {
          this.filteredEmployees = this.employees;
          this.category.responsibles.forEach(employee => {
            this.filteredEmployees = this.filteredEmployees.filter(val => val.id !== employee.id);
          });
          return this.filteredEmployees;
        }
        return this.responsibleEmployees.length === 0 ? this.employees : this.filteredEmployees;
      },
    },
    methods: {
      deleteEmployee(employee) {
        this.responsibleEmployees = this.responsibleEmployees.filter(val => val !== employee);
        this.filteredEmployees.push(employee);
      },
      addEmployee() {
        this.filteredEmployees = this.responsibleEmployees.length === 0
          ? this.employees.filter(val => val !== this.employee)
          : this.filteredEmployees.filter(val => val !== this.employee);
        this.responsibleEmployees.push(this.employee);
        this.employee = null;
      },
      async save() {
        this.loading = true;
        const data = {
          employees: this.responsibleEmployees.map(val => val.id),
          namePl: this.namePl,
          nameEn: this.nameEn,
        };
        if (this.id === -1) {
          await this.$store.dispatch('FAQ/newFAQCategory', data);
        } else {
          data.id = this.id;
          await this.$store.dispatch('FAQ/updateFAQCategory', data);
        }
        this.loading = false;
        this.$store.commit('showSnackbar', { color: 'success', text: this.$t('FAQ category has been created') });
        EventBus.$emit(eventNames.reloadFAQContent);
        this.$emit('close');
      },
    },
    i18n: {
      messages: {
        pl: {
          'Category name in polish': 'Nazwa kategorii po polsku',
          'Category name in english': 'Nazwa kategorii po angielsku',
          'Responsibles for category:': 'Odpowiedzialni za kategorię:',
          'Add responsibles': 'Dodaj odpowiedzialnych',
          'Category': 'Kategoria',
          'Add': 'Dodaj',
          'Cancel': 'Anuluj',
          'Save': 'Zapisz',
          'FAQ category has been created': 'Kategoria FAQ została dodana',
          'Field is required': 'Pole jest wymagane',
        },
      },
    },
  };
</script>
<style scoped>
  .icon {
    vertical-align: text-top;
  }
</style>
