<template>
  <v-dialog v-model="visible" :width="800">
    <v-card id="dialog-edit-post">
      <v-card-title>
        <span class="headline" v-if="post.id === -1">{{ $t('Add news') }}</span>
        <span class="headline" v-else>{{ $t('Edit news') }}</span>
      </v-card-title>
      <v-divider/>
      <v-card-text>
        <v-tabs v-model="selectedTab" centered>
          <v-tab>Markdown</v-tab>
          <v-tab>HTML</v-tab>
        </v-tabs>
        <v-tabs-items v-model="selectedTab">
          <v-tab-item>
            <v-text-field v-model="post.title" :label="$t('Title')"/>
            <div class="subheading mb-1">{{ $t('Content:') }}</div>
            <markdown-editor v-model="post.desc" ref="markdownEditor"/>
          </v-tab-item>
          <v-tab-item>
            <v-text-field v-model="post.title" :label="$t('Title')"/>
            <v-text-field v-model="post.banner"
                          :label="$t('Banner image URL')"
                          placeholder="https://optinmonster.com/wp-content/uploads/2017/09/perfect-welcome-email-for-new-subscribers.jpg"/>
            <div class="subheading mb-1">
              {{ $t('Paste HTML page here.') }}
              <i>{{ $t('Warning: This page will be displayed on different tab.') }}</i>
            </div>
            <v-textarea v-model="post.desc" style="font-family: monospace" rows="15" full-width no-resize solo/>
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn :disabled="!formValid" color="blue" text @click="save">{{ $t('Save') }}</v-btn>
        <v-btn color="black" text @click="visible = false">{{ $t('Close') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import MarkdownEditor from 'vue-simplemde/src/markdown-editor';
  import DOMPurify from 'dompurify';
  import 'simplemde/dist/simplemde.min.css';

  export default {
    name: 'PostEditView',
    components: { MarkdownEditor },
    data() {
      return {
        visible: false,
        post: { id: -1, title: '', banner: '', desc: '' },
        selectedTab: 0,
      };
    },
    computed: {
      formValid() {
        const { title, banner, desc } = this.post;
        const titleLength = (title || '').trim().length;
        const bannerLength = (banner || '').trim().length;
        const descLength = (desc || '').trim().length;
        if (this.selectedTab === 0) {
          return descLength > 0 && titleLength > 0;
        } else {
          return descLength > 0 && (titleLength > 0 || bannerLength > 0);
        }
      },
    },
    methods: {
      show(post) {
        if (this.visible) {
          return;
        }
        if (typeof post === 'undefined') {
          this.post = { id: -1, title: '', desc: '' };
        } else {
          this.post = {
            id: post.id,
            title: DOMPurify.sanitize(post.title),
            banner: DOMPurify.sanitize(post.banner),
            desc: DOMPurify.sanitize(post.desc),
          };
          this.selectedTab = post.type;
        }
        this.visible = true;
      },
      save() {
        const post = { ...this.post, type: this.selectedTab };
        post.desc = DOMPurify.sanitize(post.desc);
        post.title = DOMPurify.sanitize(post.title);
        if (post.id === -1) {
          this.$store.dispatch('News/add', post);
        } else {
          this.$store.dispatch('News/editPost', post);
        }
        this.$store.dispatch('News/loadNews');
        this.visible = false;
      },
    },
    beforeMount() {
      EventBus.$on(eventNames.showNewsEditor, this.show);
    },
    i18n: { messages: {
      pl: {
        'Content:': 'Treść:',
        'Title': 'Tytuł',
        'Add news': 'Utwórz wiadomość',
        'Edit news': 'Edytuj wiadomość',
        'Close': 'Zamknij',
        'Save': 'Zapisz',
        'Bold': 'Pogrub',
        'Paste HTML page here.': 'Wklej tutaj stronę HTML.',
        'Warning: This page will be displayed on different tab.': 'Uwaga: Ta strona zostanie wyświetlona w oddzielnej zakładce.',
      },
    },
    },
  };
</script>
