export default {
  namespaced: true,
  state: {
    news: [],
    html: '',
  },
  mutations: {
    setNews(state, news) {
      state.news = news;
    },
    addPost(state, post) {
      state.news.push(post);
    },
    setHTML(state, html) {
      state.html = html;
    },
  },
  actions: {
    async loadNews(context) {
      try {
        const news = await context.rootState.apiClient.news.getAll();
        context.commit('setNews', news);
        return news;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async add(context, post) {
      try {
        const news = await context.rootState.apiClient.news.add(post);
        context.commit('setNews', news);
        return news;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deletePost(context, id) {
      try {
        const news = await context.rootState.apiClient.news.delete(id);
        context.commit('setNews', news);
        return news;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async editPost(context, post) {
      try {
        const news = await context.rootState.apiClient.news.update(post.id, post);
        context.commit('setNews', news);
        return news;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadHTMLNewsById(context, id) {
      try {
        const html = await context.rootState.apiClient.news.getHtmlNewsById(id);
        context.commit('setHTML', html);
        return html;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
