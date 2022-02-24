<template>
  <v-card id="marketing-consents">
    <v-app-bar flat dense color="transparent">
      <v-toolbar-title>{{ $t('Marketing consents') }}</v-toolbar-title>
      <v-spacer/>
      <marketing-more-menu v-if="canView"/>
    </v-app-bar>
    <v-divider/>
    <v-card-text class="pa-0">
      <v-data-table mobile-breakpoint="0" :items-per-page="5" :items="marketingConsents"
                    :headers="headers"
                    v-model="selected"
                    :no-data-text="loading ? $t('Loading data...') : $t('No data available')"
                    :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                    show-select hide-default-footer>
        <template v-slot:item="{ item, isSelected, select }">
          <tr>
            <td>
              <v-checkbox class="ma-0" v-model="isSelected" hide-details @change="select"/>
            </td>
            <td v-if="language === 'pl'" class="description">
              {{ item.descriptionPl }}
            </td>
            <td v-if="language === 'en'" class="description">
              {{ item.descriptionEn }}
            </td>
          </tr>
        </template>
      </v-data-table>
      <v-alert :value="true" type="warning">
        <div v-html="$t('message')" class="marketing-consents__message">
          {{ $t('message') }}
        </div>
      </v-alert>
      <v-data-table mobile-breakpoint="0" :items-per-page="5" :items="noApproval"
                    v-model="selectedOff"
                    :no-data-text="loading ? $t('Loading data...') : $t('No data available')"
                    :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                    hide-default-footer>
        <template v-slot:item="{ item }">
          <tr>
            <td style="width: 64px">
              <v-checkbox class="ma-0 pa-0" v-model="selectedOff[0]" hide-details @change="showSaveButton"/>
            </td>
            <td v-if="language === 'pl'" class="description">
              {{ item.descriptionPl }}
            </td>
            <td v-if="language === 'en'" class="description">
              {{ item.descriptionEn }}
            </td>
          </tr>
        </template>
      </v-data-table>
    </v-card-text>
    <v-divider/>
    <div class="text-center">
      <v-btn class="ma-2" v-if="saveButton" @click="accept" color="primary">
        {{ $t('Save') }}
      </v-btn>
    </div>
  </v-card>
</template>


<script>
  import { mapState } from 'vuex';
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import MarketingMoreMenu from './MarketingMoreMenu';

  export default {
    name: 'MarketingConsents',
    components: { MarketingMoreMenu },
    data() {
      return {
        loading: false,
        language: getSuggestedLanguage(),
        saveButton: false,
        countSelected: 0,
        selected: [],
        selectedOff: [],
        headers: [{ text: this.$t('Select all consents'), align: 'left', sortable: false }, { text: '', align: 'left', sortable: false, class: 'd-none' }],
      };
    },
    computed: {
      ...mapState({
        allMarketingConsents: state => state.Agreements.marketingConsents,
      }),
      canView() {
        return this.$store.getters['Authorization/isSuperAdmin'];
      },
      noApproval() {
        return this.allMarketingConsents.filter(val => val.agreementNameEn === 'No approval');
      },
      marketingConsents() {
        this.showSaveButton();
        return this.allMarketingConsents.filter(val => val.agreementNameEn !== 'No approval');
      },
    },
    watch: {
      selected() {
        if (this.selected.length > 0) {
          this.selectedOff = [];
        }
      },
      selectedOff() {
        if (this.selectedOff.length > 0) {
          this.selected = [];
        }
      },
      allMarketingConsents() {
        this.selected = this.allMarketingConsents.filter(val => val.agreementNameEn !== 'No approval' && Number(val.accepted) === 1);
        this.countSelected = this.selected.length;
        this.selectedOff = this.allMarketingConsents.filter(val => val.agreementNameEn === 'No approval' && Number(val.accepted) === 1);
      },
    },
    methods: {
      showSaveButton() {
        this.saveButton = false;
        if (this.selectedOff.length > 0 && Number(this.noApproval[0].accepted) === 0) {
          this.saveButton = true;
        } else if (this.selected.length === 0) {
          this.saveButton = false;
        } else if (this.countSelected !== this.selected.length) {
          this.saveButton = true;
        } else if (this.selected.length > 0) {
          let tmp = false;
          this.selected.map(val => {
            if (Number(val.accepted) === 0) {
              tmp = true;
            }
            return tmp;
          });
          this.saveButton = tmp;
        } else {
          this.saveButton = false;
        }
        return this.saveButton;
      },
      async loadData() {
        this.loading = true;
        await this.$store.dispatch('Agreements/loadMarketingConsents');
        await this.$store.dispatch('Config/loadContentConfig');
        this.loading = false;
      },
      async accept() {
        let data = [];
        if (this.selectedOff.length > 0) {
          data = this.noApproval.map(val => Number(val.id));
        } else {
          data = this.selected.map(val => Number(val.id));
        }
        try {
          await this.$store.dispatch('Agreements/newEmployeeAgreements', data);
          this.$store.commit('showSnackbar', {
            text: this.$t('Consents have been saved'),
            color: 'success',
          });
          this.loadData();
          this.saveButton = false;
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('There is some error. Please contact with administration'),
            color: 'error',
          });
        }
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
    i18n: {
      messages: {
        pl: {
          'Select all consents': 'Zaznacz wszystkie zgody',
          'Marketing consents': 'Zgody marketingowe',
          'Save': 'Zapisz',
          'No data available': 'Brak danych',
          'Loading data...': 'Wczytywanie...',
          'Consents have been saved': 'Zgody zostały zapisane',
          'There is some error. Please contact with administration': 'Wystąpił błąd. Proszę skontaktować się z administracją',
          'message': 'Jeśli nie wyrażasz żadnej zgody na wykorzystanie Twojego wizerunku, zaznacz poniższą informację',
        },
        en: {
          message: 'If you do not agree to the use of your image, please check the information below',
        },
      },
    },
  };
</script>
