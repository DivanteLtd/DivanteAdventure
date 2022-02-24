<template>
  <hardware-card :hardware-agreements="hardwareAgreements" :loading="loading" @changeLoading="changeLoading"/>
</template>

<script>
  import { mapState } from 'vuex';
  import HardwareCard from '../../components/hardware/HardwareCard';
  import { EventBus, eventNames } from '../../eventbus';

  export default {
    name: 'HardwareAgreements',
    components: { HardwareCard },
    data() {
      return {
        loading: false,
      };
    },
    computed: {
      ...mapState({
        hardwareAgreements: state => state.Hardware.agreements,
      }),
    },
    methods: {
      async loadData() {
        this.loading = true;
        await this.$store.dispatch('Hardware/loadHardwareAgreements');
        this.loading = false;
      },
      changeLoading() {
        this.loading = !this.loading;
      },
    },
    beforeRouteEnter: (to, from, next) => next(self => self.loadData()),
    mounted() {
      this.loadData();
      EventBus.$on(eventNames.showGeneratedPassword, this.loadData);
    },
  };
</script>
