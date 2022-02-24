<template>
  <v-container id="dictionaries"
               class="gdpr-table mb-8"
               :class="{'pa-0': $vuetify.breakpoint.xs}"
               grid-list-xl fluid>
    <dictionaries-page-card :loading="loading" :contracts-type="contractsType"/>
  </v-container>
</template>

<script>
  import DictionariesPageCard from '../components/dictionaries/DictionariesPageCard';
  import { mapState } from 'vuex';

  export default {
    name: 'Dictionaries',
    components: { DictionariesPageCard },
    computed: {
      ...mapState({
        contractsType: state => state.ContractsType.contractsType,
      }),

    },
    methods: {
      async loadData() {
        this.loading = true;
        await this.$store.dispatch('ContractsType/load');
        this.loading = false;
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
  };
</script>
