<template>
  <tr>
    <agreement-row-switch :agreement="agreement" @select="$emit('select')" v-model="selected"/>
    <agreement-row-name :agreement="agreement"/>
    <row-attachment-tooltips :agreement="agreement"/>
  </tr>
</template>

<script>
  import AgreementRowSwitch from './AgreementRowSwitch';
  import AgreementRowName from './AgreementRowName';
  import RowAttachmentTooltips from './RowAttachmentTooltips';

  export default {
    name: 'AgreementRow',
    components: { RowAttachmentTooltips, AgreementRowName, AgreementRowSwitch },
    props: {
      agreement: { type: Object, required: true },
      value: { type: Boolean, default: false },
    },
    computed: {
      selected: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
  };
</script>
