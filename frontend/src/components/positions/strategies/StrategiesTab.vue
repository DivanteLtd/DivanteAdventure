<template>
  <common-list-grid :items="strategies" :headers="headers" :loading="loading" :page-text="$t('page-text')">
    <strategies-row slot-scope="props" :strategy="props.item"/>
  </common-list-grid>
</template>

<script>
  import { mapState } from 'vuex';
  import CommonListGrid from '../../utils/CommonListGrid';
  import StrategiesRow from './StrategiesRow';

  export default {
    name: 'StrategiesTab',
    components: { StrategiesRow, CommonListGrid },
    props: {
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        headers: [
          {
            text: this.$t('Name'),
            value: 'name',
          }, {
            text: this.$t('Available levels'),
            sortable: false,
          }, {
            text: this.$t('Used in positions'),
            sortable: false,
          }, {
            sortable: false,
          },
        ],
      };
    },
    computed: {
      ...mapState({
        strategies: state => state.Positions.strategies,
      }),
    },
    i18n: { messages: {
      pl: {
        'Name': 'Nazwa',
        'Available levels': 'Dostępne poziomy',
        'Used in positions': 'Użyte w stanowiskach',
        'page-text': 'Struktury {pageStart}-{pageStop} z {itemsLength}',
      },
      en: {
        'page-text': 'Structures {pageStart}-{pageStop} of {itemsLength}',
      },
    },
    },
  };
</script>
