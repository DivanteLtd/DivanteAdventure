<template>
  <v-btn block class="jbtn-file" :disabled="!enabled">
    <slot>{{ title }}</slot>
    <input ref="fileInput" type="file" @change="fileSelected">
  </v-btn>
</template>

<script>
  export default {
    name: 'FileUploader',
    props: {
      selectedCallback: { type: Function, default: () => {} },
      title: { type: String, default: '' },
      enabled: { type: Boolean, default: true },
    },
    methods: {
      fileSelected(e) {
        if (this.selectedCallback) {
          if (e.target.files[0]) {
            this.selectedCallback(e.target.files[0]);
          } else {
            this.selectedCallback(null);
          }
        }
        this.$refs.fileInput.value = '';
      },
    },
  };
</script>

<style scoped>
  .jbtn-file {
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .jbtn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    cursor: inherit;
    display: block;
  }
</style>
