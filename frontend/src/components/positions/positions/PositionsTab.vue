<template>
  <common-list-grid :items="positions" :headers="headers" :loading="loading" :page-text="$t('page-text')">
    <positions-row slot-scope="props" :position="props.item"/>
  </common-list-grid>
</template>

<script>
  import { mapState } from 'vuex';
  import CommonListGrid from '../../utils/CommonListGrid';
  import PositionsRow from './PositionsRow';

  export default {
    name: 'PositionsTab',
    components: { PositionsRow, CommonListGrid },
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
            text: this.$t('Used leveling structure'),
            sortable: false,
          }, {
            text: this.$t('Available levels'),
            sortable: false,
          }, {
            sortable: false,
          },
        ],
      };
    },
    computed: {
      ...mapState({
        positions: state => state.Positions.positions,
      }),
    },
    i18n: { messages: {
      pl: {
        'Name': 'Nazwa',
        'Used leveling structure': 'Użyta struktura poziomów',
        'Available levels': 'Dostępne poziomy',
        'page-text': 'Pozycje {pageStart}-{pageStop} z {itemsLength}',
      },
      en: {
        'page-text': 'Positions {pageStart}-{pageStop} of {itemsLength}',
      },
    },
    },
  };
</script>
