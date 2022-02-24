<template>
  <v-container id="page-overtime">
    <v-row no-gutters wrap>
      <v-col cols="12">
        <overtime-tabbed-view :loaded="!loading"/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { EventBus, eventNames } from '../eventbus';
  import OvertimeTabbedView from '../components/overtime/OvertimeTabbedView';

  export default {
    name: 'Overtime',
    components: { OvertimeTabbedView },
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
          this.$store.dispatch('Projects/loadProjects'),
        ]);
        this.loading = false;
      },
    },
    mounted() {
      EventBus.$on(eventNames.evidenceCreatedAfter, this.loadData);
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
  };
</script>
