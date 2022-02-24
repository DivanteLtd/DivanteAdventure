<template>
  <v-dialog v-model="dialogVisible" width="1000">
    <v-card>
      <v-card-title class="headline ml-0 mb-5">
        {{ $t('Feedback') }}
        <v-spacer/>
        <v-btn @click="dialogVisible = false" icon rounded><v-icon>clear</v-icon></v-btn>
      </v-card-title>
      <v-divider/>
      <v-card-text>
        <h3>{{ $t('360-degree feedback') }}</h3>
        <markdown :text="feedback.feedback" :length="20000"/>
        <h3>{{ $t('Progress feedback (goals, successes, fields to improve)') }}</h3>
        <markdown :text="feedback.progressFeedback" :length="20000"/>
        <h3>{{ $t('Technical feedback') }}</h3>
        <markdown :text="feedback.technicalFeedback" :length="20000"/>
      </v-card-text>
      <div class="d-flex justify-end pb-1">
        <v-btn text @click="dialogVisible = false">
          {{ $t('Close') }}
        </v-btn>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
  import Markdown from '../utils/Markdown';

  export default {
    name: 'FeedbackDetails',
    components: { Markdown },
    props: {
      value: { type: Boolean, required: true },
      feedback: { type: Object, required: true },
    },
    computed: {
      dialogVisible: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
    i18n: {
      messages: {
        pl: {
          'Feedback': 'Feedback',
          'Close': 'Zamknij',
          '360-degree feedback': 'Ocena 360 stopni',
          'Progress feedback (goals, successes, fields to improve)': 'Feedback rozwojowy (cele, sukcesy, obszary do poprawy)',
          'Technical feedback': 'Feedback techniczny',
        },
      },
    },
  };
</script>
