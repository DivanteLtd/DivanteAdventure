<template>
  <page-card :title="$t('People')">
    <employee-list-table :loading="loading" :search="search" :employees="employees"/>
    <template slot="options">
      <v-row no-gutters>
        <v-text-field v-model="search"
                      append-icon="search"
                      class="d-flex align-center"
                      :label="$t('search-label')"
                      single-line hide-details/>
        <employee-more-menu/>
      </v-row>
    </template>
  </page-card>
</template>

<script>
  import EmployeeListTable from './EmployeeListTable';
  import EmployeeMoreMenu from './EmployeeMoreMenu';
  import PageCard from '../utils/PageCard';

  export default {
    name: 'EmployeeListCard',
    components: { PageCard, EmployeeMoreMenu, EmployeeListTable },
    props: {
      loading: { type: Boolean, required: true },
      employees: { type: Array, default: () => ([]) },
    },
    data() {
      return {
        search: '',
      };
    },
    i18n: {
      messages: {
        pl: {
          'People': 'Osoby',
          'search-label': 'Szukaj po imieniu, nazwisku, adresie e-mail...',
        },
        en: {
          'search-label': 'Search by name, last name, e-mail...',
        },
      },
    },
  };
</script>
