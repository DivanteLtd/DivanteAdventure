<template>
  <td :class="{'pl-1 pr-1': $vuetify.breakpoint.xs}" class="text-center">
    <div v-for="(item, idx) in agreement.name" :key="idx">
      <row-attachment-tooltip :agreement="agreement" :attachment-name="item" :attachment-id="idx"/>
    </div>
  </td>
</template>

<script>
  import RowAttachmentTooltip from './RowAttachmentTooltip';

  export default {
    name: 'RowAttachmentTooltips',
    components: { RowAttachmentTooltip },
    props: {
      agreement: { type: Object, required: true },
    },
  };
</script>
