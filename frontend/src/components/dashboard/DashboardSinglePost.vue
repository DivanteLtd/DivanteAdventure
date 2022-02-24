<template>
  <li class="post--item layout row ma-0 text--primary"
      :class="{'pa-1': $vuetify.breakpoint.xs, 'pa-4': $vuetify.breakpoint.smAndUp}"
      @click="show">
    <div class="post--content"
         :class="{'pa-2': $vuetify.breakpoint.xs, 'pa-4': $vuetify.breakpoint.smAndUp}"
         style="width: 100%">
      <article class="html">
        <div class="title-row pb-2">
          <div class="banner">
            <h3 v-if="post.title" class="title post--title">{{ post.title }}</h3>
            <div class="post--desc py-2 text--secondary" v-if="checkPostType === 'markdown'">
              <markdown :text="post.desc" :length="150"/>
            </div>
            <v-img v-if="post.banner" class="pt-2" :src="post.banner" :alt="post.title"/>
          </div>
          <div class="button-menu" v-if="canDelete">
          </div>
        </div>
      </article>
    </div>
  </li>
</template>

<script>
  import Markdown from '../utils/Markdown';
  import { EventBus, eventNames } from '../../eventbus';
  import moment from '@divante-adventure/work-moment';
  import { mapGetters } from 'vuex';

  const TYPE_MARKDOWN = 0;
  const TYPE_HTML = 1;
  export default {
    name: 'DashboardSinglePost',
    components: { Markdown },
    props: {
      post: { type: Object, required: true },
    },
    data() {
      return {
        types: { TYPE_MARKDOWN, TYPE_HTML },
        deleteDialogVisible: false,
        window: {
          width: 0,
          height: 0,
        },
      };
    },
    computed: {
      ...mapGetters({
        isHr: 'Authorization/isHr',
        isManager: 'Authorization/isManager',
      }),
      checkPostType() {
        if(this.post.type === this.types.TYPE_MARKDOWN) {
          return 'markdown';
        }
        else if(this.post.type === this.types.TYPE_HTML) {
          return 'html';
        }
        return '';
      },
      canDelete() {
        return this.isHr || this.isManager;
      },
      date() {
        return moment.unix(this.post.createdAt).format('DD MMM YYYY HH:mm');
      },
    },
    methods: {
      editPost() {
        this.visible = false;
        EventBus.$emit(eventNames.showNewsEditor, this.post);
      },
      async deletePost() {
        this.deleteDialogVisible = false;
        await this.$store.dispatch('News/deletePost', this.post.id);
        this.$store.dispatch('News/loadNews');
      },
      async show() {
        if (this.post.type === TYPE_MARKDOWN) {
          EventBus.$emit(eventNames.showNews, this.post);
        } else {
          const newsHtml = await this.$store.dispatch('News/loadHTMLNewsById', this.post.id);
          const specs = `directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no, width=${this.window.width}, height=${this.window.height}`;
          const newWindow = window.open('', 'winname', specs);
          newWindow.document.body.innerHTML = newsHtml;
        }
      },
      handleResize() {
        this.window.width = window.innerWidth;
        this.window.height = window.innerHeight;
      },
    },
    created() {
      window.addEventListener('resize', this.handleResize);
      this.handleResize();
    },
    destroyed() {
      window.removeEventListener('resize', this.handleResize);
    },
    i18n: {
      messages: {
        pl: {
          'Do you really want to delete this post?': 'Czy na pewno chcesz usunąć tego posta?',
          'Can not edit HTML news': 'Nie można edytować newsów HTML',
          'Edit': 'Edytuj',
          'Delete': 'Usuń',
        },
      },
    },
  };
</script>

<style lang="scss" scoped>
  .banner{
    max-width: 60%;
  }

  .title-row{
    display: flex;
    width: 100%;
    justify-content: space-between;
    align-items: center;
  }

  .post--item:hover {
    background: #f6f6f6;
    cursor: pointer;
  }

  .post--item a {
    text-decoration: none;
  }

  .post--title {
    display: block;
    word-break: break-word;
  }

  article.html {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    flex-wrap: wrap;

    img {
      margin-right: auto;
      max-width: 100%;
      max-height: 20em
    }
  }
  .button-menu {
    display: flex;
    align-items: center;
    justify-content: space-around;
  }
</style>
