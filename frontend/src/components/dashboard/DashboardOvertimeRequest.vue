<template>
  <v-list-item @click="click">
    <v-list-item-action>
      <v-icon color="#32847B">access_time</v-icon>
    </v-list-item-action>
    <v-list-item-content>
      <v-list-item-title>{{ $t('request_id', {id: request.id}) }}</v-list-item-title>
      <v-list-item-subtitle>{{ request.employee.name }} {{ request.employee.lastName }}</v-list-item-subtitle>
    </v-list-item-content>
  </v-list-item>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';

  export default {
    name: 'DashboardOvertimeRequest',
    props: {
      request: { type: Object, required: true, default: () => ({}) },
    },
    methods: {
      click() {
        EventBus.$emit(eventNames.showEvidenceWindowForAcceptance, this.request);
      },
    },
    i18n: { messages: {
      pl: {
        request_id: 'Wniosek o nadgodziny #{id}',
      },
      en: {
        request_id: 'Overtime request #{id}',
      },
    },
    },
  };
</script>
