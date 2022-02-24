<template>
  <div class="markdown-content" v-html="parsedText"></div>
</template>

<script>
  import marked from 'marked';

  export default {
    name: 'Markdown',
    props: {
      text: { type: String, default: '' },
      length: { type: Number, default: -1 },
    },
    computed: {
      parsedText() {
        let originalText = this.text;
        if (this.length > 0 && this.length < originalText.length) {
          originalText = originalText.substring(0, this.length);
          originalText = `${originalText}...`;
        }
        return marked(originalText);
      },
    },
  };
</script>

<style scoped>
  .markdown-content >>> img {
    max-width: 33%;
  }
</style>
<style>
  .markdown-content p{
    display: block;
    word-break: break-word;
  }
</style>
