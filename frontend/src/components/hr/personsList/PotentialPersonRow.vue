<template>
  <tr>
    <td @click="showDetails" class="pointer">{{ person.name }} {{ person.lastName }}</td>
    <td @click="showDetails" class="pointer"><potential-person-status :status="person.status"/></td>
    <td @click="showDetails" class="pointer">{{ person.email }}</td>
    <td @click="showDetails" class="pointer" v-if="person.tribe">{{ person.tribe.name }}</td>
    <td @click="showDetails" class="pointer" v-else></td>
    <td @click="showDetails" class="pointer" v-if="person.position">{{ person.position.name }}</td>
    <td @click="showDetails" class="pointer" v-else></td>
    <td @click="showDetails" class="pointer">{{ hireDate }}</td>
    <td @click="showDetails" class="pointer">{{ $t(`${person.contractType}`) }}</td>
    <td v-if="person.status === STATUS_POTENTIAL_EMPLOYEE">
      <v-tooltip top>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="editPotential" icon><v-icon>edit</v-icon></v-btn>
        </template>
        {{ $t('Edit') }}
      </v-tooltip>
      <v-tooltip top>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="profileFulfillment" icon>
            <v-icon color="success">check</v-icon>
          </v-btn>
        </template>
        {{ $t('Mark as accepted') }}
      </v-tooltip>
      <v-tooltip top>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="markAsRejectedVisible = true" icon>
            <v-icon color="error">close</v-icon>
          </v-btn>
        </template>
        {{ $t('Mark as rejected') }}
      </v-tooltip>
      <confirm-dialog
        v-model="markAsAcceptedVisible"
        v-if="markAsAcceptedVisible"
        @yes="markAsAccepted"
        :question="$t('accepted-question', person)"
        yes-color="success"/>
      <confirm-dialog
        v-model="markAsRejectedVisible"
        v-if="markAsRejectedVisible"
        @yes="markAsRejected"
        :question="$t('rejected-question', person)"
        yes-color="error"/>
    </td>
    <td v-else-if="person.status === STATUS_REJECTED">
      <v-tooltip top>
        <template v-slot:activator="{ on }">
          <v-btn v-on="on" @click="deletePerson = true" icon>
            <v-icon>delete</v-icon>
          </v-btn>
        </template>
        {{ $t('Delete') }}
      </v-tooltip>
      <confirm-dialog
        v-model="deletePerson"
        v-if="deletePerson"
        @yes="deletePotentialPerson"
        :question="$t('delete-question', person)"
        yes-color="error"/>
    </td>
    <td v-else></td>
  </tr>
</template>

<script>
  import moment from '@divante-adventure/work-moment';
  import PotentialPersonStatus from './PotentialPersonStatus';
  import ConfirmDialog from '../../utils/ConfirmDialog';
  import { EventBus, eventNames } from '../../../eventbus';

  const STATUS_POTENTIAL_EMPLOYEE = 0;
  const STATUS_REJECTED = 2;
  export default {
    name: 'PotentialPersonRow',
    components: { ConfirmDialog, PotentialPersonStatus },
    props: {
      person: { type: Object, required: true },
    },
    data() {
      return {
        STATUS_REJECTED,
        STATUS_POTENTIAL_EMPLOYEE,
        markAsAcceptedVisible: false,
        markAsRejectedVisible: false,
        deletePerson: false,
      };
    },
    computed: {
      hireDate() {
        if (this.person.hireDate) {
          return moment(this.person.hireDate, 'YYYY-MM-DD').format('D MMM YYYY');
        }
        return '';
      },
    },
    methods: {
      showDetails() {
        this.person.details = true;
        return EventBus.$emit(eventNames.showPotentialEmployeeEditor, this.person);
      },
      deletePotentialPerson() {
        this.$emit('loading-start');
        this.$store.dispatch('Hr/deletePotentialEmployee', this.person.id);
        this.$emit('loading-end');
        this.$store.commit('showSnackbar', { text: this.$t('snackbar_deleted'), color: 'success' });
      },
      profileFulfillment() {
        if (!this.person.tribe || !this.person.hireDate || !this.person.city || !this.person.postalCode
          || !this.person.street || !this.person.country || !this.person.welcomeDay) {
          this.$store.commit('showSnackbar', { text: this.$t('snackbar-fulfillment'), color: 'red' });
        } else {
          this.markAsAcceptedVisible = true;
        }
      },
      editPotential() {
        this.person.details = false;
        return EventBus.$emit(eventNames.showPotentialEmployeeEditor, this.person);
      },
      async markAsAccepted() {
        const obj = { ...this.person };
        obj.status = 1;
        obj.recruiter = obj.recruiter.id;
        this.$emit('loading-start');
        try {
          await this.$store.dispatch('Hr/updatePotentialEmployee', obj);
          this.$store.commit('showSnackbar', {
            text: this.$t('Potential person has been accepted'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Potential person can not be accepted'),
            color: 'error',
          });
        }
        this.$emit('loading-end');
      },
      async markAsRejected() {
        const obj = { ...this.person };
        obj.status = 2;
        obj.recruiter = obj.recruiter.id;
        this.$emit('loading-start');
        try {
          await this.$store.dispatch('Hr/updatePotentialEmployee', obj);
          this.$store.commit('showSnackbar', {
            text: this.$t('Potential person has been rejected'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Potential person can not be rejected'),
            color: 'error',
          });
        }
        this.$emit('loading-end');
      },
    },
    mounted() {
      this.markAsAcceptedVisible = false;
      this.markAsRejectedVisible = false;
      this.deletePerson = false;
    },
    i18n: {
      messages: {
        pl: {
          'CoE': 'UoP',
          'CLC LUMP SUM': 'UCP RYCZAŁT',
          'CLC HOURLY': 'UCP GODZINOWE',
          'B2B LUMP SUM': 'B2B RYCZAŁT',
          'B2B HOURLY': 'B2B GODZINOWE',
          'Mark as accepted': 'Oznacz jako zaakceptowane',
          'Mark as rejected': 'Oznacz jako odrzucone',
          'Edit': 'Edytuj',
          'Delete': 'Usuń',
          'Potential person has been accepted': 'Potencjalna osoba została zaakceptowana',
          'Potential person has been rejected': 'Potencjalna osoba została odrzucona',
          'Potential person can not be accepted': 'Potencjalna osoba nie może zostać zaakceptowana',
          'Potential person can not be rejected': 'Potencjalna osoba nie może zostać odrzucona',
          'accepted-question': 'Czy na pewno chcesz oznaczyć osobę {name} {lastName} jako zaakceptowaną?',
          'rejected-question': 'Czy na pewno chcesz oznaczyć osobę {name} {lastName} jako odrzuconą?',
          'delete-question': 'Czy na pewno chcesz usunąć osobę {name} {lastName}?',
          'snackbar_deleted': 'Potencjalna osoba została usunięta',
          'snackbar-fulfillment': 'Proszę edytować profil aby pola plemię/dział, data zatrudnienia, data Welcome Day i pełny adres były uzupełnione',
        },
        en: {
          'snackbar-fulfillment': 'Please edit profile to have tribe/department, hire date, Welcome Day date and full address fields fulfilled',
          'accepted-question': 'Do you really want to mark {name} {lastName} as accepted?',
          'rejected-question': 'Do you really want to mark {name} {lastName} as rejected?',
          'delete-question': 'Do you really want to delete {name} {lastName}?',
          'snackbar_deleted': 'Potential person has been deleted',
        },
      },
    },
  };
</script>
<style scoped>
  .pointer{
    cursor: pointer;
  }
</style>
