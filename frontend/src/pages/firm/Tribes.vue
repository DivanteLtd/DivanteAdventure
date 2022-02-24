<template>
  <div id="page-tribes-list">
    <v-container grid-list-xl fluid>
      <tribes-list-card :loading="loading"/>
    </v-container>
  </div>
</template>

<script>
  import TribesListCard from '../../components/tribes/list/TribesListCard';
  import { mapState } from 'vuex';
  import { EventBus, eventNames } from '../../eventbus';

  export default {
    name: 'Tribes',
    components: { TribesListCard },
    data() {
      return {
        loading: false,
      };
    },
    computed: {
      ...mapState({
        tribes: state => state.Tribes.tribes,
      }),
    },
    methods: {
      async loadData() {
        this.loading = true;
        await this.$store.dispatch('Tribes/loadTribes');
        await this.$store.dispatch('Employees/loadEmployees');
        this.loading = false;
        const paramsId = this.$route.params.id;
        const id = typeof(paramsId) === 'undefined' ? undefined : parseInt(paramsId);
        const filteredTribe = this.tribes.filter(tribe => tribe.id === id);
        if (filteredTribe.length === 1) {
          EventBus.$emit(eventNames.showTribeWindow, filteredTribe[0]);
        }
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
    beforeRouteUpdate(to, from, next) {
      next();
      const paramsId = this.$route.params.id;
      const id = typeof(paramsId) === 'undefined' ? undefined : parseInt(paramsId);
      const filteredTribe = this.tribes.filter(tribe => tribe.id === id);
      if (filteredTribe.length === 1) {
        EventBus.$emit(eventNames.showTribeWindow, filteredTribe[0]);
      }
    },
  };
</script>
