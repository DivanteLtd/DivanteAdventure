<template>
  <v-list-item class="info-tile">
    <v-list-item-content>
      {{ title }}
    </v-list-item-content>
    <div class="info-tile__content">
      <slot></slot>
    </div>
  </v-list-item>
</template>

<script>
  export default {
    name: 'InfoTile',
    props: {
      title: { type: String, default: '' },
    },
  };
</script>

<style scoped>
  .info-tile{
    font-size: 13px;
  }
  .info-tile__content{
    display: block;
    text-align: right;
  }
</style>
