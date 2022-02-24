<template>
  <v-dialog v-model="visible" :width="800">
    <v-card>
      <v-card-title>
        <span class="headline" v-if="link.id === -1">{{ $t('Add link') }}</span>
        <span class="headline" v-else>{{ $t('Edit link') }}</span>
      </v-card-title>
      <v-divider/>
      <v-card-text>
        <v-text-field v-model="link.title"
                      :label="$t('Title')"
                      :rules="rules.title"
                      counter="20">
        </v-text-field>
        <v-text-field ref="url" v-model="link.url" label="Url" :rules="rules.url"></v-text-field>
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

  export default {
    name: 'LinkEditView',
    data() {
      return {
        visible: false,
        link: { id: -1, title: '', url: '' },
        rules: {
          title: [
            v => !!v || this.$t('Title field is required'),
            v => v.length <= 20 || this.$t('Max 20 characters'),
          ],
          url: [
            v => !!v || this.$t('Url field is required'),
            // eslint-disable-next-line
            v => /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#()?&//=]*)/gm
              .test(v) || this.$t('Url is not valid'),
          ],
        },
      };
    },
    computed: {
      formValid() {
        return this.link.title.trim().length > 0
          && this.link.title.trim().length <= 20
          && this.link.url.trim().length > 0
          && (this.$refs.url && this.$refs.url.validate());
      },
    },
    methods: {
      show(link) {
        if (this.visibllle) {
          return;
        }
        if (typeof link === 'undefined') {
          this.link = { id: -1, title: '', url: '' };
        } else {
          this.link = link;
        }
        this.visible = true;
      },
      save() {
        if (this.link.id === -1) {
          this.$store.dispatch('Links/new', this.link);
        } else {
          this.$store.dispatch('Links/update', this.link);
        }
        this.visible = false;
      },
      delete(link) {
        if (link.id !== -1) {
          this.$store.dispatch('Links/delete', link.id);
        }
        this.visible = false;
      },
    },
    beforeMount() {
      EventBus.$on(eventNames.showLinksEditor, this.show);
      EventBus.$on(eventNames.deleteLink, this.delete);
    },
    i18n: { messages: {
      pl: {
        'Title': 'Tytuł',
        'Add link': 'Utwórz link',
        'Edit link': 'Edytuj link',
        'Close': 'Zamknij',
        'Save': 'Zapisz',
        'Title field is required': 'Pole "Tytuł" jest wymagane',
        'Url field is required': 'Pole "Url" jest wymagane',
        'Url is not valid': 'Url jest niepoprawny',
        'Max 20 characters': 'Maksymalnie 20 znaków',
      },
    },
    },
  };
</script>
