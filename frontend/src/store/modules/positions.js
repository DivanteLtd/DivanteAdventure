export default {
  namespaced: true,
  state: {
    positions: [],
    levels: [],
    strategies: [],
  },
  mutations: {
    setPositions(state, positions) {
      state.positions = positions;
    },
    setLevels(state, levels) {
      state.levels = levels;
    },
    setStrategies(state, strategies) {
      state.strategies = strategies;
    },
  },
  actions: {
    async loadPositions(context) {
      try {
        const positions = await context.rootState.apiClient.positions.get();
        context.commit('setPositions', positions);
        return positions;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deletePosition(context, id) {
      try {
        return await context.rootState.apiClient.positions.delete(id);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async updatePosition(context, { id, name, strategyId }) {
      try {
        return await context.rootState.apiClient.positions.update(id, { name, strategyId });
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async createPosition(context, { name, strategyId }) {
      try {
        return await context.rootState.apiClient.positions.create({ name, strategyId });
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadLevels(context) {
      try {
        const levels = await context.rootState.apiClient.levels.get();
        context.commit('setLevels', levels);
        return levels;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deleteLevel(context, id) {
      try {
        return await context.rootState.apiClient.levels.delete(id);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async updateLevel(context, { id, name, priority = 0 }) {
      try {
        return await context.rootState.apiClient.levels.update(id, { name, priority });
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async createLevel(context, { name, strategyId, priority = 0 }) {
      try {
        return await context.rootState.apiClient.levels.create({ name, strategyId, priority });
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async loadStrategies(context) {
      try {
        const strategies = await context.rootState.apiClient.strategies.get();
        context.commit('setStrategies', strategies);
        return strategies;
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async deleteStrategy(context, id) {
      try {
        return await context.rootState.apiClient.strategies.delete(id);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async updateStrategy(context, { id, name }) {
      try {
        return await context.rootState.apiClient.strategies.update(id, { name });
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async createStrategy(context, { name }) {
      try {
        return await context.rootState.apiClient.strategies.create({ name });
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async assignToTribe(context, { tribeId, positionId }) {
      try {
        return await context.rootState.apiClient.tribes.addPosition(tribeId, positionId);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
    async unassignFromTribe(context, { tribeId, positionId }) {
      try {
        return await context.rootState.apiClient.tribes.deletePosition(tribeId, positionId);
      } catch (e) {
        context.dispatch('handleErrorResponse', e, { root: true });
        throw e;
      }
    },
  },
};
