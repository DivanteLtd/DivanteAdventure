<template>
  <div>
    <v-row no-gutters wrap class="ma-0 justify-center">
      <v-col cols="6" sm="4" class="d-flex"
             :class="{
               'justify-center': $vuetify.breakpoint.smAndUp,
               'ml-1': $vuetify.breakpoint.xs
             }"
      >
        <v-checkbox v-model="showPotential" :label="$t('potential', [ potentialEmployees.length ])"/>
      </v-col>
      <v-col cols="6" sm="4" class="d-flex"
             :class="{
               'justify-center': $vuetify.breakpoint.smAndUp,
               'ml-1': $vuetify.breakpoint.xs
             }"
      >
        <v-checkbox v-model="showAccepted" :label="$t('accepted', [ acceptedEmployees.length ])"/>
      </v-col>
      <v-col cols="6" sm="4" class="d-flex"
             :class="{
               'justify-center': $vuetify.breakpoint.smAndUp,
               'ml-1': $vuetify.breakpoint.xs
             }"
      >
        <v-checkbox v-model="showRejected" :label="$t('rejected', [ rejectedEmployees.length ])"/>
      </v-col>
    </v-row>
    <v-data-table mobile-breakpoint="0"
                  :items-per-page="5"
                  :no-data-text="loading || internalLoading ? $t('Loading data...') : $t('No data available.')"
                  :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                  :no-results-text="$t('No results found.')"
                  :items="selectedEmployees"
                  :loading="loading || internalLoading"
                  :headers="headers"
                  must-sort>
      <template v-slot:item="{ item }">
        <potential-person-row :person="item"
                              @loading-start="internalLoading = true"
                              @loading-end="internalLoading = false"/>
      </template>
    </v-data-table>
  </div>
</template>

<script>
  import PotentialPersonRow from './PotentialPersonRow';
  import { getSuggestedLanguage } from '../../../i18n/i18n';

  const STATUS_POTENTIAL_EMPLOYEE = 0;
  const STATUS_ACCEPTED = 1;
  const STATUS_REJECTED = 2;

  export default {
    name: 'PotentialPeopleTable',
    components: { PotentialPersonRow },
    props: {
      loading: { type: Boolean, default: false },
      employees: { type: Array, required: true },
    },
    data() {
      return {
        language: getSuggestedLanguage(),
        showPotential: true,
        showAccepted: true,
        showRejected: true,
        internalLoading: false,
        headers: [{
          text: this.$t('Person'),
          value: 'lastName',
        }, {
          text: this.$t('Status'),
          value: 'status',
        }, {
          text: this.$t('Proposed e-mail address'),
          value: 'email',
        }, {
          text: this.$t('Planned tribe'),
          value: 'tribe',
          sortable: false,
        }, {
          text: this.$t('Planned position'),
          value: 'position',
          sortable: false,
        }, {
          text: this.$t('Planned hire date'),
          value: 'hireDate',
        }, {
          text: this.$t('Contract type'),
          value: 'contractType',
        }, {
          text: this.$t('Actions'),
        }],
      };
    },
    computed: {
      potentialEmployees() {
        return this.employees.filter(employee => employee.status === STATUS_POTENTIAL_EMPLOYEE);
      },
      acceptedEmployees() {
        return this.employees.filter(employee => employee.status === STATUS_ACCEPTED);
      },
      rejectedEmployees() {
        return this.employees.filter(employee => employee.status === STATUS_REJECTED);
      },
      selectedEmployees() {
        return [
          ...(this.showPotential ? this.potentialEmployees : []),
          ...(this.showAccepted ? this.acceptedEmployees : []),
          ...(this.showRejected ? this.rejectedEmployees : []),
        ];
      },
    },
    i18n: {
      messages: {
        pl: {
          'Loading data...': 'Wczytywanie...',
          'No data available.': 'Brak danych.',
          'No results found.': 'Nie znaleziono.',
          'Rows per page:': 'Wierszy na stronę:',
          'All': 'Wszystkie',

          'Person': 'Osoba',
          'Status': 'Status',
          'Proposed e-mail address': 'Proponowany adres e-mail',
          'Planned tribe': 'Planowana praktyka',
          'Planned position': 'Planowane stanowisko',
          'Planned hire date': 'Planowana data przyjęcia',
          'Contract type': 'Typ kontraktu',
          'Actions': 'Akcje',

          'potential': 'Potencjalne ({0})',
          'accepted': 'Zaakceptowane ({0})',
          'rejected': 'Odrzucone ({0})',
        },
        en: {
          'potential': 'Potential ({0})',
          'accepted': 'Accepted ({0})',
          'rejected': 'Rejected ({0})',
          'Planned tribe': 'Planned practice',
        },
      },
    },
  };
</script>
