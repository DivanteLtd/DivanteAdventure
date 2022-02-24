<template>
  <v-data-table mobile-breakpoint="0"
                :items-per-page="1000"
                :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                :no-results-text="$t('No results found.')"
                :items="employees"
                :loading="loading"
                :headers="headers"
                hide-default-footer>
    <template v-slot:item="{ item }">
      <tribe-employee-list-entry :can-view="canView" :item="item"/>
    </template>
  </v-data-table>
</template>

<script>
  import { mapGetters } from 'vuex';
  import TribeEmployeeListEntry from './TribeEmployeeListEntry';

  export default {
    name: 'TribeEmployeeList',
    components: { TribeEmployeeListEntry },
    props: {
      employees: { type: Array, required: true },
      loading: { type: Boolean, required: true },
    },
    data() {
      return {
        unfilteredHeaders: [{
          align: 'center',
          value: 'photo',
          sortable: false,
          role: 'ROLE_USER',
        }, {
          text: this.$t('Person'),
          align: 'center',
          value: 'lastName',
          role: 'ROLE_USER',
        }, {
          text: this.$t('Contract type'),
          align: 'center',
          value: 'contract',
          sortable: false,
          role: 'ROLE_TRIBE_MASTER',
        }, {
          text: this.$t('Position'),
          align: 'center',
          value: 'position',
          sortable: false,
          role: 'ROLE_USER',
        }, {
          text: this.$t('Business phone number'),
          align: 'center',
          value: 'phone',
          sortable: false,
          role: 'ROLE_USER',
        }, {
          text: this.$t('Delete'),
          align: 'center',
          sortable: false,
          role: 'ROLE_TRIBE_MASTER',
        }],
        canView: false,
      };
    },
    computed: {
      ...mapGetters({
        isAuthorized: 'Authorization/isAuthorized',
        isSuperAdmin: 'Authorization/isSuperAdmin',
        isUser: 'Authorization/isUser',
      }),
      headers() {
        if (this.isSuperAdmin === true) {
          this.canView = true;
          return this.unfilteredHeaders;
        }
        const header = this.unfilteredHeaders.filter(header => {
          const tribeMaster = this.employees.filter(val => val.id === this.$store.getters['Authorization/getUserId']);
          if (tribeMaster.length > 0) {
            this.canView = this.isAuthorized(header.role);
            return this.isAuthorized(header.role);
          } else {
            if (this.isSuperAdmin === true) {
              this.canView = true;
              return true;
            }
            if (this.isUser === true) {
              this.canView = true;
              return this.isAuthorized(header.role);
            }
            return false;
          }
        });
        return header;
      },
    },
    i18n: {
      messages: {
        pl: {
          'Loading data...': 'Wczytywanie...',
          'No data available.': 'Brak danych.',
          'No results found.': 'Nie znaleziono.',
          'Delete': 'Usuń',

          'Person': 'Osoba',
          'Contract type': 'Forma współpracy',
          'Position': 'Stanowisko',
          'Business phone number': 'Służbowy numer telefonu',
        },
      },
    },
  };
</script>
