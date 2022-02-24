<template>
  <td :class="{'pl-1 pr-1': $vuetify.breakpoint.xs}">
    <div class="d-flex justify-center">
      <v-checkbox class="agreement__checkbox"
                  v-model="selected"
                  hide-details
                  :disabled="agreement.accepted === 1"
                  @change="$emit('select')"/>
    </div>
  </td>
</template>

<script>
  export default {
    name: 'AgreementRowSwitch',
    props: {
      agreement: { type: Object, required: true },
      value: { type: Boolean, default: false },
    },
    computed: {
      selected: {
        get() {
          if (this.agreement.accepted === 1) {
            return this.agreement.accepted;
          } else {
            return this.value;
          }
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
  };
</script>
<style scoped>
  .agreement__checkbox{
    width: 24px;
    margin: 0 auto;
  }
</style>
