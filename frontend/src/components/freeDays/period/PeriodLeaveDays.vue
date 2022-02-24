<template>
  <v-container>
    <v-row no-gutters wrap>
      <v-col cols="12" md="12">
        <v-text-field v-model="freeDays"
                      :label="$t('Paid leave days in period')"
                      min="0"
                      type="number"/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  export default {
    name: 'PeriodLeaveDays',
    props: {
      value: { type: Object, default: () => ({}) },
      valid: { type: Boolean, required: false },
    },
    data() { return {
      freeDays: this.value.freeDays || 0,
    };},
    watch: {
      value() {
        if (this.freeDays !== this.value.freeDays) {
          this.freeDays = this.value.freeDays || 0;
          this.updateValue();
        }
      },
      freeDays() {
        this.updateValue();
      },
    },
    methods: {
      updateValue() {
        const val = this.value;
        val.freeDays = this.freeDays < 0 ? 0 : this.freeDays;
        val.sickLeaveDays = 20;
        this.freeDays = val.freeDays;
        this.$emit('input', val);
        this.$emit('update:valid', val.freeDays >= 0);
      },
    },
    i18n: { messages: {
      pl: {
        'Paid leave days in period': 'Płatne dni wolne w okresie',
      },
    } },
  };
</script>
