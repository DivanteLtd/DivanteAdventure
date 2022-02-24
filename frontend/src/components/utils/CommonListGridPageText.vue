<template>
  <span>
    {{ pageTextComputed }}
  </span>
</template>

<script>
  export default {
    name: 'CommonListGridPageText',
    props: {
      pageText: { type: String, required: true },
      slotProps: { type: Object, required: true },
    },
    computed: {
      pageTextComputed() {
        return this.pageText
          .replace('{pageStart}', this.slotProps.pageStart)
          .replace('{pageStop}', this.slotProps.pageStop)
          .replace('{itemsLength}', this.slotProps.itemsLength);
      },
    },
  };
</script>
