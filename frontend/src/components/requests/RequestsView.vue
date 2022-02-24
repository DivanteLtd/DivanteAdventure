<template>
  <v-container>
    <v-row no-gutters wrap>
      <v-col sm="12" :class="{'pb-2': $vuetify.breakpoint.smAndDown, 'pb-4': $vuetify.breakpoint.mdAndUp}">
        <v-card>
          <v-card-title>
            <h1 class="headline">{{ $t('Applications') }}</h1>
          </v-card-title>
          <v-card-text class="pa-0">
            <requests-table :loading="loading" :requests="requests" awaiting/>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col sm="12">
        <v-card>
          <v-card-title>
            <h1 class="headline">{{ $t('Applications planned') }}</h1>
          </v-card-title>
          <v-card-text class="pa-0">
            <requests-table :loading="loading" :requests="plannedRequests" planned/>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col sm="12" v-if="archivalRequests.length > 0"
             :class="{'pt-2': $vuetify.breakpoint.smAndDown, 'pt-4': $vuetify.breakpoint.mdAndUp}">
        <v-card>
          <v-card-title>
            <h1 class="headline">{{ $t('Archival applications') }}</h1>
          </v-card-title>
          <v-card-text class="pa-0">
            <requests-table :loading="loading" :requests="archivalRequests"/>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import RequestsTable from './RequestsTable';
  import { EventBus, eventNames } from '../../eventbus';
  import { mapGetters } from 'vuex';

  export default {
    name: 'RequestsView',
    components: { RequestsTable },
    props: {
      loading: { type: Boolean, required: true },
      reloadFunction: { type: Function, default: () => {} },
    },
    computed: {
      ...mapGetters({
        requests: 'Requests/getRequestsWithTypes',
        archivalRequests: 'Requests/getArchivalRequestsWithTypes',
        plannedRequests: 'Requests/getPlannedRequests',
      }),
    },
    methods: {
      callReloadFunction() {
        this.reloadFunction();
      },
      callUpdate() {
        this.reloadFunction();
      },
    },
    mounted() {
      EventBus.$on(eventNames.requestStatusUpdate, this.callReloadFunction);
      EventBus.$on(eventNames.createNewLeaveRequestAfter, this.callUpdate);
      EventBus.$on(eventNames.reloadPeriods, this.callUpdate);
      this.$store.dispatch('FreeDays/loadMyPeriods');
    },
    i18n: { messages: {
      pl: {
        'Applications': 'Wnioski',
        'Applications planned': 'Wnioski zaplanowane',
        'Archival applications': 'Wnioski archiwalne',
      },
    } },
  };
</script>
