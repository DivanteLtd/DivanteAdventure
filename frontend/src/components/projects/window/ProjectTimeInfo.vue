<template>
  <v-list-item v-if="formattedStartDate !== ''">
    <v-list-item-action><v-icon>timelapse</v-icon></v-list-item-action>
    <v-list-item-content>
      <v-list-item-title>{{ formattedStartDate }} - {{ formattedEndDate }}</v-list-item-title>
      <v-list-item-subtitle>{{ projectFinishedLabel }}</v-list-item-subtitle>
    </v-list-item-content>
  </v-list-item>
</template>

<script>
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'ProjectTimeInfo',
    props: {
      project: { type: Object, required: true },
    },
    computed: {
      formattedStartDate() {
        const date = this.project.started_at;
        if (typeof date === 'string') {
          return date;
        } else if (typeof date === 'number' && date > 0) {
          return moment.unix(date).format('YYYY-MM-DD');
        } else {
          return '';
        }
      },
      formattedEndDate() {
        const date = this.project.ended_at;
        if (typeof date === 'string') {
          return date.trim();
        } else if (typeof date === 'number' && date > 0) {
          return moment.unix(date).format('YYYY-MM-DD');
        } else {
          return '';
        }
      },
      projectFinished() {
        const endDate = this.formattedEndDate;
        const endDateMoment = endDate === '' ? moment() : moment(endDate);
        return endDate !== '' && endDateMoment.isBefore(moment());
      },
      projectFinishedLabel() {
        return this.projectFinished ? this.$t('The project is finished') : this.$t('The project is underway');
      },
    },
    i18n: {
      messages: {
        pl: {
          'The project is finished': 'Projekt został zakończony',
          'The project is underway': 'Projekt trwa',
        },
      },
    },
  };
</script>
