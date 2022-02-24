<template>
  <tr>
    <td class="centered" @click="rowClicked">
      {{ project.name }}
    </td>
    <td class="centered" @click="rowClicked">
      <v-icon v-if="project.billable">attach_money</v-icon>
      <v-icon v-if="!project.billable">money_off</v-icon>
    </td>
    <td class="centered" @click="rowClicked">
      <template v-if="project.project_type !== 0">
        <v-chip color="teal" text-color="white" v-if="project.project_type === 1">
          {{ $t("Implementation") }}
        </v-chip>
        <v-chip color="green" text-color="white" v-if="project.project_type === 2">
          {{ $t("Maintenance") }}
        </v-chip>
      </template>
    </td>
    <td class="centered" @click="rowClicked">
      {{ formatDate(project.started_at) }}
    </td>
    <td class="centered" @click="rowClicked">
      {{ formatDate(project.ended_at) }}
    </td>
    <td class="centered">
      <project-list-actions :project="project"/>
    </td>
  </tr>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import moment from '@divante-adventure/work-moment';
  import ProjectListActions from './ProjectListActions';

  export default {
    name: 'ProjectListRow',
    components: { ProjectListActions },
    props: {
      project: { type: Object, required: true },
    },
    methods: {
      rowClicked() {
        EventBus.$emit(eventNames.showProjectWindow, this.project);
      },
      formatDate(date) {
        return date > 0 ? moment.unix(date).format('DD-MM-YYYY') : '';
      },
    },
    i18n: { messages: {
      pl: {
        Implementation: 'Wdrożenie',
        Maintenance: 'Utrzymanie',
      },
    },
    },
  };
</script>

<style scoped>
  td.centered {
    text-align: center;
    cursor: pointer;
  }
</style>
