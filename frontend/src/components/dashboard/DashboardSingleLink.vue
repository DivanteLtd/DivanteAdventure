<template>
  <div>
    <v-chip color="white">
      <span class="links" @click="redirect">
        <v-avatar>
          <v-img :src="getFavicon()" @error="isError = true"/>
        </v-avatar>
        {{ link.title }}
      </span>
    </v-chip>
    <v-menu offset-y v-if="isTribeMaster" @click.native.stop>
      <template v-slot:activator="{ on }">
        <v-btn v-on="on" icon>
          <v-icon>more_vert</v-icon>
        </v-btn>
      </template>
      <v-list>
        <v-list-item @click="showEditor">
          <v-list-item-title>{{ $t('Edit') }}</v-list-item-title>
        </v-list-item>
        <v-list-item @click="deleteLink">
          <v-list-item-title>{{ $t('Delete') }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </div>
</template>
<script>

  import { EventBus, eventNames } from '../../eventbus';
  import { mapGetters } from 'vuex';

  export default {
    name: 'DashboardSingleLink',
    props: {
      link: { type: Object, required: true },
    },
    data() {
      return {
        isError: false,
      };
    },
    computed: {
      ...mapGetters({
        isTribeMaster: 'Authorization/isTribeMaster',
      }),

    },
    methods: {
      redirect() {
        window.open(this.link.url, '_blank');
      },
      getFavicon() {
        if (this.isError) {
          return '/static/dvnt_logo_black.png';
        }
        const url = new URL(this.link.url);
        return `${url.protocol}//${url.hostname}/favicon.ico`;
      },
      showEditor() {
        EventBus.$emit(eventNames.showLinksEditor, this.link);
      },
      deleteLink() {
        EventBus.$emit(eventNames.deleteLink, this.link);
      },
    },
    i18n: { messages: {
      pl: {
        Edit: 'Edytuj',
        Delete: 'Usuń',
      },
    },
    },
  };
</script>

<style scoped>
  .links {
    cursor: pointer;
  }
</style>
