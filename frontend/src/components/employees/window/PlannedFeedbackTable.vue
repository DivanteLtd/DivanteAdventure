<template>
  <div>
    <h3 :class="{'pa-4': $vuetify.breakpoint.smAndDown}">{{ $t('Planned feedback') }}</h3>
    <v-list two-line dense>
      <v-list-item v-for="(feedback, index) in feedbacks" :key="index">
        <v-list-item-title>{{ feedback.date }}</v-list-item-title>
        <v-list-item-subtitle>{{ feedback.leader.name }} {{ feedback.leader.lastName }}</v-list-item-subtitle>
      </v-list-item>
    </v-list>
  </div>
</template>

<script>
  export default {
    name: 'PlannedFeedbackTable',
    props: {
      feedbacks: { type: Array, required: true },
    },
    i18n: {
      messages: {
        pl: {
          'Planned feedback': 'Zaplanowany feedback',
        },
      },
    },
  };
</script>
