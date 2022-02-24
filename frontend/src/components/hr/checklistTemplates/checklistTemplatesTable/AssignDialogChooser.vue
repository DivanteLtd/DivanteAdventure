<template>
  <div>
    <employee-chooser :employees="getResponsible"
                      v-model="responsible"
                      :label="title"
                      prepend-icon="supervisor_account"/>
    <v-btn color="primary"
           class="button mb-3"
           :disabled="!responsible"
           :loading="loading"
           @click="addResponsible"
           block>
      {{ $t('Add') }}
    </v-btn>
    <span v-if="responsibleEmployees.length" class="body-2">{{ subtitle }}</span>
    <div class="d-flex flex-wrap">
      <div v-for="(responsibleEmployee, key) in responsibleEmployees" :key="key" class="my-1 d-flex">
        <employee-chip v-if="responsibleEmployee" :employee="responsibleEmployee"/>
        <v-btn @click="deleteResponsible(responsibleEmployee)" icon>
          <v-icon class="icon" color="error">highlight_off</v-icon>
        </v-btn>
      </div>
    </div>
  </div>
</template>

<script>
  import EmployeeChooser from '../../../utils/EmployeeChooser';
  import EmployeeChip from '../../../utils/EmployeeChip';

  export default {
    name: 'AssignDialogChooser',
    components: { EmployeeChooser, EmployeeChip },
    props: {
      title: { type: String, required: true },
      subtitle: { type: String, required: true },
      employees: { type: Array, required: true },
      departments: { type: Array, required: true },
    },
    data() {
      return {
        loading: false,
        responsible: null,
        responsibleEmployees: [],
        filteredEmployees: [],
      };
    },
    computed: {
      getResponsible() {
        return this.responsibleEmployees.length
          ? this.filteredEmployees
          : [...this.departments, ...this.employees];
      },
    },
    methods: {
      deleteResponsible(responsible) {
        this.responsibleEmployees = this.responsibleEmployees.filter(val => val !== responsible);
        this.filteredEmployees.push(responsible);
        this.$emit('update', [...this.getEmployeesIds(), ...this.getEmployeesIdsByDepartments()]);
      },
      addResponsible() {
        this.filteredEmployees = this.responsibleEmployees.length
          ? this.filteredEmployees.filter(val => val !== this.responsible)
          : this.getResponsible.filter(val => val !== this.responsible);
        this.responsibleEmployees.push(this.responsible);
        this.responsible = null;
        this.$emit('update', [...this.getEmployeesIds(), ...this.getEmployeesIdsByDepartments()]);
      },
      getEmployeesIds() {
        return this.responsibleEmployees.reduce((filtered, responsible) => {
          return !responsible.employeesCount ? [...filtered, responsible.id] : filtered;
        }, []); },
      getDepartmentsIds() {
        return this.responsibleEmployees.reduce((filtered, responsible) => {
          return responsible.employeesCount ? [...filtered, responsible.id] : filtered;
        }, []);
      },
      getEmployeesIdsByDepartments() {
        const departmentsId = this.getDepartmentsIds();
        return this.employees.reduce((filtered, employee) => {
          return employee.tribe && departmentsId.includes(employee.tribe.id) ? [...filtered, employee.id] : filtered;
        }, []);
      },
    },
    i18n: {
      messages: {
        pl: {
          Add: 'Dodaj',
        },
      },
    },
  };
</script>
