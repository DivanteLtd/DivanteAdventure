<template>
  <v-dialog v-model="visible" :width="800">
    <v-card :class="{'post-view-mobile': $vuetify.breakpoint.xs}">
      <v-card-title class="title pb-4">
        <div style="float: right">
          <v-btn icon @click="visible = false">
            <v-icon>close</v-icon>
          </v-btn>
        </div>
        <span>{{ post.title }}</span>
      </v-card-title>
      <v-divider/>
      <v-card-text>
        <markdown :text="post.desc"/>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import Markdown from '../utils/Markdown';
  import { mapGetters } from 'vuex';

  export default {
    name: 'PostView',
    components: { Markdown },
    data() {
      return {
        visible: false,
        post: { title: '', desc: '', id: -1 },
      };
    },
    computed: {
      ...mapGetters({
        isHr: 'Authorization/isHr',
        isManger: 'Authorization/isManager',
      }),
    },
    methods: {
      show(post) {
        if (this.visible) {
          return;
        }
        this.post = post;
        this.visible = true;
      },
    },
    beforeMount() {
      EventBus.$on(eventNames.showNews, this.show);
    },
  };
</script>
<style scoped>
  span{
    padding-top: 12px;
    display: block;
    word-break: break-word;
  }
  .post-view-mobile span{
    font-size: large;
    word-break: break-word;
  }
  .title{
    display: table;
    width: 100%;
  }
</style>
<style>
  .post-view-mobile .v-btn--icon{
    width: unset;
  }
</style>
