<template>
  <div>
    <h3>{{ $t('Attachments') }}</h3>
    <ul>
      <li v-for="(attachment, index) in attachments" :key="index">
        <a class="attachmentLink" @click.stop="downloadFile(attachment.id)">
          {{ attachment.name }}
        </a>
      </li>
    </ul>
  </div>
</template>

<script>
  export default {
    name: 'AttachmentsList',
    props: {
      attachments: { type: Array, default: () => ([]) },
    },
    methods: {
      downloadFile(id) {
        this.$store.dispatch('FreeDays/downloadAttachment', id);
      },
    },
    i18n: { messages: {
      pl: {
        Attachments: 'Załączniki',
      },
    } },
  };
</script>

<style scoped>
  .attachmentLink {
    text-decoration: none;
    color: blue;
  }
</style>
