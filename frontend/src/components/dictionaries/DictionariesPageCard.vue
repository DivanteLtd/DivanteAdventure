<template>
  <v-row no-gutters wrap>
    <v-col sm="12" :class="{'pb-2': $vuetify.breakpoint.smAndDown, 'pb-4': $vuetify.breakpoint.mdAndUp}">
      <v-tabs v-model="tab" centered>
        <v-tab>{{ $t('Contracts type') }}</v-tab>
        <v-tab-item>
          <page-card :title="$t('Contracts type')">
            <contracts-type-table :items="contractsType"/>
            <template slot="options">
              <v-row no-gutters >
                <contracts-type-more-menu/>
              </v-row>
            </template>
          </page-card>
        </v-tab-item>
      </v-tabs>
    </v-col>
  </v-row>
</template>

<script>
  import ContractsTypeTable from './ContractsTypeTable';
  import PageCard from '../utils/PageCard';
  import ContractsTypeMoreMenu from './ContractsTypeMoreMenu';

  export default {
    name: 'DictionariesPageCard',
    components: { ContractsTypeMoreMenu, ContractsTypeTable, PageCard },
    props: {
      loading: { type: Boolean, default: false },
      contractsType: { type: Array, required: true },
    },
    i18n: {
      messages: {
        pl: {
          'Contracts type': 'Typy umów',
        },
      },
    },
  };
</script>
