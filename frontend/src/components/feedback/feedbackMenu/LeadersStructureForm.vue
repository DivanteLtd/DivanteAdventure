<template>
  <div class="pt-3">
    <v-btn class="button" color="primary" @click="structureDialog()" block>
      {{ $t('Add structures') }}
    </v-btn>
    <v-dialog v-if="structureForm" v-model="structureForm" width="800">
      <v-card>
        <v-card-title>
          <span class="headline">{{ $t('Leaders structure form') }}</span>
        </v-card-title>
        <v-divider/>
        <v-card-text>
          <span class="subtitle-1">{{ $t('Leader') }}</span>
          <employee-chooser :employees="allEmployees"
                            v-model="leader"
                            :label="$t('Choose a leader')"
                            class="pt-5"
                            prepend-icon="supervisor_account"/>
          <span v-if="padawans.length === 1" class="subtitle-1">
            {{ $t('Person in leader structure') }}
          </span>
          <span v-if="padawans.length > 1" class="subtitle-1">
            {{ $t('Persons in leader structure') }}
          </span>
          <v-list-item v-for="(item, index) in padawans" :key="index">
            <v-list-item-title>
              <employee-chip v-if="typeof(item) === 'object'" :employee="item" />
            </v-list-item-title>
          </v-list-item>
          <span v-if="!padawans.length" class="subtitle-1">
            {{ $t('Add a person to leader structure') }}
          </span>
          <span v-if="padawans.length" class="subtitle-1">
            {{ $t('Add another person to leader structure or click save') }}
          </span>
          <employee-chooser :employees="filteredEmployees"
                            v-model="padawan"
                            :label="$t('Choose a person to leaders structure and click add')"
                            class="pt-5"
                            prepend-icon="supervisor_account"/>
          <v-btn color="primary" class="button" :disabled="!padawan || leader.id === padawan.id || !padawan.name"
                 @click="addPadawan" block>
            {{ $t('Add') }}
          </v-btn>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="black" text @click="structureForm = false">{{ $t('Close') }}</v-btn>
          <v-btn :disabled="!padawans.length || !leader.id" color="blue" text @click="save">
            {{ $t('Save') }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
  import EmployeeChooser from '../../utils/EmployeeChooser';
  import EmployeeChip from '../../utils/EmployeeChip';
  import { mapState } from 'vuex';

  export default {
    name: 'LeadersStructureForm',
    components: { EmployeeChooser, EmployeeChip },
    data() {
      return {
        padawans: [],
        padawan: {},
        leader: {},
        employees: [],
        structureForm: false,
      };
    },
    computed: {
      ...mapState({
        allEmployees: state => state.Employees.employees,
      }),
      filteredEmployees() {
        if (this.employees.length === 0) {
          this.employees = this.allEmployees;
          this.padawans.forEach(employee => {
            this.employees = this.employees.filter(val => val.id !== employee.id);
          });
          return this.employees;
        }
        return this.padawans.length === 0 ? this.allEmployees : this.employees;
      },
    },
    methods: {
      structureDialog() {
        this.padawans = [];
        this.padawan = {};
        this.leader = {};
        this.employees = [];
        this.structureForm = true;
      },
      addPadawan() {
        this.employees = this.padawans.length === 0
          ? this.allEmployees.filter(val => val !== this.padawan)
          : this.employees.filter(val => val !== this.padawan);
        this.padawans.push(this.padawan);
        this.padawan = {};
      },
      async save() {
        const structure = {
          leader: this.leader.id,
          leaderStructure: this.padawans.map(val => val.id),
        };
        try {
          await this.$store.state.apiClient.employee.addLeaderStructure(structure);
          this.$store.commit('showSnackbar', {
            text: this.$t('Leaders structure has been added'),
            color: 'success',
          });
          this.dialogVisible = false;
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Leaders structure can not be added. Please contact with Adventure Team'),
            color: 'error',
          });
        }
        await this.$store.dispatch('Employees/loadLeaderStructures');
        this.structureForm = false;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Leaders structure form': 'Formularz struktury lidera',
          'Leader': 'Lider',
          'Choose a leader': 'Wybierz lidera',
          'Add': 'Dodaj',
          'Save': 'Zapisz',
          'Add structures': 'Dodaj strukturę',
          'Close': 'Zamknij',
          'Choose a person to leaders structure and click add': 'Wybierz osobę do struktury lidera i naciśnij Dodaj',
          'Add a person to leader structure': 'Dodaj osobę do struktury lidera',
          'Add another person to leader structure or click save': 'Dodaj następną osobę do struktury lidera',
          'Person in leader structure': 'Osoba w strukturze lidera',
          'Persons in leader structure': 'Osoby w strukturze lidera',
          'Leaders structure has been added': 'Struktura lidera została dodana',
          'Leaders structure can not be added. Please contact with Adventure Team': 'Struktura lidera nie może zostać dodana. Proszę o kontakt z zespołem Adventure',
        },
      },
    },
  };
</script>
