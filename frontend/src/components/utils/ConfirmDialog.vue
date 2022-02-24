<template>
  <v-dialog v-model="dialogVisible" v-if="dialogVisible" :width="width">
    <v-card id="confirm-dialog">
      <v-card-title class="subheading" style="font-weight: 500">{{ title ? title : $t('Confirmation') }}</v-card-title>
      <v-divider/>
      <v-card-text>
        {{ question }}
      </v-card-text>
      <v-card-actions>
        <v-spacer/>
        <v-btn :color="noColor" @click="noClicked" text>{{ noText ? noText : $t('No') }}</v-btn>
        <v-btn :color="yesColor" @click="yesClicked" text>{{ yesText ? yesText : $t('Yes') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    name: 'ConfirmDialog',
    props: {
      value: { type: Boolean, default: false },
      width: { type: Number, default: 600 },
      title: { type: String, default: '' },
      question: { type: String, default: '' },
      yesColor: { type: String, default: 'blue' },
      noColor: { type: String, default: 'black' },
      yesText: { type: String, default: '' },
      noText: { type: String, default: '' },
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
    methods: {
      noClicked() {
        this.dialogVisible = false;
        this.$emit('no');
      },
      yesClicked() {
        this.dialogVisible = false;
        this.$emit('yes');
      },
    },
    i18n: { messages: {
      pl: {
        Confirmation: 'Potwierdzenie',
        No: 'Nie',
        Yes: 'Tak',
      },
    },
    },
  };
</script>
