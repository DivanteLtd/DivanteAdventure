<template>
  <dashboard-card :title="$t('Useful links')" :loading="!loaded">
    <v-tooltip v-if="isTribeMaster" slot="actions" left>
      <template v-slot:activator="{ on }">
        <v-btn v-on="on" icon @click="showEditor">
          <v-icon class="text--secondary">add</v-icon>
        </v-btn>
      </template>
      {{ $t('Add link') }}
    </v-tooltip>
    <v-container grid-list-md>
      <v-row no-gutters wrap class="dashboard-links" :class="{'pa-2': $vuetify.breakpoint.xs}">
        <template v-for="(item) in links" >
          <dashboard-single-link :link="item" :key="item.id"/>
        </template>
      </v-row>
    </v-container>
  </dashboard-card>
</template>

<script>
  import { mapState, mapGetters } from 'vuex';

  import DashboardCard from './DashboardCard';
  import DashboardSingleLink from './DashboardSingleLink';
  import { EventBus, eventNames } from '../../eventbus';

  export default {
    name: 'DashboardLinks',
    components: { DashboardCard, DashboardSingleLink },
    props: {
      loaded: { type: Boolean, default: false },
    },
    data() {
      return {
        currentPage: 1,
      };
    },
    computed: {
      ...mapState({
        links: state => state.Links.links,
      }),
      ...mapGetters({
        isTribeMaster: 'Authorization/isTribeMaster',
      }),
    },
    methods: {
      showEditor() {
        EventBus.$emit(eventNames.showLinksEditor);
      },
    },
    i18n: { messages: {
      pl: {
        'Useful links': 'Przydatne linki',
        'Add link': 'Dodaj link',
      },
    },
    },
  };
</script>
<style>
  .dashboard-links .v-chip .v-chip__content{
    padding: 0 4px;
  }
</style>
