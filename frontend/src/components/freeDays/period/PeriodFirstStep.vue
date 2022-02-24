<template>
  <div>
    <period-form-employee-time
      v-model="valueContent"
      :valid.sync="formValid"
      :is-edit="isEdit"
      :min-starting-date="minStartingDate"
      :max-ending-date="maxEndingDate"
    />
    <v-btn text @click="previousPage">{{ $t('Cancel') }}</v-btn>
    <v-btn text color="primary" @click="nextPage" :disabled="!formValid">{{ $t('Next') }}</v-btn>
  </div>
</template>

<script>
  import PeriodFormEmployeeTime from './PeriodFormEmployeeTime';

  export default {
    name: 'PeriodFirstStep',
    components: { PeriodFormEmployeeTime },
    props: {
      value: { type: Object, required: false, default: () => ({}) },
      minStartingDate: { type: String, required: false, default: null },
      maxEndingDate: { type: String, required: false, default: null },
      isEdit: { type: Boolean, required: false },
    },
    data() { return {
      valueContent: this.value,
      formValid: false,
    };},
    watch: {
      valueContent() {
        this.$emit('input', this.valueContent);
      },
      value() {
        if (this.value !== this.valueContent) {
          this.valueContent = this.value;
        }
      },
    },
    methods: {
      nextPage() {
        this.$emit('next-page');
      },
      previousPage() {
        this.$emit('previous-page');
      },
    },
    i18n: { messages: {
      pl: {
        Next: 'Dalej',
        Cancel: 'Anuluj',
      },
    } },
  };
</script>
