<template>
  <v-card>
    <v-app-bar color="transparent" flat :height="checkIfMobile">
      <v-row no-gutters wrap class="align-center">
        <v-spacer/>
        <v-col cols="12" sm="8" md="6">
          <v-tabs v-model="selectedTab" centered>
            <v-tab>{{ $t('tribes-tab', [filteredTribes.length]) }}</v-tab>
            <v-tab>{{ $t('departments-tab', [filteredDepartments.length]) }}</v-tab>
          </v-tabs>
        </v-col>
        <v-col cols="12" sm="4" md="3">
          <v-row no-gutters class="d-flex flex-nowrap align-center">
            <v-text-field v-model="search" append-icon="search" :label="$t('Search')" single-line hide-details/>
            <tribe-more-menu/>
          </v-row>
        </v-col>
      </v-row>
    </v-app-bar>
    <v-divider/>
    <v-card-text class="pa-0">
      <v-tabs-items touchless v-model="selectedTab">
        <v-tab-item>
          <tribes-list-table :loading="loading" :tribes="filteredTribes"/>
        </v-tab-item>
        <v-tab-item>
          <tribes-list-table :loading="loading" :tribes="filteredDepartments" page-text-key="page-text-department"/>
        </v-tab-item>
      </v-tabs-items>
    </v-card-text>
  </v-card>
</template>

<script>
  import TribeMoreMenu from './TribeMoreMenu';
  import TribesListTable from './TribesListTable';
  import { mapState } from 'vuex';

  export default {
    name: 'TribesListCard',
    components: { TribesListTable, TribeMoreMenu },
    props: {
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        search: '',
        selectedTab: 0,
      };
    },
    computed: {
      ...mapState({
        tribesAndDepartments: state => state.Tribes.tribes,
      }),
      checkIfMobile() {
        return this.$vuetify.breakpoint.xs ? 120 : 48;
      },
      nameFilteredTribesAndDepartments() {
        const searchLower = this.search.toLowerCase().replace(/\s/g, '').split(/[ ,.;]+/);
        return this.tribesAndDepartments.filter(
          tribe => {
            const tribeText = `${tribe.name} ${tribe.url}`.replace(/\s/g, '').toLowerCase();
            return searchLower.map(key => tribeText.includes(key)).reduce((a, b) => a && b, true);
          },
        );
      },
      filteredTribes() {
        return this.nameFilteredTribesAndDepartments.filter(tribe => !tribe.isVirtual && !tribe.visibility);
      },
      filteredDepartments() {
        return this.nameFilteredTribesAndDepartments.filter(tribe => tribe.isVirtual && !tribe.visibility);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Search': 'Szukaj',
          'tribes-tab': 'Praktyki ({0})',
          'departments-tab': 'Działy ({0})',
        },
        en: {
          'tribes-tab': 'Practice ({0})',
          'departments-tab': 'Departments ({0})',
        },
      },
    },
  };
</script>
