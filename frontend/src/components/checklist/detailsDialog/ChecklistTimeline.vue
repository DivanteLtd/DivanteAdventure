<template>
  <v-timeline dense align-top style="overflow: auto; margin-left: 1em">
    <v-timeline-item v-if="checklist.startedAt" icon="timelapse" color="yellow">
      <strong>{{ $t('Checklist assigned') }}</strong>
      <div class="caption mb-2">{{ formatDate(checklist.startedAt) }}</div>
    </v-timeline-item>
    <v-timeline-item v-if="checklist.finishedAt" icon="check_circle_outline" color="green">
      <strong>{{ $t('Checklist finished') }}</strong>
      <div class="caption mb-2">{{ formatDate(checklist.finishedAt) }}</div>
    </v-timeline-item>
  </v-timeline>
</template>

<script>
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'ChecklistTimeline',
    props: {
      /** @type {Checklist} */
      checklist: { type: Object, required: true },
    },
    methods: {
      formatDate(date) {
        return moment.unix(date).format(this.$t('date_format'));
      },
    },
    i18n: {
      messages: {
        pl: {
          'Checklist assigned': 'Przypisano checklistę',
          'Checklist finished': 'Checklista zakończona',
        },
        en: {
          date_format: 'D MMM YYYY HH:mm:ss',
        },
      },
    },
  };
</script>
