<template>
  <v-container>
    <v-row no-gutters wrap>
      <v-col sm="12">
        <v-card>
          <v-card-title>
            <month-changer :update-function="updateFunction" :loading="loading"/>
          </v-card-title>
          <v-text-field class="searchArea"
                        v-model="search"
                        append-icon="search"
                        :label="$t('search-label')"
                        single-line hide-details/>
          <v-card-text>
            <dashboard-table :loading="loading" :search="search"/>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import MonthChanger from './dashboard/MonthChanger';
  import DashboardTable from './dashboard/DashboardTable';

  export default {
    name: 'DashboardView',
    components: { DashboardTable, MonthChanger },
    props: {
      loading: { type: Boolean, required: true },
      updateFunction: { type: Function, required: true },
    },
    data() { return {
      search: '',
    };},
    i18n: { messages: {
      pl: {
        'search-label': 'Szukaj po imieniu, nazwisku, adresie e-mail...',
      },
      en: {
        'search-label': 'Search by name, last name, e-mail...',
      },
    } },
  };
</script>

<style scoped>
  .searchArea {
    max-width: 400px;
    padding: 0 20px;
  }
</style>
