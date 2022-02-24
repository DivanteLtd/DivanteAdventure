<template>
  <v-dialog v-model="dialogVisible" width="400" persistent>
    <v-card>
      <v-card-title class="headline">
        <span>{{ $t('Add contract') }}</span>
      </v-card-title>
      <v-divider/>
      <v-card-text class="pt-0 pb-0">
        <v-select
          v-model="type"
          :hint="`${type.name}`"
          :items="contractsType"
          item-text="name"
          item-value="id"
          :label="$t('Contract type')"
          persistent-hint
          return-object
          single-line
          class="required"
          :error="!isSelectValid"
          :error-messages="errorMessages"
        ></v-select>
        <v-menu
          v-model="menu1"
          :close-on-content-click="false"
          :nudge-right="40"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="dateStart"
              :label="$t('Start contract date')"
              prepend-icon="mdi-calendar"
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="dateStart"
            @input="menu1 = false"
          ></v-date-picker>
        </v-menu>
        <v-menu
          v-model="menu2"
          :close-on-content-click="false"
          :nudge-right="40"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="dateEnd"
              :label="$t('End contract date')"
              prepend-icon="mdi-calendar"
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="dateEnd"
            @input="menu2 = false"
          ></v-date-picker>
        </v-menu>
        <v-switch
          v-model="lawRegulation"
          :label="$t('Compliant with labour law regulation')"
        ></v-switch>
        <v-text-field v-if="!lawRegulation"
                      v-model="noticePeriod"
                      :value="noticePeriod"
                      :label="$t('Notice period')"
        />
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue" @click="save()" :disabled="!isSelectValid" text>
          {{ $t('Save') }}
        </v-btn>
        <v-btn text @click="dialogVisible = false">
          {{ $t('Close') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import { mapState } from 'vuex';

  export default {
    name: 'AddContract',
    data() {
      return {
        lawRegulation: false,
        id: 0,
        employeeId: 0,
        contractTypeId: 0,
        dialogVisible: false,
        editFlag: false,
        dateStart: new Date().toISOString().substr(0, 10),
        dateEnd: new Date().toISOString().substr(0, 10),
        menu1: false,
        menu2: false,
        menu3: false,
        noticePeriod: 0,
        type: {},
        isSelectValid: false,
        errorMessages: this.$t('Contract type is empty'),
      };
    },
    computed: {
      dateAssign() {
        return this.dateStart;
      },

      ...mapState({
        contractsType: state => state.ContractsType.contractsType,
        employee: state => state.Employees.employee,
      }),
    },
    watch: {
      dialogVisible() {
        if (this.dialogVisible === false) {
          this.lawRegulation = false;
        }
      },
      type(n) {
        if (n.hasOwnProperty('id')) {
          this.isSelectValid = true;
          this.errorMessages = [];
        } else {
          this.isSelectValid = false;
          this.errorMessages = this.$t('Contract type is empty');
        }
      },
    },
    methods: {
      async save() {
        const data = {
          id: this.id,
          employee_id: this.employee.id,
          contract_type_id: this.type.id,
          date_star: this.dateStart,
          date_end: this.dateEnd,
          date_assign: this.dateAssign,
          notice_period: this.noticePeriod,
        };
        if (this.lawRegulation === true) {
          data.notice_period = null;
        }
        if (this.id === 0) {
          await this.$store.dispatch('Employees/addContract', data);
        } else {
          await this.$store.dispatch('Employees/editContract', data);
        }
        this.$store.dispatch('Employees/getContracts', this.employee.id);
        this.dialogVisible = false;
      },
      show(data) {
        this.dialogVisible = true;
        this.editFlag = false;
        if (data) {
          this.editFlag = true;
          this.id = data.id;
          this.employeeId = data.employeeId;
          this.type = { id: data.contractTypeId, name: data.contractType };
          this.contractTypeId = data.contractTypeId;
          this.dateStart = data.startDate;
          this.dateEnd = data.endDate;
          this.dateAssign = data.assignDate;
          this.noticePeriod = data.noticePeriod;
          if (data.hasOwnProperty('noticePeriod') === false) {
            this.lawRegulation = true;
          }
        }
      },
    },
    created() {
      EventBus.$on(eventNames.addContract, this.show);
      EventBus.$on(eventNames.editContract, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Add contract': 'Dodaj umowę',
          'Contract type': 'Typ umowy',
          'Start contract date': 'Data początku umowy',
          'Date of signing the contract': 'Data podpisu umowy',
          'End contract date': 'Data końca umowy',
          'Notice period': 'Okres wypowiedzenia',
          'Save': 'Zapisz',
          'Close': 'Zamknj',
          'Compliant with labour law regulation': 'Zgodny z przepisami prawa pracy',
        },
      },
    },
  };
</script>
