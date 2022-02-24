<template>
  <slack-status-switcher :slack-status="slackStatus" :loading="loading" @connect="connect" @disconnect="disconnect"/>
</template>

<script>
  import { mapState } from 'vuex';
  import { EventBus, eventNames } from '../../../eventbus';
  import SlackStatusSwitcher from '../../utils/SlackStatusSwitcher';

  export default {
    name: 'SlackStatusTile',
    components: { SlackStatusSwitcher },
    props: {
      employee: { type: Object, required: true, default: () => ({}) },
    },
    data() {
      return {
        loading: false,
      };
    },
    computed: {
      ...mapState({
        token: state => state.Authorization.token,
      }),
      slackStatus() {
        return this.employee.slackStatus;
      },
    },
    methods: {
      connect() {
        this.loading = true;
        const redirect = `${window.ADVENTURE_BACKEND_URL}/slack/redirectUser?token=${this.token}`;
        window.location.replace(redirect);
      },
      async disconnect() {
        this.loading = true;
        await this.$store.dispatch('Employees/hideSlackDialog');
        this.loading = false;
        EventBus.$emit(eventNames.employeeEdited);
      },
    },
  };
</script>
