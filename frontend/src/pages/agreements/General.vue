<template>
  <v-container
    grid-list-xl fluid
    class="gdpr-table mb-8"
    :class="{'pa-0': $vuetify.breakpoint.xs}"
    id="page-gdpr">
    <v-col cols="12">
      <agreement-info-top :agreement-type="99"/>
    </v-col>
    <v-col class="end">
      <agreement-more-menu v-if="isSuperAdmin"/>
    </v-col>
    <v-col v-if="fireSafetyAgreements.length" :class="{'mb-3': $vuetify.breakpoint.smAndUp}" sm="12">
      <agreement-card v-model="selectedFireSafety"
                      :employee="employee"
                      :agreements="fireSafetyAgreements"
                      :title="$t('Fire safety/OSH consents')"
                      :loading="loading"/>
    </v-col>
    <v-col :class="{'mb-3': $vuetify.breakpoint.smAndUp}" sm="12">
      <agreement-card v-model="selectedGdpr"
                      :employee="employee"
                      :agreements="gdprAgreements"
                      :title="$t('GDPR consents')"
                      :loading="loading"/>
    </v-col>
    <v-col v-if="ISOAgreements.length" :class="{'mb-3': $vuetify.breakpoint.smAndUp}" sm="12">
      <agreement-card v-model="selectedISO"
                      :employee="employee"
                      :agreements="ISOAgreements"
                      :title="$t('ISO consents')"
                      :loading="loading"/>
    </v-col>
    <div class="text-center ma-2">
      <v-btn :disabled="!showSaveButton" @click="accept" color="primary">{{ $t('Accept') }}</v-btn>
    </div>
  </v-container>
</template>

<script>
  import { mapGetters, mapState } from 'vuex';
  import { getSuggestedLanguage } from '../../i18n/i18n';
  import AgreementCard from '../../components/agreements/General/AgreementCard';
  import AgreementMoreMenu from '../../components/agreements/General/AgreementMoreMenu';
  import AgreementInfoTop from '../../components/agreements/General/AgreementInfoTop';
  import { agreementsType } from '../../util/agreements';

  export default {
    name: 'General',
    components: { AgreementCard, AgreementMoreMenu, AgreementInfoTop },
    data() { return {
      loading: false,
      language: getSuggestedLanguage(),
      selectedGdpr: [],
      selectedFireSafety: [],
      selectedISO: [],
    };},
    computed: {
      ...mapState({
        agreements: state => state.Agreements.agreements,
        employee: state => state.Employees.employee,
      }),
      ...mapGetters({
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
      checkIfMobile() {
        return this.$vuetify.breakpoint.xs;
      },
      canView() {
        return this.$store.getters['Authorization/isSuperAdmin'];
      },
      contractAgreements() {
        const contract = this.employee.contract || { id: -1 };
        const contractId = contract.id.toString();
        return this.agreements.filter(agreement => agreement.contracts.indexOf(contractId) >= 0);
      },
      gdprAgreements() {
        return this.contractAgreements.filter(agreement => agreement.type === agreementsType.TYPE_GDPR);
      },
      fireSafetyAgreements() {
        return this.contractAgreements.filter(agreement => agreement.type === agreementsType.TYPE_FIRE_SAFETY);
      },
      ISOAgreements() {
        return this.contractAgreements.filter(agreement => agreement.type === agreementsType.TYPE_ISO);
      },
      selected() {
        return [ ...this.selectedGdpr, ...this.selectedFireSafety, ...this.selectedISO ];
      },
      showSaveButton() {
        return this.selected.length > 0 && this.contractAgreements.filter(element => element.accepted === 0).length > 0;
      },
    },
    methods: {
      async accept() {
        const type = this.employee.contract.name === 'CoE' ? -1 : agreementsType.TYPE_FIRE_SAFETY;
        const required = this.agreements
          .filter(element => element.accepted === 0 && element.required === 1 && element.type !== type)
          .map(element => element.id);
        const agreementsId = this.selected.map(val => val.id);
        for (const agr of required) {
          if (!agreementsId.includes(agr)) {
            return this.$store.commit('showSnackbar', {
              text: this.$t('Not all required consents have been mark'),
              color: 'error',
            });
          }
        }
        const data = this.selected.map(val => val.id);
        try {
          await this.$store.dispatch('Agreements/newEmployeeAgreements', data);
          this.$store.commit('showSnackbar', {
            text: this.$t('Consents have been accepted'),
            color: 'success',
          });
          await this.$store.dispatch('Employees/loadLoggedEmployee');
          return this.loadData();
        } catch (e) {
          return this.$store.commit('showSnackbar', {
            text: this.$t('Consents have not been accepted'),
            color: 'error',
          });
        }
      },
      async loadData() {
        this.loading = true;
        await this.$store.dispatch('Employees/loadEmployee');
        await this.$store.dispatch('Agreements/loadAgreements');
        await this.$store.dispatch('Config/loadContentConfig');
        this.loading = false;
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
    i18n: {
      messages: {
        pl: {
          'Accept': 'Zaakceptuj',
          'Consents have been accepted': 'Zgody zostały zaakceptowane',
          'Consents have not been accepted': 'Zgody nie zostały zaakceptowane',
          'Not all required consents have been mark': 'Nie zaznaczono wszystkich wymaganych zgód',
          'GDPR consents': 'Zgody RODO',
          'ISO consents': 'Zgody ISO',
          'Fire safety/OSH consents': 'Zgody PPOŻ/BHP',
        },
      },
    },
  };
</script>
<style>
  .gdpr-table table.v-table thead th{
    padding: 0 8px;
  }
  .gdpr-table table.v-table tbody td:not(:first-child){
    padding: 0 8px;
  }
  .gdpr-table .v-datatable .v-input--selection-controls {
    justify-content: center !important;
  }
  .end {
    text-align: end;
  }
</style>
