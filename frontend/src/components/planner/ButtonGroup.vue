<template>
  <div>
    <v-btn-toggle :value="value" mandatory ref="toggleGroup" @change="updateValue">
      <v-btn v-for="(button, index) in items"
             :key="index"
             :value="button.value">
        {{ button.label }}
      </v-btn>
    </v-btn-toggle>
  </div>
</template>

<script>
  export default {
    name: 'ButtonGroup',
    props: {
      items: { type: Array, default: () => ([]) },
      value: { type: String, default: '' },
    },
    methods: {
      updateValue() {
        const selectedItems = this.$refs.toggleGroup.selectedItems;
        if (selectedItems.length > 0) {
          const selectedItem = selectedItems[0];
          this.$emit('input', selectedItem.value);
        }
      },
    },
  };
</script>
