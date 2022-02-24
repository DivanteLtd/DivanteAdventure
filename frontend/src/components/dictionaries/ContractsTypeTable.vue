<template>
  <v-data-table :loading="loading"
                :items-per-page="15"
                :items="items"
                :headers="headers"
                item-key="id">
    <template v-slot:item="{ item }">
      <contracts-type-row :item="item"/>
    </template>
  </v-data-table>
</template>

<script>
  import ContractsTypeRow from './ContractsTypeRow';

  export default {
    name: 'ContractsTypeTable',
    components: { ContractsTypeRow },
    props: {
      loading: { type: Boolean, default: false },
      items: { type: Array, required: true },
    },
    data() {
      return {
        sortBy: 'code',
        sortDesc: false,
        headers: [{
          text: this.$t('Contract code'),
          align: 'center',
          value: 'code',
        }, {
          text: this.$t('Contract name'),
          align: 'center',
          value: 'name',
        }, {
          text: this.$t('Contract description'),
          align: 'center',
          value: 'descriptions',
          sortable: false,
        }],
      };
    },
    i18n: {
      messages: {
        pl: {
          'Contract code': 'Kod umowy',
          'Contract name': 'Nazwa umowy',
          'Contract description': 'Opis umowy',
        },
      },
    },
  };
</script>
