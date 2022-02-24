<template>
  <v-container>
    <div class="text-lg-left">
      <h2>{{ $t('Planner') }}</h2>
    </div>
    <subheader/>
    <loading-indicator/>
    <calendar/>
    <forms-container/>
  </v-container>
</template>

<script>
  import { EventBus, eventNames, keyValues } from '../../eventbus';
  import { viewModeMixin } from '../../mixins/PlannerMixins';
  import Subheader from './Subheader';
  import LoadingIndicator from './LoadingIndicator';
  import FormsContainer from './forms/FormsContainer';
  import Calendar from './calendar/Calendar';

  export default {
    name: 'Planner',
    components: { Calendar, FormsContainer, LoadingIndicator, Subheader },
    mixins: [ viewModeMixin ],
    beforeMount() {
      window.addEventListener('keydown', e => {
        if (e.ctrlKey && e.which === keyValues.c) {
          EventBus.$emit(eventNames.ctrlC);
        } else if (e.ctrlKey && e.which === keyValues.v) {
          EventBus.$emit(eventNames.ctrlV);
        } else if (e.which === keyValues.escape) {
          EventBus.$emit(eventNames.escapePressed);
        } else if (e.which === keyValues.delete) {
          EventBus.$emit(eventNames.deletePressed);
        }
      });
    },
    mounted() {
      this.$store.dispatch('Planner/loadFromStorage');
      this.$store.dispatch('Planner/loadDataFromBackend');
    },
    i18n: {
      messages: {
        pl: {
          Planner: 'Planer',
        },
      },
    },
  };
</script>

<style scoped>
  .text-lg-left {
    margin-top: 20px;
    margin-left: 30px;
  }
</style>
