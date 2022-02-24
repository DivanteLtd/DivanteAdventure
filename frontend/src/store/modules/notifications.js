import Mercure from '../../util/mercure';
import notify from '../../util/NotificationApi';
import { i18n } from '../../i18n/i18n';

export default {
  namespaced: true,
  state: {
    notifications: [],
  },
  mutations: {
    setNotifications(state, notifications) {
      state.notifications = notifications;
    },
    addNewNotification(state, notification) {
      state.notifications = [ notification, ...state.notifications ];
    },
    deleteNotification(state, id) {
      state.notifications = state.notifications.filter(n => n.id !== id);
    },
    unmarkNotification(state, id) {
      state.notifications = state.notifications.map(n => (n.id === id ? { ...n, bold: false } : n));
    },
  },
  actions: {
    async loadNotifications(context) {
      try {
        const response = await context.rootState.apiClient.notifications.get();
        const mercure = new Mercure(response);
        mercure.openConnection();
        mercure.onAction('new-notification', data => context.dispatch('mercureNewNotification', data));
        const notifications = mercure.getResult();
        context.commit('setNotifications', notifications);
        return notifications.reverse();
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async mercureNewNotification(context, data) {
      const { description, subject } = data;
      const translated = i18n.t(`notifications.${description}`);
      const message = `${translated} ${subject}`;
      notify(message);
      context.commit('addNewNotification', data);
    },
    async delete(context, id) {
      try {
        context.commit('deleteNotification', id);
        await context.rootState.apiClient.notifications.delete(id);
        return context.state.notifications;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async unmark(context, id) {
      try {
        context.commit('unmarkNotification', id);
        await context.rootState.apiClient.notifications.unmark(id);
        return context.state.notifications;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
