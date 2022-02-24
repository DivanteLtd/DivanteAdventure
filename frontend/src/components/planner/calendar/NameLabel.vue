<template>
  <router-link :to="currentViewMode.createUrl($store, elementId)">
    <span :class="'name ' + (primary ? 'body-1 font-weight-bold' : 'body-1')">
      {{ currentViewMode.createElementLabel(element) }}
    </span>
    <br/>
    <span class="time">
      {{ getWorkTime }}
    </span>
  </router-link>
</template>

<script>
  import { dataModeMixin, viewModeMixin } from '../../../mixins/PlannerMixins';
  import ViewMode from '../../../util/planner/ViewMode';

  export default {
    name: 'NameLabel',
    mixins: [ dataModeMixin, viewModeMixin ],
    props: {
      element: { type: Object, required: true },
      primary: { type: Boolean, default: false },
    },
    computed: {
      getWorkTime() {
        return this.primary === true ? this.viewMode.getWorktime(this.element, this.dataMode, '(%1)') : '';
      },
      currentViewMode() {
        return this.primary ? this.viewMode : this.viewMode.opposite;
      },
      elementId() {
        if (this.primary) {
          return this.element.id;
        } else if (this.currentViewMode.value === ViewMode.employee.value) {
          return this.element.employeeId;
        } else if (this.currentViewMode.value === ViewMode.project.value) {
          return this.element.projectId;
        } else {
          return -1;
        }
      },
    },
  };
</script>

<style scoped>
  .time {
    font-style: italic;
  }
</style>
