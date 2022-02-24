<template>
  <v-container id="page-evidences">
    <evidences-tabbed-view :loaded="!loading"/>
  </v-container>
</template>

<script>
  import EvidencesTabbedView from '../components/evidences/EvidencesTabbedView';
  import { mapGetters } from 'vuex';
  import { EventBus, eventNames } from '../eventbus';

  export default {
    name: 'Evidences',
    components: { EvidencesTabbedView },
    data() { return {
      loading: false,
    };},
    computed: {
      ...mapGetters({
        bestDefaultMonth: 'Evidences/bestDefaultMonth',
      }),
    },
    methods: {
      async loadData() {
        this.loading = true;
        await Promise.all([
          this.$store.dispatch('Employees/loadEmployees'),
          this.$store.dispatch('Evidences/loadMyEvidences'),
          this.$store.dispatch('FreeDays/loadMyPeriods'),
          this.$store.dispatch('Projects/loadProjects'),
        ]);
        await this.$store.dispatch('FreeDays/loadFreeDaysForMonth', this.bestDefaultMonth);
        this.loading = false;
      },
    },
    mounted() {
      EventBus.$on(eventNames.evidenceCreatedAfter, this.loadData);
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
  };
</script>
