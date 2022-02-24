<template>
  <v-dialog v-model="dialog" max-width="600px">
    <v-card>
      <v-card-title>
        <span class="headline">{{ $t('Assign person') }}</span>
      </v-card-title>
      <v-card-text>
        <employee-chooser v-model="employee"
                          :employees="filterEmployees()"
                          :label="$t('Person')"
                          prepend-icon="supervisor_account"/>
        <v-autocomplete :no-data-text="$t('No positions available')"
                        v-if="employee !== null"
                        :label="$t('Position')"
                        :items="tribe.positions"
                        prepend-icon="work"
                        item-value="id"
                        item-text="name"
                        v-model="position"/>
        <v-autocomplete :no-data-text="$t('No levels available')"
                        v-if="levels.length > 0"
                        :label="$t('Level')"
                        :items="levels"
                        prepend-icon="reorder"
                        item-value="id"
                        item-text="name"
                        v-model="level"/>
      </v-card-text>
      <v-card-actions>
        <v-spacer/>
        <v-btn color="blue darken-1"
               :disabled="formInvalid"
               :loading="loading"
               @click="assign"
               text>
          {{ $t('Save') }}
        </v-btn>
        <v-btn text @click="dialog = false">{{ $t('Close') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import EmployeeChooser from '../../utils/EmployeeChooser';
  import { EventBus, eventNames } from '../../../eventbus';
  import { mapState } from 'vuex';

  export default {
    name: 'TribeAssignDialog',
    components: { EmployeeChooser },
    data() {
      return {
        employee: null,
        position: null,
        level: null,
        loading: false,
        dialog: false,
        tribe: {
          id: '',
          name: '',
          positions: [],
        },
      };
    },
    computed: {
      ...mapState({
        allEmployees: state => state.Employees.employees,
      }),
      formInvalid() {
        return (this.employee === null) || (this.position === null) || (this.level === null && this.levels.length > 0);
      },
      levels() {
        if (this.position === null) {
          return [];
        } else {
          return this.tribe.positions
            .filter(position => position.id === this.position)
            .map(position => position.strategy.levels)
            .reduce((a, b) => a.concat(b), [])
            .sort((a, b) => a.priority - b.priority);
        }
      },
    },
    methods: {
      async assign() {
        this.loading = true;
        try {
          await this.$store.dispatch('Employees/assignToTribe', {
            idEmployee: this.employee.id,
            idTribe: this.tribe.id,
          });
          const positionAssignData = {
            id: this.employee.id,
            position: {
              id: this.position,
            },
          };
          if (this.level !== null) {
            positionAssignData.level = {
              id: this.level,
            };
          }
          await this.$store.dispatch('Employees/saveEmployee', positionAssignData);

          this.$store.commit('showSnackbar', {
            text: this.$t('Person has been added to the tribe'),
            color: 'success',
          });
          EventBus.$emit(eventNames.refreshTribeWindow);
          this.tribe.employees.push(this.employee);
          this.dialog = false;
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Person cannot be added to the tribe'),
            color: 'error',
          });
          this.loading = false;
        }
        return this.filterEmployees();
      },
      filterEmployees() {
        if (this.tribe.employees && this.tribe.employees.length > 0) {
          return this.allEmployees.filter(employee => !this.tribe.employees.includes(employee));
        } else {
          return this.allEmployees;
        }
      },
      show(tribe) {
        if (this.dialog) {
          return;
        }
        this.tribe = tribe;
        this.employee = null;
        this.position = null;
        this.level = null;
        this.loading = false;
        this.dialog = true;
      },
    },
    mounted() {
      EventBus.$on(eventNames.showEmployeeAssignToTribeWindow, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Assign person': 'Przypisz osobę',
          'Close': 'Zamknij',
          'Save': 'Zapisz',
          'Person': 'Osoba',
          'Position': 'Stanowisko',
          'Level': 'Poziom',
          'No positions available': 'Brak dostępnych stanowisk',
          'No levels available': 'Brak dostępnych poziomów',
          'Person has been added to the tribe': 'Osoba została dodana do plemienia',
          'Person cannot be added to the tribe': 'Nie udało się dodać osoby do plemienia',
        },
      },
    },
  };
</script>
