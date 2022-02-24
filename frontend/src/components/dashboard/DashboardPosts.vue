<template>
  <dashboard-card :title="$t('News in our company')" :loading="!loaded">
    <v-card-text class="pa-0">
      <ul class="post--list flex-list vertical pa-0">
        <dashboard-single-post v-for="(item, key) in paginatedPosts" :key="key" :post="item"/>
      </ul>
    </v-card-text>
    <v-divider/>
    <v-card-actions v-if="this.$vuetify.breakpoint.smAndUp" style="justify-content: center;">
      <v-pagination v-model="currentPage" :length="pagesCount"/>
    </v-card-actions>
  </dashboard-card>
</template>

<script>
  import DashboardCard from './DashboardCard';
  import DashboardSinglePost from './DashboardSinglePost';
  import { EventBus, eventNames } from '../../eventbus';
  import { mapGetters } from 'vuex';

  const PAGE_LENGTH = 3;

  export default {
    name: 'DashboardPosts',
    components: { DashboardSinglePost, DashboardCard },
    props: {
      posts: { type: Array, default: () => ([]) },
      loaded: { type: Boolean, default: false },
    },
    data() {
      return {
        currentPage: 1,
      };
    },
    computed: {
      ...mapGetters({
        isHr: 'Authorization/isHr',
        isManger: 'Authorization/isManager',
      }),
      pagesCount() {
        return Math.ceil(this.posts.length / PAGE_LENGTH);
      },
      paginatedPosts() {
        const firstElement = (this.currentPage - 1) * PAGE_LENGTH;
        return this.posts.sort((a, b) => b.id - a.id).slice(firstElement, firstElement + PAGE_LENGTH);
      },
    },
    methods: {
      showEditor() {
        EventBus.$emit(eventNames.showNewsEditor);
      },
    },
    i18n: {
      messages: {
        pl: {
          'News in our company': 'Co nowego w naszej firmie',
          'Create news': 'Utwórz wiadomość',
        },
      },
    },
  };
</script>

<style scoped>
  .flex-list.vertical {
    flex-direction: column;
  }
  .flex-list li{
    display: flex;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
  }
  .flex-list li:last-child {
    border: none;
  }
</style>
