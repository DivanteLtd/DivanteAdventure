<template>
  <v-container>
    <v-row no-gutters wrap>
      <v-col cols="12" md="12">
        <v-text-field v-model="freeDays"
                      :label="$t('Paid free days in period')"
                      min="0"
                      type="number"/>
        <v-text-field v-model="sickDays"
                      :label="$t('Paid sick leave days in period')"
                      min="0"
                      type="number"/>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  export default {
    name: 'PeriodFreeDays',
    props: {
      value: { type: Object, default: () => ({}) },
      valid: { type: Boolean, required: false },
    },
    data() { return {
      freeDays: this.value.freeDays || 0,
      sickDays: this.value.sickLeaveDays || 0,
    };},
    watch: {
      value() {
        if (this.freeDays !== this.value.freeDays) {
          this.freeDays = this.value.freeDays || 0;
          this.updateValue();
        }
        if (this.sickDays !== this.value.sickLeaveDays) {
          this.sickDays = this.value.sickLeaveDays || 0;
          this.updateValue();
        }
      },
      freeDays() {
        this.updateValue();
      },
      sickDays() {
        this.updateValue();
      },
    },
    methods: {
      updateValue() {
        const val = this.value;
        val.freeDays = this.freeDays < 0 ? 0 : this.freeDays;
        val.sickLeaveDays = this.sickDays < 0 ? 0 : this.sickDays;
        this.freeDays = val.freeDays;
        this.sickDays = val.sickLeaveDays;
        this.$emit('input', val);
        this.$emit('update:valid', val.freeDays >= 0 && val.sickLeaveDays >= 0);
      },
    },
    i18n: { messages: {
      pl: {
        'Paid free days in period': 'Płatne dni wolne w okresie',
        'Paid sick leave days in period': 'Płatne dni chorobowe w okresie',
      },
    } },
  };
</script>
