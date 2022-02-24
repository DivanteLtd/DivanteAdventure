<template>
  <div class="search-item" @click.stop="openPage">
    <v-avatar v-if="imageType === 'icon'" size="40">
      <v-icon>{{ item.img.value }}</v-icon>
    </v-avatar>
    <v-avatar v-else-if="imageType === 'url' && item.img.value" size="40">
      <img :alt="item.displayLabel" :src="item.img.value" @error="item.img.value = ''"/>
    </v-avatar>
    <v-avatar v-else size="40">
      <v-icon large>perm_identity</v-icon>
    </v-avatar>
    <span class="ml-1">{{ displayLabel }}</span>
    <div class="buttons-container" v-if="(item.buttons || []).length > 0 && $vuetify.breakpoint.smAndUp">
      <search-item-button v-for="(button, index) in item.buttons || []" :key="index" :button="button"/>
    </div>
  </div>
</template>

<script>
  import SearchItemButton from './SearchItemButton';

  export default {
    name: 'SearchItem',
    components: { SearchItemButton },
    props: {
      item: { type: Object, required: true },
    },
    computed: {
      displayLabel() {
        const label = this.item.displayLabel;
        if (typeof label === 'string') {
          return label;
        } else if (typeof label === 'object' && label.type === 'text') {
          return label.value;
        } else if (typeof label === 'object' && label.type === 'i18n') {
          return this.$t(label.value);
        }
        return 'N/A';
      },
      imageType() {
        const img = this.item.img;
        if (typeof img === 'object') {
          return img.type;
        }
        return 'null';
      },
    },
    methods: {
      openPage() {
        this.$emit('open', this.item);
      },
    },
  };
</script>
<style scoped>
  .search-item {
    width: 100%;
  }

  .search-item > .buttons-container {
    float: right;
    clear: right;
  }
</style>
