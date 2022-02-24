<template>
  <v-dialog v-model="dialogVisible" width="1000">
    <v-card class="pa-3">
      <v-form v-model="formValid">
        <v-card-title class="headline pb-0">
          <span>{{ formText }}</span>
        </v-card-title>
        <v-container id="agreement-attachment-form">
          <v-text-field v-model="namePl" :label="$t('Name in polish')" counter="50"
                        :rules="[ rules.required, rules.countSigns ]" class="required"/>
          <v-text-field v-model="nameEn" :label="$t('Name in english')" counter="50"
                        :rules="[ rules.required, rules.countSigns ]" class="required"/>
          <v-text-field v-model="descriptionPl" :label="$t('Description in polish')"
                        :rules="[ rules.required ]" class="required"/>
          <v-text-field v-model="descriptionEn" :label="$t('Description in english')"
                        :rules="[ rules.required ]" class="required"/>
          <v-text-field type="number" v-model="displayOrder" :label="$t('Display order')"/>
          <v-checkbox v-if="!marketingFlag" v-model="isRequired" :label="$t('Is the agreement required')"/>
          <v-card-text v-if="!marketingFlag" class="pa-0 mt-0">
            <v-subheader class="subheading align-center pa-0">{{ $t('Type of agreement*') }}</v-subheader>
          </v-card-text>
          <v-row no-gutters v-if="!marketingFlag" wrap>
            <v-col cols="4" sm="3" md="2">
              <v-checkbox v-model="isGDPR" :label="$t('Is an GDPR')"/>
            </v-col>
            <v-col cols="4" sm="3" md="2">
              <v-checkbox v-model="isFireSafety" :label="$t('Is a fire safety/OSH')"/>
            </v-col>
            <v-col cols="4" sm="3" md="2">
              <v-checkbox v-model="isISO" :label="$t('Is an ISO')"/>
            </v-col>
          </v-row>
          <v-row no-gutters v-if="!marketingFlag" wrap>
            <v-col cols="12">
              <v-subheader class="subheading pa-0 align-center">{{ $t('Displayed for*') }}</v-subheader>
            </v-col>
            <v-col cols="4" sm="3" md="2">
              <v-checkbox :disabled="disable" v-model="B2B" label="B2B"/>
            </v-col>
            <v-col cols="4" sm="3" md="2">
              <v-checkbox :disabled="disable" v-model="CLC" :label="$t('CLC')"/>
            </v-col>
            <v-col cols="4" sm="3" md="2">
              <v-checkbox v-model="CoE" :label="$t('CoE')"/>
            </v-col>
          </v-row>
          <v-card-text v-if="!marketingFlag" class="pa-0 mt-0">
            <v-subheader class="subheading align-center pa-0">{{ $t('Choose attachments*') }}</v-subheader>
          </v-card-text>
          <v-text-field v-model="search"
                        v-if="!marketingFlag"
                        append-icon="search"
                        :label="$t('Search')"
                        single-line/>
          <v-data-table mobile-breakpoint="0"
                        v-if="!marketingFlag"
                        :items-per-page="5"
                        hide-default-header
                        :headers="headers"
                        :items="agreementAttachments"
                        :search="search"
                        :custom-filter="filter"
                        show-select
                        v-model="selected"
          />
        </v-container>
        <v-card-actions>
          <v-spacer/>
          <v-btn text @click="dialogVisible = false">
            {{ $t('Close') }}
          </v-btn>
          <v-btn :disabled="!checkRequired"
                 color="blue" text @click="addEditAgreement">
            {{ actionText }}
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import { mapState } from 'vuex';
  import { contractsName } from '../../util/contracts';
  import { agreementsType } from '../../util/agreements';

  export default {
    name: 'AgreementAttachmentForm',
    data() {
      return {
        singleSelect: false,
        dialogVisible: false,
        formValid: true,
        id: 0,
        headers: [
          {
            align: 'start',
            sortable: false,
            value: 'name',
          },
        ],
        search: '',
        formText: '',
        actionText: '',
        selected: [],
        namePl: '',
        nameEn: '',
        descriptionPl: '',
        descriptionEn: '',
        displayOrder: 1,
        isRequired: false,
        isGDPR: false,
        isFireSafety: false,
        isISO: false,
        marketingFlag: false,
        B2B: false,
        CLC: false,
        CoE: false,
        disable: false,
        rules: {
          required: v => !!v || this.$t('This field is required'),
          countSigns: value => value.length <= 50 || this.$t('Name is too long, max 50 signs'),
        },
      };
    },
    computed: {
      ...mapState({
        agreementAttachments: state => state.Agreements.attachments,
      }),
      checkRequired() {
        if (this.marketingFlag) {
          return this.namePl !== '' && this.nameEn !== '' && this.descriptionPl !== '' && this.descriptionEn !== '';
        }
        return this.formValid
          && (this.B2B === true || this.CLC === true || this.CoE === true)
          && this.selected.length
          && (this.isFireSafety || this.isISO || this.isGDPR);
      },
    },
    watch: {
      isGDPR() {
        if (this.isGDPR) {
          this.isFireSafety = false;
          this.isISO = false;
          this.disable = false;
        }
      },
      isFireSafety() {
        if (this.isFireSafety) {
          this.disable = true;
          this.CLC = false;
          this.B2B = false;
          this.isGDPR = false;
          this.isISO = false;
        }
      },
      isISO() {
        if (this.isISO) {
          this.disable = false;
          this.isFireSafety = false;
          this.isGDPR = false;
        }
      },
    },
    methods: {
      async show(item) {
        this.search = '';
        this.B2B = false;
        this.CLC = false;
        this.CoE = false;
        this.selected = [];
        this.marketingFlag = false;
        this.marketingFlag = false;
        this.isISO = false;
        this.isFireSafety = false;
        this.isGDPR = false;
        if (item === undefined || item === true) {
          this.actionText = this.$t('Add');
          this.formText = this.$t('Add agreement');
          this.namePl = '';
          this.nameEn = '';
          this.descriptionPl = '';
          this.descriptionEn = '';
          this.displayOrder = 1;
          this.isRequired = false;
          this.dialogVisible = true;
          if (item === true) {
            this.marketingFlag = item;
          } else {
            this.$store.dispatch('Agreements/loadAgreementAttachments');
          }
        } else {
          this.selected = [];
          this.actionText = '';
          this.formText = '';
          if (Number(item.type) === agreementsType.TYPE_MARKETING) {
            this.marketingFlag = true;
            this.actionText = this.$t('Save');
            this.formText = this.$t('Edit agreement');
            this.id = item.id;
            this.namePl = item.agreementNamePl;
            this.nameEn = item.agreementNameEn;
            this.descriptionPl = item.descriptionPl;
            this.descriptionEn = item.descriptionEn;
            this.displayOrder = item.priority;
            this.dialogVisible = true;
          } else {
            await this.$store.dispatch('Agreements/loadAgreementAttachments');
            item.attachmentsId.forEach(value => {
              this.agreementAttachments.forEach(val => {
                if (val.id === Number(value)) {
                  this.selected.push(val);
                }
              });
            });
            this.actionText = this.$t('Save');
            this.formText = this.$t('Edit agreement');
            this.id = item.id;
            this.namePl = item.agreementNamePl;
            this.nameEn = item.agreementNameEn;
            this.descriptionPl = item.descriptionPl;
            this.descriptionEn = item.descriptionEn;
            this.isGDPR = item.type === 0;
            this.isFireSafety = item.type === 2;
            this.isISO = item.type === 3;
            this.displayOrder = item.priority;
            this.isRequired = item.required;
            this.B2B = item.contracts.includes(contractsName.B2B_LUMP_SUM.id || contractsName.B2B_HOURLY.id);
            this.CLC = item.contracts.includes(contractsName.CLC_LUMP_SUM.id || contractsName.CLC_HOURLY.id);
            this.CoE = item.contracts.includes(contractsName.CoE.id);
            this.dialogVisible = true;
          }
        }
      },
      filter(value, search, item) {
        const searchLower = search.toLowerCase().split(/[ ,.;]+/);
        const entry = item.name.replace(/\s/g, '').toLowerCase();
        return searchLower.map(key => entry.includes(key)).reduce((a, b) => a && b, true);
      },
      async addEditAgreement() {
        const assignContracts = [];
        if (this.B2B) {
          assignContracts.push(1);
          assignContracts.push(2);
        }
        if (this.CLC) {
          assignContracts.push(3);
          assignContracts.push(4);
        }
        if (this.CoE) {
          assignContracts.push(5);
        }
        let type = agreementsType.TYPE_GDPR;
        if (this.marketingFlag) {
          type = agreementsType.TYPE_MARKETING;
        }
        if (this.isFireSafety) {
          type = agreementsType.TYPE_FIRE_SAFETY;
        }
        if (this.isISO) {
          type = agreementsType.TYPE_ISO;
        }
        const data = {
          namePl: this.namePl,
          nameEn: this.nameEn,
          descriptionPl: this.descriptionPl,
          descriptionEn: this.descriptionEn,
          displayOrder: this.displayOrder,
          isRequired: this.isRequired ? 1 : 0,
          contracts: assignContracts,
          attachments: this.selected.map(element => element.id),
          type,
        };
        if (this.actionText !== this.$t('Save')) {
          try {
            await this.$store.dispatch('Agreements/addAgreement', data);
            this.$store.commit('showSnackbar', {
              text: this.$t('Agreement has been added'),
              color: 'success',
            });
            this.dialogVisible = false;
          } catch (e) {
            this.$store.commit('showSnackbar', {
              text: this.$t('Agreement can not be added'),
              color: 'error',
            });
          }
        } else {
          data.id = this.id;
          try {
            await this.$store.dispatch('Agreements/editAgreement', data);
            this.$store.commit('showSnackbar', {
              text: this.$t('Agreement has been edited'),
              color: 'success',
            });
            this.dialogVisible = false;
          } catch (e) {
            this.$store.commit('showSnackbar', {
              text: this.$t('Agreement can not be edited'),
              color: 'error',
            });
          }
        }
      },
    },
    mounted() {
      EventBus.$on(eventNames.agreementAttachmentForm, this.show);
      EventBus.$on(eventNames.agreementAttachmentEdit, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'CLC': 'UCP',
          'CoE': 'UoP',
          'Name in polish': 'Nazwa po polsku',
          'Name in english': 'Nazwa po angielsku',
          'Add agreement': 'Dodaj zgodę',
          'Edit agreement': 'Edytuj zgodę',
          'Close': 'Zamknij',
          'Search': 'Szukaj',
          'Choose attachments*': 'Wybierz załączniki*',
          'Name': 'Nazwa',
          'Rows per page:': 'Wierszy na stronę:',
          'Displayed for*': 'Wyświetlane dla*',
          'Display order': 'Kolejność wyświetlania',
          'Description in polish': 'Treść zgody po polsku',
          'Description in english': 'Treść zgody po angielsku',
          'Is the agreement required': 'Czy zgoda jest wymagana?',
          'Is a fire safety/OSH': 'Zgoda PPoż/BHP',
          'Is an ISO': 'Zgoda ISO',
          'Is an GDPR': 'Zgoda RODO',
          'Add': 'Dodaj',
          'Save': 'Zapisz',
          'All': 'Wszystkie',
          'Agreement has been edited': 'Zgoda została zmieniona',
          'Agreement can not be edited': 'Zgoda nie została zmieniona',
          'Agreement has been added': 'Zgoda została dodana',
          'Agreement can not be added': 'Zgoda nie została dodana',
          'Type of agreement*': 'Typ zgody*',
          'This field is required': 'To pole jest wymagane',
          'Name is too long, max 50 signs': 'Nazwa jest za długa, max 50 znaków',
        },
      },
    },
  };
</script>
<style scoped>
  .required >>> label::after {
    content: "*";
  }
</style>
<style>
  #agreement-attachment-form td:first-child {
    width: 24px !important;
    margin: 0 !important;
  }
</style>
