<template>
  <v-dialog v-model="valueWrapper" width="600">
    <v-card>
      <v-card-title class="title">
        {{ $t('Rejecting a question') }}
        <v-spacer/>
        <v-btn @click="valueWrapper = false" icon rounded><v-icon>clear</v-icon></v-btn>
      </v-card-title>
      <v-card-text>
        <span>{{ $t('Please write a reason why you want to reject this question.') }}</span>
        <v-alert :value="true" type="info">
          {{ $t('This message will be sent to the questioner.') }}
        </v-alert>
        <v-textarea v-model="reason" :label="$t('Rejection reason')" :rules="[ validateNotEmpty ]" rows="1" auto-grow/>
      </v-card-text>
      <v-card-actions>
        <v-spacer/>
        <v-btn text @click="valueWrapper = false">{{ $t('Cancel') }}</v-btn>
        <v-btn text color="primary" @click="reject" :loading="loading" :disabled="reason === ''">
          {{ $t('Save') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import ApiAccess from '../../mixins/ApiAccess';

  export default {
    name: 'RejectQuestionDialog',
    mixins: [ ApiAccess ],
    props: {
      value: { type: Boolean, required: true },
      question: { type: Object, required: true },
    },
    data() {
      return {
        reason: '',
        loading: false,
        validateNotEmpty: v => !!v || this.$t('Field cannot be empty'),
      };
    },
    computed: {
      valueWrapper: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
    methods: {
      async reject() {
        this.loading = true;
        try {
          await this.apiClient.faq.rejectQuestion(this.question.id, this.reason);
        } finally {
          this.$store.commit('showSnackbar', {
            text: this.$t('Question has been rejected.'),
            color: 'success',
          });
          this.$emit('reload');
          this.valueWrapper = false;
          this.loading = false;
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Rejecting a question': 'Odrzucanie pytania',
          'Please write a reason why you want to reject this question.': 'Proszę podać powód odrzucenia pytania.',
          'This message will be sent to the questioner.': 'Ta wiadomość zostanie wysłana do osoby pytającej.',
          'Rejection reason': 'Powód odrzucenia',
          'Field cannot be empty': 'Pole nie może być puste',
          'Save': 'Zapisz',
          'Question has been rejected.': 'Pytanie zostało odrzucone',
          'Cancel': 'Anuluj',
        },
      },
    },
  };
</script>
