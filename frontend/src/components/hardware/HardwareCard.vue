<template>
  <page-card id="hardware-card" :title="$t('Hardware lending agreements')">
    <v-tabs v-model="selectedTab" centered class="py-2">
      <v-tab>{{ $t('To generate') }}</v-tab>
      <v-tab>{{ $t('Not signed') }}</v-tab>
    </v-tabs>
    <template slot="options">
      <v-row no-gutters class="d-flex align-center">
        <v-text-field v-model="search"
                      append-icon="search"
                      :label="$t('search-label')"
                      single-line hide-details/>
      </v-row>
    </template>
    <v-card-text class="pa-0">
      <v-tabs-items touchless v-model="selectedTab">
        <v-tab-item>
          <hardware-table-to-generate
            :loading="loading"
            @changeLoading="$emit('changeLoading')"
            :hardware-agreements="hardwareAgreements.filter(val => !val.generated)"
            :search="search"/>
        </v-tab-item>
        <v-tab-item>
          <hardware-table-not-signed
            :loading="loading"
            @changeLoading="$emit('changeLoading')"
            :hardware-agreements="hardwareAgreements.filter(val => val.generated)"
            :search="search"/>
        </v-tab-item>
      </v-tabs-items>
    </v-card-text>
  </page-card>
</template>

<script>
  import HardwareTableToGenerate from './HardwareTableToGenerate';
  import HardwareTableNotSigned from './HardwareTableNotSigned';
  import PageCard from '../utils/PageCard';
  import { mapGetters } from 'vuex';

  export default {
    name: 'HardwareCard',
    components: { HardwareTableToGenerate, HardwareTableNotSigned, PageCard },
    props: {
      loading: { type: Boolean, required: true },
      hardwareAgreements: { type: Array, default: () => ([]) },
    },
    data() {
      return {
        selectedTab: 0,
        search: '',
      };
    },
    computed: {
      ...mapGetters({
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
    },
    i18n: {
      messages: {
        pl: {
          'Hardware lending agreements': 'Umowy użyczenia sprzętu',
          'Search': 'Szukaj',
          'To generate': 'Do wygenerowania',
          'Not signed': 'Nie podpisane',
          'search-label': 'Szukaj po osobie, kategorii, producencie, modelu...',
        },
        en: {
          'search-label': 'Search by person, category, manufacturer, model...',
        },
      },
    },
  };
</script>
