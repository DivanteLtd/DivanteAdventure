<template>
  <v-card>
    <v-app-bar color="transparent" flat :height="checkIfMobile">
      <v-row no-gutters wrap class="align-center">
        <v-col md="1" lg="3" class="hidden-sm-and-down">
          <v-spacer/>
        </v-col>
        <v-col cols="12" sm="4" md="6" lg="4" :class="{'mt-2 tabs': $vuetify.breakpoint.xs}">
          <v-tabs v-model="selectedTab" fixed-tabs>
            <v-tab>{{ $t('GDPR') }}</v-tab>
            <v-tab>{{ $t('Marketing') }}</v-tab>
            <v-tab>{{ $t('ISO') }}</v-tab>
          </v-tabs>
        </v-col>
        <v-col md="1" lg="2" class="hidden-sm-and-down">
          <v-spacer/>
        </v-col>
        <v-col cols="10" sm="4" md="4" lg="3" class="flex-end" :class="{'pb-1': $vuetify.breakpoint.xs}">
          <v-row no-gutters class="align-center" :class="{'pb-2': $vuetify.breakpoint.smAndUp}">
            <v-col cols="10">
              <v-text-field v-model="search"
                            append-icon="search"
                            :label="$t('search-label')"
                            single-line hide-details/>
            </v-col>
            <v-col cols="2">
              <acceptation-more-menu :class="{'pt-3': $vuetify.breakpoint.smAndUp}"/>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-app-bar>
    <v-divider/>
    <v-card-text class="pa-0">
      <v-tabs-items touchless v-model="selectedTab">
        <v-tab-item>
          <g-d-p-r-acceptation-list :loading="loading" :search="search"/>
        </v-tab-item>
        <v-tab-item>
          <marketing-acceptation-list :loading="loading" :search="search"/>
        </v-tab-item>
        <v-tab-item>
          <i-s-o-acceptation-list :loading="loading" :search="search"/>
        </v-tab-item>
      </v-tabs-items>
    </v-card-text>
    <loading-dialog :visible="visible"/>
  </v-card>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import AcceptationMoreMenu from './AcceptationMoreMenu';
  import GDPRAcceptationList from './GDPRAcceptationList';
  import MarketingAcceptationList from './Marketing/MarketingAcceptationList';
  import ISOAcceptationList from './ISO/ISOAcceptationList';
  import LoadingDialog from '../utils/LoadingDialog';

  export default {
    name: 'AgreementAcceptationContainer',
    components: {
      MarketingAcceptationList,
      ISOAcceptationList,
      GDPRAcceptationList,
      AcceptationMoreMenu,
      LoadingDialog },
    props: {
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        visible: false,
        search: '',
        selectedTab: 0,
        EventBus,
        eventNames,
      };
    },
    computed: {
      checkIfMobile() {
        return this.$vuetify.breakpoint.xs ? 120 : 48;
      },
    },
    methods: {
      show(value) {
        this.visible = value;
      },
    },
    mounted() {
      EventBus.$on(eventNames.showLoadingDialog, this.show);
    },
    i18n: { messages: {
      pl: {
        'GDPR': 'RODO',
        'Marketing': 'Marketingowe',
        'search-label': 'Szukaj po imieniu, nazwisku, adresie e-mail',
      },
      en: {
        'search-label': 'Search by name, last name, e-mail',
      },
    },
    },
  };
</script>
<style scoped>
  .tabs {
    margin-left: -10px;
  }
</style>
