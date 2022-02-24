<template>
  <v-list-item @click="showChecklist">
    <v-list-item-content>
      <v-list-item-title>
        {{ label }}
      </v-list-item-title>
      <v-list-item-subtitle>
        <v-row no-gutters wrap>
          <v-col cols="10">
            <colored-progress-bar :max="checklist.tasksAllCount" :value="checklist.tasksFinishedCount"/>
          </v-col>
          <v-col cols="2" style="text-align: center;">
            {{ checklist.tasksFinishedCount }} / {{ checklist.tasksAllCount }}
          </v-col>
        </v-row>
      </v-list-item-subtitle>
    </v-list-item-content>
  </v-list-item>
</template>

<script>
  import { getSuggestedLanguage } from '../../../i18n/i18n';
  import { EventBus, eventNames } from '../../../eventbus';
  import ColoredProgressBar from '../../utils/ColoredProgressBar';

  export default {
    name: 'ChecklistDataRow',
    components: { ColoredProgressBar },
    props: {
      /** @type {ChecklistListEntry} checklist */
      checklist: { type: Object, default: () => ({ tasksFinishedCount: 0, tasksAllCount: 1 }) },
      employeeId: { type: Number, required: true },
    },
    computed: {
      label() {
        const lang = getSuggestedLanguage();
        switch (lang) {
          case 'en': return this.checklist.nameEn;
          case 'pl': return this.checklist.namePl;
          default: return '';
        }
      },
    },
    methods: {
      showChecklist() {
        EventBus.$emit(eventNames.showChecklistDetails, this.checklist.id, this.employeeId);
      },
    },
  };
</script>
